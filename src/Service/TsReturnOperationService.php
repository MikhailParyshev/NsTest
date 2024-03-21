<?php

namespace NsTest\Service;

use Exception;
use NsTest\Enums\Event;
use NsTest\Enums\NotificationEventStatus;
use NsTest\Enums\NotificationType;
use NsTest\Helpers\ConfigHelper;

class TsReturnOperationService
{
    private TsReturnOperationResponse $response;

    public function __construct(
        private readonly EmailNotificationManager $emailNotificationManager,
        private readonly SmsNotificationManager   $smsNotificationManager,
    ) {
        $this->response = new TsReturnOperationResponse();
    }

    /**
     * @throws Exception
     */
    public function notify(TsReturnOperationModel $model): TsReturnOperationResponse
    {
        $emailsTo = ConfigHelper::getEmployeeEmailsByReseller($model->reseller->id, Event::TsGoodsReturn);

        $emailFrom = $model->reseller?->email;

        $templateData = $this->formatData($model);

        if (isset($emailFrom) && count($emailsTo) > 0)
            $this->response->notificationEmployeeByEmail = $this->emailNotificationManager->sendToEmployees(
                $emailFrom,
                $emailsTo,
                $templateData,
                $model,
            );

        if ($model->notificationType !== NotificationType::Change)
            return $this->response;

        if (isset($emailFrom) && isset($model->client->email))
            $this->response->notificationClientByEmail = $this->emailNotificationManager->sendToClient(
                $emailFrom,
                $model->client->email,
                $templateData,
                $model,
            );

        if (isset($model->client->mobile))
            $this->response->notificationClientBySms = $this->smsNotificationManager->send(
                $model->client->mobile,
                $templateData,
                NotificationEventStatus::ChangeReturn,
                $model,
            );

        return $this->response;
    }

    private function formatData(TsReturnOperationModel $model): array
    {
        return [
            'COMPLAINT_ID'       => $model->complaintId,
            'COMPLAINT_NUMBER'   => $model->complaintNumber,
            'CREATOR_ID'         => $model->creator->id,
            'CREATOR_NAME'       => $model->creator->getFullName(),
            'EXPERT_ID'          => $model->expert->id,
            'EXPERT_NAME'        => $model->expert->getFullName(),
            'CLIENT_ID'          => $model->client->id,
            'CLIENT_NAME'        => $model->client->name,
            'CONSUMPTION_ID'     => $model->consumptionId,
            'CONSUMPTION_NUMBER' => $model->consumptionNumber,
            'AGREEMENT_NUMBER'   => $model->agreementNumber,
            'DATE'               => $model->date,
            'DIFFERENCES'        => $this->formatDifferences($model),
        ];
    }

    private function formatDifferences(TsReturnOperationModel $model): string
    {
        if ($model->notificationType === NotificationType::New)
            return __('NewPositionAdded', null, $model->reseller->id);

        return __(
            'PositionStatusHasChanged',
            [
                'FROM' => $model->differences->from->name,
                'TO'   => $model->differences->to->name,
            ],
            $model->reseller->id
        );
    }
}