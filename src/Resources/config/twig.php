<?php declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Danilovl\RenderServiceTwigExtensionBundle\Twig\RenderServiceExtension;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set(RenderServiceExtension::class, RenderServiceExtension::class)
        ->autowire()
        ->arg('$container', service('service_container'))
        ->private()
        ->tag('twig.extension');
};
