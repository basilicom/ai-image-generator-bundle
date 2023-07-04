<?php

namespace Basilicom\AiImageGeneratorBundle\Config\Model;

use Basilicom\AiImageGeneratorBundle\Config\Configuration;

class DreamStudioApiConfig extends Configuration
{
    private string $apiKey;

    public function __construct(string $baseUrl, string $model, int $steps, string $upscaler, string $apiKey)
    {
        parent::__construct($baseUrl, $model, $steps, $upscaler);
        $this->apiKey = $apiKey;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}
