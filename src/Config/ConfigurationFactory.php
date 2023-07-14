<?php

namespace Basilicom\AiImageGeneratorBundle\Config;

use Basilicom\AiImageGeneratorBundle\Config\Model\DreamStudioApiConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\OpenAiApiConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\StableDiffusionApiConfig;

class ConfigurationFactory
{
    public function create(string $apiService, array $configurationData): Configuration
    {
        return match ($apiService) {
            ConfigurationDefinition::STABLE_DIFFUSION_API => new StableDiffusionApiConfig(
                $configurationData[ConfigurationDefinition::BASE_URL],
                $configurationData[ConfigurationDefinition::MODEL],
                $configurationData[ConfigurationDefinition::STEPS],
                $configurationData[ConfigurationDefinition::UPSCALER]
            ),
            ConfigurationDefinition::DREAMSTUDIO => new DreamStudioApiConfig(
                $configurationData[ConfigurationDefinition::BASE_URL],
                $configurationData[ConfigurationDefinition::MODEL],
                $configurationData[ConfigurationDefinition::STEPS],
                $configurationData[ConfigurationDefinition::UPSCALER],
                $configurationData[ConfigurationDefinition::API_KEY]
            ),
            ConfigurationDefinition::OPEN_AI => new OpenAiApiConfig(
                $configurationData[ConfigurationDefinition::BASE_URL],
                $configurationData[ConfigurationDefinition::API_KEY]
            ),
        };
    }
}
