<?php

namespace NsTest\Enums;

enum NotificationEventStatus: string
{
    case ChangeReturn = 'changeReturnStatus';
    case NewReturn = 'newReturnStatus';
}