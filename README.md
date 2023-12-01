[![phpunit](https://github.com/danilovl/render-service-twig-extension-bundle/actions/workflows/phpunit.yml/badge.svg)](https://github.com/danilovl/render-service-twig-extension-bundle/actions/workflows/phpunit.yml)
[![downloads](https://img.shields.io/packagist/dt/danilovl/render-service-twig-extension-bundle)](https://packagist.org/packages/danilovl/render-service-twig-extension-bundle)
[![latest Stable Version](https://img.shields.io/packagist/v/danilovl/render-service-twig-extension-bundle)](https://packagist.org/packages/danilovl/render-service-twig-extension-bundle)
[![license](https://img.shields.io/packagist/l/danilovl/render-service-twig-extension-bundle)](https://packagist.org/packages/danilovl/render-service-twig-extension-bundle)

# RenderServiceTwigExtensionBundle #

## About ##

Symfony twig extension bundle provides rendering service method.

The main task this extension is to replace the existing twig method to render controller method `render(controller())`. 

The main disadvantage of the existing method is that it does sub request, which increases total query execution time.

## Comparison performance metrics ##

In this example, the same page was used with the only difference in the number of uses of the standard `render(controller())` and new `render_service` twig function.

On the left side you can see the use of the standard twig function, on the right side is used the new twig function.

Metrics were taken from debug panel. The purpose of these metrics is to show that using a new `render_service` 
twig function reduces total query execution time, makes your application faster. No more sub requests.

Render three times.

![Alt text](/pic/example-1.png?raw=true "First example")

Render ten times.

![Alt text](/pic/example-2.png?raw=true "Second example")

### Requirements 

  * PHP 8.3.0 or higher
  * Symfony 6.3 or higher

### 1. Installation

Install `danilovl/render-service-twig-extension-bundle` package by Composer:
 
``` bash
$ composer require danilovl/render-service-twig-extension-bundle
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
 
For example you have some `UserController` with `detail` method, which you want to render in twig template.
 
```php
<?php declare(strict_types=1);

namespace App\Controller;

// ...

class UserController extends AbstractController
{
    public function detail(
        User $user,
        EventDispatcherInterface $eventDispatcher,
        string $defaultLanguage = null
    ): Response {
       
        //some code

        return $this->render('user_information.html.twig', [
            'user' => $user
        ]);
    }   

    public function detailString(): string 
    {
        //some code

        return 'string';
    }
}
```

If you're using the default `services.yaml` configuration, your controllers are already registered as services.

If not, then you need to register controller as service. One specific controller as service.

```yaml
# config/services.yaml

...
services:
  app.controller.user:
    class: App\Controller\UserController
    public: true
    tags: ['controller.service_arguments']
    calls:
      - [setContainer, ["@service_container"]]
```

All controllers in folder, then default service name will be like `App\Controller\UserController`.

```yaml
# config/services.yaml

...
services:
  App\Controller\:
    resource: '../src/Controller'
    public: true
    tags: ['controller.service_arguments']
    calls:
      - [setContainer, ["@service_container"]]
```
 
### 3. Description of parameters
       	
First parameter is name of service.(string)

Second parameter is method name of service.(string)

Third parameter is list of parameters usage in method of service.(array)

Fourth parameter is list of service names.(array)

### 4. Usage 

For example, method `detail(User $user, EventDispatcherInterface $eventDispatcher, string $defaultLanguage = null)` uses dynamic variable `$user`, 
service `$eventDispatcher` and default variable `$defaultLanguage`.

In first parameter you define `app.controller.user` or `App\\Controller\\UserController`. 

In second parameter you define `detail`.

In third parameter you define array with key `user` with value object `user`.

Because method `detail` uses additional service `EventDispatcherInterface`, in the fourth parameter 
you defined array with name of service, which must be available in the service container. 
For example allies for `EventDispatcherInterface` is `event_dispatcher`.
 
The name of the variables should be equivalent to the names in the service method without `$`.
  
```twig
{# templates/example.html.twig #}

{{ render_service('app.controller.user', 'detail', 
    {'user': user}, 
    {'eventDispatcher': 'event_dispatcher'}
) }}
 
{{ render_service('App\\Controller\\UserController', 'detail', 
    {'user': user}, 
    {'eventDispatcher': 'event_dispatcher'}
) }}
```

If you want to replace default variable `$defaultLanguage`.

```twig
{# templates/example.html.twig #}

{{ render_service('app.controller.user', 'detail', 
    {'user': user, 'defaultLanguage': app.request.locale}, 
    {'eventDispatcher': 'event_dispatcher'}
) }}
```

If you want manually inject `EventDispatcherInterface` service. 

```twig
{# templates/example.html.twig #}

{{ render_service('app.controller.user', 'detail', {
    'user': user, 
    'eventDispatcher': eventDispatcher,
    'defaultLanguage': 'en'
}) }}
```

## License

The RenderServiceTwigExtensionBundle is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).