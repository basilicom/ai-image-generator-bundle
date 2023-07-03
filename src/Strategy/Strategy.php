<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy;

use Basilicom\AiImageGeneratorBundle\Config\AbstractConfiguration;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;

interface Strategy
{
    public function setConfig(AbstractConfiguration $config): void;

    public function requestImage(): AiImage;
}
