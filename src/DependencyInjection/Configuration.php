<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Twig\Node\Expression\{
    FilterExpression,
    FunctionExpression
};

class Configuration implements ConfigurationInterface
{
    public const string ALIAS = 'danilovl_render_service';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(self::ALIAS);
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('prefix')
                    ->defaultNull()
                ->end()
                ->scalarNode('to_snake_case')
                    ->defaultTrue()
                ->end()
            ->end()
            ->children()
                ->arrayNode('filter_options')
                    ->children()
                        ->scalarNode('needs_environment')
                             ->defaultFalse()
                        ->end()
                        ->scalarNode('needs_context')
                            ->defaultFalse()
                        ->end()
                        ->scalarNode('is_variadic')
                            ->defaultFalse()
                        ->end()
                        ->arrayNode('is_safe')
                            ->prototype('scalar')
                                ->defaultNull()
                            ->end()
                        ->end()
                        ->arrayNode('is_safe_callback')
                            ->prototype('scalar')
                                ->defaultNull()
                            ->end()
                        ->end()
                        ->scalarNode('pre_escape')
                            ->defaultNull()
                        ->end()
                        ->arrayNode('preserves_safety')
                            ->prototype('scalar')
                                ->defaultNull()
                            ->end()
                        ->end()
                        ->scalarNode('node_class')
                            ->defaultValue(FilterExpression::class)
                        ->end()
                        ->scalarNode('deprecated')
                            ->defaultFalse()
                        ->end()
                        ->scalarNode('alternative')
                            ->defaultNull()
                        ->end()
                        ->scalarNode('deprecated')
                            ->defaultFalse()
                        ->end()
                        ->scalarNode('alternative')
                            ->defaultNull()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->children()
                ->arrayNode('function_options')
                    ->children()
                        ->scalarNode('needs_environment')
                             ->defaultFalse()
                        ->end()
                        ->scalarNode('needs_context')
                            ->defaultFalse()
                        ->end()
                        ->scalarNode('is_variadic')
                            ->defaultFalse()
                        ->end()
                        ->arrayNode('is_safe')
                            ->prototype('scalar')
                                ->defaultNull()
                            ->end()
                        ->end()
                        ->arrayNode('is_safe_callback')
                            ->prototype('scalar')
                                ->defaultNull()
                            ->end()
                        ->end()
                        ->scalarNode('node_class')
                            ->defaultValue(FunctionExpression::class)
                        ->end()
                        ->scalarNode('deprecated')
                            ->defaultFalse()
                        ->end()
                        ->scalarNode('alternative')
                            ->defaultNull()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
