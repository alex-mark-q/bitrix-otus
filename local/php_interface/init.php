<?php
use Bitrix\Main\EventManager;
$eventManager = EventManager::getInstance();

// var_dump($_SERVER["DOCUMENT_ROOT"].'/local/php_interface/src/autoloader.php');

if(file_exists($_SERVER["DOCUMENT_ROOT"].'/local/php_interface/src/autoloader.php')) { 
    require_once($_SERVER["DOCUMENT_ROOT"].'/local/php_interface/src/autoloader.php');
}

function pr($var, $type = false) {
    echo '<pre style="font-size:10px; border:1px solid #000; background:#FFF; text-align:left; color:#000;">';
    if ($type)
        var_dump($var);
    else
        print_r($var);
    echo '</pre>';
}

// пользовательский тип для свойства инфоблока
$eventManager->AddEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    [
        'Otus\UserTypes\OnlineRecord', // класс обработчик пользовательского типа свойства 
        'GetUserTypeDescription'
    ]
);

// пользовательский тип для UF поля
$eventManager->AddEventHandler(
    'main',
    'OnUserTypeBuildList',
    [
        'Otus\UserTypes\FormatOnlineLink', // класс обработчик пользовательского типа UF поля
        'GetUserTypeDescription'
    ]
);

\Bitrix\Main\UI\Extension::load([
    'homework.begin_date_button',
]);