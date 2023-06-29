<?php

namespace Basilicom\AiImageGeneratorBundle\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigurationDefinition implements ConfigurationInterface
{
    const STABLE_DIFFUSION_API = 'stable_diffusion_api';
    const MIDJOURNEY = 'midjourney';
    const DREAMSTUDIO = 'dream_studio';

    const BASE_URL = 'baseUrl';
    const MODEL = 'model';
    const STEPS = 'steps';
    const API_KEY = 'apiKey';
    const API_SECRET = 'secret';

    private const SUPPORTED_APIS = [
        self::STABLE_DIFFUSION_API,
        self::DREAMSTUDIO,
        //self::MIDJOURNEY,
    ];

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ai_image_generator');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->validate()
                ->ifTrue(function ($config) {
                    return count($config) !== 1; // Only one of the keys should be present
                })
                ->thenInvalid('Exactly one key (' . implode(', ', self::SUPPORTED_APIS) . ') should be present.')
            ->end()
            ->validate()
                ->ifTrue(function ($config) {
                    foreach (self::SUPPORTED_APIS as $supportedApi) {
                        if (isset($config[$supportedApi])) {
                            return false;
                        }
                    }

                    return true;
                })
                ->thenInvalid('One of the keys (' . implode(', ', self::SUPPORTED_APIS) . ') should be present.')
            ->end()
            ->children()
                ->arrayNode(self::STABLE_DIFFUSION_API)
                    ->children()
                        ->scalarNode(self::BASE_URL)->end()
                        ->scalarNode(self::MODEL)->end()
                        ->integerNode(self::STEPS)->defaultValue(10)->end()
                    ->end()
                ->end()

                ->arrayNode(self::MIDJOURNEY)
                    ->children()
                        ->scalarNode(self::API_KEY)->end()
                        ->scalarNode(self::API_SECRET)->end()
                    ->end()
                ->end()

                ->arrayNode(self::DREAMSTUDIO)
                    ->children()
                        ->scalarNode(self::API_KEY)->end()
                        ->scalarNode(self::API_SECRET)->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
