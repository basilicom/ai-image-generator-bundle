<?php

namespace Basilicom\AiImageGeneratorBundle\Config\Model;

use Basilicom\AiImageGeneratorBundle\Config\Configuration;

class StableDiffusionApiConfig implements Configuration
{
    private string $baseUrl;
    private string $model;
    private int $steps;

    public function __construct(string $baseUrl, string $model, int $steps)
    {
        $this->baseUrl = $baseUrl;
        $this->model = $model;
        $this->steps = $steps;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getSteps(): int
    {
        return $this->steps;
    }
}
