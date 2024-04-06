<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\DependencyInjection\Compiler;

use Danilovl\RenderServiceTwigExtensionBundle\Attribute\{
    AsTwigFilter,
    AsTwigFunction
};
use Danilovl\RenderServiceTwigExtensionBundle\Exception\RuntimeException;
use Danilovl\RenderServiceTwigExtensionBundle\Helper\FormatNameHelper;
use Danilovl\RenderServiceTwigExtensionBundle\Service\TwigExtensionContainer;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RenderServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $twigExtensionContainer = $container->getDefinition(TwigExtensionContainer::class);

        /** @var array{
         *  prefix: string,
         *  to_snake_case: bool,
         *  filterOptions: array,
         *  functionOptions: array
         * } $configuration
         */
        $configuration = $container->getParameter('danilovl_render_service.configuration');
        $configuration = $this->configurationDefault($configuration);

        $result = [
            'function' => [],
            'filter' => [],
        ];

        foreach ($container->getDefinitions() as $id => $definition) {
            if ($definition->hasTag('container.excluded') || $definition->isAbstract() || $definition->isSynthetic()) {
                continue;
            }

            $class = $definition->getClass();
            if (!$class || !class_exists($class)) {
                continue;
            }

            $reflectionClass = (new ReflectionClass($class));
            $classAttribute = $this->validationCountAttributes('class', $reflectionClass);

            $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

            if ($classAttribute !== null) {
                foreach ($methods as $method) {
                    $this->createExtension($result, $id, $method->getName(), $configuration, $classAttribute, true);
                }

                continue;
            }

            foreach ($methods as $method) {
                $methodAttribute = $this->validationCountAttributes('method', $method);
                if ($methodAttribute === null) {
                    continue;
                }

                $this->createExtension($result, $id, $method->getName(), $configuration, $methodAttribute);
            }
        }

        $twigExtensionContainer->addMethodCall('setFunctions', [$result['function']]);
        $twigExtensionContainer->addMethodCall('setFilters', [$result['filter']]);
    }

    private function createExtension(
        array &$result,
        string $service,
        string $method,
        array $configuration,
        ReflectionAttribute $reflectionAttribute,
        bool $useMethodName = false
    ): void {
        /** @var AsTwigFunction|AsTwigFilter $asTwigFunctionAttribute */
        $asTwigFunctionAttribute = $reflectionAttribute->newInstance();

        $key = match ($asTwigFunctionAttribute::class) {
            AsTwigFunction::class => 'function',
            AsTwigFilter::class => 'filter',
        };

        $configurationKey = $key . 'Options';

        $options = [...$configuration[$configurationKey], ...$asTwigFunctionAttribute->options];
        $prefix = $configuration['prefix'];

        $name = $asTwigFunctionAttribute->alias;

        if ($useMethodName) {
            $methodSuffix = $method;
            if ($configuration['to_snake_case']) {
                $methodSuffix = FormatNameHelper::camelCaseToSnakeCase($methodSuffix);
            }

            $name = sprintf('%s%s', $name, $methodSuffix);
        }

        if ($prefix) {
            $name = sprintf('%s%s', $prefix, $name);
        }

        $result[$key][] = [
            'name' => $name,
            'service' => $service,
            'method' => $method,
            'options' => $options
        ];
    }

    private function configurationDefault(array $configuration): array
    {
        $default = [
            'filterOptions' => [
                'is_safe',
                'is_safe_callback',
                'preserves_safety'
            ],
            'functionOptions' => [
                'is_safe',
                'is_safe_callback'
            ]
        ];

        foreach ($default as $key => $options) {
            if (empty($configuration[$key])) {
                continue;
            }

            foreach ($options as $option) {
                $item = $configuration[$key][$option];
                if (empty($item)) {
                    $item = null;
                }

                $configuration[$key][$option] = $item;
            }

        }

        return $configuration;
    }

    private function validationCountAttributes(
        string $place,
        ReflectionClass|ReflectionMethod $reflection
    ): ?ReflectionAttribute {
        $asTwigFunctionAttributes = $reflection->getAttributes(AsTwigFunction::class);
        if (count($asTwigFunctionAttributes) > 1) {
            $message = sprintf('You can not use more than one AsTwigFunction on a %s.', $place);

            throw new RuntimeException($message);
        }

        $asTwigFilterAttributes = $reflection->getAttributes(AsTwigFilter::class);
        if (count($asTwigFilterAttributes) > 1) {
            $message = sprintf('You can not use more than one AsTwigFilter on a %s.', $place);

            throw new RuntimeException($message);
        }

        $asTwigFunctionAttribute = $reflection->getAttributes(AsTwigFunction::class)[0] ?? null;
        $asTwigFilterAttribute = $reflection->getAttributes(AsTwigFilter::class)[0] ?? null;

        if ($asTwigFunctionAttribute !== null && $asTwigFilterAttribute !== null) {
            throw new RuntimeException('You can not use both AsTwigFunction and AsTwigFilter at the same time.');
        }

        return $asTwigFunctionAttribute ?? $asTwigFilterAttribute ?? null;
    }
}
