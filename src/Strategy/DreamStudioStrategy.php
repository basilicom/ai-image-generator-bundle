<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy;

use Basilicom\AiImageGeneratorBundle\Config\Configuration;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\MetaDataEnum;
use Basilicom\AiImageGeneratorBundle\Service\RequestService;
use Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory\DreamStudioRequestFactory;

class DreamStudioStrategy extends Strategy
{
    public function __construct(RequestService $requestService, DreamStudioRequestFactory $requestFactory)
    {
        parent::__construct($requestService, $requestFactory);
    }

    protected function createAiImageFromResponse(Configuration $config, array $response): AiImage
    {
        $imageData = $response['artifacts'][0]['base64'];
        $seed = $response['artifacts'][0]['seed'];

        $aiImage = new AiImage();
        $aiImage->setData($imageData);
        $aiImage->setMetadata(MetaDataEnum::MODEL, $config->getModel());
        $aiImage->setMetadata(MetaDataEnum::STEPS, $config->getSteps());
        $aiImage->setMetadata(MetaDataEnum::PROMPT, implode(', ', $config->getPromptParts()));
        $aiImage->setMetadata(MetaDataEnum::NEGATIVE_PROMPT, implode(', ', $config->getNegativePromptParts()));
        $aiImage->setMetadata(MetaDataEnum::SEED, $seed);

        return $aiImage;
    }
}
