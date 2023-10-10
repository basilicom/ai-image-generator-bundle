<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory;

use Basilicom\AiImageGeneratorBundle\Config\ServiceConfiguration;
use Basilicom\AiImageGeneratorBundle\Config\Model\DreamStudioApiConfig;
use Basilicom\AiImageGeneratorBundle\Helper\AspectRatioCalculator;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\ServiceRequest;
use Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory;
use Symfony\Component\HttpFoundation\Request;

class DreamStudioRequestFactory implements RequestFactory
{
    private AspectRatioCalculator $aspectRatioCalculator;

    public function __construct(AspectRatioCalculator $aspectRatioCalculator)
    {
        $this->aspectRatioCalculator = $aspectRatioCalculator;
    }

    public function createTxt2ImgRequest(ServiceConfiguration|DreamStudioApiConfig $configuration): ServiceRequest
    {
        $aspectRatio = $configuration->getAspectRatio();
        $getRelativeAspectRatio = $this->aspectRatioCalculator->calculateAspectRatio($aspectRatio, 512, 64);

        $uri = sprintf('%s/generation/%s/text-to-image', rtrim($configuration->getBaseUrl(), '/'), $configuration->getModel());
        $method = Request::METHOD_POST;

        $payload = [
            'steps' => $configuration->getSteps(),
            'width' => $getRelativeAspectRatio->getWidth(),
            'height' => $getRelativeAspectRatio->getHeight(),

            'text_prompts' => [
                ['text' => implode(',', $configuration->getPromptParts()), 'weight' => 1.0]
            ],

            'seed' => max($configuration->getSeed(), 0), // [0 .. 4294967295]

            'sampler' => 'K_EULER_ANCESTRAL', // DDIM DDPM K_DPMPP_2M K_DPMPP_2S_ANCESTRAL K_DPM_2 K_DPM_2_ANCESTRAL K_EULER K_EULER_ANCESTRAL K_HEUN K_LMS
            'cfg_scale' => 7, // [ 0 .. 35 ]
            'samples' => 1
        ];

        return new ServiceRequest($uri, $method, $payload, ['Authorization' => $configuration->getApiKey()]);
    }

    public function createImgVariationsRequest(
        ServiceConfiguration|DreamStudioApiConfig $configuration,
        AiImage                                   $baseImage
    ): ServiceRequest {
        return $this->createTxt2ImgRequest($configuration);
    }

    public function createUpscaleRequest(
        ServiceConfiguration|DreamStudioApiConfig $configuration,
        AiImage                                   $baseImage
    ): ServiceRequest {
        $uri = sprintf('%s/generation/%s/image-to-image/upscale', rtrim($configuration->getBaseUrl(), '/'), $configuration->getUpscaler());
        $method = Request::METHOD_POST;

        $tmpFilePath = sys_get_temp_dir() . '/ai-image-generator--dream-studio.png';
        file_put_contents($tmpFilePath, $baseImage->getData(true));

        $targetAspectRatio = $this->aspectRatioCalculator->calculateAspectRatio($configuration->getAspectRatio(), 2048);

        $width = $targetAspectRatio->getWidth();
        $height = $targetAspectRatio->getHeight();

        $payload = [['name' => 'image', 'contents' => fopen($tmpFilePath, 'rb')]];
        if ($width > $height) {
            $payload[] = ['name' => 'width', 'contents' => $width];
        } else {
            $payload[] = ['name' => 'height', 'contents' => $height];
        }

        unlink($tmpFilePath);

        return new ServiceRequest($uri, $method, $payload, ['Authorization' => $configuration->getApiKey()], true);
    }

    public function createInpaintBackgroundRequest(ServiceConfiguration $configuration, AiImage $baseImage): ServiceRequest
    {
        $dimensions = $this->getValidDimensions($configuration->getAspectRatio());

        $resizedImage = $baseImage->getResizedImage($dimensions['width'], $dimensions['height'], true, false);
        $resizedMask = $baseImage->getResizedMask($dimensions['width'], $dimensions['height'], true, false);

        return $this->createInpaintingRequest($configuration, $resizedImage, $resizedMask);
    }

    public function createInpaintRequest(ServiceConfiguration $configuration, AiImage $baseImage): ServiceRequest
    {
        $dimensions = $this->getValidDimensions($configuration->getAspectRatio());

        $resizedImage = $baseImage->getResizedImage($dimensions['width'], $dimensions['height'], true, false);
        $resizedMask = $configuration->getInpaintingMask()->getResized($dimensions['width'], $dimensions['height'], true, false);

        return $this->createInpaintingRequest($configuration, $resizedImage, $resizedMask);
    }

    private function createInpaintingRequest(ServiceConfiguration $configuration, string $imageData, string $maskImageData): ServiceRequest
    {
        $uri = sprintf('%s/generation/%s/image-to-image/masking', rtrim($configuration->getBaseUrl(), '/'), $configuration->getInpaintModel());
        $method = Request::METHOD_POST;

        $tmpImageFilePath = sys_get_temp_dir() . '/ai-image-generator--dream-studio--inpaint.png';
        file_put_contents($tmpImageFilePath, $imageData);

        // we could also use "mask_source=INIT_IMAGE_ALPHA" but i want to use the mask image also for other services
        $tmpMaskFilePath = sys_get_temp_dir() . '/ai-image-generator--dream-studio--mask.png';
        file_put_contents($tmpMaskFilePath, $maskImageData);

        $payload = [
            ['name' => 'text_prompts[0][text]', 'contents' => implode(',', $configuration->getPromptParts())],
            ['name' => 'text_prompts[0][weight]', 'contents' => 1.0],

            ['name' => 'init_image', 'contents' => fopen($tmpImageFilePath, 'rb')],
            ['name' => 'mask_image', 'contents' => fopen($tmpMaskFilePath, 'rb')],
            ['name' => 'mask_source', 'contents' => 'MASK_IMAGE_BLACK'],

            ['name' => 'steps', 'contents' => $configuration->getSteps()],
            ['name' => 'sampler', 'contents' => 'K_EULER_ANCESTRAL'], // DDIM DDPM K_DPMPP_2M K_DPMPP_2S_ANCESTRAL K_DPM_2 K_DPM_2_ANCESTRAL K_EULER K_EULER_ANCESTRAL K_HEUN K_LMS
            ['name' => 'cfg_scale', 'contents' => 7],
            ['name' => 'samples', 'contents' => 1],
        ];

        unlink($tmpImageFilePath);
        unlink($tmpMaskFilePath);

        return new ServiceRequest($uri, $method, $payload, ['Authorization' => $configuration->getApiKey()], true);
    }

    private function getValidDimensions(string $aspectRatio): array
    {
        $aspectRatio = $this->aspectRatioCalculator->calculateAspectRatio($aspectRatio, 1024);

        $validDimensions = [
            '1024x1024', '1152x896', '1216x832', '1344x768', '1536x640',
            '640x1536', '768x1344', '832x1216', '896x1152'
        ];

        $closestDimension = null;
        $closestDistance = PHP_INT_MAX; // Start with a large value

        foreach ($validDimensions as $validDimension) {
            list($validWidth, $validHeight) = explode('x', $validDimension);
            $distance = sqrt(pow($aspectRatio->getWidth() - $validWidth, 2) + pow($aspectRatio->getHeight() - $validHeight, 2));

            // Check if this dimension is closer than the current closest
            if ($distance < $closestDistance) {
                $closestDimension = $validDimension;
                $closestDistance = $distance;
            }
        }

        return [
            'width' => (int) explode('x', $closestDimension)[0],
            'height' => (int) explode('x', $closestDimension)[1],
        ];
    }
}
