<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\Tests\Twig;

use Generator;

class AsAttributeWithoutPrefixTwigExtensionTest extends BaseAsAttributeTwigExtension
{
    public static function extensionProvider(): Generator
    {
        yield ["{{ 'tests' | filter_upper }}", 'TESTS'];
        yield ["{{ 'AbCdE' | filter_upper }}", 'ABCDE'];

        yield ["{{ function_sum(0,0) }}", 0];
        yield ["{{ function_sum(1,1) }}", 2];
        yield ["{{ function_sum(2,3) }}", 5];

        yield ["{{ math_sum(0,0) }}", 0];
        yield ["{{ math_sum(1,1) }}", 2];
        yield ["{{ math_min(2,3) }}", -1];
        yield ["{{ math_min(20,3) }}", 17];
    }

    protected function configData(): array
    {
        return [
            'danilovl_render_service' => [
                'prefix' => null,
                'filter_options' => [
                    'is_safe' => ['html']
                ],
                'function_options' => [
                    'is_safe' => ['html']
                ]
            ]
        ];
    }
}
