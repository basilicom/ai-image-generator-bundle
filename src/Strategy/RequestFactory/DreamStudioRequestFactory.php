<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory;

use Basilicom\AiImageGeneratorBundle\Config\ServiceConfiguration;
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

    public function createTxt2ImgRequest(ServiceConfiguration|DreamStudioApiConfig $configuration): ServiceRequest
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
        ServiceConfiguration|DreamStudioApiConfig $configuration,
        AiImage                                   $baseImage
    ): ServiceRequest {
        return $this->createTxt2ImgRequest($configuration);
    }

    public function createUpscaleRequest(
        ServiceConfiguration|DreamStudioApiConfig $configuration,
        AiImage                                   $baseImage
    ): ServiceRequest {
        $uri = sprintf('%s/generation/%s/image-to-image/upscale', rtrim($configuration->getBaseUrl(), '/'), $configuration->getUpscaler());
        $method = Request::METHOD_POST;

        $tmpFilePath = sys_get_temp_dir() . '/ai-image-generator--dream-studio.png';
        file_put_contents($tmpFilePath, $baseImage->getData(true));

        $targetAspectRatio = $this->aspectRatioCalculator->calculateAspectRatio($configuration->getAspectRatio(), 4096);

        $payload = [
            [
                'name' => 'image',
                'contents' => fopen($tmpFilePath, 'rb')
            ],
            [
                'name' => 'width',
                'contents' => $targetAspectRatio->getWidth()
            ],
            [
                'name' => 'height',
                'contents' => $targetAspectRatio->getHeight()
            ]
        ];

        unlink($tmpFilePath);

        return new ServiceRequest($uri, $method, $payload, ['Authorization' => $configuration->getApiKey()], true);
    }

    public function createInpaintBackgroundRequest(ServiceConfiguration $configuration, AiImage $baseImage): ServiceRequest
    {
        $uri = sprintf('%s/generation/%s/image-to-image/masking', rtrim($configuration->getBaseUrl(), '/'), $configuration->getInpaintModel());
        $method = Request::METHOD_POST;

        $tmpImageFilePath = sys_get_temp_dir() . '/ai-image-generator--dream-studio--inpaint.png';
        file_put_contents($tmpImageFilePath, $baseImage->getData(true));

        // we could also use "mask_source=INIT_IMAGE_ALPHA" but i want to use the mask image also for other services
        $tmpMaskFilePath = sys_get_temp_dir() . '/ai-image-generator--dream-studio--mask.png';
        file_put_contents($tmpMaskFilePath, $baseImage->getMask(true));

        $payload = [
            ['name' => 'text_prompts[0][text]', 'contents' => implode(',', $configuration->getPromptParts())],
            ['name' => 'text_prompts[0][weight]', 'contents' => 1.0],

            ['name' => 'init_image', 'contents' => fopen($tmpImageFilePath, 'rb')],
            ['name' => 'mask_image', 'contents' => fopen($tmpMaskFilePath, 'rb')],
            ['name' => 'mask_source', 'contents' => 'MASK_IMAGE_BLACK'],

            ['name' => 'steps', 'contents' => $configuration->getSteps()],
            ['name' => 'sampler', 'contents' => 'K_EULER_ANCESTRAL'], // DDIM DDPM K_DPMPP_2M K_DPMPP_2S_ANCESTRAL K_DPM_2 K_DPM_2_ANCESTRAL K_EULER K_EULER_ANCESTRAL K_HEUN K_LMS
            ['name' => 'cfg_scale', 'contents' => 7],
            ['name' => 'samples', 'contents' => 1],
        ];

        unlink($tmpImageFilePath);
        unlink($tmpMaskFilePath);

        return new ServiceRequest($uri, $method, $payload, ['Authorization' => $configuration->getApiKey()], true);
    }
}
