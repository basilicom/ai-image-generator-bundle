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
        $getRelativeAspectRatio = $this->aspectRatioCalculator->calculateAspectRatio($configuration->getAspectRatio(), 512);
        $uri = sprintf('%s/sdapi/v1/txt2img', $configuration->getBaseUrl());
        $method = Request::METHOD_POST;
        $payload = [
            'steps' => $configuration->getSteps(),
            'sd_model_checkpoint' => $configuration->getModel(),

            'prompt' => implode(',', $configuration->getPromptParts()),
            'negative_prompt' => implode(',', $configuration->getNegativePromptParts()),
            'seed' => $configuration->getSeed(),
            'width' => $getRelativeAspectRatio->getWidth(),
            'height' => $getRelativeAspectRatio->getHeight(),

            'batch_size' => 1,
            'sampler_name' => 'Euler a',
            'cfg_scale' => 7,
        ];

        return new ServiceRequest($uri, $method, $payload);
    }

    public function createImg2ImgRequest(Configuration $configuration, AiImage $baseImage): ServiceRequest
    {
        $uri = sprintf('%s/sdapi/v1/img2img', $configuration->getBaseUrl());
        $method = Request::METHOD_POST;
        $payload = [
            'init_images' => [$baseImage->getData()],

            'steps' => $configuration->getSteps(),
            'sd_model_checkpoint' => $configuration->getModel(),

            'prompt' => implode(',', $configuration->getPromptParts()),
            'negative_prompt' => implode(',', $configuration->getNegativePromptParts()),
            'seed' => $configuration->getSeed(),

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
            'denoising_strength' => 0.1, // no/nearly no additional rendering of new features

            'steps' => $configuration->getSteps(),
            'sd_model_checkpoint' => $configuration->getModel(),

            'prompt' => implode(',', $configuration->getPromptParts()),
            'negative_prompt' => implode(',', $configuration->getNegativePromptParts()),
            'seed' => $configuration->getSeed(),

            'script_name' => 'sd upscale',
            'script_args' => ['', 64, $configuration->getUpscaler(), 2], // unknown, tileOverlap, upscaler, factor

            'batch_size' => 1,
            'sampler_name' => 'Euler a',
            'cfg_scale' => 7,
        ];

        /**
         *  todo ==>  use ControlNet Tiling to Upscale
         *      ==> controlnet
         *               "alwayson_scripts": {
         *                  "controlnet": {
         *                  "args": [
         *                      {
         *                          "input_image": $encodedImage, // source image to be get preprocessed image to be applied on source
         *                          "module": "depth",
         *                          "model": "diff_control_sd15_depth_fp16 [978ef0a1]"
         *                      }
         *                  ]
         *                  }
         *               }
         */

        return new ServiceRequest($uri, $method, $payload);
    }
}
