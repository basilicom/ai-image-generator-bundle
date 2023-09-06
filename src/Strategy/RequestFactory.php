<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy;

use Basilicom\AiImageGeneratorBundle\Config\Configuration;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\ServiceRequest;

interface RequestFactory
{
    /**
     * @throws NotSupportedException
     */
    public function createTxt2ImgRequest(Configuration $configuration): ServiceRequest;

    /**
     * @throws NotSupportedException
     */
    public function createImgVariationsRequest(Configuration $configuration, AiImage $baseImage): ServiceRequest;

    /**
     * @throws NotSupportedException
     */
    public function createUpscaleRequest(Configuration $configuration, AiImage $baseImage): ServiceRequest;

    /**
     * @throws NotSupportedException
     */
    public function createInpaintBackgroundRequest(Configuration $configuration, AiImage $baseImage): ServiceRequest;
}
