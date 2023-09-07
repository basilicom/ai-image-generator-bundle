<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory;

use Basilicom\AiImageGeneratorBundle\Config\Configuration;
use Basilicom\AiImageGeneratorBundle\Config\Model\StableDiffusionApiConfig;
use Basilicom\AiImageGeneratorBundle\Helper\AspectRatioCalculator;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\ServiceRequest;
use Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory;
use Symfony\Component\HttpFoundation\Request;

class StableDiffusionRequestFactory implements RequestFactory
{
    private AspectRatioCalculator $aspectRatioCalculator;

    public function __construct(AspectRatioCalculator $aspectRatioCalculator)
    {
        $this->aspectRatioCalculator = $aspectRatioCalculator;
    }

    public function createTxt2ImgRequest(Configuration $configuration): ServiceRequest
    {
        $configuration->setUpscale(true);

        $getRelativeAspectRatio = $this->aspectRatioCalculator->calculateAspectRatio($configuration->getAspectRatio(), 512);
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

    public function createImgVariationsRequest(Configuration $configuration, AiImage $baseImage): ServiceRequest
    {
        $configuration->setUpscale(true);

        return $this->createTxt2ImgRequest($configuration);
    }

    public function createUpscaleRequest(Configuration|StableDiffusionApiConfig $configuration, AiImage $baseImage): ServiceRequest
    {
        $configuration->setUpscale(true);

        $uri = rtrim($configuration->getBaseUrl(), '/') . '/img2img';
        $method = Request::METHOD_POST;
        $payload = [
            'init_images' => [$baseImage->getData()],
            'denoising_strength' => 0.1, // no/nearly no additional rendering of new features

            'steps' => $configuration->getSteps(),
            'sd_model_checkpoint' => $configuration->getModel(),

            'prompt' => implode(',', $configuration->getPromptParts()),
            'negative_prompt' => implode(',', $configuration->getNegativePromptParts()),
            'seed' => $configuration->getSeed(),

            'script_name' => 'sd upscale',
            'script_args' => ['', 64, $configuration->getUpscaler(), 2], // unknown, tileOverlap, upscaler, factor

            'batch_size' => 1,
            'sampler_name' => 'Euler a',
            'cfg_scale' => 7,
        ];

        return new ServiceRequest($uri, $method, $payload);
    }

    public function createInpaintBackgroundRequest(
        Configuration|StableDiffusionApiConfig $configuration,
        AiImage                                $baseImage
    ): ServiceRequest {
        $configuration->setUpscale(true);

        $resizedImage = $baseImage->getResizedImage($baseImage->getData(true), 512, 512);
        $resizedMaskImage = $baseImage->getResizedImage($baseImage->getMask(true), 512, 512);

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
            'height' => 512,
            'width' => 512,

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
                            'processor_res' => 512,
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
}
