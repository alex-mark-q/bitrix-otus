<?php

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

$APPLICATION->IncludeComponent(
    "bitrix:main.file.input",
    "drag_n_drop_custom",
    array(
        "INPUT_NAME" => "NEW_FILE_UPLOAD_SUCCESS_RR",
        "MULTIPLE" => "Y", 
        "MODULE_ID" => "main",
        "MAX_FILE_SIZE" => "",
        "ALLOW_UPLOAD_EXT" => "", 
        "INPUT_CAPTION" => "ДОБАВИТЬ ФАЙЛ",
        "INPUT_VALUE" => $_POST['NEW_FILE_UPLOAD_SUCCESS_RR']
    )
);