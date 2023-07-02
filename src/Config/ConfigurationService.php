<?php

namespace Basilicom\AiImageGeneratorBundle\Config;

class ConfigurationService
{
    private ConfigurationFactory $configFactory;
    private array $config;

    public function __construct(ConfigurationFactory $customLayoutConfigFactory, array $config)
    {
        $this->configFactory = $customLayoutConfigFactory;
        $this->config = $config;
    }

    public function getConfiguration(): AbstractConfiguration
    {
        $apiService = array_key_first($this->config);
        $configurationData = reset($this->config);

        return $this->configFactory->create($apiService, $configurationData);
    }
}
