<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\Service;

readonly class TwigExtensionItem
{
    public function __construct(
        public string $name,
        public string $service,
        public string $class,
        public string $method,
        public array $options
    ) {}
}
