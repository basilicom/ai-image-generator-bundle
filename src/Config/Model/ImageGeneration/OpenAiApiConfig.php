<?php

namespace Basilicom\AiImageGeneratorBundle\Config\Model\ImageGeneration;

use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGenerationConfig;

class OpenAiApiConfig extends ImageGenerationConfig
{
    protected ?string $name = 'OpenAi';
    protected string $apiKey;

    public function __construct(string $baseUrl, string $apiKey)
    {
        parent::__construct(
            baseUrl: $baseUrl,
            model: '',
            inpaintModel: '',
            steps: 10,
            upscaler: ''
        );
        $this->apiKey = $apiKey;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}
