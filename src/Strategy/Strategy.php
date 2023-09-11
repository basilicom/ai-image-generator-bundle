<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy;

use Basilicom\AiImageGeneratorBundle\Config\ServiceConfiguration;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Service\RequestService;
use Exception;
use Psr\Http\Message\ResponseInterface;

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
    public function textToImage(ServiceConfiguration $config): AiImage
    {
        $request = $this->requestFactory->createTxt2ImgRequest($config);
        $response = $this->requestService->callApi($request);

        return $this->createAiImageFromResponse($config, $response);
    }

    /**
     * @throws Exception
     */
    public function imageVariations(ServiceConfiguration $config, AiImage $image): AiImage
    {
        $request = $this->requestFactory->createImgVariationsRequest($config, $image);
        $response = $this->requestService->callApi($request);

        return $this->createAiImageFromResponse($config, $response);
    }

    /**
     * @throws Exception
     */
    public function upscale(ServiceConfiguration $config, AiImage $image): AiImage
    {
        $request = $this->requestFactory->createUpscaleRequest($config, $image);
        $response = $this->requestService->callApi($request);

        return $this->createAiImageFromResponse($config, $response);
    }

    /**
     * @throws Exception
     */
    public function inpaintBackground(ServiceConfiguration $config, AiImage $image): AiImage
    {
        $request = $this->requestFactory->createInpaintBackgroundRequest($config, $image);
        $response = $this->requestService->callApi($request);

        return $this->createAiImageFromResponse($config, $response);
    }

    /**
     * @throws Exception
     */
    public function inpaint(ServiceConfiguration $config, AiImage $image): AiImage
    {
        $request = $this->requestFactory->createInpaintRequest($config, $image);
        $response = $this->requestService->callApi($request);

        return $this->createAiImageFromResponse($config, $response);
    }

    abstract protected function createAiImageFromResponse(ServiceConfiguration $config, ResponseInterface $response): AiImage;
}
