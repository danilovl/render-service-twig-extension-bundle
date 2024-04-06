<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\Attribute;

use Attribute;

#[Attribute]
readonly class AsTwigFilter
{
    /**
     * @param array{
     *      needs_environment?: bool,
     *      needs_context?: bool,
     *      is_variadic?: bool,
     *      is_safe?: array<string>|null,
     *      is_safe_callback?: array<string>|null,
     *      pre_escape?: string|null,
     *      preserves_safety?: bool|null,
     *      node_class?: string,
     *      deprecated?: bool,
     *      alternative?: string|null
     * } $options
     */
    public function __construct(
        public string $alias,
        public array $options = []
    ) {}
}
