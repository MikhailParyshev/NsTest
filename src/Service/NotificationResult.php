<?php

namespace NsTest\Service;

class NotificationResult
{
    public function __construct(
        public ?string $error,
        public bool $isSent = false,
    ) { }
}