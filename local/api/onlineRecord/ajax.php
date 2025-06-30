<?php

use Bitrix\Crm\Service\Container;
use Bitrix\Main\Application;
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC','Y');
define('NO_AGENT_CHECK', true);
define('PUBLIC_AJAX_MODE', true);
define('DisableEventsCheck', true);
$siteID = isset($_REQUEST['site'])? mb_substr(preg_replace('/[^a-z0-9_]/i', '', $_REQUEST['site']), 0, 2) : '';
if($siteID !== '')
{
    define('SITE_ID', $siteID);
}

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
    die();
}


$request = Application::getInstance()->getContext()->getRequest();
$params = $request->getPostList()->toArray();
$paramsFromQuery = $request->getPostList()->toArray();

// pr($paramsFromQuery);

global $APPLICATION;

Header('Content-Type: application/json; charset='.LANG_CHARSET);
if (check_bitrix_sessid()) {
    $procedureId = $paramsFromQuery['procedure'];
    $elementId = $paramsFromQuery['element_id'];
    $time = $paramsFromQuery['time'];
    $name = $paramsFromQuery['name'];

    //час на процедуру
    $dateFrom = $paramsFromQuery['time'];
    $dateTo = $paramsFromQuery['time'];

    $dateFrom = new DateTime($dateTo);
    $dateFrom->modify('-1 hour');

    $dateTo = new DateTime($dateTo);
    $dateTo->modify('+1 hour');

    $arFilter = [
        'IBLOCK_ID'       => 18,
        'ACTIVE'          => 'Y',
        'PROPERTY_PROTSEDURA'  => $procedureId,
        'PROPERTY_DOCTOR' => $elementId,
        ">=PROPERTY_VREMYA_ZAPISI" =>  $dateFrom->format('Y-m-d H:i:s'),
        "<=PROPERTY_VREMYA_ZAPISI" => $dateTo->format('Y-m-d H:i:s'),
    ];

    $arProcedures = CIBlockElement::GetList([],
        $arFilter,
        false,
        false,
        ['ID', 'NAME', 'ACTIVE', 'IBLOCK_ID', 'PROPERTY_VREMYA_ZAPISI'])->Fetch();
    if (!empty($arProcedures)) {
        $result['error'] = 'Данное время недоступно для записи';
        echo json_encode($result);
        die();
    }

    $element = new CIBlockElement();
    $id = $element->add([
        'NAME'            => $name,
        'ACTIVE'          => 'Y',
        'IBLOCK_ID'       => 18,
        'PROPERTY_VALUES' => [
            'PROTSEDURA' => $procedureId,
            'DOCTOR' => $elementId,
            'VREMYA_ZAPISI'       => $time,
        ],
    ]);

    if ($id > 0) {
        $result['result'] = 'Запись на процедуру добавлена в список "Бронирование"';
        echo json_encode($result);
        die();
    }

    $error = $element->LAST_ERROR;
    $result['error'] = $error;

    echo json_encode($result);
    die();
}

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
die();