<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy;

use Basilicom\AiImageGeneratorBundle\Config\ServiceConfiguration;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\ServiceRequest;

interface RequestFactory
{
    /**
     * @throws NotSupportedException
     */
    public function createTxt2ImgRequest(ServiceConfiguration $configuration): ServiceRequest;

    /**
     * @throws NotSupportedException
     */
    public function createImgVariationsRequest(ServiceConfiguration $configuration, AiImage $baseImage): ServiceRequest;

    /**
     * @throws NotSupportedException
     */
    public function createUpscaleRequest(ServiceConfiguration $configuration, AiImage $baseImage): ServiceRequest;

    /**
     * @throws NotSupportedException
     */
    public function createInpaintBackgroundRequest(ServiceConfiguration $configuration, AiImage $baseImage): ServiceRequest;

    /**
     * @throws NotSupportedException
     */
    public function createInpaintRequest(ServiceConfiguration $configuration, AiImage $baseImage): ServiceRequest;
}
