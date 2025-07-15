<?php
namespace Otus\Models;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;

use Otus\Models\HospitalTable as Hospital;

/**
 * Class DoctorsTable
 * 
 * Fields:
 * <ul>
 * <li> id int mandatory
 * <li> characters text optional
 * <li> doctor_id int optional
 * </ul>
 *
 * @package Bitrix\About
 **/

class DoctorsTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'otus_about_doctors';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return [
			'id' => (new IntegerField('id',
					[]
				))->configureTitle(Loc::getMessage('DOCTORS_ENTITY_ID_FIELD'))
						->configurePrimary(true)
						->configureAutocomplete(true)
			,
			'characters' => (new TextField('characters',
					[]
				))->configureTitle(Loc::getMessage('DOCTORS_ENTITY_CHARACTERS_FIELD'))
			,
			'doctor_id' => (new IntegerField('doctor_id',
					[]
				))->configureTitle(Loc::getMessage('DOCTORS_ENTITY_DOCTOR_ID_FIELD'))
			,
		];
	}
}