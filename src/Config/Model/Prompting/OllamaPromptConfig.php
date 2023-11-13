<?php

namespace Basilicom\AiImageGeneratorBundle\Config\Model\Prompting;

use Basilicom\AiImageGeneratorBundle\Config\Model\PromptEnhancementConfig;

class OllamaPromptConfig extends PromptEnhancementConfig
{
    private string $model;

    public function __construct(string $baseUrl, string $model)
    {
        parent::__construct($baseUrl);
        $this->model = $model;
    }

    public function getModel(): string
    {
        return $this->model;
    }
}
