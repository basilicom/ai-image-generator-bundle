<?php

namespace Basilicom\AiImageGeneratorBundle\Config;

use Basilicom\AiImageGeneratorBundle\Config\Model\BrandConfiguration;
use Basilicom\AiImageGeneratorBundle\Config\Model\BundleConfiguration;
use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGenerationConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\Prompting\BasilicomPromptConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGeneration\ClipDropApiConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGeneration\DreamStudioApiConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\Prompting\NullApiConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\Prompting\OllamaPromptConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGeneration\OpenAiApiConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\Prompting\OpenAIPromptConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\PromptEnhancementConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGeneration\StableDiffusionApiConfig;

class ConfigurationFactory
{
    public function createBundleConfiguration(array $configData): BundleConfiguration
    {
        $featureConfiguration = $configData[ConfigurationDefinition::FEATURE_SERVICES];
        $brandConfiguration = $this->createBrandConfiguration($configData);
        $promptingConfiguration = $this->createPromptingConfiguration($configData);
        $serviceConfigurations = [];
        foreach ($configData[ConfigurationDefinition::SERVICES] as $serviceKey => $data) {
            $serviceConfigurations[$serviceKey] = $this->createImageServiceConfiguration($serviceKey, $data);
        }

        return new BundleConfiguration(
            $featureConfiguration,
            $serviceConfigurations,
            $brandConfiguration,
            $promptingConfiguration
        );
    }

    private function createImageServiceConfiguration(string $apiService, array $configurationData): ImageGenerationConfig
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
            ConfigurationDefinition::DREAM_STUDIO => new DreamStudioApiConfig($baseUrl, $model, $inpaintModel, $steps, $upscaler, $apiKey),
            ConfigurationDefinition::OPEN_AI => new OpenAiApiConfig($baseUrl, $apiKey),
            ConfigurationDefinition::CLIP_DROP => new ClipDropApiConfig($baseUrl, $apiKey),
        };
    }

    private function createBrandConfiguration(array $configData): BrandConfiguration
    {
        $colors = $configData[ConfigurationDefinition::BRAND][ConfigurationDefinition::COLORS] ?? [];

        return new BrandConfiguration($colors);
    }

    private function createPromptingConfiguration(array $configData): PromptEnhancementConfig
    {
        $configData = $configData[ConfigurationDefinition::PROMPT_ENHANCEMENT];
        $apiService = $configData[ConfigurationDefinition::SERVICE] ?? '';

        $serviceConfigurations = [];
        foreach ($configData[ConfigurationDefinition::SERVICES] as $serviceKey => $data) {
            $baseUrl = $data[ConfigurationDefinition::BASE_URL];
            $model = $data[ConfigurationDefinition::MODEL] ?? '';
            $apiKey = $data[ConfigurationDefinition::API_KEY] ?? '';
            $serviceConfigurations[$serviceKey] = match ($apiService) {
                ConfigurationDefinition::BASILICOM => new BasilicomPromptConfig($baseUrl),
                ConfigurationDefinition::OLLAMA => new OllamaPromptConfig($baseUrl, $model),
                ConfigurationDefinition::OPEN_AI => new OpenAiPromptConfig($baseUrl, $apiKey, $model),
            };
        }

        return $serviceConfigurations[$apiService] ?? new NullApiConfig();
    }
}
