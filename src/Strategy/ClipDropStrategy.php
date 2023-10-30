<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy;

use Basilicom\AiImageGeneratorBundle\Config\ServiceConfiguration;
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

    protected function createAiImageFromResponse(ServiceConfiguration $config, ResponseInterface $response): AiImage
    {
        $body = $response->getBody()->getContents();

        $aiImage = new AiImage();
        $aiImage->setData(base64_encode($body));
        $aiImage->setMetadata(MetaDataEnum::PROMPT, $config->getPrompt());
        $aiImage->setMetadata(MetaDataEnum::NEGATIVE_PROMPT, $config->getNegativePrompt());
        $aiImage->setMetadata(MetaDataEnum::ASPECT_RATIO, $config->getAspectRatio());

        return $aiImage;
    }
}
