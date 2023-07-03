<?php

namespace Basilicom\AiImageGeneratorBundle\Config;

abstract class AbstractConfiguration
{
    protected string $baseUrl;
    protected string $model;
    protected int $steps;

    protected array $promptParts;
    protected array $negativePromptParts;
    protected string $aspectRatio;

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

}
