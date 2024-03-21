<?php

namespace NsTest\Service;

use NsTest\Service\TsReturnDto;

class TsReturnDtoFactory
{
    public static function create(array $data): TsReturnDto
    {
//        Валидируем схему данных (наличие параметра и соответсвие типу) из запроса самостоятельно либо с использованием сторонней библиотеки.
//        При отсутствии параметра, выбрасываем исключение MissingRequiredParameterException
//        При неправильном типе параметра, выбрасываем исключение ParameterTypeErrorException
//        Возвращаем валидный Dto
    }
}