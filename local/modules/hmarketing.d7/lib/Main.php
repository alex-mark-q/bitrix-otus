<?
// пространство имен модуля
namespace Hmarketing\d7;

// пространство имен для подключения класса с ORM
use \Hmarketing\d7\DataTable;
// пространство имен для получения данных сущности таблицы по событиям
use \Bitrix\Main\Entity\Event;

// основной класс модуля
class Main
{
    // метод для получения строки из таблицы базы данных
    public static function get()
    {
        // запрос к базе
        $result = DataTable::getList(
            array(
                'select' => array('*')
            )
        );
        // преобразование запроса от базы
        $row = $result->fetch();
        // распечатываем массив с ответом на экран
        print "<pre>";
        print_r($row);
        print "</pre>";
        // возвращаем ответ от баззы
        return $row;
    }
}
