<?php
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    $doctors = \Bitrix\Iblock\Elements\ElementDoctorsTable::getList([
        'select' => [
            'ID', 
            'NAME', 
            'SPECIALIZATION',
            'PROTSEDURA_M.ELEMENT.NAME'
        ], 
        'filter' => [
            'ACTIVE' => 'Y',
        ],
    ])
    ->fetchCollection(); 

    $arr_doctors = [];

    foreach ($doctors as $doctor) {

        $arr_doctors["doctors"][$doctor->getId()]["doctor"] = $doctor->getName();
        $arr_doctors["doctors"][$doctor->getId()]["specialization"] = $doctor->getSpecialization()->getValue();

        foreach($doctor->getProtseduraM()->getAll() as $prItem) {
            $arr_doctors["doctors"][$doctor->getId()]["procedure"][] = $prItem->getElement()->getName();
        }


    }

    $procedures = \Bitrix\Iblock\Elements\ElementProceduresTable::getList([
        'select' => [
            'ID', 
            'NAME', 
        ], 
        'filter' => [
            'ACTIVE' => 'Y',
        ],
    ])
    ->fetchCollection(); 

    foreach ($procedures as $procedure) {
        $arr_doctors["procedure"][$procedure->getId()] = $procedure->getName();
    }

    echo json_encode($arr_doctors);
    // pr($arr_doctors);