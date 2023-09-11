<?php

namespace Basilicom\AiImageGeneratorBundle\Config;

use Basilicom\AiImageGeneratorBundle\Config\Model\NullConfig;
use InvalidArgumentException;

class BundleConfiguration
{
    private array $featureConfiguration;
    private array $serviceConfigurations;

    /**
     * @param array $featureConfiguration
     * @param ServiceConfiguration[] $serviceConfigurations
     */
    public function __construct(array $featureConfiguration, array $serviceConfigurations)
    {
        $this->featureConfiguration = $featureConfiguration;
        $this->serviceConfigurations = $serviceConfigurations;

        foreach ($this->serviceConfigurations as $serviceConfiguration) {
            if (!($serviceConfiguration instanceof ServiceConfiguration)) {
                throw new InvalidArgumentException('Invalid service configuration');
            }
        }
    }

    public function getUsedServiceForFeature(string $feature): string
    {
        if (!array_key_exists($feature, $this->featureConfiguration)) {
            throw new InvalidArgumentException('Invalid feature');
        }

        return $this->featureConfiguration[$feature] ?? '';
    }

    public function getServiceConfiguration(string $serviceKey): ?ServiceConfiguration
    {
        return $this->serviceConfigurations[$serviceKey] ?? new NullConfig();
    }
}
