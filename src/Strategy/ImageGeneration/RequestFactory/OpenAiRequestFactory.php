<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\ImageGeneration\RequestFactory;

use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGenerationConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGeneration\OpenAiApiConfig;
use Basilicom\AiImageGeneratorBundle\Helper\AspectRatioCalculator;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\ServiceRequest;
use Basilicom\AiImageGeneratorBundle\Strategy\NotSupportedException;
use Basilicom\AiImageGeneratorBundle\Strategy\ImageGeneration\RequestFactory;
use Symfony\Component\HttpFoundation\Request;

class OpenAiRequestFactory implements RequestFactory
{
    private const MAX_WIDTH = 1792;
    private const MAX_HEIGHT = 1792;
    private const BASE_SIZE = 1024;
    private AspectRatioCalculator $aspectRatioCalculator;

    public function __construct(AspectRatioCalculator $aspectRatioCalculator)
    {
        $this->aspectRatioCalculator = $aspectRatioCalculator;
    }

    public function createTxt2ImgRequest(ImageGenerationConfig|OpenAiApiConfig $configuration): ServiceRequest
    {
        $uri = $configuration->getBaseUrl() . '/images/generations';
        $method = Request::METHOD_POST;

        $aspectRatio = $configuration->getAspectRatio();
        if ($this->aspectRatioCalculator->isLandscape($aspectRatio)) {
            $size = self::MAX_WIDTH . 'x' . self::BASE_SIZE;
        } elseif ($this->aspectRatioCalculator->isPortrait($aspectRatio)) {
            $size = self::BASE_SIZE . 'x' . self::MAX_HEIGHT;
        } else {
            $size = self::BASE_SIZE . 'x' . self::BASE_SIZE;
        }

        $payload = [
            'prompt' => $configuration->getPrompt()->getPositive(),
            'model' => 'dall-e-3',
            'quality' => 'hd', // only available for model dall-e-3
            'size' => $size,
            'n' => 1, // number of images ==> todo workflow to choose
            'response_format' => 'b64_json',
        ];

        return new ServiceRequest(
            $uri,
            $method,
            $payload,
            ['Authorization' => 'Bearer ' . $configuration->getApiKey()]
        );
    }

    public function createImgVariationsRequest(
        ImageGenerationConfig|OpenAiApiConfig $configuration,
        AiImage $baseImage
    ): ServiceRequest {
        $uri = $configuration->getBaseUrl() . '/images/variations';
        $method = Request::METHOD_POST;

        $tmpFilePath = sys_get_temp_dir() . '/ai-image-generator--open-ai.png';
        file_put_contents($tmpFilePath, $baseImage->getData(true));

        $payload = [
            [
                'name' => 'image',
                'contents' => fopen($tmpFilePath, 'rb'),
            ],
            [
                'name' => 'size',
                'contents' => '1024x1024',
            ],
            [
                'model' => 'n',
                'contents' => 1,
            ],
            [
                'name' => 'model',
                'contents' => 'dall-e-2',
            ],
            [
                'name' => 'response_format',
                'contents' => 'b64_json',
            ],
        ];

        unlink($tmpFilePath);

        return new ServiceRequest(
            $uri,
            $method,
            $payload,
            ['Authorization' => 'Bearer ' . $configuration->getApiKey()],
            true
        );
    }

    /**
     * @throws NotSupportedException
     */
    public function createUpscaleRequest(
        ImageGenerationConfig|OpenAiApiConfig $configuration,
        AiImage $baseImage
    ): ServiceRequest {
        throw new NotSupportedException('Upscaling is currently not supported');
    }

    public function createInpaintRequest(
        ImageGenerationConfig|OpenAiApiConfig $configuration,
        AiImage $baseImage
    ): ServiceRequest {
        $resizedImage = $baseImage->getResizedImage(
            self::BASE_SIZE,
            self::BASE_SIZE,
            true,
            mode: AiImage::MODE_RESIZE
        );
        $resizedMaskImage = $configuration->getInpaintingMask()->getResized(
            self::BASE_SIZE,
            self::BASE_SIZE,
            true,
            useAlpha: true,
            invert: true,
            mode: AiImage::MODE_RESIZE
        );

        return $this->requestInpaintEndpoint($configuration, $resizedImage, $resizedMaskImage);
    }

    public function createInpaintBackgroundRequest(
        ImageGenerationConfig|OpenAiApiConfig $configuration,
        AiImage $baseImage
    ): ServiceRequest {
        $resizedImage = $baseImage->getResizedImage(
            self::BASE_SIZE,
            self::BASE_SIZE,
            true,
            mode: AiImage::MODE_RESIZE
        );
        $resizedMaskImage = $baseImage->getResizedMask(
            self::BASE_SIZE,
            self::BASE_SIZE,
            true,
            useAlpha: true,
            invert: true,
            mode: AiImage::MODE_RESIZE
        );

        return $this->requestInpaintEndpoint($configuration, $resizedImage, $resizedMaskImage);
    }

    /**
     * @param ImageGenerationConfig $configuration
     * @param string $resizedImage
     * @param string $resizedMaskImage
     * @return ServiceRequest
     */
    private function requestInpaintEndpoint(
        ImageGenerationConfig $configuration,
        string $resizedImage,
        string $resizedMaskImage
    ): ServiceRequest {
        $uri = $configuration->getBaseUrl() . '/images/edits';
        $method = Request::METHOD_POST;

        $tmpFilePath = sys_get_temp_dir() . '/ai-image-generator--open-ai.png';
        file_put_contents($tmpFilePath, $resizedImage);

        $tmpMaskPath = sys_get_temp_dir() . '/ai-image-generator-mask--open-ai.png';
        file_put_contents($tmpMaskPath, $resizedMaskImage);

        $payload = [
            [
                'name' => 'prompt',
                'contents' => $configuration->getPrompt()->getPositive(),
            ],
            [
                'name' => 'image',
                'contents' => fopen($tmpFilePath, 'rb'),
            ],
            [
                'name' => 'mask',
                'contents' => fopen($tmpMaskPath, 'rb'),
            ],
            [
                'name' => 'size',
                'contents' => self::BASE_SIZE . 'x' . self::BASE_SIZE,
            ],
            [
                'name' => 'n',
                'contents' => 1,
            ],
            [
                'name' => 'model',
                'contents' => 'dall-e-2',
            ],
            [
                'name' => 'response_format',
                'contents' => 'b64_json',
            ],
        ];

        unlink($tmpFilePath);
        unlink($tmpMaskPath);

        return new ServiceRequest(
            $uri,
            $method,
            $payload,
            ['Authorization' => 'Bearer ' . $configuration->getApiKey()],
            true
        );
    }

    public function createBrandedTxt2ImgRequest(ImageGenerationConfig $configuration): ServiceRequest
    {
        throw new NotSupportedException('Branding is currently not supported');
    }
}
