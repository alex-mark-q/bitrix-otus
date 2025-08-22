<?php

namespace Otus\UserTypes;
use Bitrix\Main\Data\Cache;

class OnlineRecord
{
    const DEFAULT_SELECT_VALUE = '(не установлено)';

    public static function GetUserTypeDescription()
    {
        return array(
            'PROPERTY_TYPE'        => 'S', // тип поля
            'USER_TYPE'            => 'iblock_link', // код типа пользовательского свойства
            'DESCRIPTION'          => 'Онлайн бронирование', // название типа пользовательского свойства
            'GetPropertyFieldHtml' => array(self::class, 'GetPropertyFieldHtml'), // метод отображения свойства
            'GetSearchContent' => array(self::class, 'GetSearchContent'), // метод поиска
            'GetAdminListViewHTML' => array(self::class, 'GetAdminListViewHTML'),  // метод отображения значения в списке
            'GetPublicEditHTML' => array(self::class, 'GetPropertyFieldHtml'), // метод отображения значения в форме редактирования
            'GetPublicViewHTML' => array(self::class, 'GetPublicViewHTML'), // метод отображения значения
        );
    }


    public static function PrepareSettings($arFields)
    {
        // return array("_BLANK" => ($arFields["USER_TYPE_SETTINGS"]["_BLANK"] == "Y" ? "Y" : "N"));
        if(is_array($arFields["USER_TYPE_SETTINGS"]) && $arFields["USER_TYPE_SETTINGS"]["_BLANK"] == "Y"){
            return array("_BLANK" =>  "Y");
        }else{
            return array("_BLANK" =>  "N");
        }
    }

   
    public static function GetPublicViewHTML($arProperty, $arValue, $strHTMLControlName)
    {
        $arSettings = self::PrepareSettings($arProperty);
        $doctorData = self::prependDataDoctors();
        $procedureId = $arValue['VALUE'];
        $elementId = $arProperty['ELEMENT_ID'];
        
        $linkid = 'link_' . md5($arValue['VALUE'] . '_' . $elementId); // Более уникальный ID
        
        $arVals = array();
        if (!is_array($arProperty['VALUE'])) {
            $arProperty['VALUE'] = array($arProperty['VALUE']);
            $arProperty['DESCRIPTION'] = array($arProperty['DESCRIPTION']);
        }
        
        foreach ($arProperty['VALUE'] as $i => $value) {
            $arVals[$value] = $arProperty['DESCRIPTION'][$i];
        }

        // $displayValue = trim($arVals[$arValue['VALUE']] ?? $arValue['VALUE']);
        
        $displayValue = $doctorData['procedure'][$arValue['VALUE']];
        // pr($displayValue);
        $excludeValue = ($displayValue !== self::DEFAULT_SELECT_VALUE) ? $displayValue : null;
        //pr($excludeValue);
        
        // Добавляем data-атрибуты для удобства
        $strResult = '<a id="' . $linkid . '" class="procedure-link" 
                    data-procedure-id="' . htmlspecialcharsbx($procedureId) . '"
                    data-element-id="' . htmlspecialcharsbx($elementId) . '"
                    ' . ($arSettings["_BLANK"] == 'Y' ? 'target="_blank"' : '') . ' 
                    href="javascript:void(0);">' . $excludeValue . '</a>';
        
        $strResult .= '
        <div id="popup-' . $linkid . '" style="display:none; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <h1 style="color: #333;">Процедура</h1>
            <form method="POST" action="">
                <div class="ui-ctl ui-ctl-textbox" style="margin-top: 20px;">
                    <input name="name" type="text" class="ui-ctl-element" placeholder="ФИО пациента" style="padding: 10px;">
                </div>
                <div class="ui-ctl ui-ctl-textbox" style="margin-top: 20px; margin-left: 0;">
                    <input onclick="BX.calendar({node: this, field: this, bTime: true});" 
                        name="time" 
                        type="text" 
                        class="ui-ctl-element" 
                        placeholder="Время записи" 
                        style="padding: 10px;">
                </div>
                <input class="ui-ctl-element" type="hidden" value="" name="procedure">
                <input class="ui-ctl-element" type="hidden" value="" name="element_id">
            </form>
        </div>
        <script>
            BX.ready(function() {
                var link = document.getElementById("' . $linkid . '");
                if (link) {
                    BX.bind(link, "click", function(e) {
                        e.preventDefault();
                        console.log("Click on procedure:", {
                            procedureId: this.getAttribute("data-procedure-id"),
                            elementId: this.getAttribute("data-element-id")
                        });
                        
                        // Создаем popup с простым текстом
                        var popup = BX.PopupWindowManager.create(
                            "popup-message-' . $linkid . '", 
                            link,
                            {
                                content: document.getElementById("popup-' . $linkid . '"),
                                className: "bx-filter-select-popup-window",
                                autoHide: true,
                                offsetLeft: 0,
                                offsetTop: 8,
                                overlay: false,
                                draggable: false,
                                closeByEsc: true,
                                closeIcon: {right: "10px", top: "15px"},
                                width: 550,
                                maxHeight: 550,
                                titleBar: "Запись на процедуру",
                                buttons: [
                                    new BX.PopupWindowButton({
                                        text: "Записать",
                                        id: "save-btn",
                                        className: "ui-btn ui-btn-success",
                                        events: {
                                            click: function() {
                                                BX.remove(BX("alert"));
                                                let formData = new FormData();
                                                let inputs = BX.findChild(BX("popup-'. $linkid .'"), {
                                                    tag: "input",
                                                    props: {className: "ui-ctl-element"}
                                                }, true, true);
                                                
                                                console.log("Записать ",inputs);

                                                let allValid = true;
                                                inputs.forEach((element) => {
                                                console.log("Записать 2", element, element.value, !element.value);
                                                    BX.bind(element, "focus", function() {
                                                        BX.remove(BX("alert"));
                                                        let parent = BX.findParent(element, {className : "ui-ctl"});
                                                        BX.removeClass(parent, "ui-ctl-warning");
                                                    });

                                                    if (!element.value) {
                                                        allValid = false;
                                                        let parent = BX.findParent(element, {className : "ui-ctl"});
                                                        BX.addClass(parent, "ui-ctl-warning");
                                                    }

                                                    formData.append(element.getAttribute("name"), element.value);
                                                });

                                                console.log("allValid", allValid);

                                                if (!allValid) {
                                                    let alert = BX.create({
                                                        tag: "div",
                                                        props: {
                                                            className: "ui-alert ui-alert-danger",
                                                            id: "alert"
                                                        },
                                                        children: [
                                                            BX.create({
                                                                tag: "span",
                                                                props: {className: "ui-alert-message"},
                                                                text: "Не заполнены обязательные поля"
                                                            }),
                                                        ]
                                                    });
                                                    BX.prepend(alert, BX("popup-'. $linkid .'"));
                                                    return;
                                                }

                                                formData.append("sessid", BX.bitrix_sessid());

                                                BX.ajax({
                                                    dataType: "json",
                                                    processData: false,
                                                    preparePost: false,
                                                    url: "/local/api/onlineRecord/ajax.php",
                                                    method: "POST",
                                                    data: formData,
                                                    onsuccess: function (data) {
                                                        let result = JSON.parse(data);
                                                        let alertClass = result.error ? "ui-alert-danger" : "ui-alert-success";
                                                        let alertText = result.error || result.result;

                                                        let alert = BX.create({
                                                            tag: "div",
                                                            props: {
                                                                className: "ui-alert " + alertClass,
                                                                id: "alert"
                                                            },
                                                            children: [
                                                                BX.create({
                                                                    tag: "span",
                                                                    props: {className: "ui-alert-message"},
                                                                    text: alertText
                                                                }),
                                                            ]
                                                        });
                                                        BX.prepend(alert, BX("popup-' . $linkid . '"));
                                                    },
                                                    onfailure: function () {
                                                        let alert = BX.create({
                                                            tag: "div",
                                                            props: {
                                                                className: "ui-alert ui-alert-danger",
                                                                id: "alert"
                                                            },
                                                            children: [
                                                                BX.create({
                                                                    tag: "span",
                                                                    props: {className: "ui-alert-message"},
                                                                    text: "Ошибка"
                                                                }),
                                                            ]
                                                        });
                                                        BX.prepend(alert, BX("popup-' . $linkid . '"));
                                                    }
                                                });
                                            }
                                        }
                                    }),
                                    new BX.PopupWindowButton({
                                        text: "Закрыть",
                                        id: "copy-btn",
                                        className: "ui-btn ui-btn-primary",
                                        events: {
                                            click: function() {
                                                this.popupWindow.close();
                                            }
                                        }
                                    })
                                ],
                                events: {
                                    onPopupShow: () => {
                                        let popupContent = BX("popup-' . $linkid . '");

                                        let procedureInput = popupContent.querySelector(`input[name="procedure"]`);
                                        let elementIdInput = popupContent.querySelector(`input[name="element_id"]`);

                                        

                                        let procedureId = link.getAttribute("data-procedure-id");
                                        let elementId = link.getAttribute("data-element-id");
                                        let procedureTitle = link.text;

                                        procedureInput.value = procedureId;
                                        elementIdInput.value = elementId;

                                        console.log(procedureInput, elementIdInput, procedureId, elementId);

                                        let h1 = popupContent.querySelector("h1");
                                        h1.textContent = procedureTitle;
                                    },
                                    onAfterPopupShow: function () {
                                        popup.adjustPosition();
                                        popup.resizeOverlay();
                                    }
                                }
                            }
                        );
                        popup.show();
                    });
                }
            });
        </script>';
        
        return $strResult;
    }


    public static function GetAdminListViewHTML($arProperty, $arValue, $strHTMLControlName)
    {
        $arSettings = self::PrepareSettings($arProperty);

        $strResult = '';
        $strResult = '<a ' . ($arSettings["_BLANK"] == 'Y' ? 'target="_blank"' : '') . ' href="' . trim($arValue['VALUE']) . '">' . (trim($arValue['DESCRIPTION']) ? trim($arValue['DESCRIPTION']) : trim($arValue['VALUE'])) . '</a>';
        return $strResult;
    }


    public static function GetSearchContent($arProperty, $value, $strHTMLControlName)
    {
        if (trim($value['VALUE']) != '') {
            return $value['VALUE'] . ' ' . $value['DESCRIPTION'];
        }

        return '';
    }

    public static function GetPropertyFieldHtml($arProperty, $arValue, $strHTMLControlName)
    {
        // pr($arProperty);
        // echo "arValue";
        // pr($arValue);
        // pr($strHTMLControlName);
        $strResult = '';

        $arr = array(
            "REFERENCE" => // массив заголовков элементов
                array("Вариант 1", "Вариант 2", "Вариант 3"),
            "REFERENCE_ID" => // массив значений элементов
                array(1, 2, 3)
        ); 

        global $bVarsFromForm, $bCopy, $PROP, $APPLICATION;

        // pr(self::prependDataDoctors());
        // $defaultProcedure = array(
        //     "REFERENCE" => array_values($arr_doctors['procedure']), 
        //     "REFERENCE_ID" => array_keys($arr_doctors['procedure']) 
        // );
        $doctorData = self::prependDataDoctors();
        $defaultProcedure = array(
            "REFERENCE" => array_values($doctorData['procedure']), 
            "REFERENCE_ID" => array_keys($doctorData['procedure']) 
        );
        
        $selectedValue = isset($arValue["VALUE"]) ? $strHTMLControlName['VALUE'] : "";
        // echo "selectedValue";
        // pr($selectedValue);
        // Формируем HTML
        ob_start(); // Включаем буферизацию вывода

        ?>
        <?
            echo SelectBoxFromArray(
                $selectedValue, 
                $defaultProcedure, 
                (isset($arValue["VALUE"]) && $arValue["VALUE"] !== '') ? $arValue["VALUE"] : "", 
                "", 
                "", 
                false, 
                $selectedValue
            );
            // return (isset($arValue["VALUE"]) && $arValue["VALUE"] !== '') 
            // ? SelectBoxFromArray(
            //     $selectedValue,
            //     $defaultProcedure,
            //     $arValue["VALUE"],
            //     "",
            //     "",
            //     false,
            //     $selectedValue
            // )
            // : SelectBoxFromArray(
            //     "",
            //     $defaultProcedure,
            //     "",
            //     "",
            //     "",
            //     false,
            //     $selectedValue
            // );
        ?>
        <?php
            return ob_get_clean(); // Возвращаем весь HTML

    }

    public static function prependDataDoctors() {

        $cacheTime = 30*60; // время кеширования, указывается в секундах
        $cacheId = 'doctors_data_' . $_REQUEST['CACHE_ID']; // формируем идентификатор кеша в зависимости от параметров
        $cacheDir = '/'; // директория кеша

        $cache = Cache::createInstance();
        if ($cache->initCache($cacheTime, $cacheId, $cacheDir)) {
            $doctors = $cache->getVars();
            return $doctors;
        } elseif ($cache->startDataCache()) {
            $arr_doctors = [];
            $doctors = \Bitrix\Iblock\Elements\ElementDoctorsTable::getList([
                'select' => [
                    'ID', 
                    'NAME', 
                    'SPECIALIZATION',
                    'PROTSEDURY.VALUE',
                ], 
                'filter' => [
                    'ACTIVE' => 'Y',
                ],
            ])->fetchCollection();
            
            foreach ($doctors as $doctor) {
                $arr_doctors["doctors"][$doctor->getId()] = [
                    "doctor" => $doctor->getName(),
                    "specialization" => $doctor->getSpecialization()->getValue(),
                    "procedures" => []
                ];

                // Получаем все значения множественного свойства PROTSEDURY
                foreach ($doctor->getProtsedury() as $procedure) {
                    $arr_doctors["doctors"][$doctor->getId()]["procedures"][] = $procedure->getValue();
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

            $arr_doctors['procedure'][0] = self::DEFAULT_SELECT_VALUE;
            foreach ($procedures as $procedure) {
                $arr_doctors["procedure"][$procedure->getId()] = $procedure->getName();
            }
            $cache->endDataCache($arr_doctors);
        }

        return $arr_doctors;
    }
}

