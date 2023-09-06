<?php

namespace Basilicom\AiImageGeneratorBundle\Config;

use Basilicom\AiImageGeneratorBundle\Config\Model\ClipDropApiConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\DreamStudioApiConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\OpenAiApiConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\StableDiffusionApiConfig;

class ConfigurationFactory
{
    public function create(string $apiService, array $configurationData): Configuration
    {
        $baseUrl = $configurationData[ConfigurationDefinition::BASE_URL];
        $apiKey = $configurationData[ConfigurationDefinition::API_KEY] ?? '';

        $model = $configurationData[ConfigurationDefinition::MODEL] ?? '';
        $inpaintModel = $configurationData[ConfigurationDefinition::INPAINT_MODEL] ?? '';
        $inpaintModel = !empty($inpaintModel) ? $inpaintModel : $model;
        $steps = $configurationData[ConfigurationDefinition::STEPS] ?? 50;
        $upscaler = $configurationData[ConfigurationDefinition::UPSCALER] ?? '';

        return match ($apiService) {
            ConfigurationDefinition::STABLE_DIFFUSION_API => new StableDiffusionApiConfig($baseUrl, $model, $inpaintModel, $steps, $upscaler),
            ConfigurationDefinition::DREAMSTUDIO => new DreamStudioApiConfig($baseUrl, $model, $inpaintModel, $steps, $upscaler, $apiKey),
            ConfigurationDefinition::OPEN_AI => new OpenAiApiConfig($baseUrl, $apiKey),
            ConfigurationDefinition::CLIP_DROP => new ClipDropApiConfig($baseUrl, $apiKey),
        };
    }
}
