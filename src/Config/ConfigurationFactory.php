<?php

namespace Basilicom\AiImageGeneratorBundle\Config;

use Basilicom\AiImageGeneratorBundle\Config\Model\StableDiffusionApiConfig;

class ConfigurationFactory
{
    public function create(string $apiService, array $configurationData): Configuration
    {
        return match ($apiService) {
            // todo ==> add additional APIs
            ConfigurationDefinition::STABLE_DIFFUSION_API => $this->createStableDiffusionApi($configurationData),
        };
    }

    protected function createStableDiffusionApi(array $configurationData): Configuration
    {
        return new StableDiffusionApiConfig(
            $configurationData[ConfigurationDefinition::BASE_URL],
            $configurationData[ConfigurationDefinition::MODEL],
            $configurationData[ConfigurationDefinition::STEPS]
        );
    }
}
