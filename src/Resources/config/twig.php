<?php declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Danilovl\RenderServiceTwigExtensionBundle\Service\TwigExtensionContainer;
use Danilovl\RenderServiceTwigExtensionBundle\Twig\{
    AsAttributeTwigExtension,
};

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set(AsAttributeTwigExtension::class, AsAttributeTwigExtension::class)
        ->autowire()
        ->arg('$container', service('service_container'))
        ->public()
        ->tag('twig.extension');

    $container->services()
        ->set(TwigExtensionContainer::class, TwigExtensionContainer::class)
        ->public();
};
