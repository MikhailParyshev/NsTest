<?php

namespace NsTest\Service;

use NsTest\Enums\OperationStatus;

class Differences
{
    public function __construct(
        public readonly OperationStatus $from,
        public readonly OperationStatus $to,
    ) {}
}