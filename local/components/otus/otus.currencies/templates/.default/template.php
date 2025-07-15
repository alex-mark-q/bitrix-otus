<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

use Bitrix\Main\Type;
use Bitrix\Main\Grid\Panel\Snippet\Onchange;
use Bitrix\Main\Grid\Panel\Actions;

use Bitrix\Main\Grid\Options as GridOptions;

?>

<?php
// Кнопка удалить
$onchange = new Onchange();
$onchange->addAction(
    [
        'ACTION' => Actions::CALLBACK,
        'CONFIRM' => true,
        'CONFIRM_APPLY_BUTTON'  => 'Подтвердить',
        'DATA' => [
            ['JS' => 'Grid.removeSelected()']
        ]
    ]
);
?>

<?php
    $APPLICATION->IncludeComponent(
        'bitrix:main.ui.grid',
        '',
        [
            'GRID_ID' => 'MY_GRID_ID',
            'COLUMNS' => $arResult['COLUMNS'],
            'ROWS' => $arResult['LISTS'],
            'AJAX_MODE' => 'Y',
            'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
            'PAGE_SIZES' =>  [
                ['NAME' => '20', 'VALUE' => '20'],
                ['NAME' => '50', 'VALUE' => '50'],
                ['NAME' => '100', 'VALUE' => '100']
            ],
            'AJAX_OPTION_JUMP'          => 'N',
            'SHOW_CHECK_ALL_CHECKBOXES' => false,
            'SHOW_ROW_ACTIONS_MENU'     => false,
            'SHOW_GRID_SETTINGS_MENU'   => true,
            'SHOW_NAVIGATION_PANEL'     => true,
            'SHOW_PAGINATION'           => true,
            'SHOW_SELECTED_COUNTER'     => true,
            'SHOW_TOTAL_COUNTER'        => true,
            'SHOW_PAGESIZE'             => true,
            'SHOW_ACTION_PANEL'         => true,
            'ALLOW_COLUMNS_SORT'        => true,
            'ALLOW_COLUMNS_RESIZE'      => true,
            'ALLOW_HORIZONTAL_SCROLL'   => true,
            'ALLOW_SORT'                => true,
            'ALLOW_PIN_HEADER'          => true,
            'AJAX_OPTION_HISTORY'       => 'N',
            'ACTION_PANEL'              => [ 
                'GROUPS' => [ 
                    'TYPE' => [ 
                        'ITEMS' => [ 
                            [
                                'ID'       => 'delete',
                                'TYPE'     => 'BUTTON',
                                'TEXT'     => 'Удалить',
                                'CLASS'    => 'icon remove grid-delete-button',
                                'ONCHANGE' => ''
                            ],
                            [ 
                                'ID'       => 'edit', 
                                'TYPE'     => 'BUTTON', 
                                'TEXT'        => 'Редактировать', 
                                'CLASS'        => 'icon edit', 
                                'ONCHANGE' => '' 
                            ], 
                        ], 
                    ] 
                ], 
            ], 
            'TOTAL_ROWS_COUNT' => $count
        ]
    );
?>
<script>
    BX.ready(function() {
        
        console.log('BX');
              
        BX.bindDelegate(document.body, 'click', {className: 'grid-delete-button'}, function(event) {
            
            console.log('bindDelegate');
            
            // event.preventDefault();

            var grid = BX.Main.gridManager.getById('MY_GRID_ID');
            var selectedIds = grid.instance.getRows().getSelectedIds();

            if (selectedIds.length > 0) {
                if (confirm('Вы уверены, что хотите удалить выбранные записи?')) {
                    
                    BX.ajax.runComponentAction('otus:otus.currencies', 'deleteRecords', {
                        mode: 'class',
                        data: { ids: selectedIds }
                    }).then(function(response) {

                        if (response.status === 'success') {
                            grid.reload();
                        } else {
                            console.log('Ошибка при удалении записей');
                        }
                    });
                }
            } else {
                console.log('Выберите хотя бы одну запись для удаления');
            }
        });
        
    });
</script>