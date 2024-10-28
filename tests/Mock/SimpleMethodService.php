<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\Tests\Mock;

use Danilovl\RenderServiceTwigExtensionBundle\Attribute\{
    AsTwigFilter,
    AsTwigFunction
};

class SimpleMethodService
{
    #[AsTwigFunction('function_sum')]
    public function twigFunctionSum(int $one, int $two): int
    {
        return $one + $two;
    }

    #[AsTwigFilter('filter_upper')]
    public function twigFilterUpper(string $text): string
    {
        return mb_strtoupper($text);
    }
}
