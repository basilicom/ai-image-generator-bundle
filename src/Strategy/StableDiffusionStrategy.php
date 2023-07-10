<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy;

use Basilicom\AiImageGeneratorBundle\Config\Configuration;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\MetaDataEnum;
use Basilicom\AiImageGeneratorBundle\Service\RequestService;
use Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory\StableDiffusionRequestFactory;

class StableDiffusionStrategy extends Strategy
{
    public function __construct(RequestService $requestService, StableDiffusionRequestFactory $requestFactory)
    {
        parent::__construct($requestService, $requestFactory);
    }

    protected function createAiImageFromResponse(Configuration $config, array $response): AiImage
    {
        $imageData = $response['images'][0];
        $info = json_decode($response['info'], true);
        $seed = $info['seed'] ?? 0;
        $subSeed = $info['subseed'] ?? 0;

        $aiImage = new AiImage();
        $aiImage->setData($imageData);
        $aiImage->setMetadata(MetaDataEnum::MODEL, $config->getModel());
        $aiImage->setMetadata(MetaDataEnum::STEPS, $config->getSteps());
        $aiImage->setMetadata(MetaDataEnum::PROMPT, implode(', ', $config->getPromptParts()));
        $aiImage->setMetadata(MetaDataEnum::NEGATIVE_PROMPT, implode(', ', $config->getNegativePromptParts()));
        $aiImage->setMetadata(MetaDataEnum::SEED, $seed);
        $aiImage->setMetadata(MetaDataEnum::SUBSEED, $subSeed);

        return $aiImage;
    }
}
