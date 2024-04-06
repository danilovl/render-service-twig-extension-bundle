<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\Tests\Twig;

use Generator;

class AsAttributeWithPrefixTwigExtensionTest extends BaseAsAttributeTwigExtension
{
    public static function extensionProvider(): Generator
    {
        yield ["{{ 'tests' | with_prefix_filter_upper }}", 'TESTS'];
        yield ["{{ 'AbCdE' | with_prefix_filter_upper }}", 'ABCDE'];

        yield ["{{ with_prefix_function_sum(0,0) }}", 0];
        yield ["{{ with_prefix_function_sum(1,1) }}", 2];
        yield ["{{ with_prefix_function_sum(2,3) }}", 5];

        yield ["{{ with_prefix_math_sum(0,0) }}", 0];
        yield ["{{ with_prefix_math_sum(1,1) }}", 2];
        yield ["{{ with_prefix_math_min(2,3) }}", -1];
        yield ["{{ with_prefix_math_min(20,3) }}", 17];
    }

    protected function configData(): array
    {
        return [
            'danilovl_render_service' => [
                'prefix' => 'with_prefix_',
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
