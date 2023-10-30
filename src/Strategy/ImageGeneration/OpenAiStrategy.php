<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\ImageGeneration;

use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGenerationConfig;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Service\RequestService;
use Basilicom\AiImageGeneratorBundle\Strategy\ImageGeneration\RequestFactory\OpenAiRequestFactory;
use Psr\Http\Message\ResponseInterface;

class OpenAiStrategy extends Strategy
{
    public function __construct(RequestService $requestService, OpenAiRequestFactory $requestFactory)
    {
        parent::__construct($requestService, $requestFactory);
    }

    protected function createAiImageFromResponse(ImageGenerationConfig $config, ResponseInterface $response): AiImage
    {
        $response = json_decode($response->getBody()->getContents(), true);
        $imageData = (string)$response['data'][0]['b64_json'];

        return $this->createAiImageObject($config, $imageData);
    }
}
