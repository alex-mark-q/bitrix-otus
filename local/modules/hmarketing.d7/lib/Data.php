<?
// пространство имен модуля
namespace Hmarketing\d7;

// пространство имен для ORM
use \Bitrix\Main\Entity;
// пространство имен для кеша
use \Bitrix\Main\Application;

// сущность ORM унаследованная от DataManager
class DataTable extends Entity\DataManager
{
	// название таблицы в базе данных, если не указывать данную функцию, то таблица в бд сформируется автоматически из неймспейса
	public static function getTableName()
	{
		return "pop_up_table";
	}

	// подключение к БД, если не указывать, то будет использовано значение по умолчанию подключения из файла .settings.php. Если указать, то можно выбрать подключение, которое может быть описано в .setting.php
	public static function getConnectionName()
	{
		return "default";
	}

	// метод возвращающий структуру ORM-сущности
	public static function getMap()
	{
		/*
         * Типы полей: 
         * DatetimeField - дата и время
         * DateField - дата
         * BooleanField - логическое поле да/нет
         * IntegerField - числовой формат
         * FloatField - числовой дробный формат
         * EnumField - список, можно передавать только заданные значения
         * TextField - text
         * StringField - varchar
         */

		return array(
			// ID
			new Entity\IntegerField(
				// имя сущности
				"ID",
				array(
					// первичный ключ
					"primary" => true,
					// AUTO INCREMENT
					"autocomplete" => true,
				)
			),
			// активность
			new Entity\BooleanField(
				'ACTIVE',
				array(
					"values" => array('N', 'Y')
				)
			),
			// cайты
			new Entity\StringField(
				// имя сущности
				"SITE",
				array(
					// обязательное поле
					"required" => true,
				)
			),
			// ссылка перехода
			new Entity\StringField(
				// имя сущности
				"LINK",
				array(
					// обязательное поле
					"required" => true,
				)
			),
			// ссылка на картинку
			new Entity\StringField(
				// имя сущности
				"LINK_PICTURE",
				array(
					// имя колонки в таблице
					"column_name" => "LINK_PICTURE_CODE",
					// если необходима валидация поля, то используем массив валидации, можем передать сколько угодно валидаторов, использовать как штатные, так и самописные
					"validation" => function () {
						return array(
							// первым укажем штаный валидатор проверки на уникальность поля
							new Entity\Validator\Unique,
							// вторым напишем свою функцию, которая проверит на длину строку. Аргументы функции: value - значение поля, primary - массив с первичным ключом, row - весь массив данных, переданный в ::add или ::update, field - объект валидируемого поля - Entity\StringField('LINK_PICTURE', ...)
							function ($value, $primary, $row, $field) {
								if (strlen($value) <= 100)
									return true;
								else
									return "Код LINK_PICTURE должен содержать не более 100 символов";
							}
						);
					}
				)
			),
			// описание картинки
			new Entity\StringField(
				// имя сущности
				"ALT_PICTURE",
				array(
					// обязательное поле
					"required" => true,
				)
			),
			// исключения
			new Entity\TextField(
				// имя сущности
				"EXCEPTIONS"
			),
			// дата и время заполнения
			new Entity\DatetimeField(
				// имя сущности
				"DATE",
				array(
					'required' => true,
				)
			),
			// затемнение баннера
			new Entity\EnumField(
				// имя сущности
				"TARGET",
				array(
					// значения доступные для записи
					"values" => array('self', 'blank'),
					// обязательное поле
					"required" => true,
				)
			),
			// поле для хранения айди автора, информация о которых будет храниться в другой таблице, свяжем данную таблицу с другой
			new Entity\IntegerField(
				// имя сущности
				"AUTHOR_ID"
			),
			// для связи двух таблиц, нужно будет создать поле зависимости, фактически такого поля нет в базе, оно является виртуальным
			new Entity\ReferenceField(
				// имя сущности
				"AUTHOR",
				// связываемая сущность другой таблицы
				'\Hmarketing\d7\AuthorTable',
				// this - текущая сущность, ref - связываемая    
				array("=this.AUTHOR_ID" => "ref.ID")
			),
		);
	}

	// // события можно задавать прямо в ORM-сущности, для примера запретим изменять поле LINK_PICTURE
	// public static function onBeforeUpdate(Entity\Event $event)
	// {
	// 	$result = new Entity\EventResult;
	// 	$data = $event->getParameter("fields");
	// 	if (isset($data["LINK_PICTURE"])) {
	// 		$result->addError(
	// 			new Entity\FieldError(
	// 				$event->getEntity()->getField("LINK_PICTURE"),
	// 				"Запрещено менять LINK_PICTURE код у баннера"
	// 			)
	// 		);
	// 	}
	// 	return $result;
	// }

	// очистка тегированного кеша при добавлении
	public static function onAfterAdd(Entity\Event $event)
	{
		DataTable::clearCache();
	}
	// очистка тегированного кеша при изменении
	public static function onAfterUpdate(Entity\Event $event)
	{
		DataTable::clearCache();
	}
	// очистка тегированного кеша при удалении
	public static function onAfterDelete(Entity\Event $event)
	{
		DataTable::clearCache();
	}
	// основной метод очистки кеша по тегу
	public static function clearCache()
	{
		// служба пометки кеша тегами
		$taggedCache = Application::getInstance()->getTaggedCache();
		$taggedCache->clearByTag('popup');
	}
}
