<?php
namespace Otus\Customtab\Crm;

use Otus\Customtab\Orm\HospitalTable;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
class Handlers
{
    public static function updateTabs(Event $event): EventResult
    {
        $entityTypeId = $event->getParameter('entityTypeID'); // тип сущности лид или сделка
        $entityId = $event->getParameter('entityID');
        $tabs = $event->getParameter('tabs');
        $tabs[] = [
            'id' => 'book_tab_' . $entityTypeId . '_' . $entityId,
            'name' => Loc::getMessage('CRMOTUSTAB_TAB_TITLE'),
            'enabled' => true,
            'loader' => [
                'serviceUrl' => sprintf(
                    '/bitrix/components/otus.customtab/book.grid/lazyload.ajax.php?site=%s&%s', // здесь подключается свой компонент
                    \SITE_ID,
                    \bitrix_sessid_get(),
                ),
                'componentData' => [
                    'template' => '',
                    'params' => [
                        'ORM' => HospitalTable::class,
                        'DEAL_ID' => $entityId,
                    ],
                ],
            ],
        ];

        return new EventResult(EventResult::SUCCESS, ['tabs' => $tabs,]);
    }
}
