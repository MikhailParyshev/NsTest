<?php

namespace NsTest\Service;

use NsTest\EmailClient;

class EmailNotificationManager
{
    public function sendToEmployees(
        string $emailFrom,
        array $emailsTo,
        array $templateData,
        TsReturnOperationModel $model,
    ): NotificationResult
    {
        try {
            foreach ($emailsTo as $emailTo) {
                EmailClient::sendMessage(
                    $emailFrom,
                    $emailTo,
                    __('complaintEmployeeEmailSubject', $templateData, $model->reseller->id),
                    __('complaintEmployeeEmailBody', $templateData, $model->reseller->id),
                );
                // Остальной код, для которого нам нужны были данные из модели
            }
            return new NotificationResult(null, true);
        } catch (\Throwable $e) {
            return new NotificationResult($e->getMessage());
        }
    }

    public function sendToClient(
        string $emailFrom,
        string $emailTo,
        array $templateData,
        TsReturnOperationModel $model,
    ): NotificationResult
    {
        try {
            EmailClient::sendMessage(
                $emailFrom,
                $emailTo,
                __('complaintClientEmailSubject', $templateData, $model->reseller->id),
                __('complaintClientEmailBody', $templateData, $model->reseller->id),
            );
            // Остальной код, для которого нам нужны были остальные данные из модели
            return new NotificationResult(null, true);
        } catch (\Throwable $e) {
            return new NotificationResult($e->getMessage());
        }
    }
}