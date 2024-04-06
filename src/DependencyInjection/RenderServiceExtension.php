<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class RenderServiceExtension extends Extension
{
    private const string DIR_CONFIG = '/../Resources/config';

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration;
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('danilovl_render_service.configuration', [
            'prefix' => $config['prefix'],
            'to_snake_case' => $config['to_snake_case'],
            'filterOptions' => $config['filter_options'] ?? [],
            'functionOptions' => $config['function_options'] ?? []
        ]);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . self::DIR_CONFIG));
        $loader->load('twig.yaml');
    }

    public function getAlias(): string
    {
        return Configuration::ALIAS;
    }
}
