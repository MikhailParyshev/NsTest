<?php

namespace NsTest\Service;

use NsTest\Enums\NotificationEventStatus;
use NsTest\SmsClient;

class SmsNotificationManager
{
    public function send(
        string $phone,
        array $templateData,
        NotificationEventStatus $status,
        TsReturnOperationModel $model,
    ): NotificationResult
    {
        try {
            SmsClient::send(
                $phone,
                $templateData,
                $status,
            );
            // Остальной код, для которого нам нужны были данные из модели
            return new NotificationResult(null, true);
        } catch (\Throwable $e) {
            return new NotificationResult($e->getMessage());
        }
    }
}