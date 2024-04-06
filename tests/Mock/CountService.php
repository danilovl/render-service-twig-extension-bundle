<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\Tests\Mock;

use Danilovl\RenderServiceTwigExtensionBundle\Attribute\AsTwigFunction;

#[AsTwigFunction('math_')]
class CountService
{
    public function sum(int $one, int $two): int
    {
        return $one + $two;
    }

    public function min(int $one, int $two): int
    {
        return $one - $two;
    }
}
