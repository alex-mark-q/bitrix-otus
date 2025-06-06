<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;

use Otus\Models\CurrencyTable as Currency;

class COtusCurrenciesComponent extends CBitrixComponent implements Controllerable
{
    public function configureActions():array {
        return [
            'deleteRecords' => [
                'prefilters' => []
            ]
        ];
    }

    public function deleteRecordsAction(array $ids):array {
        foreach ($ids as $id) {
            $result = Currency::delete($id);
            if (!$result->isSuccess()) {
                return ['status' => 'error', 'message' => 'Ошибка при удалении записи с ID: ' . $id];
            }
        }
        return ['status' => 'success'];
    }

    public function getColumn():array {
        $fieldMap = Currency::getMap(); 
        $columns = [];
        foreach ($fieldMap as $key => $field) {
            $columns[] = array(
                'id' => $field->getName(),
                'name' => $field->getTitle()
            );
        }
        return $columns;
    }
    public function getList($page = 1, $limit = 1):array {
        $offset = $limit * ($page-1);
        $list = [];
        $data = Currency::getList([
            'select' => ['CURRENCY','AMOUNT','BASE','CURRENT_BASE_RATE'],
            'order' => ['CURRENT_BASE_RATE' => 'ASC'],
            'limit' => $limit,
            'offset' => $offset
        ]);
        
        while ($item = $data->fetch()) {
            $list[] = array('data' => $item);
        }

        return $list;
    }

    private function prepareCurrency():array {
        $listCurrency = $this->getList($page, $this->arResult['NUM_PAGE']); // получаем записи таблицы
        // pr($listCurrency);
        $filteredArray = array_filter($listCurrency, function($item) {
            return $item['data']['CURRENCY'] === $this->arParams["CURRENCY"];
        });
        return $filteredArray;
    }

    public function executeComponent() {
        try {

            $this->$request = Application::getInstance()->getContext()->getRequest();

            // echo "<pre>";
            // print_r($this->arParams["CURRENCY"]);
            // echo "</pre>";

            if($this->arParams["CURRENCY"]) {
                // в дальнейшем можно проводить некоторые операции, например crud
            }

            if(isset($this->$request['report_list'])) {
                $page = explode('page-', $this->$request['report_list']);
                $page = $page[1];
            } else {
                $page = 1;
            }

            $this->arResult['COLUMNS'] = $this->getColumn(); // получаем названия полей таблицы
            $this->arResult['LISTS'] = $this->prepareCurrency();
            $this->arResult['COUNT'] =  Currency::getCount(); // количество записей

            $this->includeComponentTemplate($this->page);
        } catch(SystemException $e) {
            ShowError($e->getMessage());
        }
        
    }

}