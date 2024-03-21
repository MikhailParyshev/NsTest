<?php

namespace NsTest\Service;

use DateTime;
use NsTest\Entities\Client;
use NsTest\Entities\Deal;
use NsTest\Entities\Employee;
use NsTest\Entities\Seller;
use NsTest\Enums\NotificationType;
use NsTest\Enums\OperationStatus;
use NsTest\Exceptions\EntityNotFoundException;
use NsTest\Exceptions\PropertyValueNotFoundException;

class TsReturnOperationModelFactory
{
    static function create(TsReturnDto $dto): TsReturnOperationModel
    {
        $deal = Deal::getByIdAndMembers($dto->dealId, $dto->clientId, $dto->resellerId)
            ?? throw new EntityNotFoundException(Deal::class, $dto->dealId);

        list($reseller, $client, $creator, $expert) = static::uploadEntitiesOrFail([
            [$dto->resellerId, Seller::class],
            [$dto->clientId, Client::class],
            [$dto->creatorId, Employee::class],
            [$dto->expertId, Employee::class],
        ]);

        $notificationType = NotificationType::tryFrom($dto->notificationType)
            ?? throw new PropertyValueNotFoundException(NotificationType::class, $dto->notificationType);

        if ($notificationType === NotificationType::Change) {
            $from = OperationStatus::tryFrom($dto->differences['from'])
                ?? throw new PropertyValueNotFoundException('StatusChangeFrom', $dto->differences['from']);
            $to = OperationStatus::tryFrom($dto->differences['to'])
                ?? throw new PropertyValueNotFoundException('StatusChangeTo', $dto->differences['to']);
            $differences = new Differences($from, $to);
        }

        $date = DateTime::createFromFormat(DATE_ATOM, $dto->date);

        return new TsReturnOperationModel(
            deal: $deal,
            reseller: $reseller,
            creator: $creator,
            client: $client,
            expert: $expert,
            notificationType: $notificationType,
            differences: $differences ?? null,
            complaintId: $dto->complaintId,
            complaintNumber: $dto->complaintNumber,
            consumptionId: $dto->consumptionId,
            consumptionNumber: $dto->consumptionNumber,
            agreementNumber: $dto->agreementNumber,
            date: $date,
        );
    }

    private static function uploadEntitiesOrFail(array $entities): array
    {
        return array_reduce(
            $entities,
            function ($carry, $item) {
                $entityId = $item[0];
                $className = $item[1];
                $carry[] = $className::getById($entityId) ?? throw new EntityNotFoundException($className, $entityId);
                return $carry;
            },
            []
        );
    }
}