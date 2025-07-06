<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Bizproc\Activity\BaseActivity;
use Bitrix\Bizproc\FieldType;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Localization\Loc;
use Bitrix\Bizproc\Activity\PropertiesDialog;
use Otus\DadataService\Dadata;
use Bitrix\Main\Engine\CurrentUser;


class CBPSearchByInnActivity extends BaseActivity
{
    // protected static $requiredModules = ["crm"];
    
    /**
     * @see parent::_construct()
     * @param $name string Activity name
     */
    public function __construct($name)
    {
        parent::__construct($name);

        $this->arProperties = [
            'Inn' => '',

            // return
            'Text' => null,
        ];

        $this->SetPropertiesTypes([
            'Text' => ['Type' => FieldType::STRING],
        ]);
    }

    /**
     * Return activity file path
     * @return string
     */
    protected static function getFileName(): string
    {
        return __FILE__;
    }

    /**
     * @return ErrorCollection
     */
    protected function internalExecute(): ErrorCollection 
    {
        \Bitrix\Main\Loader::includeModule('crm');
        $errors = parent::internalExecute();

        // token и secret лучше передавать в виде переменных БП в активити
        $token = $this->GetVariable("TOKEN"); 
        $secret = $this->GetVariable("SECRET"); 
        // file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs/task_1.log', $token . PHP_EOL, FILE_APPEND);
        
        $dadata = new Dadata($token, $secret);
        $dadata->init();

        $fields = array("query" => $this->Inn, "count" => 5);
        $response = $dadata->suggest("party", $fields);
        
        $companyName = 'Компания не найдена!';
        $companyPhone = 'отсутствует';
        $companyAddress = 'отсутствует';
        if(!empty($response['suggestions'])) { // если копания найдена
            // по ИНН возвращается массив в котором может быть несколько элементов (компаний)
            $companyName = $response['suggestions'][0]['value']; // получаем имя компании из первого элемента
            $companyPhone = $response['suggestions'][0]['phones'];
            $companyAddress = $response['suggestions'][0]['data']['address']['value'];
            // file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs/task_1.log', print_r($companyName, true) . PHP_EOL, FILE_APPEND);
        }  

        // в рабочем активити необходимо будет создать отдельный метод который будет получать результат ответа сервиса Dadata, 
        // обходить циклом результат и сохранять в массив все полученные организации

        $this->preparedProperties['Text'] = $companyName;
        $this->log($this->preparedProperties['Text']);
        
        $rootActivity = $this->GetRootActivity(); // получаем объект активити
        // сохранение полученных результатов работы активити в переменную бизнес процесса
        $rootActivity->SetVariable("INN", $this->preparedProperties['Text']); 

        //id ответственного и уведомителя
        $responsible = CurrentUser::get()->getId();

        // создаем компанию
        $arNewCompany = array(
            "TITLE" => $companyName,
            "OPENED" => "Y",
            "COMPANY_TYPE" => "CUSTOMER",
            "ASSIGNED_BY_ID" => $responsible,
        );
        $arNewCompany['FM']['PHONE'] = array(
            "n0" => array(
                "VALUE_TYPE" => "WORK",
                "VALUE" => $companyPhone,
            )
        );
        $arNewCompany['FM']['EMAIL'] = array(
            "n0" => array(
                "VALUE_TYPE" => "WORK",
                "VALUE" => $companyAddress,
            )
        );
        $company = new CCrmCompany(false);
        $companyID = $company->Add($arNewCompany);

        return $errors;
    }

    /**
     * @param PropertiesDialog|null $dialog
     * @return array[]
     */
    public static function getPropertiesDialogMap(?PropertiesDialog $dialog = null): array
    {
        $map = [
            'Inn' => [
                'Name' => Loc::getMessage('SEARCHBYINN_ACTIVITY_FIELD_SUBJECT'),
                'FieldName' => 'inn',
                'Type' => FieldType::STRING,
                'Required' => true,
                'Options' => [],
            ],
        ];
        return $map;
    }

}