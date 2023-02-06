<?php
namespace WebSupply\RouteAnnotation\Tests\Functional\Fixtures\Controller;

use Neos\Flow\Mvc\Controller\ActionController;
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
