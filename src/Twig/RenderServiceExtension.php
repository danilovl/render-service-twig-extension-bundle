<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\Twig;

use ReflectionException;
use ReflectionMethod;
use Twig\TwigFunction;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Extension\AbstractExtension;

class RenderServiceExtension extends AbstractExtension
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_service', [$this, 'renderService'], ['is_safe' => ['html']])
        ];
    }

    /**
     * @param string $service
     * @param string $method
     * @param array $parameters
     * @param array $services
     * @return string|null
     * @throws ReflectionException
     */
    public function renderService(
        string $service,
        string $method,
        array $parameters = [],
        array $services = []
    ): ?string {
        $service = $this->container->get($service);

        $injectParameters = [];
        foreach ($services as $key => $value) {
            $injectParameters[$key] = $this->container->get($value);
        }

        $parameters = $this->orderParameters(
            get_class($service),
            $method,
            array_replace($injectParameters, $parameters)
        );

        $response = call_user_func_array([$service, $method], $parameters);
        if ($response instanceof Response) {
            $response = $response->getContent();
        }

        if (!is_string($response)) {
            return null;
        }

        return $response;
    }

    /**
     * @param string $class
     * @param string $method
     * @param array $parameters
     * @return array
     * @throws ReflectionException
     */
    private function orderParameters(
        string $class,
        string $method,
        array $parameters
    ): array {
        $reflectionMethod = new ReflectionMethod($class, $method);
        $reflectionParameters = $reflectionMethod->getParameters();

        $injectParameters = [];
        foreach ($reflectionParameters as $reflectionParameter) {
            $refName = $reflectionParameter->getName();
            if (!isset($parameters[$refName]) && $reflectionParameter->isDefaultValueAvailable()) {
                $injectParameters[$refName] = $reflectionParameter->getDefaultValue();
            } else {
                $injectParameters[$refName] = $parameters[$refName];
            }
        }

        return $injectParameters;
    }
}