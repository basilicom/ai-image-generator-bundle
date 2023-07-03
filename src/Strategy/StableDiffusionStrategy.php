<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy;

use Basilicom\AiImageGeneratorBundle\Config\AbstractConfiguration;
use Basilicom\AiImageGeneratorBundle\Helper\AspectRatioCalculator;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\ServiceRequest;
use Basilicom\AiImageGeneratorBundle\Service\RequestService;
use Exception;
use Symfony\Component\HttpFoundation\Request;

class StableDiffusionStrategy implements Strategy
{
    private const API_URL = '%s/sdapi/v1/txt2img';

    private AbstractConfiguration $config;
    private RequestService $requestService;
    private AspectRatioCalculator $aspectRatioCalculator;

    public function __construct(RequestService $requestService, AspectRatioCalculator $aspectRatioCalculator)
    {
        $this->requestService = $requestService;
        $this->aspectRatioCalculator = $aspectRatioCalculator;
    }

    public function setConfig(AbstractConfiguration $config): void
    {
        $this->config = $config;
    }

    /**
     * @throws Exception
     */
    public function requestImage(): AiImage
    {
        $prompt = implode(',', $this->config->getPromptParts());
        $negativePrompt = implode(',', $this->config->getNegativePromptParts());
        $aspectRatio = $this->config->getAspectRatio();

        $serviceRequest = $this->createServiceRequest($prompt, $negativePrompt, $aspectRatio);
        $serviceResponse = $this->requestService->generateImage($serviceRequest);

        $imageData = base64_decode($serviceResponse['images'][0]);
        $info = json_decode($serviceResponse['info'], true);

        $aiImage = new AiImage();
        $aiImage->setData($imageData);
        $aiImage->setMetadata('prompt', $info['prompt']);
        $aiImage->setMetadata('negative-prompt', $info['negative_prompt']);
        $aiImage->setMetadata('seed', $info['seed']);
        $aiImage->setMetadata('subseed', $info['subseed']);

        return $aiImage;
    }

    private function createServiceRequest(string $prompt, string $negativePrompt, string $aspectRatio): ServiceRequest
    {
        $getRelativeAspectRatio = $this->aspectRatioCalculator->calculateAspectRatio($aspectRatio);

        $uri = sprintf(self::API_URL, $this->config->getBaseUrl());
        $method = Request::METHOD_POST;
        $payload = [
            'steps' => $this->config->getSteps(),
            'sd_model_checkpoint' => $this->config->getModel(),

            'prompt' => $prompt,
            'negative_prompt' => $negativePrompt,
            'width' => $getRelativeAspectRatio['width'],
            'height' => $getRelativeAspectRatio['height'],

            // todo => make configurable
            'seed' => -1,
            'batch_size' => 1,
            'sampler_index' => 'Euler a',
            'cfg_scale' => 7,
        ];

        return new ServiceRequest($uri, $method, $payload);
    }
}
