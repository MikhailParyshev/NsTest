<?php

namespace NsTest\Entities;

class Deal
{
    public Client $client;
    public Seller $seller;

    public static function getByIdAndMembers(int $dealId, int $clientId, int $resellerId)
    {
        // Находим сделку с указаныыми id и участниками
    }
}