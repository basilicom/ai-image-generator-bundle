<?php

namespace Basilicom\AiImageGeneratorBundle\Model;

use Basilicom\AiImageGeneratorBundle\Config\ConfigurationService;
use Basilicom\AiImageGeneratorBundle\Config\Model\StableDiffusionApiConfig;
use Basilicom\AiImageGeneratorBundle\Helper\AspectRatioCalculator;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation;

class RequestFactory
{
    private ConfigurationService $configurationService;

    public function __construct(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }

    public function getRequest(): Request
    {
        $config = $this->configurationService->getConfiguration();
        if ($config instanceof StableDiffusionApiConfig) {
            return $this->createStableDiffusionApiRequest($config);
        } else {
            throw new InvalidArgumentException('Configured API is currently not supported.');
        }

        // todo ==> add additional APIs
    }

    private function createStableDiffusionApiRequest(StableDiffusionApiConfig $config): Request
    {
        // todo ==> inject from outside
        $aspectRatio = AspectRatioCalculator::calculateAspectRatio('16:9');

        // SD-API specific
        $uri = $config->getBaseUrl() . '/sdapi/v1/txt2img';
        $method = HttpFoundation\Request::METHOD_POST;
        $payload = [
            'steps' => 10, // todo ==> read config
            'sd_model_checkpoint' => 'realisticVisionV20_v20', // todo ==> read config

            'prompt' => 'b&w photo of cat sitting on a stone, half body, body, high detailed skin, skin pores, coastline, overcast weather, wind, waves, 8k uhd, dslr, soft lighting, high quality, film grain, Fujifilm XT3',
            'negative_prompt' => '(semi-realistic, cgi, 3d, render, sketch, cartoon, drawing, anime:1.4), text, close up, cropped, out of frame, worst quality, low quality, jpeg artifacts, ugly, duplicate, morbid, mutilated, extra fingers, mutated hands, poorly drawn hands, poorly drawn face, mutation, deformed, blurry, dehydrated, bad anatomy, bad proportions, extra limbs, cloned face, disfigured, gross proportions, malformed limbs, missing arms, missing legs, extra arms, extra legs, fused fingers, too many fingers, long neck',

            'seed' => -1,
            'batch_size' => 1,

            'width' => $aspectRatio['width'],
            'height' => $aspectRatio['height'],

            'sampler_index' => 'Euler a',
            'cfg_scale' => 7,
        ];
        // -------------------

        return new Request($uri, $method, $payload);
    }
}
