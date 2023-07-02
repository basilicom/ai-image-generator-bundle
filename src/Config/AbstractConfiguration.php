<?php

namespace Basilicom\AiImageGeneratorBundle\Config;

abstract class AbstractConfiguration
{
    protected string $baseUrl;
    protected string $model;
    protected int $steps;
    protected string $prompt;
    protected string $negativePrompt;
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

    public function getPrompt(): string
    {
        return $this->prompt;
    }

    public function getNegativePrompt(): string
    {
        return $this->negativePrompt;
    }

    public function getAspectRatio(): string
    {
        return $this->aspectRatio;
    }

    public function setPrompt(string $prompt): void
    {
        $this->prompt = $prompt;
    }

    public function setNegativePrompt(string $negativePrompt): void
    {
        $this->negativePrompt = $negativePrompt;
    }

    public function setAspectRatio(string $aspectRatio): void
    {
        $this->aspectRatio = $aspectRatio;
    }
}
