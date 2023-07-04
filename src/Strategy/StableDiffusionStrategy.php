<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy;

use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Service\RequestService;
use Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory\StableDiffusionRequestFactory;

class StableDiffusionStrategy extends Strategy
{
    public function __construct(RequestService $requestService, StableDiffusionRequestFactory $requestFactory)
    {
        parent::__construct($requestService, $requestFactory);
    }

    protected function createAiImageFromResponse(array $response): AiImage
    {
        $imageData = $response['images'][0];
        $info = json_decode($response['info'], true);

        $aiImage = new AiImage();
        $aiImage->setData($imageData);
        $aiImage->setMetadata('prompt', $info['prompt'] ?? '');
        $aiImage->setMetadata('negative-prompt', $info['negative_prompt'] ?? '');
        $aiImage->setMetadata('seed', $info['seed'] ?? 0);
        $aiImage->setMetadata('subseed', $info['subseed'] ?? 0);

        return $aiImage;
    }
}
