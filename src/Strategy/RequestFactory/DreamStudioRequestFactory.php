<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory;

use Basilicom\AiImageGeneratorBundle\Config\Configuration;
use Basilicom\AiImageGeneratorBundle\Helper\AspectRatioCalculator;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\ServiceRequest;
use Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory;
use GuzzleHttp\Client;
use Pimcore\Model\Asset;
use Symfony\Component\HttpFoundation\Request;

class DreamStudioRequestFactory implements RequestFactory
{
    private AspectRatioCalculator $aspectRatioCalculator;

    public function __construct(AspectRatioCalculator $aspectRatioCalculator)
    {
        $this->aspectRatioCalculator = $aspectRatioCalculator;
    }

    public function createTxt2ImgRequest(Configuration $configuration): ServiceRequest
    {
        $aspectRatio = $configuration->getAspectRatio();
        $getRelativeAspectRatio = $this->aspectRatioCalculator->calculateAspectRatio($aspectRatio, 64);

        $uri = sprintf('%s/v1/generation/%s/text-to-image', $configuration->getBaseUrl(), $configuration->getModel());
        $method = Request::METHOD_POST;

        $payload = [
            'steps' => $configuration->getSteps(),
            'width' => $getRelativeAspectRatio['width'], // increment of 64
            'height' => $getRelativeAspectRatio['height'], // increment of 64

            // todo => different weight based on h1, h2. also set max 10 prompts..
            'text_prompts' => [
                ['text' => implode(',', $configuration->getPromptParts()), 'weight' => 1.0]
            ],

            // todo => not supported?
            //'negative_prompt' => $config->getNegativePrompt(),

            'seed' => 0, // [0 .. 4294967295]

            'sampler' => 'K_EULER_ANCESTRAL', // DDIM DDPM K_DPMPP_2M K_DPMPP_2S_ANCESTRAL K_DPM_2 K_DPM_2_ANCESTRAL K_EULER K_EULER_ANCESTRAL K_HEUN K_LMS
            'cfg_scale' => 7, // [ 0 .. 35 ]
            'samples' => 1
        ];

        return new ServiceRequest($uri, $method, $payload, ['Authorization' => $configuration->getApiKey()]);
    }

    public function createImg2ImgRequest(Configuration $configuration, AiImage $baseImage): ServiceRequest
    {
        $uri = sprintf('%s/v1/generation/%s/image-to-image', $configuration->getBaseUrl(), $configuration->getModel());
        $method = Request::METHOD_POST;

        $payload = [
            'init_image' => $baseImage->getData(),
            'image_strength' => 0.9,

            'steps' => $configuration->getSteps(),
            'seed' => 0, // [0 .. 4294967295]

            'sampler' => 'K_EULER_ANCESTRAL', // DDIM DDPM K_DPMPP_2M K_DPMPP_2S_ANCESTRAL K_DPM_2 K_DPM_2_ANCESTRAL K_EULER K_EULER_ANCESTRAL K_HEUN K_LMS
            'cfg_scale' => 7, // [ 0 .. 35 ]
            'samples' => 1
        ];

        // todo => different weight based on h1, h2. also set max 10 prompts..
        foreach ($configuration->getPromptParts() as $index => $promptPart) {
            $payload['text_prompts[' . $index . '][text]'] = $promptPart;
            $payload['text_prompts[' . $index . '][weight]'] = 1;
        }

        return new ServiceRequest($uri, $method, $payload, ['Authorization' => $configuration->getApiKey()], true);
    }

    public function createUpscaleRequest(Configuration $configuration, AiImage $baseImage): ServiceRequest
    {
        $aspectRatio = $configuration->getAspectRatio();
        $getRelativeAspectRatio = $this->aspectRatioCalculator->calculateAspectRatio($aspectRatio, 64);

        $uri = sprintf('%s/v1/generation/%s/image-to-image/upscale', $configuration->getBaseUrl(), $configuration->getUpscaler());
        $method = Request::METHOD_POST;

        $tmpFilePath = sys_get_temp_dir() . '/ai-image-generator.png';
        file_put_contents($tmpFilePath, $baseImage->getData(true));

        $payload = [
            [
                'name' => 'image',
                'contents' => fopen($tmpFilePath, 'rb')
            ],
            [
                'name' => 'width',
                'contents' => $getRelativeAspectRatio['width'] * 2, // todo => set 2k if esgran, use 4k to latent upscale but with prompt.
            ]
        ];

        unlink($tmpFilePath);

        return new ServiceRequest($uri, $method, $payload, ['Authorization' => $configuration->getApiKey()], true);
    }
}
