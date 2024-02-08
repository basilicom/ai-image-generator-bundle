<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\ImageGeneration;

use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGenerationConfig;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\MetaDataEnum;
use Basilicom\AiImageGeneratorBundle\Service\RequestService;
use Basilicom\AiImageGeneratorBundle\Strategy\NotSupportedException;
use Exception;
use Imagick;
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

    protected function createAiImageObject(ImageGenerationConfig $config, string $imageData): AiImage
    {
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

        $newAiImage = $this->createAiImageFromResponse($config, $response);

        return $this->cropImageToOriginalDimensions($image, $newAiImage);
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

    private function cropImageToOriginalDimensions(AiImage $originalImage, AiImage $generatedImage): AiImage
    {
        $image1 = new Imagick();
        $image1->readImageBlob($originalImage->getData(true));
        $image2 = new Imagick();
        $image2->readImageBlob($generatedImage->getData(true));

        $image1Width = $image1->getImageWidth();
        $image1Height = $image1->getImageHeight();
        $image1AspectRatio = $image1Width / $image1Height;

        $image2Width = $image2->getImageWidth();
        $image2Height = $image2->getImageHeight();
        $image2AspectRatio = $image2Width / $image2Height;

        if ($image1AspectRatio > $image2AspectRatio) {
            // Image 1 is wider
            $cropWidth = $image2Width;
            $cropHeight = $cropWidth / $image1AspectRatio;
            $cropX = 0;
            $cropY = ($image2Height - $cropHeight) / 2;
        } else {
            // Image 1 is taller
            $cropHeight = $image2Height;
            $cropWidth = $cropHeight * $image1AspectRatio;
            $cropX = ($image2Width - $cropWidth) / 2;
            $cropY = 0;
        }

        $image2->cropImage($cropWidth, $cropHeight, $cropX, $cropY);

        $generatedImage->setData(base64_encode($image2->getImageBlob()));

        $image1->destroy();
        $image2->destroy();

        return $generatedImage;
    }

    abstract protected function createAiImageFromResponse(
        ImageGenerationConfig $config,
        ResponseInterface $response
    ): AiImage;
}
