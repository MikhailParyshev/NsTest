<?php

namespace NsTest\Service;

class TsReturnDto
{
    public int $dealId;
    public int $resellerId;
    public int $clientId;
    public int $creatorId;
    public int $expertId;
    public int $notificationType;
    public ?array $differences;
    public string $date;
    public int $complaintId;
    public string $complaintNumber;
    public int $consumptionId;
    public string $consumptionNumber;
    public string $agreementNumber;
}