<?php
namespace Otus\EventsHandler;

class IblockHandler
{

    //---- события для инфоблока
    public static function onAfterIBlockAddHandler(&$arFields): void
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt', date('Y-m-d H:i:s') . " :событие OnAfterIBlockElementAdd" . PHP_EOL, FILE_APPEND);
    }

    public static function onAfterIBlockUpdateHandler(&$arFields): void
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt', date('Y-m-d H:i:s') . " :событие OnAfterIBlockElementUpdate" . PHP_EOL, FILE_APPEND);
    }

    public static function onAfterIBlockDeleteHandler(&$arFields): void
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt', date('Y-m-d H:i:s') . " :событие OnAfterIBlockElementDelete" . PHP_EOL, FILE_APPEND);
    }

    //---- события для сделок
    public static function onAfterCrmDealAddHandler(&$arFields): void
    {
        $IBLOCK_ID = 21;
        // Логирование события
        file_put_contents(
            $_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt', 
            date('Y-m-d H:i:s') . " :событие OnAfterCrmDealAdd" . PHP_EOL . print_r($arFields), 
            FILE_APPEND
        );
        
        // Проверяем, что сделка успешно создана и есть ID
        if (empty($arFields['ID'])) {
            return;
        }
        
        $dealId = $arFields['ID'];
        
        try {
            // Получаем данные сделки
            $deal = \CCrmDeal::GetByID($dealId);
            if (!$deal) {
                throw new Exception("Deal not found");
            }
            
            // Получаем данные ответственного
            $assignedById = $deal['ASSIGNED_BY_ID'];
            $assignedByUser = \CUser::GetByID($assignedById)->Fetch();
            $assignedByName = \CUser::FormatName(
                '#NAME# #LAST_NAME#',
                [
                    'NAME' => $assignedByUser['NAME'],
                    'LAST_NAME' => $assignedByUser['LAST_NAME']
                ],
                true,
                false
            );

            file_put_contents(
                $_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt', 
                date('Y-m-d H:i:s') . " :событие OnAfterCrmDealAdd Информация о созданной сделки" . PHP_EOL . print_r($deal) . PHP_EOL, 
                FILE_APPEND
            );
            
            // Подготовка данных для элемента инфоблока
            $elementFields = [
                'NAME' => 'Сделка #' . $dealId,
                'IBLOCK_ID' => $IBLOCK_ID, // Замените на реальный ID инфоблока
                'PROPERTY_VALUES' => [
                    'SDELKA' => $dealId,          // Свойство "Сделка"
                    'SUMMA' => $deal['OPPORTUNITY'], // Свойство "Сумма"
                    'OTVETSTVENNYY' => $assignedByName // Свойство "Ответственный"
                ],
                'ACTIVE' => 'Y',
            ];
            
            // Создаем экземпляр CIBlockElement
            $element = new \CIBlockElement();
            
            // Добавляем элемент
            $elementId = $element->Add($elementFields);
            
            if (!$elementId) {
                throw new Exception($element->LAST_ERROR);
            }
            
            file_put_contents(
                $_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt', 
                date('Y-m-d H:i:s') . " : Создан элемент инфоблока ID " . $elementId . PHP_EOL, 
                FILE_APPEND
            );
            
        } catch (Exception $e) {
            file_put_contents(
                $_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt', 
                date('Y-m-d H:i:s') . " : Ошибка: " . $e->getMessage() . PHP_EOL, 
                FILE_APPEND
            );
        }
    }

    public static function onAfterCrmDealUpdateHandler(&$arFields): void
    {
        ob_start();
		echo PHP_EOL."<pre>".PHP_EOL;
		var_dump($arFields);
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt', date('Y-m-d H:i:s') . " :событие OnAfterCrmDealUpdate" . PHP_EOL . ob_get_clean(), FILE_APPEND);
        
        // Проверяем, что сделка успешно создана и есть ID
        if (empty($arFields['ID'])) {
            return;
        }
        
        try {
            $dealId = (int)$arFields['ID'];
            $elementData = self::getInfoBlockElementIdByDealId($dealId);

            $el = new \CIBlockElement();
            $PROP = [];
            
            $PROP[$elementData['SUMMA_PROP_ID']] = $arFields['OPPORTUNITY'];
            $PROP[$elementData['OTVETSTVENNYY_PROP_ID']] = $arFields['ASSIGNED_BY_ID'];
            $PROP[$elementData['SDELKA_PROP_ID']] = $arFields['ID'];

            $arLoadProductArray = [
                "MODIFIED_BY" => $arFields['MODIFY_BY_ID'],
                "PROPERTY_VALUES" => $PROP,
                "ACTIVE" => "Y"
            ];

            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt', 
                date('Y-m-d H:i:s') . " : Событие OnAfterCrmDealUpdate свойства перед обновлением " . PHP_EOL . print_r($elementData, true) . PHP_EOL . print_r($arLoadProductArray, true), FILE_APPEND);

            $el->Update($elementData['ID'], $arLoadProductArray);

            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt', 
                date('Y-m-d H:i:s') . " : Элемент инфоблока ID=".$elementData['ID']." успешно обновлен" . PHP_EOL, 
                FILE_APPEND);

        } catch (Exception $e) {
            file_put_contents(
                $_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt',
                date('Y-m-d H:i:s') . " : Ошибка: " . $e->getMessage() . PHP_EOL,
                FILE_APPEND
            );
        }

    }

    public static function onAfterCrmDealDeleteHandler(&$arFields): void
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt', date('Y-m-d H:i:s') . " :событие OnAfterCrmDealDelete" . PHP_EOL, FILE_APPEND);
    }

    public static function getInfoBlockElementIdByDealId(int $dealId): ?array
    {
        try {
            $elements = \Bitrix\Iblock\Elements\ElementOtusDealTable::getList([
                'select' => [
                    'ID', 
                    'NAME',
                    'SDELKA_GENERIC_VALUE' => 'SDELKA.IBLOCK_GENERIC_VALUE',
                    'SUMMA_PROP_ID' => 'SUMMA.IBLOCK_PROPERTY_ID',
                    'OTVETSTVENNYY_PROP_ID' => 'OTVETSTVENNYY.IBLOCK_PROPERTY_ID',
                ],
                'filter' => [
                    '=SDELKA.VALUE' => $dealId
                ],
                'limit' => 1
            ]);

            if ($element = $elements->fetch()) {
                file_put_contents(
                    $_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt', 
                    date('Y-m-d H:i:s') . " : Найден элемент инфоблока: " . print_r($element, true) . PHP_EOL, 
                    FILE_APPEND
                );

                return [
                    'ID' => (int)$element['ID'],
                    'SDELKA_PROP_ID' => (int)$element['SDELKA_GENERIC_VALUE'],
                    'SUMMA_PROP_ID' => (int)$element['SUMMA_PROP_ID'],
                    'OTVETSTVENNYY_PROP_ID' => (int)$element['OTVETSTVENNYY_PROP_ID'],
                ];
            }
        } catch (Exception $e) {
            file_put_contents(
                $_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt', 
                date('Y-m-d H:i:s') . " : Ошибка поиска элемента: " . $e->getMessage() . PHP_EOL, 
                FILE_APPEND
            );
        }

        return null;
    }
}