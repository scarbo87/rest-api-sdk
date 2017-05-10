<?php

namespace scarbo87\RestApiSdk\Domain\Exception;

class InvalidEnumNameException extends \InvalidArgumentException
{
    /**
     * @param string $name
     * @param string $enumClass
     *
     * @return static
     */
    public static function create($name, $enumClass)
    {
        return new static(sprintf('Name "%s" does not exist in %s', $name, $enumClass));
    }
}
