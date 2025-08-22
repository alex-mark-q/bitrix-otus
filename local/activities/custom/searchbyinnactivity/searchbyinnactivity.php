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
            $companyName = $response['suggestions'][0]['data']['name']['short_with_opf']; 
            $companyPhone = !empty($response['suggestions'][0]['data']['phones'][0]['value']) 
                ? $response['suggestions'][0]['data']['phones'][0]['value'] 
                : '';
            $companyAddress = $response['suggestions'][0]['data']['address']['value']; // "г Санкт-Петербург, Лахтинский пр-кт, д 2 к 3 стр 1"
            // file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs/task_1.log', print_r($response['suggestions'][0], true) . PHP_EOL, FILE_APPEND);
        }  
        
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
                "VALUE" => "test@test.ru",
            )
        );
        try {
            $company = new CCrmCompany(false);
            $companyID = $company->Add($arNewCompany, $bUpdateSearch = true, $arOptions = [
                /**
                 * ID пользователя, от лица которого выполняется действие
                 * в том числе проверка прав
                 * @var integer
                 */
                'CURRENT_USER' => \CCrmSecurityHelper::GetCurrentUserID(),

                /**
                 * Устанавливайте флаг, только если сущность проходит
                 * процедуру восстановления. В случае если флаг есть
                 * можно заполнять технические поля DATE_CREATE, DATE_MODIFY
                 * @var boolean
                 */
                // 'IS_RESTORATION' => true,
            ]);
        }catch (\Exception $exception) {
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs/crm_error.log', $exception->getMessage() . PHP_EOL, FILE_APPEND);
            throw $exception;
        }

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