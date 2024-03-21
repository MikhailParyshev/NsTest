<?php

namespace NsTest\Helpers;

use NsTest\Enums\Event;

class ConfigHelper
{
    public static function getEmployeeEmailsByReseller(int $id, Event $event): array
    {
        return ['someemeil@example.com', 'someemeil2@example.com'];
    }
}