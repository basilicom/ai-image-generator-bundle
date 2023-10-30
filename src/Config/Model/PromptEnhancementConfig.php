<?php

namespace Basilicom\AiImageGeneratorBundle\Config\Model;

abstract class PromptEnhancementConfig
{
    protected ?string $name = null;

    protected string $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getBaseUrl(): string
    {
        return rtrim($this->baseUrl, '/');
    }
}
