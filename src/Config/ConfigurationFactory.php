<?php

namespace Basilicom\AiImageGeneratorBundle\Config;

use Basilicom\AiImageGeneratorBundle\Config\Model\DreamStudioApiConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\StableDiffusionApiConfig;

class ConfigurationFactory
{
    public function create(string $apiService, array $configurationData): Configuration
    {
        return match ($apiService) {
            ConfigurationDefinition::STABLE_DIFFUSION_API => $this->createStableDiffusionApi($configurationData),
            ConfigurationDefinition::DREAMSTUDIO => $this->createDreamStudioApi($configurationData),
        };
    }

    protected function createStableDiffusionApi(array $configurationData): Configuration
    {
        return new StableDiffusionApiConfig(
            $configurationData[ConfigurationDefinition::BASE_URL],
            $configurationData[ConfigurationDefinition::MODEL],
            $configurationData[ConfigurationDefinition::STEPS],
            $configurationData[ConfigurationDefinition::UPSCALER]
        );
    }

    protected function createDreamStudioApi(array $configurationData): Configuration
    {
        return new DreamStudioApiConfig(
            $configurationData[ConfigurationDefinition::BASE_URL],
            $configurationData[ConfigurationDefinition::MODEL],
            $configurationData[ConfigurationDefinition::STEPS],
            $configurationData[ConfigurationDefinition::UPSCALER],
            $configurationData[ConfigurationDefinition::API_KEY]
        );
    }
}
