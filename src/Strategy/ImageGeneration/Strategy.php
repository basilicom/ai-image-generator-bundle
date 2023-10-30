<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\ImageGeneration;

use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGenerationConfig;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\MetaDataEnum;
use Basilicom\AiImageGeneratorBundle\Service\RequestService;
use Basilicom\AiImageGeneratorBundle\Strategy\NotSupportedException;
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

    protected function createAiImageObject(ImageGenerationConfig $config, string $imageData):AiImage {
        $aiImage = new AiImage();
        $aiImage->setData($imageData);
        $aiImage->setMetadata(MetaDataEnum::MODEL, $config->getModel());
        $aiImage->setMetadata(MetaDataEnum::STEPS, $config->getSteps());
        $aiImage->setMetadata(MetaDataEnum::PROMPT, $config->getPrompt()->getPositive());
        $aiImage->setMetadata(MetaDataEnum::NEGATIVE_PROMPT, $config->getPrompt()->getNegative());
        $aiImage->setMetadata(MetaDataEnum::ASPECT_RATIO, $config->getAspectRatio());

        return $aiImage;
    }

    /**
     * @throws Exception
     */
    public function textToImage(ImageGenerationConfig $config): AiImage
    {
        $request = $this->requestFactory->createTxt2ImgRequest($config);
        if ($config->isBrandingEnabled()) {
            try {
                $request = $this->requestFactory->createBrandedTxt2ImgRequest($config);
            } catch (NotSupportedException) {
            }
        }

        $response = $this->requestService->callApi($request);

        return $this->createAiImageFromResponse($config, $response);
    }

    /**
     * @throws Exception
     */
    public function imageVariations(ImageGenerationConfig $config, AiImage $image): AiImage
    {
        $request = $this->requestFactory->createImgVariationsRequest($config, $image);
        $response = $this->requestService->callApi($request);

        return $this->createAiImageFromResponse($config, $response);
    }

    /**
     * @throws Exception
     */
    public function upscale(ImageGenerationConfig $config, AiImage $image): AiImage
    {
        $request = $this->requestFactory->createUpscaleRequest($config, $image);
        $response = $this->requestService->callApi($request);

        return $this->createAiImageFromResponse($config, $response);
    }

    /**
     * @throws Exception
     */
    public function inpaintBackground(ImageGenerationConfig $config, AiImage $image): AiImage
    {
        $request = $this->requestFactory->createInpaintBackgroundRequest($config, $image);
        $response = $this->requestService->callApi($request);

        return $this->createAiImageFromResponse($config, $response);
    }

    /**
     * @throws Exception
     */
    public function inpaint(ImageGenerationConfig $config, AiImage $image): AiImage
    {
        $request = $this->requestFactory->createInpaintRequest($config, $image);
        $response = $this->requestService->callApi($request);

        return $this->createAiImageFromResponse($config, $response);
    }

    abstract protected function createAiImageFromResponse(ImageGenerationConfig $config, ResponseInterface $response): AiImage;
}
