<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle;

use Danilovl\RenderServiceTwigExtensionBundle\DependencyInjection\RenderServiceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RenderServiceTwigExtensionBundle extends Bundle
{
    public function getContainerExtension(): RenderServiceExtension
    {
        return new RenderServiceExtension;
    }
}
