<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory;

use Basilicom\AiImageGeneratorBundle\Config\Configuration;
use Basilicom\AiImageGeneratorBundle\Config\Model\StableDiffusionApiConfig;
use Basilicom\AiImageGeneratorBundle\Helper\AspectRatioCalculator;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\ServiceRequest;
use Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory;
use Symfony\Component\HttpFoundation\Request;

class StableDiffusionRequestFactory implements RequestFactory
{
    private AspectRatioCalculator $aspectRatioCalculator;

    public function __construct(AspectRatioCalculator $aspectRatioCalculator)
    {
        $this->aspectRatioCalculator = $aspectRatioCalculator;
    }

    public function createTxt2ImgRequest(Configuration $configuration): ServiceRequest
    {
        $prompt = implode(',', $configuration->getPromptParts());
        $negativePrompt = implode(',', $configuration->getNegativePromptParts());
        $getRelativeAspectRatio = $this->aspectRatioCalculator->calculateAspectRatio($configuration->getAspectRatio());

        $uri = sprintf('%s/sdapi/v1/txt2img', $configuration->getBaseUrl());
        $method = Request::METHOD_POST;
        $payload = [
            'steps' => $configuration->getSteps(),
            'sd_model_checkpoint' => $configuration->getModel(),

            'prompt' => $prompt,
            'negative_prompt' => $negativePrompt,
            'width' => $getRelativeAspectRatio['width'],
            'height' => $getRelativeAspectRatio['height'],

            // todo => make configurable
            'seed' => -1,
            'batch_size' => 1,
            'sampler_name' => 'Euler a',
            'cfg_scale' => 7,
        ];

        return new ServiceRequest($uri, $method, $payload);
    }

    public function createImg2ImgRequest(Configuration $configuration, AiImage $baseImage): ServiceRequest
    {
        $prompt = implode(',', $configuration->getPromptParts());
        $negativePrompt = implode(',', $configuration->getNegativePromptParts());

        $uri = sprintf('%s/sdapi/v1/img2img', $configuration->getBaseUrl());
        $method = Request::METHOD_POST;
        $payload = [
            'init_images' => [$baseImage->getData()],

            'steps' => $configuration->getSteps(),
            'sd_model_checkpoint' => $configuration->getModel(),

            'prompt' => $prompt,
            'negative_prompt' => $negativePrompt,

            // todo => make configurable
            'seed' => -1,
            'batch_size' => 1,
            'sampler_name' => 'Euler a',
            'cfg_scale' => 7,
        ];

        return new ServiceRequest($uri, $method, $payload);
    }

    public function createUpscaleRequest(Configuration|StableDiffusionApiConfig $configuration, AiImage $baseImage): ServiceRequest
    {
        $uri = sprintf('%s/sdapi/v1/img2img', $configuration->getBaseUrl());
        $method = Request::METHOD_POST;
        $payload = [
            'init_images' => [$baseImage->getData()],
            'denoising_strength' => 0, // not additional rendering of new features

            'steps' => $configuration->getSteps(),
            'sd_model_checkpoint' => $configuration->getModel(),

            'prompt' => '',
            'negative_prompt' => '',

            'script_name' => 'sd upscale',
            'script_args' => ['', 64, $configuration->getUpscaler(), 2], // unknown, tileOverlap, upscaler, factor

            // todo => make configurable
            'seed' => -1,
            'batch_size' => 1,
            'sampler_name' => 'Euler a',
            'cfg_scale' => 7,
        ];

        // todo ==>  use ControlNet Tiling to Upscale

        return new ServiceRequest($uri, $method, $payload);
    }
}
