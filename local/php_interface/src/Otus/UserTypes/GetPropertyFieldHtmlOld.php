public static function GetPropertyFieldHtml($arProperty, $arValue, $strHTMLControlName)
    {
        // pr($arProperty);
        // pr($arValue);
        // pr($strHTMLControlName);
        $strResult = '';

        global $bVarsFromForm, $bCopy, $PROP, $APPLICATION;
        // $strResult = '</a>';

        $inpid = md5('link_' . rand(0, 999)); 

        $arr = array(
            "REFERENCE" => // массив заголовков элементов
                array("Вариант 1", "Вариант 2", "Вариант 3"),
            "REFERENCE_ID" => // массив значений элементов
                array(1, 2, 3)
        );

        $cacheTime = 30*60; // время кеширования, указывается в секундах
        $cacheId = 'doctors_data_' . $_REQUEST['CACHE_ID']; // формируем идентификатор кеша в зависимости от параметров
        $cacheDir = 'services/lists/'; // директория кеша

        $cache = Cache::createInstance();
        if ($cache->initCache($cacheTime, $cacheId, $cacheDir)) {
            $arrDoctorsData = $cache->getVars();
        } elseif ($cache->startDataCache()) {
            $arrDoctorsData = \Bitrix\Iblock\Elements\ElementDoctorsTable::getList([
                'select' => [
                    'ID', 
                    'NAME', 
                    'SPECIALIZATION',
                    'PROTSEDURY.VALUE', // Важно: указываем, что нужно выбрать значение
                ], 
                'filter' => [
                    'ACTIVE' => 'Y',
                ],
            ])->fetchCollection();

            $proceduresList = [
                "REFERENCE" => [],
                "REFERENCE_ID" => []
            ];

            foreach ($arrDoctorsData as $procedure) {
                echo "dsf";
                pr($procedure->getId());
                $proceduresList["REFERENCE_ID"][] = $procedure->getId();
                $proceduresList["REFERENCE"][] = $procedure->getName();
            }

            $cache->endDataCache($proceduresList);
        }
        // 2. Формируем массив для SelectBoxFromArray
        $selectOptions = [
            "REFERENCE" => [],
            "REFERENCE_ID" => []
        ];

        // Добавляем дефолтные варианты из $arrDoctorsData
        foreach ($arrDoctorsData as $doctorId => $doctorData) {
            print_r($doctorData);
            foreach ($doctorData['procedures'] as $procedure) {
                $selectOptions["REFERENCE_ID"][] = $procedure['id'];
                $selectOptions["REFERENCE"][] = $doctorData['name'] . ' - ' . $procedure['name'];
            }
        }

        pr($selectOptions);

        // Добавляем значения из $arProperty['VALUE'], если они есть
        if (!empty($arProperty['VALUE']) && is_array($arProperty['VALUE'])) {
            foreach ($arProperty['VALUE'] as $item) {
                if (!empty($item['VALUE']) && !in_array($item['VALUE'], $selectOptions["REFERENCE_ID"])) {
                    $selectOptions["REFERENCE_ID"][] = $item['VALUE'];
                    $selectOptions["REFERENCE"][] = $item['VALUE'];
                }
            }
        }

        // 3. Определяем выбранное значение
        $selectedValue = $arValue['VALUE'];
        if (is_array($selectedValue)) {
            $selectedValue = reset($selectedValue); // берем первое значение, если это массив
        }

        echo "?/" . PHP_EOL;

       
        // pr($selectedValue);

        // Формируем HTML
        ob_start(); // Включаем буферизацию вывода

        ?>
        <form name="formchoiceprocedure" method="POST" action="">
            <?=
                SelectBoxFromArray($strHTMLControlName['VALUE'], $selectOptions, "", "", "", false, "formchoiceprocedure");
            ?>
        </form>
        <?php
            return ob_get_clean(); // Возвращаем весь HTML

    }