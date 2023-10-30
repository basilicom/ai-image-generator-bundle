<?php

namespace Basilicom\AiImageGeneratorBundle\Config\Model\ImageGeneration;

use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGenerationConfig;

class DreamStudioApiConfig extends ImageGenerationConfig
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
