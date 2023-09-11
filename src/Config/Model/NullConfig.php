<?php

namespace Basilicom\AiImageGeneratorBundle\Config\Model;

use Basilicom\AiImageGeneratorBundle\Config\ServiceConfiguration;

class NullConfig extends ServiceConfiguration
{
    public function __construct(string $baseUrl = '', string $model = '', string $inpaintModel = '', int $steps = 20, string $upscaler = '')
    {
        parent::__construct($baseUrl, $model, $inpaintModel, $steps, $upscaler);
    }
}
