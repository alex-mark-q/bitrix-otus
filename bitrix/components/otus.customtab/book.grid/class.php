<?php

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\Filter\Options as FilterOptions;
use Otus\Customtab\Orm\HospitalTable;
use Bitrix\Main\Loader;
use Bitrix\Main\ORM\Query\Result;

Loader::includeModule('otus.customtab');
class HospitalGrid extends \CBitrixComponent implements Controllerable
{
    public function configureActions(): array
    {
        return [];
    }

    private function getElementActions(): array
    {
        return [];
    }

    private function getHeaders(): array
    {
        return [
            [
                'id' => 'id',
                'name' => 'id',
                'sort' => 'id',
                'default' => true,
            ],
            [
                'id' => 'hospital_name',
                'name' => Loc::getMessage('HOSPITAL_GRID_TITLE_LABEL'),
                'sort' => 'hospital_name',
                'default' => true,
            ],
            [
                'id' => 'city',
                'name' => Loc::getMessage('HOSPITAL_GRID_PUBLISHING_YEAR_LABEL'),
                'sort' => 'city',
                'default' => true,
            ],
            [
                'id' => 'doctor_id',
                'name' => Loc::getMessage('HOSPITAL_GRID_PAGES_LABEL'),
                'sort' => 'doctor_id',
                'default' => true,
            ],
        ];
    }

    public function executeComponent(): void
    {
        $this->prepareGridData();
        $this->includeComponentTemplate();
    }

    private function prepareGridData(): void
    {
        $this->arResult['HEADERS'] = $this->getHeaders();
        $this->arResult['FILTER_ID'] = 'HOSPITAL_GRID';

        $gridOptions = new GridOptions($this->arResult['FILTER_ID']);
        $navParams = $gridOptions->getNavParams();

        $nav = new PageNavigation($this->arResult['FILTER_ID']);
        $nav->allowAllRecords(true)
            ->setPageSize($navParams['nPageSize'])
            ->initFromUri();

        $filterOption = new FilterOptions($this->arResult['FILTER_ID']);
        $filterData = $filterOption->getFilter([]);
        // $filter = $this->prepareFilter($filterData);
        // pr($filter);

        $sort = $gridOptions->getSorting([
            'sort' => [
                'ID' => 'DESC',
            ],
        ]);

        $hospitalIdsQuery = HospitalTable::query()
            ->setSelect(['ID'])
            ->setFilter($filterData)
            ->setLimit($nav->getLimit())
            ->setOffset($nav->getOffset())
            ->setOrder($sort['sort'])
        ;
        

        $countQuery = HospitalTable::query()
            ->setSelect(['ID'])
            ->setFilter($filterData)
        ;
        // pr($nav->setRecordCount($countQuery->queryCountTotal()));
        $nav->setRecordCount($countQuery->queryCountTotal());

        $hospitalIds = array_column($hospitalIdsQuery->exec()->fetchAll(), 'ID');
        // pr($hospitalIds);

        $resultsHospital = [];
        $hospitalsData = HospitalTable::getList([
            'filter' => [],
            'select' => [
                'id',
                'hospital_name',
                'city',
                'doctor_id'
            ],
            'order' => $sort['sort'],
        ]);

        $this->arResult['GRID_LIST'] = $this->prepareGridList($hospitalsData);
        // $this->arResult['GRID_LIST'] = $resultsHospital;

        $this->arResult['NAV'] = $nav;
        $this->arResult['UI_FILTER'] = $this->getFilterFields();
    }

    private function prepareFilter(array $filterData): array
    {
        $filter = [];

        if (!empty($filterData['FIND'])) {
            $filter['%TITLE'] = $filterData['FIND'];
        }

        return $filter;
    }

    private function prepareGridList(Result $hospitals): array
    {
        $gridList = [];
        $groupedBooks = [];

        while ($hospital = $hospitals->fetch()) {
            $gridList[] = [
                'data' => [
                    'id' => $hospital['ID'],
                    'hospital_name' => $hospital['HOSPITAL_NAME'],
                    'city' => $hospital['CITY'],
                    'doctor_id' => $hospital['DOCTOR_ID'],
                ],
                'actions' => $this->getElementActions(),
            ];
        }

        return $gridList;
    }

    private function getFilterFields(): array
    {
        return [
            [
                'id' => 'hospital_name',
                'name' => Loc::getMessage('HOSPITAL_GRID_TITLE_LABEL'),
                'type' => 'string',
                'default' => true,
            ]
        ];
    }
}
