<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory;

use Basilicom\AiImageGeneratorBundle\Config\Configuration;
use Basilicom\AiImageGeneratorBundle\Config\Model\DreamStudioApiConfig;
use Basilicom\AiImageGeneratorBundle\Helper\AspectRatioCalculator;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\ServiceRequest;
use Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory;
use Symfony\Component\HttpFoundation\Request;

class DreamStudioRequestFactory implements RequestFactory
{
    private AspectRatioCalculator $aspectRatioCalculator;

    public function __construct(AspectRatioCalculator $aspectRatioCalculator)
    {
        $this->aspectRatioCalculator = $aspectRatioCalculator;
    }

    public function createTxt2ImgRequest(Configuration|DreamStudioApiConfig $configuration): ServiceRequest
    {
        $aspectRatio = $configuration->getAspectRatio();
        $getRelativeAspectRatio = $this->aspectRatioCalculator->calculateAspectRatio($aspectRatio, 512, 64);

        $uri = sprintf('%s/generation/%s/text-to-image', rtrim($configuration->getBaseUrl(), '/'), $configuration->getModel());
        $method = Request::METHOD_POST;

        $payload = [
            'steps' => $configuration->getSteps(),
            'width' => $getRelativeAspectRatio->getWidth(),
            'height' => $getRelativeAspectRatio->getHeight(),

            'text_prompts' => [
                ['text' => implode(',', $configuration->getPromptParts()), 'weight' => 1.0]
            ],

            'seed' => max($configuration->getSeed(), 0), // [0 .. 4294967295]

            'sampler' => 'K_EULER_ANCESTRAL', // DDIM DDPM K_DPMPP_2M K_DPMPP_2S_ANCESTRAL K_DPM_2 K_DPM_2_ANCESTRAL K_EULER K_EULER_ANCESTRAL K_HEUN K_LMS
            'cfg_scale' => 7, // [ 0 .. 35 ]
            'samples' => 1
        ];

        return new ServiceRequest($uri, $method, $payload, ['Authorization' => $configuration->getApiKey()]);
    }

    public function createImgVariationsRequest(
        Configuration|DreamStudioApiConfig $configuration,
        AiImage                            $baseImage
    ): ServiceRequest {
        return $this->createTxt2ImgRequest($configuration);
    }

    public function createUpscaleRequest(
        Configuration|DreamStudioApiConfig $configuration,
        AiImage                            $baseImage
    ): ServiceRequest {
        $uri = sprintf('%s/generation/%s/image-to-image/upscale', rtrim($configuration->getBaseUrl(), '/'), $configuration->getUpscaler());
        $method = Request::METHOD_POST;

        $tmpFilePath = sys_get_temp_dir() . '/ai-image-generator--dream-studio.png';
        file_put_contents($tmpFilePath, $baseImage->getData(true));

        $imageSize = getimagesize($tmpFilePath);
        $width = $imageSize[0];
        $height = $imageSize[1];

        $payload = [
            [
                'name' => 'image',
                'contents' => fopen($tmpFilePath, 'rb')
            ]
        ];

        if ($width > $height) {
            $payload[] = [
                'name' => 'width',
                'contents' => $width * 2
            ];
        } else {
            $payload[] = [
                'name' => 'height',
                'contents' => $height * 2
            ];
        }

        unlink($tmpFilePath);

        return new ServiceRequest($uri, $method, $payload, ['Authorization' => $configuration->getApiKey()], true);
    }
}
