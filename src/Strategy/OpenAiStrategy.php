<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy;

use Basilicom\AiImageGeneratorBundle\Config\Configuration;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\MetaDataEnum;
use Basilicom\AiImageGeneratorBundle\Service\RequestService;
use Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory\OpenAiRequestFactory;
use Psr\Http\Message\ResponseInterface;

class OpenAiStrategy extends Strategy
{
    public function __construct(RequestService $requestService, OpenAiRequestFactory $requestFactory)
    {
        parent::__construct($requestService, $requestFactory);
    }

    protected function createAiImageFromResponse(Configuration $config, ResponseInterface $response): AiImage
    {
        $response = json_decode($response->getBody()->getContents(), true);

        $imageData = (string)$response['data'][0]['b64_json'];

        $aiImage = new AiImage();
        $aiImage->setData($imageData);
        $aiImage->setMetadata(MetaDataEnum::PROMPT, implode(', ', $config->getPromptParts()));
        $aiImage->setMetadata(MetaDataEnum::ASPECT_RATIO, $config->getAspectRatio());

        return $aiImage;
    }
}
