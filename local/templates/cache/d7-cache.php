<?php

    require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php";

    $cacheTime = 30*60; // время кеширования, указывается в секундах
    $cacheId = $_REQUEST['CACHE_ID']; // формируем идентификатор кеша в зависимости от параметров
    $cacheDir = '/'; // директория кеша

    $cache = Bitrix\Main\Data\Cache::createInstance();
    if ($cache->initCache($cacheTime, $cacheId, $cacheDir)) {
        $result = $cache->getVars();
        var_dump($result);
    }
    elseif ($cache->startDataCache()) {
        $result = [
            'Kurt Cobain',
            'Krist Novoselic',
            'Dave Grohl'
        ];
        $cache->endDataCache($result);
    }
    // var_dump($result);
    // $cache->clean($cacheId); //сброс