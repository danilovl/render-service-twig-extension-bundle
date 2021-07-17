<?php declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Danilovl\RenderServiceTwigExtensionBundle\Twig\RenderServiceExtension;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('danilovl_render_service_twig_extension', RenderServiceExtension::class)
        ->autowire()
        ->private()
        ->tag('twig.extension')
        ->alias(RenderServiceExtension::class, 'danilovl_render_service_twig_extension');
};
