<?php

namespace Basilicom\AiImageGeneratorBundle\Config;

use Basilicom\AiImageGeneratorBundle\Config\Model\NullConfig;
use InvalidArgumentException;

class BundleConfiguration
{
    private array $featureConfiguration;
    private array $serviceConfigurations;
    private BrandConfiguration $brandConfiguration;

    /**
     * @param array $featureConfiguration
     * @param ServiceConfiguration[] $serviceConfigurations
     * @param BrandConfiguration $brandConfiguration
     */
    public function __construct(array $featureConfiguration, array $serviceConfigurations, BrandConfiguration $brandConfiguration)
    {
        $this->featureConfiguration = $featureConfiguration;
        $this->serviceConfigurations = $serviceConfigurations;
        $this->brandConfiguration = $brandConfiguration;
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

    public function getBrandConfiguration(): BrandConfiguration
    {
        return $this->brandConfiguration;
    }
}
