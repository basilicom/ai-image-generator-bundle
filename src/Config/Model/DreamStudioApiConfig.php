<?php

namespace Basilicom\AiImageGeneratorBundle\Config\Model;

use Basilicom\AiImageGeneratorBundle\Config\ServiceConfiguration;

class DreamStudioApiConfig extends ServiceConfiguration
{
    protected ?string $name = 'DreamStudio';
    protected int $seed = 0;
    protected string $apiKey;

    public function __construct(string $baseUrl, string $model, string $inpaintModel, int $steps, string $upscaler, string $apiKey)
    {
        parent::__construct(
            baseUrl: $baseUrl,
            model: $model,
            inpaintModel: $inpaintModel,
            steps: $steps,
            upscaler: $upscaler
        );
        $this->apiKey = $apiKey;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}
