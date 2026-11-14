<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\Twig;

use Danilovl\RenderServiceTwigExtensionBundle\Service\TwigExtensionContainer;
use Twig\{
    TwigFilter,
    TwigFunction
};
use Twig\Extension\AbstractExtension;

class AsAttributeTwigExtension extends AbstractExtension
{
    public function __construct(private readonly TwigExtensionContainer $containerTwigExtension) {}

    public function getFilters(): array
    {
        $result = [];

        foreach ($this->containerTwigExtension->getFilters() as $filter) {
            $result[] = new TwigFilter($filter->name, [$filter->class, $filter->method], $filter->options);
        }

        return $result;
    }

    public function getFunctions(): array
    {
        $result = [];

        foreach ($this->containerTwigExtension->getFunctions() as $function) {
            $result[] = new TwigFunction($function->name, [$function->class, $function->method], $function->options);
        }

        return $result;
    }
}
