<?php

require_once __DIR__ . '/src/Otus/EventsHandler/IblockHandler.php';
// pr(__DIR__ . '/src/Otus/EventsHandler/IblockHandler.php');
use Bitrix\Main\EventManager;
$eventManager = EventManager::getInstance();

// пользовательский тип для свойства инфоблока
$eventManager->addEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    [
        'Otus\UserTypes\OnlineRecord', // класс обработчик пользовательского типа свойства 
        'GetUserTypeDescription'
    ]
);

// пользовательский тип для UF поля
$eventManager->addEventHandler(
    'main',
    'OnUserTypeBuildList',
    [
        'Otus\UserTypes\FormatOnlineLink', // класс обработчик пользовательского типа UF поля
        'GetUserTypeDescription'
    ]
);

// события для инфоблока
$eventManager->addEventHandler(
    'iblock', 
    'OnAfterIBlockElementAdd', 
    [
        'Otus\EventsHandler\IblockHandler',
        'onAfterIBlockAddHandler',
    ]
);

$eventManager->addEventHandler(
    'iblock', 
    'OnAfterIBlockElementUpdate', 
    [
        '\Otus\EventsHandler\IblockHandler',
        'onAfterIBlockUpdateHandler',
    ]
);

$eventManager->addEventHandler(
    'iblock', 
    'OnAfterIBlockElementDelete', 
    [
        '\Otus\EventsHandler\IblockHandler',
        'onAfterIBlockDeleteHandler',
    ]
);
// события для инфоблока --end

// события для сделок

$eventManager->addEventHandler(
    'crm', 
    'OnAfterCrmDealAdd', 
    [
        'Otus\EventsHandler\IblockHandler',
        'onAfterCrmDealAddHandler',
    ]
);

$eventManager->addEventHandler(
    'crm', 
    'OnAfterCrmDealUpdate', 
    [
        'Otus\EventsHandler\IblockHandler',
        'onAfterCrmDealUpdateHandler',
    ]
);

$eventManager->addEventHandler(
    'crm', 
    'OnBeforeCrmDealDelete', 
    [
        '\Otus\EventsHandler\IblockHandler',
        'onAfterCrmDealDeleteHandler',
    ]
);

// события для сделок --end