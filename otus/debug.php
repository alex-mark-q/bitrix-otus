<?php

use Bitrix\Main\Diag\Debug as DebugAlias;

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php';
/*
 * @var Cmain  $APPLICATION
 */

$APPLICATION -> setTitle("Отладка и логирование. ДЗ - 1");
DebugAlias::writeToFile(date("d.m.Y H:i:s"),'',"/logs/otus.txt");

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php';