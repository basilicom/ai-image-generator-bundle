<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy;

use Basilicom\AiImageGeneratorBundle\Config\AbstractConfiguration;
use Basilicom\AiImageGeneratorBundle\Config\Model\DreamStudioApiConfig;
use Basilicom\AiImageGeneratorBundle\Helper\AspectRatioCalculator;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\ServiceRequest;
use Basilicom\AiImageGeneratorBundle\Service\RequestService;
use Exception;
use Symfony\Component\HttpFoundation\Request;

class DreamStudioStrategy implements Strategy
{
    private const API_URL = '%s/v1/generation/%s/text-to-image';

    private DreamStudioApiConfig $config;
    private RequestService $requestService;
    private AspectRatioCalculator $aspectRatioCalculator;

    public function __construct(RequestService $requestService, AspectRatioCalculator $aspectRatioCalculator)
    {
        $this->requestService = $requestService;
        $this->aspectRatioCalculator = $aspectRatioCalculator;
    }

    public function setConfig(AbstractConfiguration|DreamStudioApiConfig $config): void
    {
        $this->config = $config;
    }

    /**
     * @throws Exception
     */
    public function requestImage(): AiImage
    {
        $serviceRequest = $this->createServiceRequest();
        $serviceResponse = $this->requestService->generateImage($serviceRequest);

        $imageData = base64_decode($serviceResponse['artifacts'][0]['base64']);
        $seed = $serviceResponse['artifacts'][0]['seed'];

        $aiImage = new AiImage();
        $aiImage->setData($imageData);
        $aiImage->setMetadata('prompt', implode(', ', $this->config->getPromptParts()));
        $aiImage->setMetadata('seed', $seed);

        return $aiImage;
    }

    private function createServiceRequest(): ServiceRequest
    {
        $aspectRatio = $this->config->getAspectRatio();

        $getRelativeAspectRatio = $this->aspectRatioCalculator->calculateAspectRatio($aspectRatio, 64);

        $uri = sprintf(self::API_URL, $this->config->getBaseUrl(), $this->config->getModel());
        $method = Request::METHOD_POST;

        $payload = [
            'steps' => $this->config->getSteps(),
            'width' => $getRelativeAspectRatio['width'], // increment of 64
            'height' => $getRelativeAspectRatio['height'], // increment of 64

            // todo => different weight based on h1, h2. also set max 10 prompts..
            'text_prompts' => [
                ['text' => implode(',', $this->config->getPromptParts()), 'weight' => 1.0]
            ],

            // todo => not supported?
            //'negative_prompt' => $config->getNegativePrompt(),

            'seed' => 0, // [0 .. 4294967295]

            'sampler_index' => 'K_EULER_ANCESTRAL', // DDIM DDPM K_DPMPP_2M K_DPMPP_2S_ANCESTRAL K_DPM_2 K_DPM_2_ANCESTRAL K_EULER K_EULER_ANCESTRAL K_HEUN K_LMS
            'cfg_scale' => 7, // [ 0 .. 35 ]
        ];

        return new ServiceRequest($uri, $method, $payload, ['Authorization' => $this->config->getApiKey()]);
    }
}
