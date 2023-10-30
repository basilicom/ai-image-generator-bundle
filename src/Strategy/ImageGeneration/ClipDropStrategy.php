<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\ImageGeneration;

use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGenerationConfig;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Service\RequestService;
use Basilicom\AiImageGeneratorBundle\Strategy\ImageGeneration\RequestFactory\ClipDropRequestFactory;
use Psr\Http\Message\ResponseInterface;

class ClipDropStrategy extends Strategy
{
    public function __construct(RequestService $requestService, ClipDropRequestFactory $requestFactory)
    {
        parent::__construct($requestService, $requestFactory);
    }

    protected function createAiImageFromResponse(ImageGenerationConfig $config, ResponseInterface $response): AiImage
    {
        $body = $response->getBody()->getContents();
        $imageData = base64_encode($body);

        return $this->createAiImageObject($config, $imageData);
    }
}
