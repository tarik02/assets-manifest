<?php

namespace Tarik02\AssetsManifest\Traits;

use LogicException;

trait ImmutableArrayAccess
{
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $class = \get_class($this);
        throw new LogicException("Instance of type {$class} is not mutable");
    }

    public function offsetUnset(mixed $offset): void
    {
        $class = \get_class($this);
        throw new LogicException("Instance of type {$class} is not mutable");
    }
}
