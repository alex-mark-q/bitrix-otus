<?php
namespace Otus\EventsHandler;
use Bitrix\Main\Loader;
use Bitrix\Crm\DealTable;

class IblockHandler
{

    //---- события для инфоблока
    public static function onAfterIBlockAddHandler(&$arFields): void
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt', date('Y-m-d H:i:s') . " :событие OnAfterIBlockElementAdd" . PHP_EOL, FILE_APPEND);
    }

    public static function onAfterIBlockUpdateHandler(&$arFields): void
    {
        ob_start();
		echo PHP_EOL."<pre>".PHP_EOL;
		var_dump($arFields);
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt', date('Y-m-d H:i:s') . " :событие OnAfterIBlockElementUpdate" . PHP_EOL . ob_get_clean() . PHP_EOL, FILE_APPEND);
        try {
            // Подключаем необходимые модули
            if (!Loader::includeModule('iblock') || !Loader::includeModule('crm')) {
                throw new \Exception('Требуемые модули не подключены');
            }

            // Получаем элемент инфоблока с нужными свойствами
            $element = \Bitrix\Iblock\Elements\ElementOtusDealTable::getList([
                'select' => [
                    'ID', 
                    'NAME',
                    'SDELKA_VALUE' => 'SDELKA.VALUE',
                    // Другие свойства при необходимости
                    'SUMMA_VALUE' => 'SUMMA.VALUE',
                    'OTVETSTVENNYY_VALUE' => 'OTVETSTVENNYY.VALUE'
                ],
                'filter' => [
                    'ID' => $arFields['ID']
                ],
                'limit' => 1
            ])->fetch();

            if (!$element) {
                throw new \Exception('Элемент не найден');
            }
            file_put_contents(
                $_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt', 
                date('Y-m-d H:i:s') . "Параметры инфоблока перед обновлением сделки " . var_dump($element) . PHP_EOL, 
                FILE_APPEND
            );

            $fieldsToUpdate = [];

            if (isset($element['NAME'])) {
                $fieldsToUpdate['NAME'] = $element['NAME'];
            }
            if (isset($element['SUMMA_VALUE'])) {
                $fieldsToUpdate['OPPORTUNITY'] = (float)$element['SUMMA_VALUE'];
            }
            if (isset($element['OTVETSTVENNYY_VALUE'])) {
                $fieldsToUpdate['ASSIGNED_BY_ID'] = $element['OTVETSTVENNYY_VALUE'];
            }

            $dealId = (int)$element['SDELKA_VALUE'];
            if($dealId) {
                $updateResult = DealTable::update($dealId, $fieldsToUpdate);
            }

            if (!$updateResult->isSuccess()) {
                throw new \Exception('Ошибка обновления сделки: ' . implode(', ', $updateResult->getErrorMessages()));
            }

            // Логирование успеха
            file_put_contents(
                $_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt', 
                date('Y-m-d H:i:s') . " : Сделка {$dealId} обновлена" . PHP_EOL, 
                FILE_APPEND
            );

        } catch (\Exception $e) {
            file_put_contents(
                $_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt', 
                date('Y-m-d H:i:s') . " : Ошибка: " . $e->getMessage() . PHP_EOL, 
                FILE_APPEND
            );
        }
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
                throw new \Exception("Deal not found");
            }
            
            // Получаем данные ответственного
            $assignedById = $deal['ASSIGNED_BY_ID'];

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
                    'OTVETSTVENNYY' => $assignedById // Свойство "Ответственный"
                ],
                'ACTIVE' => 'Y',
            ];
            
            // Создаем экземпляр CIBlockElement
            $element = new \CIBlockElement();
            
            // Добавляем элемент
            $elementId = $element->Add($elementFields);
            
            if (!$elementId) {
                throw new \Exception($element->LAST_ERROR);
            }
            
            file_put_contents(
                $_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt', 
                date('Y-m-d H:i:s') . " : Создан элемент инфоблока ID " . $elementId . PHP_EOL, 
                FILE_APPEND
            );
            
        } catch (\Exception $e) {
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
            
            if(isset($arFields['OPPORTUNITY']) || isset($arFields["OPPORTUNITY_ACCOUNT"])) {
                $PROP[$elementData['SUMMA_PROP_ID']] = $arFields['OPPORTUNITY'] ? $arFields['OPPORTUNITY'] : $arFields["OPPORTUNITY_ACCOUNT"];
            }
            if(isset($arFields['ASSIGNED_BY_ID'])) {
                $PROP[$elementData['OTVETSTVENNYY_PROP_ID']] = (int)$arFields['ASSIGNED_BY_ID'];
            }
            if(isset($arFields['ID'])) {
                $PROP[$elementData['SDELKA_PROP_ID']] = (string)$arFields['ID'];
            }
            
            $arLoadProductArray = [
                "MODIFIED_BY" => $arFields['MODIFY_BY_ID'],
                "PROPERTY_VALUES" => $PROP,
                "ACTIVE" => "Y"
            ];

            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt', 
                date('Y-m-d H:i:s') . " : Событие OnAfterCrmDealUpdate свойства перед обновлением " . PHP_EOL . print_r($elementData, true) . PHP_EOL . print_r($arLoadProductArray, true), FILE_APPEND);

            if(!empty($PROP)) {
                if (!$el->Update($elementData['ID'], $arLoadProductArray)) {
                file_put_contents(
                    $_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt',
                    date('Y-m-d H:i:s') . " : Ошибка обновления элемента: " . $el->LAST_ERROR . PHP_EOL,
                    FILE_APPEND
                );
            }
            }

            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt', 
                date('Y-m-d H:i:s') . " : Элемент инфоблока ID=".$elementData['ID']." успешно обновлен" . PHP_EOL, 
                FILE_APPEND);

        } catch (\Exception $e) {
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
                    'SDELKA_PROP_ID' => 'SDELKA.IBLOCK_PROPERTY_ID',
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
                    'ID' => $element['ID'],
                    'SDELKA_PROP_ID' => $element['SDELKA_PROP_ID'],
                    'SUMMA_PROP_ID' => $element['SUMMA_PROP_ID'],
                    'OTVETSTVENNYY_PROP_ID' => $element['OTVETSTVENNYY_PROP_ID'],
                ];
            }
        } catch (\Exception $e) {
            file_put_contents(
                $_SERVER['DOCUMENT_ROOT'] . '/logs/otus.txt', 
                date('Y-m-d H:i:s') . " : Ошибка поиска элемента: " . $e->getMessage() . PHP_EOL, 
                FILE_APPEND
            );
        }

        return null;
    }
}