<?php

namespace Basilicom\AiImageGeneratorBundle\Config;

use Basilicom\AiImageGeneratorBundle\Helper\AspectRatioCalculator;
use Basilicom\AiImageGeneratorBundle\Model\InpaintingMask;
use Basilicom\AiImageGeneratorBundle\Service\PromptService;

abstract class ServiceConfiguration
{
    protected ?string $name = null;

    protected string $baseUrl;
    protected string $model;
    protected string $inpaintModel;
    protected int $steps;
    protected string $upscaler;
    protected ?InpaintingMask $inpaintingMask = null;

    protected string $prompt = '';
    protected string $aspectRatio = AspectRatioCalculator::DEFAULT_ASPECT_RATIO;
    protected int $seed = -1;

    private bool $useBrand;

    public function __construct(string $baseUrl, string $model, string $inpaintModel, int $steps, string $upscaler, bool $useBrand = false)
    {
        $this->baseUrl = $baseUrl;
        $this->model = $model;
        $this->inpaintModel = $inpaintModel;
        $this->steps = $steps;
        $this->upscaler = $upscaler;
        $this->useBrand = $useBrand;
    }

    public function getName(): ?string
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

    public function getInpaintModel(): string
    {
        return $this->inpaintModel;
    }

    public function getSteps(): int
    {
        return $this->steps;
    }

    public function getUpscaler(): string
    {
        return $this->upscaler;
    }

    public function getPrompt(): string
    {
        return $this->prompt;
    }

    public function setPrompt(string $promptParts): void
    {
        $this->prompt = $promptParts;
    }

    public function getNegativePrompt(): string
    {
        return implode(',', PromptService::DEFAULT_NEGATIVE_PROMPT);
    }

    public function getAspectRatio(): string
    {
        return $this->aspectRatio;
    }

    public function setAspectRatio(string $aspectRatio): void
    {
        $this->aspectRatio = $aspectRatio;
    }

    public function getSeed(): int
    {
        return $this->seed;
    }

    public function setSeed(int $seed): void
    {
        $this->seed = $seed;
    }

    public function getInpaintingMask(): ?InpaintingMask
    {
        return $this->inpaintingMask;
    }

    public function setInpaintingMask(?InpaintingMask $inpaintingMask): void
    {
        $this->inpaintingMask = $inpaintingMask;
    }

    public function isBrandingEnabled(): bool
    {
        return $this->useBrand;
    }

    public function setUseBrand(bool $useBrand): void
    {
        $this->useBrand = $useBrand;
    }
}
