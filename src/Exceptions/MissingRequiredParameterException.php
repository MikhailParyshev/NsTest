<?php

namespace NsTest\Exceptions;

class MissingRequiredParameterException extends \Exception
{
    public function __construct(string $parameterName)
    {
        parent::__construct('Required parameter not found: ' . $parameterName);
    }
}