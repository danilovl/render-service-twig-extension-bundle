<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\Twig;

use Danilovl\RenderServiceTwigExtensionBundle\Service\TwigExtensionContainer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\{
    TwigFilter,
    TwigFunction
};
use Twig\Extension\AbstractExtension;

class AsAttributeTwigExtension extends AbstractExtension
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly TwigExtensionContainer $containerTwigExtension
    ) {}

    public function getFilters(): array
    {
        $result = [];
        $filters = $this->containerTwigExtension->getFilters();

        foreach ($filters as $filter) {
            $service = $this->container->get($filter->service);
            $callable = [$service, $filter->method];

            $result[] = new TwigFilter($filter->name, $callable, $filter->options);
        }

        return $result;
    }

    public function getFunctions(): array
    {
        $result = [];
        $functions = $this->containerTwigExtension->getFunctions();

        foreach ($functions as $function) {
            $service = $this->container->get($function->service);
            $callable = [$service, $function->method];

            $result[] = new TwigFunction($function->name, $callable, $function->options);
        }

        return $result;
    }
}
