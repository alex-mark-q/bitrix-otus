<?
// пространство имен модуля
namespace Hmarketing\d7;

// пространство имен для ORM
use \Bitrix\Main\Entity;

// вторая таблица, с которой связана первая, хранит информацию об авторе

class AuthorTable extends Entity\DataManager
{

    public static function getTableName()
    {
        return "pop_up_authors";
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField(
                "ID",
                array(
                    "primary" => true,
                    "autocomplete" => true,
                )
            ),
            new Entity\StringField(
                "NAME",
                array(
                    "required" => true,
                )
            ),
            new Entity\StringField("LAST_NAME")
        );
    }
}
