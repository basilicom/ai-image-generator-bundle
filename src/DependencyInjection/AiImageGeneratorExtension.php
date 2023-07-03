<?php

declare(strict_types=1);

namespace Basilicom\AiImageGeneratorBundle\DependencyInjection;

use Basilicom\AiImageGeneratorBundle\Config\ConfigurationDefinition;
use Basilicom\AiImageGeneratorBundle\Config\ConfigurationService;
use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class AiImageGeneratorExtension extends Extension
{
    /**
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new ConfigurationDefinition();
        $config = $this->processConfiguration($configuration, $configs);
        $config = $container->resolveEnvPlaceholders($config, true);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

        $container->getDefinition(ConfigurationService::class)->setArgument('$config', $config);
    }
}
