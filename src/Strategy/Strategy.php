<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy;

use Basilicom\AiImageGeneratorBundle\Config\Configuration;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Service\RequestService;
use Exception;

abstract class Strategy
{
    protected RequestService $requestService;
    protected RequestFactory $requestFactory;

    public function __construct(RequestService $requestService, RequestFactory $requestFactory)
    {
        $this->requestService = $requestService;
        $this->requestFactory = $requestFactory;
    }

    /**
     * @throws Exception
     */
    public function textToImage(Configuration $config): AiImage
    {
        $request = $this->requestFactory->createTxt2ImgRequest($config);
        $response = $this->requestService->callApi($request);

        return $this->createAiImageFromResponse($config, $response);
    }

    /**
     * @throws Exception
     */
    public function imageToImage(Configuration $config, AiImage $image): AiImage
    {
        $request = $this->requestFactory->createImg2ImgRequest($config, $image);
        $response = $this->requestService->callApi($request);

        return $this->createAiImageFromResponse($config, $response);
    }

    /**
     * @throws Exception
     */
    public function upscale(Configuration $config, AiImage $image): AiImage
    {
        $request = $this->requestFactory->createUpscaleRequest($config, $image);
        $response = $this->requestService->callApi($request);

        return $this->createAiImageFromResponse($config, $response);
    }

    abstract protected function createAiImageFromResponse(Configuration $config, array $response): AiImage;
}
