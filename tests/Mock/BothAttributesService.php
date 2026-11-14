<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\Tests\Mock;

use Danilovl\RenderServiceTwigExtensionBundle\Attribute\{
    AsTwigFilter,
    AsTwigFunction
};

class BothAttributesService
{
    #[AsTwigFunction('both_function')]
    #[AsTwigFilter('both_filter')]
    public function both(string $text): string
    {
        return $text;
    }
}
