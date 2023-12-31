<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\ImageGeneration\RequestFactory;

use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGenerationConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGeneration\ClipDropApiConfig;
use Basilicom\AiImageGeneratorBundle\Helper\AspectRatioCalculator;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\ServiceRequest;
use Basilicom\AiImageGeneratorBundle\Strategy\NotSupportedException;
use Basilicom\AiImageGeneratorBundle\Strategy\ImageGeneration\RequestFactory;
use Symfony\Component\HttpFoundation\Request;

class ClipDropRequestFactory implements RequestFactory
{
    private AspectRatioCalculator $aspectRatioCalculator;

    public function __construct(AspectRatioCalculator $aspectRatioCalculator)
    {

        $this->aspectRatioCalculator = $aspectRatioCalculator;
    }

    public function createTxt2ImgRequest(ImageGenerationConfig|ClipDropApiConfig $configuration): ServiceRequest
    {
        /* @see https://clipdrop.co/apis/docs/text-to-image
         * Clipdrop text to image API is currently using SDXL1.0. Our text to image API is a subset of the SDXL1.0 API provided by Stability that only exposes the prompt parameter for now and can only create 1024x1024 images.
         */
        $configuration->setAspectRatio('1:1');

        $uri = $configuration->getBaseUrl() . '/text-to-image/v1';
        $method = Request::METHOD_POST;

        $payload = [
            [
                'name' => 'prompt',
                'contents' => $configuration->getPrompt()->getPositive(),
            ]
        ];

        return new ServiceRequest($uri, $method, $payload, ['x-api-key' => $configuration->getApiKey()], true);
    }

    public function createImgVariationsRequest(
        ImageGenerationConfig|ClipDropApiConfig $configuration,
        AiImage                                 $baseImage
    ): ServiceRequest {
        $uri = $configuration->getBaseUrl() . '/reimagine/v1/reimagine';
        $method = Request::METHOD_POST;

        $resizedImage = $baseImage->getResizedImage(1024, 1024);

        $tmpFilePath = sys_get_temp_dir() . '/ai-image-generator--clip-drop.png';
        file_put_contents($tmpFilePath, base64_decode($resizedImage));

        $payload = [
            [
                'name' => 'image_file',
                'contents' => fopen($tmpFilePath, 'rb'),
            ]
        ];

        unlink($tmpFilePath);

        return new ServiceRequest($uri, $method, $payload, ['x-api-key' => $configuration->getApiKey()], true);
    }

    public function createUpscaleRequest(
        ImageGenerationConfig|ClipDropApiConfig $configuration,
        AiImage                                 $baseImage
    ): ServiceRequest {
        $uri = $configuration->getBaseUrl() . '/image-upscaling/v1/upscale';
        $method = Request::METHOD_POST;

        $resizedImage = $baseImage->getResizedImage(1024, 1024);

        $targetAspectRatio = $this->aspectRatioCalculator->calculateAspectRatio($configuration->getAspectRatio(), 4096);

        $tmpFilePath = sys_get_temp_dir() . '/ai-image-generator--clip-drop.png';
        file_put_contents($tmpFilePath, base64_decode($resizedImage));

        $payload = [
            [
                'name' => 'image_file',
                'contents' => fopen($tmpFilePath, 'rb'),
            ],
            [
                'name' => 'target_width',
                'contents' => $targetAspectRatio->getWidth(),
            ],
            [
                'name' => 'target_height',
                'contents' => $targetAspectRatio->getHeight(),
            ]
        ];

        unlink($tmpFilePath);

        return new ServiceRequest($uri, $method, $payload, ['x-api-key' => $configuration->getApiKey()], true);
    }

    public function createInpaintBackgroundRequest(
        ImageGenerationConfig|ClipDropApiConfig $configuration,
        AiImage                                 $baseImage
    ): ServiceRequest {
        $uri = $configuration->getBaseUrl() . '/replace-background';
        $method = Request::METHOD_POST;

        $resizedImage = $baseImage->getResizedImage(1024, 1024);

        $tmpFilePath = sys_get_temp_dir() . '/replace-background';
        file_put_contents($tmpFilePath, base64_decode($resizedImage));

        $payload = [
            [
                'name' => 'image_file',
                'contents' => fopen($tmpFilePath, 'rb'),
            ],
            [
                'name' => 'prompt',
                'contents' => $configuration->getPrompt()->getPositive(),
            ]
        ];

        unlink($tmpFilePath);

        return new ServiceRequest(
            $uri,
            $method,
            $payload,
            [
                'x-api-key' => $configuration->getApiKey(),
                'Accept' => 'image/jpeg',
            ],
            true
        );
    }

    public function createInpaintRequest(ImageGenerationConfig $configuration, AiImage $baseImage): ServiceRequest
    {
        throw new NotSupportedException('Not implemented yet');
    }

    public function createBrandedTxt2ImgRequest(ImageGenerationConfig $configuration): ServiceRequest
    {
        throw new NotSupportedException('Upscaling is currently not supported');
    }
}
