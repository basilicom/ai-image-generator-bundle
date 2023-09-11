<?php

namespace Basilicom\AiImageGeneratorBundle\Config;

use Basilicom\AiImageGeneratorBundle\Config\Model\ClipDropApiConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\DreamStudioApiConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\OpenAiApiConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\StableDiffusionApiConfig;

class ConfigurationFactory
{
    public function createBundleConfiguration(array $configData): BundleConfiguration
    {
        $serviceConfigurationData = $configData[ConfigurationDefinition::SERVICES];

        $serviceConfigurations = [];
        foreach($serviceConfigurationData as $serviceKey => $data) {
            $serviceConfigurations[$serviceKey] = $this->createServiceConfiguration($serviceKey, $data);
        }

        return new BundleConfiguration(
            $configData[ConfigurationDefinition::FEATURE_SERVICES],
            $serviceConfigurations
        );
    }

    private function createServiceConfiguration(string $apiService, array $configurationData): ServiceConfiguration
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
}
