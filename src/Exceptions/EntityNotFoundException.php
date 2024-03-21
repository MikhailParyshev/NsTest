<?php

namespace NsTest\Exceptions;

class EntityNotFoundException extends \Exception
{
    public function __construct(string $entityName, int $entityId)
    {
        parent::__construct('Entity not found: ' . $entityName . ', id: ' . $entityId);
    }
}