<?php

namespace Basilicom\AiImageGeneratorBundle\Config;

use Basilicom\AiImageGeneratorBundle\Model\FeatureEnum;
use InvalidArgumentException;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigurationDefinition implements ConfigurationInterface
{
    public const FEATURE_SERVICES = 'feature_services';
    public const SERVICES = 'services';

    public const STABLE_DIFFUSION_API = 'stable_diffusion_api';
    public const DREAM_STUDIO = 'dream_studio';
    public const OPEN_AI = 'open_ai';
    public const CLIP_DROP = 'clip_drop';

    public const BASE_URL = 'baseUrl';
    public const MODEL = 'model';
    public const INPAINT_MODEL = 'inpaint_model';
    public const STEPS = 'steps';
    public const API_KEY = 'apiKey';
    public const UPSCALER = 'upscaler';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ai_image_generator');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode(self::FEATURE_SERVICES)
                    ->isRequired()
                    ->children()
                        ->scalarNode(FeatureEnum::TXT_2_IMG)->isRequired()->end()
                        ->scalarNode(FeatureEnum::UPSCALE)->isRequired()->end()
                        ->scalarNode(FeatureEnum::INPAINT)->isRequired()->end()
                        ->scalarNode(FeatureEnum::INPAINT_BACKGROUND)->isRequired()->end()
                        ->scalarNode(FeatureEnum::IMAGE_VARIATIONS)->isRequired()->end()
                    ->end()
                ->end()
                ->arrayNode(self::SERVICES)
                    ->isRequired()
                    ->useAttributeAsKey('serviceKey')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode(self::BASE_URL)->isRequired()->end()
                            ->scalarNode(self::MODEL)->end()
                            ->scalarNode(self::INPAINT_MODEL)->end()
                            ->integerNode(self::STEPS)->end()
                            ->scalarNode(self::UPSCALER)->end()
                            ->scalarNode(self::API_KEY)
                                ->validate()
                                    ->ifTrue(function ($apiKey, $serviceKey) use ($rootNode) {
                                        $isServiceWithApiKey = $serviceKey === self::DREAM_STUDIO
                                            || $serviceKey === self::CLIP_DROP
                                            || $serviceKey === self::OPEN_AI;

                                        return !($isServiceWithApiKey && !empty($apiKey));
                                    })
                                    ->thenInvalid('API key is required for this service.')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->validate()
                        ->ifTrue(function ($configurationData) {
                            foreach ($configurationData as $serviceKey => $serviceConfig) {
                                $expectedKeys = match ($serviceKey) {
                                    self::STABLE_DIFFUSION_API => ['baseUrl', 'model', 'inpaint_model', 'steps', 'upscaler'],
                                    self::DREAM_STUDIO => ['baseUrl', 'model', 'inpaint_model', 'steps', 'upscaler', 'apiKey'],
                                    self::OPEN_AI => ['baseUrl', 'apiKey'],
                                    self::CLIP_DROP => ['baseUrl', 'apiKey'],
                                };

                                foreach ($expectedKeys as $key) {
                                    if (!array_key_exists($key, $serviceConfig) || empty($serviceConfig[$key])) {
                                        throw new InvalidArgumentException('Expected ' . $key . ' to be provided for ' . $serviceKey);
                                    }
                                }
                            }
                        })
                        ->thenInvalid('Invalid config provided.')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
