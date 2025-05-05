<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Localization\Loc;
use CBitrixComponent;

class COtusDoctorsComponent extends \CBitrixComponent implements Controllerable
{
    
    public function configureActions():array {
        return [];
    }
    public function executeComponent() {
        $this->includeComponentTemplate($this->page);
    }

}