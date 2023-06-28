<?php

namespace Basilicom\AiImageGeneratorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('basilicom_ai_images');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('stable-diffusion-api')
                    ->children()
                        ->scalarNode('baseUrl')->end()
                        ->scalarNode('model')->end()
                        ->integerNode('steps')->defaultValue(10)->end()
                    ->end()
                ->end()

                ->arrayNode('midjourney')
                    ->children()
                        ->scalarNode('apiKey')->end()
                        ->scalarNode('secret')->end()
                    ->end()
                ->end()

                ->arrayNode('dreamstudio')
                    ->children()
                        ->scalarNode('apiKey')->end()
                        ->scalarNode('secret')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
