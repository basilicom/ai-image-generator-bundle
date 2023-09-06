<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy;

use Basilicom\AiImageGeneratorBundle\Config\Configuration;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\MetaDataEnum;
use Basilicom\AiImageGeneratorBundle\Service\RequestService;
use Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory\ClipDropRequestFactory;
use Psr\Http\Message\ResponseInterface;

class ClipDropStrategy extends Strategy
{
    public function __construct(RequestService $requestService, ClipDropRequestFactory $requestFactory)
    {
        parent::__construct($requestService, $requestFactory);
    }

    protected function createAiImageFromResponse(Configuration $config, ResponseInterface $response): AiImage
    {
        $body = $response->getBody()->getContents();

        $aiImage = new AiImage();
        $aiImage->setData(base64_encode($body));
        $aiImage->setMetadata(MetaDataEnum::PROMPT, implode(', ', $config->getPromptParts()));

        return $aiImage;
    }
}
