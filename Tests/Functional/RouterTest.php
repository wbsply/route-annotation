<?php

namespace WebSupply\RouteAnnotation\Tests\Functional;

use GuzzleHttp\Psr7\Uri;
use Neos\Flow\Mvc\Routing\Dto\ResolveContext;
use Neos\Flow\Mvc\Routing\Dto\RouteParameters;
use Neos\Flow\Mvc\Routing\Route;
use Neos\Flow\Mvc\Routing\RouterInterface;
use Neos\Flow\Tests\FunctionalTestCase;
use Psr\Http\Message\ServerRequestFactoryInterface;

class RouterTest extends FunctionalTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->router = $this->objectManager->get(RouterInterface::class);

        /*\Neos\Flow\var_dump(
            array_map(fn(Route $route) => \Neos\Flow\var_dump($route->getDefaults()),
            $this->router->getRoutes())
        );
        die;*/
    }

    /**
     * @test
     */
    public function resolveUriPatternFromRouteAnnotation()
    {
        $routeValues = [
            '@package' => 'WebSupply.RouteAnnotation',
            '@subpackage' => 'Tests\Functional\Fixtures',
            '@controller' => 'RouteAnnotated',
            '@action' => 'annotatedWithPath',
            '@format' => 'html'
        ];
        $baseUri = new Uri('http://localhost');
        $actualResult = $this->router->resolve(new ResolveContext($baseUri, $routeValues, false, 'index.php/', RouteParameters::createEmpty()));

        self::assertSame('/index.php/class-annotation/annotated/with/path', (string)$actualResult);
    }


    /**
     * @test
     */
    public function resolveUriPatternFromRouteAnnotationWithArgument()
    {
        $routeValues = [
            '@package' => 'WebSupply.RouteAnnotation',
            '@subpackage' => 'Tests\Functional\Fixtures',
            '@controller' => 'RouteAnnotated',
            '@action' => 'annotatedUriWithArgument',
            '@format' => 'html',
            'name' => 'soren'
        ];
        $baseUri = new Uri('http://localhost');
        $actualResult = $this->router->resolve(new ResolveContext($baseUri, $routeValues, false, 'index.php/', RouteParameters::createEmpty()));

        self::assertSame('/index.php/class-annotation/annotated/with/argument/soren', (string)$actualResult);
    }

}
