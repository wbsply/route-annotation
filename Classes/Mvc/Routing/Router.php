<?php

namespace WebSupply\RouteAnnotation\Mvc\Routing;

use Neos\Flow\Mvc\Controller\ActionController;
use WebSupply\RouteAnnotation\Annotations\Route;
use Doctrine\Common\Annotations\AnnotationException;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Configuration\ConfigurationManager;
use Neos\Flow\ObjectManagement\Exception\UnknownObjectException;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Neos\Flow\Reflection\ReflectionService;

final class Router extends \Neos\Flow\Mvc\Routing\Router
{

    #[Flow\Inject]
    protected ObjectManagerInterface $objectManager;

    public function initializeAnnotatedRoutes(): array
    {
        return static::resolveRouteAnnotatedMethods($this->objectManager);
    }

    /**
     * @param ObjectManagerInterface $objectManager
     * @Flow\CompileStatic
     * @return array
     * @throws AnnotationException if a none-Action method is annotated
     */
    protected static function resolveRouteAnnotatedMethods(ObjectManagerInterface $objectManager): array
    {
        $annotatedRoutes = [];
        $reflectionService = $objectManager->get(ReflectionService::class);
        $controllerClassNames = $reflectionService->getAllSubClassNamesForClass(ActionController::class);
        foreach ($controllerClassNames as $controllerClassName) {
            if ($reflectionService->isClassAbstract($controllerClassName)) {
                continue;
            }

            /** @var Route $classAnnotation */
            $classAnnotation = $reflectionService->getClassAnnotation($controllerClassName, Route::class);

            $methodNames = $reflectionService->getMethodsAnnotatedWith($controllerClassName, Route::class);
            foreach ($methodNames as $methodName) {
                if (preg_match('/.*Action$/', $methodName) === 0 || $reflectionService->isMethodPublic($controllerClassName, $methodName) === false) {
                    throw new AnnotationException(sprintf('The method %s->%s is annotated with @Dafis\Route. The Route annotation is only meant for *Action methods in your controller', $controllerClassName, $methodName));
                }
                $controllerObjectName = $objectManager->getCaseSensitiveObjectName($controllerClassName);

                if ($controllerObjectName === null) {
                    throw new UnknownObjectException(sprintf('The object "%s" is not registered.', $controllerClassName), 1618384920);
                }

                $controllerPackageKey = $objectManager->getPackageKeyByObjectName($controllerObjectName);

                $matches = [];
                $subject = substr($controllerObjectName, strlen($controllerPackageKey) + 1);

                preg_match(
                    '/
                        ^(
                            Controller
                        |
                            (?P<subpackageKey>.+)\\\\Controller
                        )
                        \\\\(?P<controllerName>[a-z\\\\]+)Controller
                        $
                    /ix',
                    $subject,
                    $matches
                );

                $controllerSubpackageKey = $matches['subpackageKey'] ?? null;
                $controllerName = $matches['controllerName'];

                /** @var Route $methodAnnotation */
                $methodAnnotation = $reflectionService->getMethodAnnotation($controllerClassName, $methodName, Route::class);

                $defaults = [
                    '@package' => $controllerPackageKey,
                    '@subpackage' => $controllerSubpackageKey,
                    '@controller' => $controllerName,
                    '@action' => substr($methodName, 0, -6),
                    '@format' => $methodAnnotation->format
                ];

                $routeConfiguration = [
                    'name' => $methodAnnotation->name ?? sprintf('Annotated Route (%s->%s)', $controllerClassName, $methodName),
                    'uriPattern' => $classAnnotation ? implode('/', [$classAnnotation->path, $methodAnnotation->path]) : $methodAnnotation->path,
                    'defaults' => $defaults
                ];

                if ($methodAnnotation->appendExceedingArguments !== null) {
                    $routeConfiguration['appendExceedingArguments'] = $methodAnnotation->appendExceedingArguments;
                } elseif ($classAnnotation && $classAnnotation->appendExceedingArguments !== null) {
                    $routeConfiguration['appendExceedingArguments'] = $classAnnotation->appendExceedingArguments;
                }

                if ($methodAnnotation->method !== null) {
                    $routeConfiguration['httpMethods'] = is_array($methodAnnotation->method) ? $methodAnnotation->method : [$methodAnnotation->method];
                } elseif ($classAnnotation && $classAnnotation->method !== null) {
                    $routeConfiguration['httpMethods'] = is_array($classAnnotation->method) ? $classAnnotation->method : [$classAnnotation->method];
                }

                $annotatedRoutes[] = $routeConfiguration;
            }
        }
        return $annotatedRoutes;
    }

    protected function initializeRoutesConfiguration()
    {
        if ($this->routesConfiguration === null) {
            $annotatedRoutes = $this->initializeAnnotatedRoutes();
            $configuredRoutes = $this->configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_ROUTES);
            $this->routesConfiguration = array_merge(array_values($annotatedRoutes), $configuredRoutes);
        }
    }


}
