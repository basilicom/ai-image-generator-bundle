<?php

namespace Basilicom\AiImageGeneratorBundle\Config\Model\Prompting;

use Basilicom\AiImageGeneratorBundle\Config\Model\PromptEnhancementConfig;

class OpenAIPromptConfig extends PromptEnhancementConfig
{
    private string $apiKey;
    private string $model;

    public function __construct(string $baseUrl, string $apiKey, string $model)
    {
        parent::__construct($baseUrl);
        $this->apiKey = $apiKey;
        $this->model = $model;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getModel(): string
    {
        return $this->model;
    }
}
