# Annotations based routing for Neos Flow

Move your routing configuration into the controller with the a `Route` annotation

## Install

`composer require websupply/route-annotation`

## Example
Configuring a controller as follows
```php
use WebSupply\RouteAnnotation\Annotations as WebSupply;

#[WebSupply\Route(path: 'class-annotation')]
class RouteAnnotatedController extends ActionController
{
    #[WebSupply\Route("annotated/with/path")]
    public function annotatedWithPathAction(): string
    {
        return 'Hello';
    }

    #[WebSupply\Route("annotated/with/argument/{name}")]
    public function annotatedUriWithArgumentAction(string $name):string
    {
        return 'Hello ' . $name;
    }
}
```

Gives you the following routes
```
$ ./flow routing:list
Currently registered routes:
+---+-------------------------------------------------+----------------+-----------------------------------------------------------------------------------------------------------------+
| # | Uri Pattern                                     | HTTP Method(s) | Name                                                                                                            |
+---+-------------------------------------------------+----------------+-----------------------------------------------------------------------------------------------------------------+
| 1 | class-annotation/annotated/with/path            | any            | Annotated Route (WebSupply\RouteAnnotation\Controller\RouteAnnotatedController->annotatedWithPathAction)        |
| 2 | class-annotation/annotated/with/argument/{name} | any            | Annotated Route (WebSupply\RouteAnnotation\Controller\RouteAnnotatedController->annotatedUriWithArgumentAction) |
+---+-------------------------------------------------+----------------+-----------------------------------------------------------------------------------------------------------------+
```

## Annotation properties

The `Route` annotation can take following properties (known from the `Routes.yaml` configuration)

```php
        string $path,
        null|string|array $method = null,
        string $format = 'html',
        ?bool $appendExceedingArguments = null
```

It respects the core routers handling of these configuration, as this is merely a different way of writing the configuration itself.

## Support and sponsoring
Work on this package is supported by the danish web company **WebSupply ApS** 
