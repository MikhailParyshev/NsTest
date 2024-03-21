<?php

namespace NsTest;

use NsTest\Enums\NotificationEventStatus;

class SmsClient
{
    public static function send(
        string $phone,
        array $templateData,
        NotificationEventStatus $status,
    )
    {
//        Отправляем sms-уведомление
    }
}