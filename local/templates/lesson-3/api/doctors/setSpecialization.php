<?php

    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

    $result = [
        'success' => false,
        'message' => 'Empty'
    ];
    if(isset($_GET['idProcedure']) && isset($_GET['idDoctor'])) {
        try {

            $element_id = $_GET['idDoctor'];
            $iblock_id = 16;
            $linkedElements = [$_GET['idProcedure']]; // Массив ID привязанных элементов
            $res = CIBlockElement::SetPropertyValuesEx(
                $element_id , // ID элемента, значение свойства которого мы изменяем
                $iblock_id ,   // ID инфоблока
                ["PROTSEDURA_M" => $linkedElements] // Массив ID привязанных элементов
            );
    
        } catch(Exception $e) {
            $result['success'] = false;
            $result['message'] = "Error: " . $e;
        }
    }


    echo json_encode($result);

    