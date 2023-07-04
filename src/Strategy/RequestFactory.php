<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy;

use Basilicom\AiImageGeneratorBundle\Config\Configuration;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\ServiceRequest;

interface RequestFactory
{
    public function createTxt2ImgRequest(Configuration $configuration): ServiceRequest;

    public function createImg2ImgRequest(Configuration $configuration, AiImage $baseImage): ServiceRequest;

    public function createUpscaleRequest(Configuration $configuration, AiImage $baseImage): ServiceRequest;
}
