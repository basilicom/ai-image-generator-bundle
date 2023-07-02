<?php

namespace Basilicom\AiImageGeneratorBundle\Model;

use Basilicom\AiImageGeneratorBundle\Config\AbstractConfiguration;
use Basilicom\AiImageGeneratorBundle\Config\Model\DreamStudioApiConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\StableDiffusionApiConfig;
use Basilicom\AiImageGeneratorBundle\Helper\AspectRatioCalculator;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpFoundation\Request;

class ServiceRequestFactory
{
    private AspectRatioCalculator $aspectRatioCalculator;

    public function __construct(AspectRatioCalculator $aspectRatioCalculator)
    {
        $this->aspectRatioCalculator = $aspectRatioCalculator;
    }

    public function createServiceRequest(AbstractConfiguration $config): ServiceRequest
    {
        if ($config instanceof StableDiffusionApiConfig) {
            return $this->createStableDiffusionApiRequest($config);
        } elseif ($config instanceof DreamStudioApiConfig) {
            return $this->createDreamStudioApiRequest($config);
        } else {
            throw new InvalidArgumentException('Configured API is currently not supported.');
        }
    }

    private function createStableDiffusionApiRequest(StableDiffusionApiConfig $config): ServiceRequest
    {
        $getRelativeAspectRatio = $this->aspectRatioCalculator->calculateAspectRatio($config->getAspectRatio());

        $uri = sprintf('%s/sdapi/v1/txt2img', $config->getBaseUrl());
        $method = Request::METHOD_POST;
        $payload = [
            'steps' => $config->getSteps(),
            'sd_model_checkpoint' => $config->getModel(),

            'prompt' => $config->getPrompt(),
            'negative_prompt' => $config->getNegativePrompt(),

            // todo
            'seed' => -1,
            'batch_size' => 1,

            'width' => $getRelativeAspectRatio['width'],
            'height' => $getRelativeAspectRatio['height'],

            'sampler_index' => 'Euler a',
            'cfg_scale' => 7,
        ];
        // -------------------

        return new ServiceRequest($uri, $method, $payload);
    }

    private function createDreamStudioApiRequest(DreamStudioApiConfig $config): ServiceRequest
    {
        $getRelativeAspectRatio = $this->aspectRatioCalculator->calculateAspectRatio($config->getAspectRatio());

        $uri = sprintf('%s/v1/generation/%s/text-to-image', $config->getBaseUrl(), $config->getModel());
        $method = Request::METHOD_POST;

        $payload = [
            'steps' => $config->getSteps(),
            'sd_model_checkpoint' => $config->getModel(),

            'text_prompts' => [
                $config->getPrompt() // todo ==> use different strategies to build prompts as we might want to add h1 with higher weight than h2 contexts
            ],
            //'negative_prompt' => $config->getNegativePrompt(), // todo => not supported?

            'seed' => -1,
            'batch_size' => 1,

            'width' => $getRelativeAspectRatio['width'],
            'height' => $getRelativeAspectRatio['height'],

            'sampler_index' => 'K_EULER_ANCESTRAL',
            'cfg_scale' => 7,
        ];
        // -------------------

        return new ServiceRequest($uri, $method, $payload);
    }
}
