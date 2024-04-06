<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\Service;

class TwigExtensionContainer
{
    /**
     * @var TwigExtensionItem[]
     */
    private array $functions = [];

    /**
     * @var TwigExtensionItem[]
     */
    private array $filters = [];

    public function getFunctions(): array
    {
        return $this->functions;
    }

    /**
     * @param array{name: string, service: string, method: string, options: array} $functions
     */
    public function setFunctions(array $functions): void
    {
        $this->functions = array_map(static function (array $item): TwigExtensionItem {
            return new TwigExtensionItem(
                $item['name'],
                $item['service'],
                $item['method'],
                $item['options']
            );
        }, $functions);
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param array{name: string, service: string, method: string, options: array} $filters
     */
    public function setFilters(array $filters): void
    {
        $this->filters = array_map(static function (array $item): TwigExtensionItem {
            return new TwigExtensionItem(
                $item['name'],
                $item['service'],
                $item['method'],
                $item['options']
            );
        }, $filters);
    }
}
