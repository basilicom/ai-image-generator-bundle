<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\ImageGeneration;

use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGenerationConfig;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\MetaDataEnum;
use Basilicom\AiImageGeneratorBundle\Service\RequestService;
use Basilicom\AiImageGeneratorBundle\Strategy\ImageGeneration\RequestFactory\StableDiffusionRequestFactory;
use Psr\Http\Message\ResponseInterface;

class StableDiffusionStrategy extends Strategy
{
    public function __construct(RequestService $requestService, StableDiffusionRequestFactory $requestFactory)
    {
        parent::__construct($requestService, $requestFactory);
    }

    protected function createAiImageFromResponse(ImageGenerationConfig $config, ResponseInterface $response): AiImage
    {
        $response = json_decode($response->getBody()->getContents(), true);

        $imageData = (string)$response['images'][0];
        $info = json_decode((string)$response['info'], true);

        $aiImage = $this->createAiImageObject($config, $imageData);
        $aiImage->setMetadata(MetaDataEnum::SEED, $info['seed'] ?? 0);
        $aiImage->setMetadata(MetaDataEnum::SUBSEED, $info['subseed'] ?? 0);

        return $this->createAiImageObject($config, $imageData);
    }
}
