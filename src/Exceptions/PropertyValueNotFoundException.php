<?php

namespace NsTest\Exceptions;

class PropertyValueNotFoundException extends \Exception
{
    public function __construct(string $propertyName, int $propertyValue)
    {
        parent::__construct(sprintf('Property value not found: %s, value: \'%d\'', $propertyName, $propertyValue));
    }
}