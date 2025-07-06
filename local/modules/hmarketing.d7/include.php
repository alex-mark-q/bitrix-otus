<?
Bitrix\Main\Loader::registerAutoloadClasses(
	// имя модуля
	"hmarketing.d7",
	array(
		// ключ - имя класса с простанством имен, значение - путь относительно корня сайта к файлу
		"hmarketing\\d7\\Main" => "lib/Main.php",
		// файл инклудится за счет правильных имен, иначе будет ошибка при установке и удаленни модуля
		//"Hmarketing\\d7\\DataTable" => "lib/data.php",
	)
);
