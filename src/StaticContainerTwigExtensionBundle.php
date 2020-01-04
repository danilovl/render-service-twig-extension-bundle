<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle;

use Danilovl\RenderServiceTwigExtensionBundle\DependencyInjection\RenderServiceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class StaticContainerTwigExtensionBundle extends Bundle
{
    /**
     * @return RenderServiceExtension
     */
    public function getContainerExtension(): RenderServiceExtension
    {
        return new RenderServiceExtension;
    }
}
