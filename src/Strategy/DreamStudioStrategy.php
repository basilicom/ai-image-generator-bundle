<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy;

use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Service\RequestService;
use Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory\DreamStudioRequestFactory;

class DreamStudioStrategy extends Strategy
{
    public function __construct(RequestService $requestService, DreamStudioRequestFactory $requestFactory)
    {
        parent::__construct($requestService, $requestFactory);
    }

    protected function createAiImageFromResponse(array $response): AiImage
    {
        $imageData = $response['artifacts'][0]['base64'];
        $seed = $response['artifacts'][0]['seed'];

        $aiImage = new AiImage();
        $aiImage->setData($imageData);
        $aiImage->setMetadata('seed', $seed);

        return $aiImage;
    }
}
