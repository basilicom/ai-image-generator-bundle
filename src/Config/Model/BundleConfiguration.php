<?php

namespace Basilicom\AiImageGeneratorBundle\Config\Model;

use InvalidArgumentException;

class BundleConfiguration
{
    private array $featureConfiguration;
    private array $serviceConfigurations;
    private BrandConfiguration $brandConfiguration;
    private PromptEnhancementConfig $promptingConfiguration;

    /**
     * @param array $featureConfiguration
     * @param ImageGenerationConfig[] $serviceConfigurations
     * @param BrandConfiguration $brandConfiguration
     * @param PromptEnhancementConfig $promptingConfiguration
     */
    public function __construct(
        array                  $featureConfiguration,
        array                  $serviceConfigurations,
        BrandConfiguration     $brandConfiguration,
        PromptEnhancementConfig $promptingConfiguration
    ) {
        $this->featureConfiguration = $featureConfiguration;
        $this->serviceConfigurations = $serviceConfigurations;
        $this->brandConfiguration = $brandConfiguration;
        $this->promptingConfiguration = $promptingConfiguration;
    }

    public function getUsedServiceForFeature(string $feature): string
    {
        if (!array_key_exists($feature, $this->featureConfiguration)) {
            throw new InvalidArgumentException('Invalid feature');
        }

        return $this->featureConfiguration[$feature] ?? '';
    }

    public function getServiceConfiguration(string $serviceKey): ?ImageGenerationConfig
    {
        return $this->serviceConfigurations[$serviceKey] ?? new NullConfig();
    }

    public function getBrandConfiguration(): BrandConfiguration
    {
        return $this->brandConfiguration;
    }

    public function getPromptEnhancementConfiguration(): PromptEnhancementConfig
    {
        return $this->promptingConfiguration;
    }
}
