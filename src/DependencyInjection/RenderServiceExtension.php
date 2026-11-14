<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\DependencyInjection;

use Danilovl\RenderServiceTwigExtensionBundle\Attribute\{
    AsTwigFilter,
    AsTwigFunction
};
use Reflector;
use Symfony\Component\DependencyInjection\{
    ChildDefinition,
    ContainerBuilder
};
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class RenderServiceExtension extends Extension
{
    public const string TAG = 'danilovl.render_service.twig';
    public const string TWIG_RUNTIME_TAG = 'twig.runtime';

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

        $container->registerAttributeForAutoconfiguration(
            AsTwigFunction::class,
            static function (ChildDefinition $definition, AsTwigFunction $attribute, Reflector $reflector): void {
                self::tagDefinition($definition);
            }
        );

        $container->registerAttributeForAutoconfiguration(
            AsTwigFilter::class,
            static function (ChildDefinition $definition, AsTwigFilter $attribute, Reflector $reflector): void {
                self::tagDefinition($definition);
            }
        );

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . self::DIR_CONFIG));
        $loader->load('twig.yaml');
    }

    private static function tagDefinition(ChildDefinition $definition): void
    {
        foreach ([self::TAG, self::TWIG_RUNTIME_TAG] as $tag) {
            if (!$definition->hasTag($tag)) {
                $definition->addTag($tag);
            }
        }
    }

    public function getAlias(): string
    {
        return Configuration::ALIAS;
    }
}
