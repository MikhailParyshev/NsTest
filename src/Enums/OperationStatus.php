<?php

namespace NsTest\Enums;

enum OperationStatus: int
{
    case Completed = 0;
    case Pending = 1;
    case Rejected = 2;
}
