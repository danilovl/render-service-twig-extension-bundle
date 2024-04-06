<?php declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Danilovl\RenderServiceTwigExtensionBundle\Service\TwigExtensionContainer;
use Danilovl\RenderServiceTwigExtensionBundle\Twig\AsAttributeTwigExtension;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
        ->autowire()
        ->public();

    $services
        ->set(AsAttributeTwigExtension::class, AsAttributeTwigExtension::class)
        ->arg('$container', service('service_container'))
        ->tag('twig.extension');

    $services->set(TwigExtensionContainer::class, TwigExtensionContainer::class);
};
