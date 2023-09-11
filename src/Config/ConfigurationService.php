<?php

namespace Basilicom\AiImageGeneratorBundle\Config;

class ConfigurationService
{
    private BundleConfiguration $config;

    public function __construct(ConfigurationFactory $factory, array $configData)
    {
        $this->config = $factory->createBundleConfiguration($configData);
    }

    public function getServiceConfiguration(string $feature): ?ServiceConfiguration
    {
        $usedService = $this->config->getUsedServiceForFeature($feature);

        return $this->config->getServiceConfiguration($usedService);
    }
}
