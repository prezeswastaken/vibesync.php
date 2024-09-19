<?php

declare(strict_types=1);

namespace App\DTOs;

abstract class DTO
{
    public function __get($name)
    {
        $method = 'get'.ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new \Exception("Property {$name} does not exist.");
    }
}
