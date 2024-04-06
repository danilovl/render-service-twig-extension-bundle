<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\Attribute;

use Attribute;

#[Attribute]
readonly class AsTwigFunction
{
    /**
     * @param array{
     *      needs_environment?: bool,
     *      needs_context?: bool,
     *      is_variadic?: bool,
     *      is_safe?: array<string>|null,
     *      is_safe_callback?: array<string>|null,
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
