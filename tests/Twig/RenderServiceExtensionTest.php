<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\Tests\Twig;

use Danilovl\RenderServiceTwigExtensionBundle\Twig\RenderServiceExtension;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\{
    Container,
    ContainerInterface
};
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class RenderServiceExtensionTest extends TestCase
{
    private Environment $twig;

    public function setUp(): void
    {
        $this->twig = new Environment(new FilesystemLoader, [
            'cache' => __DIR__ . '/../../var/cache/twig-test',
        ]);

        $renderServiceExtension = new RenderServiceExtension($this->createContainer());
        $this->twig->addExtension($renderServiceExtension);
    }

    /**
     * @dataProvider filtersProvider
     */
    public function testFilters(string $template, mixed $result): void
    {
        $output = $this->twig->createTemplate($template)->render();

        $this->assertEquals($output, $result);
    }

    public function filtersProvider(): Generator
    {
        yield ["{{ render_service('app.service.controller', 'simpleText') }}", 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.'];
        yield ["{{ render_service('App\\\\Controller\\\\ServiceController', 'simpleText') }}", 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.'];
    }

    private function createContainer(): ContainerInterface
    {
        $service = new class {
            public function simpleText(): string
            {
                return 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.';
            }
        };

        $container = new Container;
        $container->set('app.service.controller', $service);
        $container->set('App\\Controller\\ServiceController', $service);

        return $container;
    }
}
