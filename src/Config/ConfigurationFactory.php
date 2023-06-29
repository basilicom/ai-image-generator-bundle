<?php

namespace Basilicom\AiImageGeneratorBundle\Config;

use Basilicom\AiImageGeneratorBundle\Config\Model\DreamStudioConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\StableDiffusionApiConfig;

class ConfigurationFactory
{
    public function create(string $apiService, array $configurationData): Configuration
    {
        return match ($apiService) {
            ConfigurationDefinition::STABLE_DIFFUSION_API => $this->createStableDiffusionApi($configurationData),
            ConfigurationDefinition::DREAMSTUDIO => $this->createDreamStudioConfig($configurationData),
        };

        // todo ==> add additional APIs
    }

    protected function createStableDiffusionApi(array $configurationData): Configuration
    {
        return new StableDiffusionApiConfig(
            $configurationData[ConfigurationDefinition::BASE_URL],
            $configurationData[ConfigurationDefinition::MODEL],
            $configurationData[ConfigurationDefinition::STEPS]
        );
    }

    protected function createDreamStudioConfig(array $configurationData): Configuration
    {
        return new DreamStudioConfig(
            $configurationData[ConfigurationDefinition::BASE_URL],
            $configurationData[ConfigurationDefinition::API_KEY],
            $configurationData[ConfigurationDefinition::API_SECRET]
        );
    }
}
