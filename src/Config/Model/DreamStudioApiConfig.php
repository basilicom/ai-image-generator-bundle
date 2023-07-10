<?php

namespace Basilicom\AiImageGeneratorBundle\Config\Model;

use Basilicom\AiImageGeneratorBundle\Config\Configuration;

class DreamStudioApiConfig extends Configuration
{
    protected string $name = 'DreamStudio';
    protected int $seed = 0;
    protected string $apiKey;

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
