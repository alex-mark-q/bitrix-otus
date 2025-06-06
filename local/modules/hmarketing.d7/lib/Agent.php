<?
// пространство имен модуля
namespace Hmarketing\d7;

// класс агента
class Agent
{
    // для примера функция пишет в папку модуля время
    static public function superAgent()
    {
        if (is_dir($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/hmarketing.d7/"))
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/hmarketing.d7/superAgentLog.txt", date("Y-m-d H:i:s"), FILE_APPEND);
        elseif (is_dir($_SERVER["DOCUMENT_ROOT"] . "/local/modules/hmarketing.d7/"))
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/local/modules/hmarketing.d7/superAgentLog.txt", date("Y-m-d H:i:s"), FILE_APPEND);
        // функция обязательно должна возвращать имя по которому вызывается, иначе битрикс её удаляет
        return "\Hmarketing\d7\Agent::superAgent();";
    }
}
