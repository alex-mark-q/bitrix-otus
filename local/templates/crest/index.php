<?php
    // echo "hh";
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
    // echo $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/src/Otus/CRestService/CRest.php';
    // require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/src/Otus/CRestService/CRest.php';
    use Otus\CRestService\CRest;
    use Bitrix\Main\Engine\CurrentUser;

    $userId = CurrentUser::get()->getId();
    $result = CRest::call('timeman.status', ['USER_ID' => $userId]);

    // $result = CRest::call(
    //     'timeman.open',
    //     [
    //         'USER_ID' => 1,
    //         'TIME' => '2025-03-27T08:00:01+00:00',
    //         'REPORT' => 'Забыл открыть рабочий день',
    //         'LAT' => 53.548841,
    //         'LON' => 9.987274
    //     ]
    // );

    // Обработка ответа от Битрикс24
    if ($result['error']) {
        echo 'Error: '.$result['error_description'];
    } else {
        print_r($result['result']);
    }

