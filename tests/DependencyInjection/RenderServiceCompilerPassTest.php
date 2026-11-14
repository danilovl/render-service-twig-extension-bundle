<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\Tests\DependencyInjection;

use Danilovl\RenderServiceTwigExtensionBundle\DependencyInjection\Compiler\RenderServiceCompilerPass;
use Danilovl\RenderServiceTwigExtensionBundle\DependencyInjection\RenderServiceExtension;
use Danilovl\RenderServiceTwigExtensionBundle\Exception\RuntimeException;
use Danilovl\RenderServiceTwigExtensionBundle\Service\TwigExtensionContainer;
use Danilovl\RenderServiceTwigExtensionBundle\Tests\Mock\{
    BothAttributesService,
    CountService,
    SimpleMethodService
};
use ErrorException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\{
    ContainerBuilder,
    Definition
};

class RenderServiceCompilerPassTest extends TestCase
{
    public function testPartialOptionsDoNotTriggerWarning(): void
    {
        $container = $this->createContainer();
        $container->setParameter('danilovl_render_service.configuration', [
            'prefix' => null,
            'to_snake_case' => true,
            'filterOptions' => ['is_safe' => ['html']],
            'functionOptions' => ['is_safe_callback' => ['callback']]
        ]);
        $this->addTaggedService($container, SimpleMethodService::class);

        set_error_handler(static function (int $severity, string $message): never {
            throw new ErrorException($message, 0, $severity);
        });

        try {
            (new RenderServiceCompilerPass)->process($container);
        } finally {
            restore_error_handler();
        }

        $filters = $this->getRegistered($container, 'setFilters');
        $this->assertSame(['html'], $filters[0]['options']['is_safe']);
        $this->assertArrayHasKey('is_safe_callback', $filters[0]['options']);
        $this->assertNull($filters[0]['options']['is_safe_callback']);
        $this->assertArrayHasKey('preserves_safety', $filters[0]['options']);
        $this->assertNull($filters[0]['options']['preserves_safety']);

        $functions = $this->getRegistered($container, 'setFunctions');
        $this->assertSame(['callback'], $functions[0]['options']['is_safe_callback']);
        $this->assertArrayHasKey('is_safe', $functions[0]['options']);
        $this->assertNull($functions[0]['options']['is_safe']);
    }

    public function testUntaggedServiceIsIgnored(): void
    {
        $container = $this->createContainer();
        $container->setDefinition(CountService::class, new Definition(CountService::class));

        (new RenderServiceCompilerPass)->process($container);

        $this->assertSame([], $this->getRegistered($container, 'setFunctions'));
        $this->assertSame([], $this->getRegistered($container, 'setFilters'));
        $this->assertFalse($container->getDefinition(CountService::class)->hasTag(RenderServiceExtension::TWIG_RUNTIME_TAG));
    }

    public function testManuallyTaggedServiceIsRegisteredAsTwigRuntime(): void
    {
        $container = $this->createContainer();
        $this->addTaggedService($container, 'custom.count_service', CountService::class);

        (new RenderServiceCompilerPass)->process($container);

        $this->assertTrue($container->getDefinition('custom.count_service')->hasTag(RenderServiceExtension::TWIG_RUNTIME_TAG));

        $functions = $this->getRegistered($container, 'setFunctions');
        $this->assertSame(['math_sum', 'math_min'], array_column($functions, 'name'));
        $this->assertSame(['custom.count_service', 'custom.count_service'], array_column($functions, 'service'));
        $this->assertSame([CountService::class, CountService::class], array_column($functions, 'class'));
    }

    public function testSameClassInTwoServicesThrowsException(): void
    {
        $container = $this->createContainer();
        $this->addTaggedService($container, 'count_service_one', CountService::class);
        $this->addTaggedService($container, 'count_service_two', CountService::class);

        $this->expectException(RuntimeException::class);

        (new RenderServiceCompilerPass)->process($container);
    }

    public function testFunctionAndFilterOnOneMethodThrowsException(): void
    {
        $container = $this->createContainer();
        $this->addTaggedService($container, BothAttributesService::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('You can not use both AsTwigFunction and AsTwigFilter at the same time.');

        (new RenderServiceCompilerPass)->process($container);
    }

    private function createContainer(): ContainerBuilder
    {
        $container = new ContainerBuilder;
        (new RenderServiceExtension)->load([], $container);

        return $container;
    }

    private function addTaggedService(ContainerBuilder $container, string $id, ?string $class = null): void
    {
        $definition = new Definition($class ?? $id);
        $definition->addTag(RenderServiceExtension::TAG);

        $container->setDefinition($id, $definition);
    }

    /**
     * @return array<array{name: string, service: string, class: string, method: string, options: array}>
     */
    private function getRegistered(ContainerBuilder $container, string $method): array
    {
        foreach ($container->getDefinition(TwigExtensionContainer::class)->getMethodCalls() as [$name, $arguments]) {
            if ($name === $method) {
                /** @var array<array{name: string, service: string, class: string, method: string, options: array}> $items */
                $items = $arguments[0];

                return $items;
            }
        }

        $this->fail(sprintf('Method call "%s" not found.', $method));
    }
}
