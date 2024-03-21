<?php

namespace NsTest\Exceptions;

class ParameterTypeErrorException extends \Exception
{
    public function __construct(string $parameterName, string $expectedType)
    {
        parent::__construct(
            sprintf(
                'Wrong parameter value type: %s, expected: \'%s\'',
                $parameterName,
                $expectedType,
            )
        );
    }
}