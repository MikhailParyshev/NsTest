<?php

namespace NsTest\Service;

class TsReturnOperationResponse
{
    public NotificationResult $notificationEmployeeByEmail;
    public NotificationResult $notificationClientByEmail;
    public NotificationResult $notificationClientBySms;

    public function __construct()
    {
        $this->notificationEmployeeByEmail = new NotificationResult(null);
        $this->notificationClientByEmail = new NotificationResult(null);
        $this->notificationClientBySms = new NotificationResult(null);
    }
}