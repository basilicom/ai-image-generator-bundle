<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\ImageGeneration;

use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGenerationConfig;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\ServiceRequest;
use Basilicom\AiImageGeneratorBundle\Strategy\NotSupportedException;

interface RequestFactory
{
    /**
     * @throws NotSupportedException
     */
    public function createTxt2ImgRequest(ImageGenerationConfig $configuration): ServiceRequest;

    /**
     * @throws NotSupportedException
     */
    public function createBrandedTxt2ImgRequest(ImageGenerationConfig $configuration): ServiceRequest;

    /**
     * @throws NotSupportedException
     */
    public function createImgVariationsRequest(ImageGenerationConfig $configuration, AiImage $baseImage): ServiceRequest;

    /**
     * @throws NotSupportedException
     */
    public function createUpscaleRequest(ImageGenerationConfig $configuration, AiImage $baseImage): ServiceRequest;

    /**
     * @throws NotSupportedException
     */
    public function createInpaintBackgroundRequest(ImageGenerationConfig $configuration, AiImage $baseImage): ServiceRequest;

    /**
     * @throws NotSupportedException
     */
    public function createInpaintRequest(ImageGenerationConfig $configuration, AiImage $baseImage): ServiceRequest;
}
