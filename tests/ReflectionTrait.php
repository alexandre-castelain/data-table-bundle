<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests;

trait ReflectionTrait
{
    private function getPrivatePropertyValue(object $object, string $property): mixed
    {
        return (new \ReflectionProperty($object, $property))->getValue($object);
    }

    private function setPrivatePropertyValue(object $object, string $property, mixed $value): void
    {
        (new \ReflectionProperty($object, $property))->setValue($object, $value);
    }
}
