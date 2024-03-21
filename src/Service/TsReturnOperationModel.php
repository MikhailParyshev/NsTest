<?php

namespace NsTest\Service;

use NsTest\Entities\Client;
use NsTest\Entities\Deal;
use NsTest\Entities\Employee;
use NsTest\Entities\Seller;
use NsTest\Enums\NotificationType;

class TsReturnOperationModel
{
    public function __construct(
        public readonly Deal $deal,
        public readonly Seller $reseller,
        public readonly Employee $creator,
        public readonly Client $client,
        public readonly Employee $expert,
        public readonly NotificationType $notificationType,
        public readonly ?Differences $differences,

        public readonly int $complaintId,
        public readonly string $complaintNumber,
        public readonly int $consumptionId,
        public readonly string $consumptionNumber,
        public readonly string $agreementNumber,
        public readonly string $date,
    ) {}
}