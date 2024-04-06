[![phpunit](https://github.com/danilovl/render-service-twig-extension-bundle/actions/workflows/phpunit.yml/badge.svg)](https://github.com/danilovl/render-service-twig-extension-bundle/actions/workflows/phpunit.yml)
[![downloads](https://img.shields.io/packagist/dt/danilovl/render-service-twig-extension-bundle)](https://packagist.org/packages/danilovl/render-service-twig-extension-bundle)
[![latest Stable Version](https://img.shields.io/packagist/v/danilovl/render-service-twig-extension-bundle)](https://packagist.org/packages/danilovl/render-service-twig-extension-bundle)
[![license](https://img.shields.io/packagist/l/danilovl/render-service-twig-extension-bundle)](https://packagist.org/packages/danilovl/render-service-twig-extension-bundle)

# RenderServiceTwigExtensionBundle #

## About ##

Symfony twig extension bundle provides an easy way for creating functions or filters from service methods or objects.

The main task of this extension is to replace the existing twig method `render(controller())`.

The main disadvantage of the existing method is that it performs a sub-request, which increases the total query execution time.

## Comparison performance metrics ##

In this example, the same page was used, with the only difference being the number of times the standard `render(controller())` method was used compared to the new runtime twig function.

On the left side, you can see the use of the standard twig function, while on the right side, the runtime twig function is used.

Render three times.

![Alt text](/pic/example-1.png?raw=true "First example")

Render ten times.

![Alt text](/pic/example-2.png?raw=true "Second example")

### Requirements

* PHP 8.3 or higher
* Symfony 7.0 or higher

### 1. Installation

Install `danilovl/render-service-twig-extension-bundle` package by Composer:

``` bash
composer require danilovl/render-service-twig-extension-bundle
```
Add the RenderServiceTwigExtensionBundle to your application's bundles if does not add automatically:

``` php
<?php
// config/bundles.php

return [
    // ...
    Danilovl\RenderServiceTwigExtensionBundle\RenderServiceTwigExtensionBundle::class => ['all' => true]
];
```

### 2. Configuration

You can set global parameters for all extensions.

```yaml
danilovl_render_service:
  prefix: null
  to_snake_case: true

  filter_options:
    needs_environment: false
    needs_context: false
    is_variadic: false
    is_safe: []
    is_safe_callback: []
    pre_escape: null
    preserves_safety: []
    node_class: FunctionExpression::class
    deprecated: false
    alternative: null

  function_options:
    needs_environment: false
    needs_context: false
    is_variadic: false
    is_safe: []
    is_safe_callback: []
    node_class: FunctionExpression::class
    deprecated: false
    alternative: null

```

### 3. Usage

There exist two attributes `AsTwigFilter` and `AsTwigFunction`, which can be used with classes or methods.

When you use attributes with a class, it means that all public class methods are automatically transformed into filters or functions.

For example, it creates two function: `math_sum`, `math_min`.

If a global prefix like `app_` is set, then it will create: `app_math_sum`,`app_math_min`.

The global parameter `to_snake_case` is set to `true`, which means method names are converted to `snake_case`. You can disable this feature.

```php
<?php declare(strict_types=1);

namespace App\Application\Controller;

use Danilovl\RenderServiceTwigExtensionBundle\Attribute\AsTwigFunction;

#[AsTwigFunction('math_')]
class CountService
{
    public function sum(int $one, int $two): int
    {
        return $one + $two;
    }

    public function min(int $one, int $two): int
    {
        return $one - $two;
    }

    public function multiplicationOperation(int $one, int $two): int
    {
        return $one * $two;
    }
}
```

```twig
{{ app_math_sum(2,3) }}
{{ app_math_min(2,3) }}
{{ app_math_multiplication_operation(2,3) }}
```  

You can use the attribute separately with a specific method.

```php
<?php declare(strict_types=1);

namespace App\Application\Controller;

use Danilovl\RenderServiceTwigExtensionBundle\Attribute\{
    AsTwigFilter,
    AsTwigFunction
};

class RenderServiceController
{
    #[AsTwigFunction('function_sum')]
    public function twigFunctionSum(int $one, int $two): int
    {
        return $one + $two;
    }

    #[AsTwigFilter('filter_upper')]
    public function twigFilterUpper(string $text): string
    {
        return strtoupper($text);
    }
}
```  

```twig
{{ 'text' | app_filter_upper }}
{{ app_function_sum(2,3) }}
```

## License

The RenderServiceTwigExtensionBundle is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
