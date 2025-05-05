<?php
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

    use Otus\Models\Lists\DoctorsPropertyValuesTable as DoctorsTable;
    use Bitrix\Main\ORM\Query\Query;

    // Подключим модуль «Информационные блоки»
    \Bitrix\Main\Loader::includeModule('iblock');

    // Генерируем имя ORM класса для работы с инфоблоком, где в моем случае IBLOCK_CATALOG_ID содержит 26 модуль «Информационные блоки»
    echo \Bitrix\Iblock\Iblock::wakeUp(16)->getEntityDataClass();

    // Запрос в класс ORM, 99 это ID элемента инфоблока
    $res = \Bitrix\Iblock\Elements\ElementDoctorsTable::getByPrimary(29, [
        'select' => [
            'ID', 
            'NAME', 
            // 'MANUFACTURER_' => 'MANUFACTURER'
        ],
    ])->fetch();

    // Распечатываем массив
    pr($res);



    $docId = 29; // идентификатор доктора из инфоблока Доктора
    $doctors = \Bitrix\Iblock\Elements\ElementDoctorsTable::getList([ // получение списка процедур у врачей
        'select' => [
            'ID', 
            'NAME', 
            'SPECIALIZATION',
            'PROTSEDURA_M.ELEMENT.NAME'
        ], 
        'filter' => [
            // 'ID' => $docId,
            'ACTIVE' => 'Y',
        ],
    ])
    ->fetchCollection(); 

    foreach ($doctors as $doctor) {
        echo "Доктор: " . $doctor->getName() . " (ID: " . $doctor->getId() . ")<br>";
        echo "Специализация: " . $doctor->getSpecialization()->getValue() . "<br>";
        
        echo "Процедуры: ";
        foreach($doctor->getProtseduraM()->getAll() as $prItem) {
            echo $prItem->getElement()->getName() . ", ";
        }
        echo "<br><br>";
    }

    // $q = new Query(Books::getEntity());
    // // регистрируем новое временное поле для исходной сущности
    // $q->registerRuntimeField(
    //     // поле element как ссылка на таблицу b_iblock_element
    //     'WIKIPROFILE',
    //     array(
    //         // тип — сущность ElementTable
    //         'data_type' => 'Models\WikiprofileTable',
    //         // this.ID относится к таблице, относительно которой строится запрос, т.е. b_iblock.ID = b_iblock_element.IBLOCK_ID
    //         'reference' => array('=this.wikiprofile_id' => 'ref.id'),
    //         // тип соединения INNER JOIN
    //         'join_type' => 'INNER'
    //     )
    // );

    // // выбираем название инфоблока, символьный код инфоблока, название элемента, символьный код элемента и идентификатор типа инфоблока
    // $q->setSelect(array('id', 'name', 'publish_date', 'WIKIPROFILE.wikiprofile_ru'));

    // // выполняем запрос
    // $result = $q->exec();
    // while ($row = $result->fetch()) {
    //     pr($row);
    // }

    // получение списка процедур у врачей с использованием метода query()
    // $doctors2 = \Bitrix\Iblock\Elements\ElementDoctorsTable::query([
    //     'NAME',
    //     'PROTSEDURA_M.ELEMENT.NAME',
    //     'PROTSEDURA_M.ELEMENT.DESCRIPTION' // PROTSEDURA_M - множественное поле инфоблока Доктора 
    // ])
    // ->setFilter(array('ACTIVE' => 'Y'))
    // setSelect([
    //     'ID',
    // ])
    // ->fetchCollection();
    // // затем обходим коллекцию и получаем процедуры
    // $procedures = []; 
    // foreach ($doctors2 as $doctor){
    //     foreach($doctor->getProtseduraM()->getAll() as $prItem) {
    //         $procedures[] = [
    //             'name'=> $prItem->getElement()->getName(),                
    //             'id' => $prItem->getElement()->getId()
    //         ];
    //     }
    // }
    // pr($procedures);

    // $cars = DoctorsTable::query()
    // ->setSelect([
    //     '*',
    //     'NAME' => 'ELEMENT.NAME',
    //     // 'MANUFACTURER_NAME' => 'MANUFACTURER.ELEMENT.NAME',
    // ])
    // ->registerRuntimeField(
    //     null,
    //     new \Bitrix\Main\Entity\ReferenceField(
    //         'SPECIALIZATION',
    //         \Bitrix\Iblock\Elements\ElementProceduresTable::getEntity(),
    //         ['=this.SPECIALIZATION' => 'ref.IBLOCK_ELEMENT_ID']
    //     )
    // )
    // ->fetchAll();

    // pr($cars);