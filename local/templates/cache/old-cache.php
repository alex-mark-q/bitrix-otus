<?php

    require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php";

    $cacheTime = 30*60; // время кеширования, указывается в секундах
    $cacheId = $_REQUEST['CACHE_ID']; // формируем идентификатор кеша в зависимости от параметров
    $cacheDir = '/'; // директория кеша
    // создаем объект
    $obCache = new CPHPCache; // если кеш есть и он ещё не истек, то
    if($obCache->InitCache($cacheTime, $cacheId, $cacheDir)) {
        // получаем закешированные переменные
        $arResult = $obCache->GetVars();
    } else // иначе обращаемся к базе
    {
        $arResult = [
            'REAL_MADRID' => ['Toni Kroos', 'Lika Modric', 'Federico Valverde22222'],
            'FC_BAYERN' => ['Thomas Müller', 'Leon Goretzka', 'Joshua Kimmich']
        ];
    }
    // начинаем буферизирование вывода
    if($obCache->StartDataCache()) {
        var_dump($arResult);
        // записываем данные в файл кеша
        $obCache->EndDataCache(['RESULT' => $arResult]);
    }
    $obCache->CleanDir(); //сброс