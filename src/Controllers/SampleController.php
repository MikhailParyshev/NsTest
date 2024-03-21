<?php

namespace NsTest\Controllers;

use NsTest\Exceptions\EntityNotFoundException;
use NsTest\Exceptions\MissingRequiredParameterException;
use NsTest\Exceptions\ParameterTypeErrorException;
use NsTest\Exceptions\PropertyValueNotFoundException;
use NsTest\Service\EmailNotificationManager;
use NsTest\Service\SmsNotificationManager;
use NsTest\Service\TsReturnOperationModelFactory;
use NsTest\Service\TsReturnOperationService;
use NsTest\Service\TsReturnDtoFactory;

class SampleController
{
    public function actionIndex($request)
    {
        try {
            $dto = TsReturnDtoFactory::create($request['data']);
        } catch (MissingRequiredParameterException | ParameterTypeErrorException $e) {
//            Возвращаем 400 ответ с описанием ошибки
        }

        try {
            $model = TsReturnOperationModelFactory::create($dto);
        } catch (EntityNotFoundException | PropertyValueNotFoundException $e) {
//            Возвращаем 422 ответ с описанием ошибки
        }

        $service = new TsReturnOperationService(
            new EmailNotificationManager(),
            new SmsNotificationManager(),
        );
        $response = $service->notify($model);
//         Возвращаем 200 ответ с отформатированным результатом запроса
    }
}