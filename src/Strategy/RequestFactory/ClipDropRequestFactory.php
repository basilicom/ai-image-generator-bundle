<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory;

use Basilicom\AiImageGeneratorBundle\Config\ServiceConfiguration;
use Basilicom\AiImageGeneratorBundle\Config\Model\ClipDropApiConfig;
use Basilicom\AiImageGeneratorBundle\Helper\AspectRatioCalculator;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\ServiceRequest;
use Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory;
use Symfony\Component\HttpFoundation\Request;

class ClipDropRequestFactory implements RequestFactory
{
    private AspectRatioCalculator $aspectRatioCalculator;

    public function __construct(AspectRatioCalculator $aspectRatioCalculator)
    {

        $this->aspectRatioCalculator = $aspectRatioCalculator;
    }

    public function createTxt2ImgRequest(ServiceConfiguration|ClipDropApiConfig $configuration): ServiceRequest
    {
        /* @see https://clipdrop.co/apis/docs/text-to-image
         * Clipdrop text to image API is currently using SDXL1.0. Our text to image API is a subset of the SDXL1.0 API provided by Stability that only exposes the prompt parameter for now and can only create 1024x1024 images.
         */
        $configuration->setAspectRatio('1:1');

        $uri = rtrim($configuration->getBaseUrl(), '/') . '/text-to-image/v1';
        $method = Request::METHOD_POST;

        $payload = [
            [
                'name' => 'prompt',
                'contents' => implode(',', $configuration->getPromptParts()),
            ]
        ];

        return new ServiceRequest($uri, $method, $payload, ['x-api-key' => $configuration->getApiKey()], true);
    }

    public function createImgVariationsRequest(
        ServiceConfiguration|ClipDropApiConfig $configuration,
        AiImage                                $baseImage
    ): ServiceRequest {
        $uri = rtrim($configuration->getBaseUrl(), '/') . '/reimagine/v1/reimagine';
        $method = Request::METHOD_POST;

        $resizedImage = $baseImage->getResizedImage($baseImage->getData(true), 1024, 1024);

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
        ServiceConfiguration|ClipDropApiConfig $configuration,
        AiImage                                $baseImage
    ): ServiceRequest {
        $uri = rtrim($configuration->getBaseUrl(), '/') . '/image-upscaling/v1/upscale';
        $method = Request::METHOD_POST;

        $resizedImage = $baseImage->getResizedImage($baseImage->getData(true), 1024, 1024);

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
        ServiceConfiguration|ClipDropApiConfig $configuration,
        AiImage                                $baseImage
    ): ServiceRequest {
        $uri = rtrim($configuration->getBaseUrl(), '/') . '/replace-background';
        $method = Request::METHOD_POST;

        $resizedImage = $baseImage->getResizedImage($baseImage->getData(true), 1024, 1024);

        $tmpFilePath = sys_get_temp_dir() . '/replace-background';
        file_put_contents($tmpFilePath, base64_decode($resizedImage));

        $payload = [
            [
                'name' => 'image_file',
                'contents' => fopen($tmpFilePath, 'rb'),
            ],
            [
                'name' => 'prompt',
                'contents' => implode(',', $configuration->getPromptParts()),
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
}
