<?php

namespace Basilicom\AiImageGeneratorBundle\Config\Model;

class NullConfig extends ImageGenerationConfig
{
    public function __construct(string $baseUrl = '', string $model = '', string $inpaintModel = '', int $steps = 20, string $upscaler = '')
    {
        parent::__construct($baseUrl, $model, $inpaintModel, $steps, $upscaler);
    }
}
