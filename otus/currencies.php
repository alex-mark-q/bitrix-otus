<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Курсы валют");

$APPLICATION->IncludeComponent(
	"otus:otus.currencies", 
	".default", 
	array(
		"CURRENCY" => "USD",
		"NUM_PAGE" => "1",
		"COMPONENT_TEMPLATE" => ".default",
		"SHOW_CHECKBOXES" => "Y"
	),
	false
);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");