<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\ImageGeneration;

use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGenerationConfig;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\MetaDataEnum;
use Basilicom\AiImageGeneratorBundle\Service\RequestService;
use Basilicom\AiImageGeneratorBundle\Strategy\ImageGeneration\RequestFactory\DreamStudioRequestFactory;
use Psr\Http\Message\ResponseInterface;

class DreamStudioStrategy extends Strategy
{
    public function __construct(RequestService $requestService, DreamStudioRequestFactory $requestFactory)
    {
        parent::__construct($requestService, $requestFactory);
    }

    protected function createAiImageFromResponse(ImageGenerationConfig $config, ResponseInterface $response): AiImage
    {
        $response = json_decode($response->getBody()->getContents(), true);

        $imageData = (string)$response['artifacts'][0]['base64'];
        $seed = (int)$response['artifacts'][0]['seed'];

        $aiImage = $this->createAiImageObject($config, $imageData);
        $aiImage->setMetadata(MetaDataEnum::SEED, $seed);

        return $aiImage;
    }
}
