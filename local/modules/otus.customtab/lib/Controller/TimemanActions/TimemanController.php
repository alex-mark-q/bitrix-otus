<?php
namespace Otus\Customtab\Controller\TimemanActions;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Otus\CRestService\CRest;

Loader::includeModule('otus.customtab');
class TimemanController extends Controller
{
    public function configureActions()
    {
        return [
            'startDate' => [
                'prefilters' => [],
                'postfilters' => [],
            ],
        ];
    }

    public function startDateAction($action, $data, $module): array
    {
        pr("startDateAction");
        $userId = CurrentUser::get()->getId();
        // @TODO реализовать запуск рабочего дня, бработку ошибок, возврат ответа в JS
        $currentDateTime = new \Bitrix\Main\Type\DateTime();
        $currentDate = $currentDateTime->format('Y-m-d');
    
        $status = CRest::call('timeman.status', ['USER_ID' => $userId]);

        // if($status["STATUS"] === "OPENED") {

        // }

        $result = CRest::call(
            'timeman.open',
            [
                'USER_ID' => $userId,
                // 'TIME' => $currentDateTime->format(\DateTimeInterface::ATOM),
                'REPORT' => 'Забыл открыть рабочий день',
                'LAT' => 53.548841,
                'LON' => 9.987274
            ]
        );

        // Обработка ответа от Битрикс24
        if ($result['error']) {
            echo 'Error: '.$result['error_description'];
        } else {
            print_r($result['result']);
        }
        ?>
        <script>
            (function(){
                // BX.onCustomEvent("onTaskTimerChange",["action" => $action, "taskId" => $data->TASK, "data" => $module]);
            })();
        </script>
        <?php
            return [$status, $result];
    }
    public function stopDateAction($data): array
    {
        $userId = CurrentUser::get()->getId();
        // @TODO реализовать запуск рабочего дня, бработку ошибок, возврат ответа в JS

        $result = CRest::call(
            'timeman.pause',
            [
                'USER_ID' => $userId
            ]
        );

        // Обработка ответа от Битрикс24
        if ($result['error']) {
            echo 'Error: '.$result['error_description'];
        } else {
            print_r($result['result']);
        }
        return [$data, $result];
    }
    public function closeDateAction(): array
    {
        $userId = CurrentUser::get()->getId();
        // @TODO реализовать запуск рабочего дня, бработку ошибок, возврат ответа в JS
        $currentDateTime = new \Bitrix\Main\Type\DateTime();
        $currentDate = $currentDateTime->format('Y-m-d');

        $result = CRest::call(
            'timeman.close',
            [
                'USER_ID' => $userId,
                'TIME' => $currentDateTime->format(\DateTimeInterface::ATOM),
                'REPORT' => 'Забыл закрыть рабочий день',
                'LAT' => 53.548841,
                'LON' => 9.987274
            ]
        );

        // Обработка ответа от Битрикс24
        if ($result['error']) {
            echo 'Error: '.$result['error_description'];
        } else {
            print_r($result['result']);
        }
        return [$result];
    }
}
