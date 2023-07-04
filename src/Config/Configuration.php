<?php

namespace Basilicom\AiImageGeneratorBundle\Config;

abstract class Configuration
{
    protected string $name = '';

    protected string $baseUrl;
    protected string $model;
    protected int $steps;
    protected string $upscaler;

    protected array $promptParts;
    protected array $negativePromptParts;
    protected string $aspectRatio;

    protected bool $upscale = false;

    public function __construct(string $baseUrl, string $model, int $steps, string $upscaler)
    {
        $this->baseUrl = $baseUrl;
        $this->model = $model;
        $this->steps = $steps;
        $this->upscaler = $upscaler;
    }

    public function getName(): string
    {
        return $this->name;
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

    public function getUpscaler(): string
    {
        return $this->upscaler;
    }

    public function getPromptParts(): array
    {
        return $this->promptParts;
    }

    public function setPromptParts(array $promptParts): void
    {
        $this->promptParts = $promptParts;
    }

    public function getNegativePromptParts(): array
    {
        return $this->negativePromptParts;
    }

    public function setNegativePromptParts(array $negativePromptParts): void
    {
        $this->negativePromptParts = $negativePromptParts;
    }

    public function getAspectRatio(): string
    {
        return $this->aspectRatio;
    }

    public function setAspectRatio(string $aspectRatio): void
    {
        $this->aspectRatio = $aspectRatio;
    }

    public function isUpscale(): bool
    {
        return $this->upscale;
    }

    public function setUpscale(bool $upscale): void
    {
        $this->upscale = $upscale;
    }
}
