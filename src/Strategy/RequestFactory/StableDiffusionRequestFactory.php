<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory;

use Basilicom\AiImageGeneratorBundle\Config\ServiceConfiguration;
use Basilicom\AiImageGeneratorBundle\Config\Model\StableDiffusionApiConfig;
use Basilicom\AiImageGeneratorBundle\Helper\AspectRatioCalculator;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\ServiceRequest;
use Basilicom\AiImageGeneratorBundle\Strategy\NotSupportedException;
use Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory;
use Symfony\Component\HttpFoundation\Request;

class StableDiffusionRequestFactory implements RequestFactory
{
    private const BASE_SIZE = 512;
    private AspectRatioCalculator $aspectRatioCalculator;

    public function __construct(AspectRatioCalculator $aspectRatioCalculator)
    {
        $this->aspectRatioCalculator = $aspectRatioCalculator;
    }

    public function createTxt2ImgRequest(ServiceConfiguration $configuration): ServiceRequest
    {
        $getRelativeAspectRatio = $this->aspectRatioCalculator->calculateAspectRatio($configuration->getAspectRatio(), self::BASE_SIZE);
        $uri = rtrim($configuration->getBaseUrl(), '/') . '/txt2img';
        $method = Request::METHOD_POST;

        $payload = [
            'steps' => $configuration->getSteps(),
            'sd_model_checkpoint' => $configuration->getModel(),

            'prompt' => implode(',', $configuration->getPromptParts()),
            'negative_prompt' => implode(',', $configuration->getNegativePromptParts()),
            'seed' => $configuration->getSeed(),
            'width' => $getRelativeAspectRatio->getWidth(),
            'height' => $getRelativeAspectRatio->getHeight(),

            'batch_size' => 1,
            'sampler_name' => 'Euler a',
            'cfg_scale' => 7,
        ];

        return new ServiceRequest($uri, $method, $payload);
    }

    public function createImgVariationsRequest(ServiceConfiguration $configuration, AiImage $baseImage): ServiceRequest
    {
        return $this->createTxt2ImgRequest($configuration);
    }

    public function createUpscaleRequest(ServiceConfiguration|StableDiffusionApiConfig $configuration, AiImage $baseImage): ServiceRequest
    {
        $tmpFilePath = sys_get_temp_dir() . '/ai-image-generator--a1111.png';
        file_put_contents($tmpFilePath, $baseImage->getData(true));
        $imageSize = getimagesize($tmpFilePath);
        $width = $imageSize[0];
        $height = $imageSize[1];

        $uri = rtrim($configuration->getBaseUrl(), '/') . '/img2img';
        $method = Request::METHOD_POST;
        $upscaleFactor = $this->aspectRatioCalculator->calculateUpscaleFactor($width, $height);

        $payload = [
            'init_images' => [$baseImage->getData()],
            'denoising_strength' => 0.1, // no/nearly no additional rendering of new features

            'steps' => $configuration->getSteps(),
            'sd_model_checkpoint' => $configuration->getModel(),

            'prompt' => implode(',', $configuration->getPromptParts()),
            'negative_prompt' => implode(',', $configuration->getNegativePromptParts()),
            'seed' => $configuration->getSeed(),

            'script_name' => 'sd upscale',
            'script_args' => [
                '',  // unknown
                64, // tileOverlap
                $configuration->getUpscaler(), // upscaler
                $upscaleFactor // factor
            ],

            'batch_size' => 1,
            'sampler_name' => 'Euler a',
            'cfg_scale' => 7,
        ];

        return new ServiceRequest($uri, $method, $payload);
    }

    public function createInpaintBackgroundRequest(
        ServiceConfiguration|StableDiffusionApiConfig $configuration,
        AiImage                                       $baseImage
    ): ServiceRequest {
        $resizedImage = $baseImage->getResizedImage(self::BASE_SIZE, self::BASE_SIZE);
        $resizedMaskImage = $baseImage->getResizedMask(self::BASE_SIZE, self::BASE_SIZE);

        $uri = rtrim($configuration->getBaseUrl(), '/') . '/img2img';
        $method = Request::METHOD_POST;
        $payload = [
            'steps' => $configuration->getSteps(),
            'sd_model_checkpoint' => $configuration->getModel(),

            'prompt' => implode(',', $configuration->getPromptParts()) . ', ((product photo))',
            'negative_prompt' => implode(',', $configuration->getNegativePromptParts()) . ', dark, ((fantasy))',
            'seed' => $configuration->getSeed(),

            'init_images' => [$resizedImage],
            'mask' => $resizedMaskImage,
            'mask_blur' => 0,
            'denoising_strength' => 1,
            'inpaint_full_res_padding' => 0,
            'inpainting_mask_invert' => 1,
            'inpaint_full_res' => true,

            //Resize_mode
            //  0: Just Resize
            //  1: Crop and Resize
            //  2: Resize and Fill
            'resize_mode' => 1,
            'height' => self::BASE_SIZE,
            'width' => self::BASE_SIZE,

            //Inpainting Fill
            //  0: fill
            //  1: original
            //  2: latent noise
            //  3: latent nothing
            'inpainting_fill' => 0,

            'batch_size' => 1,
            'sampler_name' => 'Euler a',
            'cfg_scale' => 7,

            'alwayson_scripts' => [
                'controlnet' => [
                    'args' => [
                        [
                            'input_image' => $resizedImage,
                            'module' => 'canny',
                            'model' => 'control_sd15_canny [fef5e48e]',
                            //Resize_mode
                            //  0: Just Resize
                            //  1: Crop and Resize
                            //  2: Resize and Fill
                            'resize_mode' => 1,
                            'control_mode' => 0,
                            'pixel_perfect' => true,
                            'processor_res' => self::BASE_SIZE,
                            'guidance_start' => 0.01,
                            'guidance_end' => 1.0,
                            'weight' => 1,
                            'threshold_a' => 8,
                            'threshold_b' => 8,
                        ]
                    ],
                ],
            ],
        ];

        return new ServiceRequest($uri, $method, $payload);
    }

    public function createInpaintRequest(ServiceConfiguration $configuration, AiImage $baseImage): ServiceRequest
    {
        // todo ==> try to get the image but only the inpainting, than scale it to the source image width/height and apply it via imagick.
        $inpaintingMask = $configuration->getInpaintingMask();

        $resizedImage = $baseImage->getResizedImage(self::BASE_SIZE, self::BASE_SIZE);
        $resizedMaskImage = $inpaintingMask->getResized(self::BASE_SIZE, self::BASE_SIZE);

        $imageInfo = getimagesizefromstring(base64_decode($resizedImage));
        $width = $imageInfo[0];
        $height = $imageInfo[1];

        $uri = rtrim($configuration->getBaseUrl(), '/') . '/img2img';
        $method = Request::METHOD_POST;
        $payload = [
            'steps' => $configuration->getSteps(),
            'sd_model_checkpoint' => $configuration->getInpaintModel(),

            'prompt' => implode(',', $configuration->getPromptParts()),
            'negative_prompt' => implode(',', $configuration->getNegativePromptParts()),
            'seed' => $configuration->getSeed(),

            'init_images' => [$resizedImage],
            'mask' => $resizedMaskImage,
            'mask_blur' => 2,
            'denoising_strength' => 0.9,

            'inpaint_full_res_padding' => 32,
            'inpaint_full_res' => 1,
            'inpainting_mask_invert' => 1,

            //Resize_mode
            //  0: Just Resize
            //  1: Crop and Resize
            //  2: Resize and Fill
            'resize_mode' => 1,
            'height' => $height,
            'width' => $width,

            //Inpainting Fill
            //  0: fill
            //  1: original
            //  2: latent noise
            //  3: latent nothing
            'inpainting_fill' => 1,

            'batch_size' => 1,
            'sampler_name' => 'Euler a',
            'cfg_scale' => 7,
        ];

        return new ServiceRequest($uri, $method, $payload);
    }

    public function createBrandedTxt2ImgRequest(ServiceConfiguration $configuration): ServiceRequest
    {
        // todo ==> check img2img vs IPAdapter
        throw new NotSupportedException('Upscaling is currently not supported');
    }
}
