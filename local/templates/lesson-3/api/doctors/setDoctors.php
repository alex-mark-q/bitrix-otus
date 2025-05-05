<?php

    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    // file_put_contents(
    //     $_SERVER["DOCUMENT_ROOT"] . "/upload/sync_logs/class - " . date("Y-m-d") . ".json",
    //     "Hello" . PHP_EOL, 
    //     FILE_APPEND
    // ); 
    // подключаем нужный класс для работы с инфоблоком
    CModule::IncludeModule('iblock');

    // use Bitrix\Iblock\IblockTable;

    // $iblock = IblockTable::getList([
    //     'filter' => [
    //         'CODE' => 'doctors', // или 'NAME' => 'Врачи'
    //     ],
    //     'select' => ['ID'],
    // ])->fetch();

    // if ($iblock) {
    //     echo "ID инфоблока: " . $iblock['ID'];
    // } else {
    //     echo "Инфоблок не найден";
    // }

    $specialty = trim(preg_replace('/\s+/', ' ', $_GET['specialty']));
    $name = trim(preg_replace('/\s+/', ' ', $_GET['name']));

    $result = [
        'success' => false,
        'message' => 'Empty'
    ];
    try {
        // обязательно указываем класс
        $el = new CIBlockElement;
        // здесь у нас будут храниться свойства
        $PROP = array();
        // свойство типа чекс бокс
        $PROP[66] = $specialty;
        // массив данных для добавления элемента
        $arLoadDoctorsArray = array(
            // обязательно нужно указать дату начала активности элемента
            "ACTIVE_FROM" => date('d.m.Y H:i:s'),
            // указываем какой пользователь добавил элемент
            "MODIFIED_BY" => $USER->GetID(),
            // id инфоблока куда будем добавлять новый элемент
            "IBLOCK_ID" => 16,
            // название элемента
            "NAME" => $name,
            // превью описание элемента
            // "PREVIEW_TEXT" => "Превью описание элемента",
            // Детальное описание элемента
            // "DETAIL_TEXT" => "Детальный текст",
            // активен (Y), или не активен (N) 
            "ACTIVE" => "Y",
            // добавляем символьный код
            // "CODE" => "test",
            // сортировка
            "SORT" => 100,
            "PROPERTY_VALUES" => $PROP,
        );
        if ($newElement = $el->Add($arLoadDoctorsArray)) {
            $result['success'] = true;
            $result['message'] = "ID нового элемента: " . $newElement;
        } else {
            // echo "Error: " . $el->LAST_ERROR;
            $result['success'] = false;
            $result['message'] = "Error: " . $el->LAST_ERROR;
        }
        // file_put_contents(
        //     $_SERVER["DOCUMENT_ROOT"] . "/upload/sync_logs/class - " . date("Y-m-d") . ".json",
        //     print_r($result, true), 
        //     FILE_APPEND
        // ); 
    } catch(Exception $e) {
        $result['success'] = false;
        $result['message'] = "Error: " . $e;
    }

    // header('Location: https://cp36741.tw1.ru/local/templates/lesson-3/index.php?message=' . urlencode($result['message']));
    // exit;

    echo json_encode($result);

    