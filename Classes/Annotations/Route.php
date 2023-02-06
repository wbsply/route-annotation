<?php

namespace WebSupply\RouteAnnotation\Annotations;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
* @Annotation
* @NamedArgumentConstructor
*/
#[\Attribute(\Attribute::TARGET_METHOD|\Attribute::TARGET_CLASS)]
final class Route
{
    public function __construct(
        public readonly string $path,
        public readonly null|string|array $method = null,
        public readonly string $format = 'html',
        public readonly ?bool $appendExceedingArguments = null
    ) {}
}
