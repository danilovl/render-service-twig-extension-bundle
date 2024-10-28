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

abstract class BaseAsAttributeTwigExtension extends TestCase
{
    private Environment $twig;

    protected function setUp(): void
    {
        $this->twig = new Environment(new FilesystemLoader, [
            'cache' => __DIR__ . '/../../var/cache/twig-test',
        ]);
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
        $container->setDefinition(SimpleMethodService::class, $definition);

        $definition = new Definition(CountService::class);
        $definition->setPublic(true);
        $container->setDefinition(CountService::class, $definition);

        $this->expectNotToPerformAssertions();

        return $container;
    }

    #[Depends('testAddAttributeService')]
    public function testCompilerPass(ContainerBuilder $container): ContainerBuilder
    {
        (new RenderServiceCompilerPass)->process($container);

        $this->expectNotToPerformAssertions();

        $container->compile();

        return $container;
    }

    #[DataProvider('extensionProvider')]
    #[Depends('testCompilerPass')]
    public function testTwigExtension(string $template, mixed $result, ContainerInterface $container): void
    {
        /** @var ExtensionInterface $renderServiceExtension */
        $renderServiceExtension = $container->get(AsAttributeTwigExtension::class);
        $this->twig->addExtension($renderServiceExtension);

        $output = $this->twig->createTemplate($template)->render();

        $this->assertEquals($output, $result);
    }

    abstract public static function extensionProvider(): Generator;

    abstract protected function configData(): array;
}
