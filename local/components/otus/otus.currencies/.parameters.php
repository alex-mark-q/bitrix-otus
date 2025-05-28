<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */

use Bitrix\Main\Loader;
use Bitrix\Currency\CurrencyManager;

if(!Loader::includeModule('currency'))
	return;

$arCurrencies = [];
$currencies = CurrencyManager::getCurrencyList();
foreach ($currencies as $currencyCode => $currencyName) {
    $arCurrencies[$currencyCode] = $currencyName;
}

$arComponentParameters = array(
	"GROUPS" => array(
		"LIST"=>array(
			"NAME"=>GetMessage("GRID_PARAMETERS"),
			"SORT"=>"300"
		)
	),
	"PARAMETERS" => array(
		"SHOW_CHECKBOXES" =>  array(
			"PARENT" => "LIST",
			"NAME"=>GetMessage("SHOW_ACTION_BTNS"),
			"TYPE"=>"CHECKBOX",
			"DEFAULT"=>"N"
		),
		"NUM_PAGE" =>  array(
			"PARENT" => "LIST",
			"NAME"=>GetMessage("NUM_PAGE"),
			"TYPE"=>"STRING",
			"DEFAULT"=>"20"
		),
		"CURRENCY" => array(
            "PARENT" => "BASE",
            "NAME" => "Выбор валюты",
            "TYPE" => "LIST",
            "VALUES" => $arCurrencies,
            "DEFAULT" => "USD",
            "REFRESH" => "N",
		),
	)
);


