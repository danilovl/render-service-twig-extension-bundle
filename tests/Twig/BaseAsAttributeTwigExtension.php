<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\Tests\Twig;

use Danilovl\RenderServiceTwigExtensionBundle\DependencyInjection\Compiler\RenderServiceCompilerPass;
use Danilovl\RenderServiceTwigExtensionBundle\DependencyInjection\RenderServiceExtension;
use Danilovl\RenderServiceTwigExtensionBundle\Tests\Mock\{
    CountService,
    SimpleMethodService
};
use Danilovl\RenderServiceTwigExtensionBundle\Twig\AsAttributeTwigExtension;
use Generator;
use PHPUnit\Framework\Attributes\{
    DataProvider,
    Depends
};
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\{
    Definition,
    ContainerBuilder,
    ContainerInterface,
    ParameterBag\ParameterBag
};
use Twig\Environment;
use Twig\Extension\ExtensionInterface;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;

abstract class BaseAsAttributeTwigExtension extends TestCase
{
    private Environment $twig;

    protected function setUp(): void
    {
        $this->twig = new Environment(new FilesystemLoader, ['cache' => false]);
    }

    public function testExtension(): ContainerBuilder
    {
        $container = new ContainerBuilder(new ParameterBag);
        (new RenderServiceExtension)->load($this->configData(), $container);

        $this->expectNotToPerformAssertions();

        return $container;
    }

    #[Depends('testExtension')]
    public function testAddAttributeService(ContainerBuilder $container): ContainerInterface
    {
        $definition = new Definition(SimpleMethodService::class);
        $definition->setPublic(true);
        $definition->setAutoconfigured(true);
        $container->setDefinition(SimpleMethodService::class, $definition);

        $definition = new Definition(CountService::class);
        $definition->setPublic(true);
        $definition->setAutoconfigured(true);
        $container->setDefinition(CountService::class, $definition);

        $this->expectNotToPerformAssertions();

        return $container;
    }

    #[Depends('testAddAttributeService')]
    public function testCompilerPass(ContainerBuilder $container): ContainerBuilder
    {
        $container->addCompilerPass(new RenderServiceCompilerPass);
        $container->compile();

        foreach ([SimpleMethodService::class, CountService::class] as $id) {
            $this->assertTrue($container->getDefinition($id)->hasTag(RenderServiceExtension::TAG));
            $this->assertTrue($container->getDefinition($id)->hasTag(RenderServiceExtension::TWIG_RUNTIME_TAG));
        }

        return $container;
    }

    #[DataProvider('extensionProvider')]
    #[Depends('testCompilerPass')]
    public function testTwigExtension(string $template, mixed $result, ContainerInterface $container): void
    {
        /** @var ExtensionInterface $renderServiceExtension */
        $renderServiceExtension = $container->get(AsAttributeTwigExtension::class);
        $this->twig->addExtension($renderServiceExtension);

        $created = [];
        $runtimeLoader = new FactoryRuntimeLoader([
            SimpleMethodService::class => static function () use ($container, &$created): object {
                $created[] = SimpleMethodService::class;

                return $container->get(SimpleMethodService::class);
            },
            CountService::class => static function () use ($container, &$created): object {
                $created[] = CountService::class;

                return $container->get(CountService::class);
            }
        ]);

        $this->twig->addRuntimeLoader($runtimeLoader);

        $this->twig->getFunctions();
        $this->twig->getFilters();
        $this->assertSame([], $created, 'Owner services must not be created while registering functions/filters.');

        $output = $this->twig->createTemplate($template)->render();

        $this->assertEquals($output, $result);
        $this->assertCount(1, $created, 'Owner service must be created on first call.');
    }

    abstract public static function extensionProvider(): Generator;

    abstract protected function configData(): array;
}
