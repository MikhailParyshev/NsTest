<?php

namespace NW\WebService\References\Operations\Notification;

class TsReturnOperation extends ReferencesOperation
{
    public const TYPE_NEW    = 1;
    public const TYPE_CHANGE = 2;

    /**
     * @throws \Exception
     */
    public function doOperation(): void // метод возвращает $result, возвращаемое значение не может быть void
    {
//        Мы предполагаем, что $_REQUEST['data'] является массивом. Если это так, приведение не нужно. Если $_REQUEST['data'] это int, float, string, bool или resource, при приведении к массиву мы получим массив с одним значением. Таким образом, приведение к массиву не имеет смысла. Вместо этого необходимо сделать проверку is_array($this->getRequest('data')
        $data = (array)$this->getRequest('data');
//        Если элемент массива 'resellerId' отсутствует, вылетит с ошибкой
        $resellerId = $data['resellerId'];
//        Если элемент массива 'notificationType' отсутствует, вылетит с ошибкой
        $notificationType = (int)$data['notificationType'];
//        При отправке уведомлений по электронной почте также может возникнуть ошибка, но поля для неё не предусмотрено. Добавим при рефакторинге
        $result = [
            'notificationEmployeeByEmail' => false,
            'notificationClientByEmail'   => false,
            'notificationClientBySms'     => [
                'isSent'  => false,
                'message' => '',
            ],
        ];

//        Если элемент массива $data['resellerId'] отсутствует, вылетит с ошибкой выше по коду. Нижестоящее условие сработает только если приведение $resellerId к int даёт 0, а значит ошибка 'Empty resellerId' не соответствует действительности
        if (empty((int)$resellerId)) {
            $result['notificationClientBySms']['message'] = 'Empty resellerId';
            return $result;
        }

//        Мы уже ранее привели $notificationType к int
//        Валидация элемента $data['notificationType'] проведена неверно, аналогично ситуации с $data['resellerId']
        if (empty((int)$notificationType)) {
            throw new \Exception('Empty notificationType', 400);
        }

//        Не проверили, что $resellerId является числом или числовой строкой
        $reseller = Seller::getById((int)$resellerId);
        if ($reseller === null) {
            throw new \Exception('Seller not found!', 400);
        }

//        Не провалидировали $data['clientId'], может вылететь с ошибкой при отсутствии элемента
        $client = Contractor::getById((int)$data['clientId']);
//        Для клиента можем выделить отдельный класс Client, чтобы не проверять $client->type
//        Здесь мы проверяем, что поле клиента Seller соответствует сущности Reseller. Исходя из неполных данных, можно сделать предположение что у нас обрабатывается некая сделка между продавцом и покупателем, в которой также участвуют expert и creator. Схема данных, при которой у класса Client есть поле Seller, имеет смысл только если между покупателем и продавцом не может быть больше одной сделки. В ином случае, нам необходим класс сделки Deal, с которым связаны продавец и покупатель по принципу many to many. При такой схеме данных, необходимость в проверке $client->Seller->id !== $resellerId отпадает
        if ($client === null || $client->type !== Contractor::TYPE_CUSTOMER || $client->Seller->id !== $resellerId) {
            throw new \Exception('сlient not found!', 400);
        }

        $cFullName = $client->getFullName();
//        Условие никогда не сработает, исходя из кода функции getFullName()
        if (empty($client->getFullName())) {
            $cFullName = $client->name;
        }

//        Ошибка валидации, как в вышеописанных случаях
        $cr = Employee::getById((int)$data['creatorId']);
        if ($cr === null) {
            throw new \Exception('Creator not found!', 400);
        }

//        Ошибка валидации, как в вышеописанных случаях
        $et = Employee::getById((int)$data['expertId']);
        if ($et === null) {
            throw new \Exception('Expert not found!', 400);
        }

        $differences = '';
        if ($notificationType === self::TYPE_NEW) {
            $differences = __('NewPositionAdded', null, $resellerId);
        } elseif ($notificationType === self::TYPE_CHANGE && !empty($data['differences'])) {
            $differences = __('PositionStatusHasChanged', [
//                Параметры $data['differences']['from'] и $data['differences']['from'] не прошли валидацию на наличие, тип и соответствие одному из значений Status
                    'FROM' => Status::getName((int)$data['differences']['from']),
                    'TO'   => Status::getName((int)$data['differences']['to']),
                ], $resellerId);
        }

//        Для некоторых нижеперечисленных полей не провалидированы наличие в запросе и тип
        $templateData = [
            'COMPLAINT_ID'       => (int)$data['complaintId'],
            'COMPLAINT_NUMBER'   => (string)$data['complaintNumber'],
            'CREATOR_ID'         => (int)$data['creatorId'],
            'CREATOR_NAME'       => $cr->getFullName(),
            'EXPERT_ID'          => (int)$data['expertId'],
            'EXPERT_NAME'        => $et->getFullName(),
            'CLIENT_ID'          => (int)$data['clientId'],
            'CLIENT_NAME'        => $cFullName,
            'CONSUMPTION_ID'     => (int)$data['consumptionId'],
            'CONSUMPTION_NUMBER' => (string)$data['consumptionNumber'],
            'AGREEMENT_NUMBER'   => (string)$data['agreementNumber'],
            'DATE'               => (string)$data['date'],
            'DIFFERENCES'        => $differences,
        ];

        // Если хоть одна переменная для шаблона не задана, то не отправляем уведомления
        foreach ($templateData as $key => $tempData) {
            if (empty($tempData)) {
                throw new \Exception("Template Data ({$key}) is empty!", 500);
            }
        }

//        Получаем телефон продавца через Reseller->phone
        $emailFrom = getResellerEmailFrom($resellerId);
        // Получаем email сотрудников из настроек
        $emails = getEmailsByPermit($resellerId, 'tsGoodsReturn');
//        !empty($emailFrom) в условии можно заменить на $emailFrom, поскольку функция getResellerEmailFrom() выше либы вернёт значение либо вылетит с ошибкой
        if (!empty($emailFrom) && count($emails) > 0) {
            foreach ($emails as $email) {
//                Создадим отдельный класс для отправки электронной почты, чтобы не передавать ему тип сообщения "EMAIL"
//                Исходя из двух использованных ниже применений метода MessagesClient::sendMessage(), для него не существует сигнатуры, которая подошла бы для обоих наборов параметров
                MessagesClient::sendMessage([
                    0 => [ // MessageTypes::EMAIL
                           'emailFrom' => $emailFrom,
                           'emailTo'   => $email,
                           'subject'   => __('complaintEmployeeEmailSubject', $templateData, $resellerId),
                           'message'   => __('complaintEmployeeEmailBody', $templateData, $resellerId),
                    ],
                ], $resellerId, NotificationEvents::CHANGE_RETURN_STATUS);
                $result['notificationEmployeeByEmail'] = true;

            }
        }

        // Шлём клиентское уведомление, только если произошла смена статуса
        if ($notificationType === self::TYPE_CHANGE && !empty($data['differences']['to'])) {
//            Условие можно заменить на if ($emailFrom && $client->email), поскольку переменные точно определены в коде выше
            if (!empty($emailFrom) && !empty($client->email)) {
                MessagesClient::sendMessage([
                    0 => [ // MessageTypes::EMAIL
                           'emailFrom' => $emailFrom,
                           'emailTo'   => $client->email,
                           'subject'   => __('complaintClientEmailSubject', $templateData, $resellerId),
                           'message'   => __('complaintClientEmailBody', $templateData, $resellerId),
                    ],
//                    Неочивидно, зачем мы отдаём в этот метод именно такой набор параметров помимо необходимых для отправки сообщения. Предположим, что они нужны для логирования. Перенесём эту логику в адаптер
                ], $resellerId, $client->id, NotificationEvents::CHANGE_RETURN_STATUS, (int)$data['differences']['to']);
                $result['notificationClientByEmail'] = true;
            }

//            Условие можно заменить на if ($client->mobile), поскольку переменная точно определена в коде выше
            if (!empty($client->mobile)) {
//                Переменная $error не определена
                $res = NotificationManager::send($resellerId, $client->id, NotificationEvents::CHANGE_RETURN_STATUS, (int)$data['differences']['to'], $templateData, $error);
                if ($res) {
                    $result['notificationClientBySms']['isSent'] = true;
                }
                if (!empty($error)) {
                    $result['notificationClientBySms']['message'] = $error;
                }
            }
        }

//        В одном методе содержится логика валидации, загрузки сущностей и отправки уведомлений
//        Распределим по разным классам логику: валидации схемы и типов данных в запросе, загрузки сущностей из хранилища, отправки уведомлений и формирования ответа.
//        Создадим несколько классов исключений.
//        Вынесем магические константы в перечисления.
//        Создадим классы для объекта ответа и статуса отправки для каждого типа сообщений.
//        См. следующий коммит

        return $result;
    }
}
