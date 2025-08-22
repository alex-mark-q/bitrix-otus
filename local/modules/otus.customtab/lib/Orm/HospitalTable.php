<?php
namespace Otus\Customtab\Orm;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Fields\Relations\OneToMany;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;
use Bitrix\Main\Entity\Query\Join;

/**
 * Class HospitalTable
 * 
 * Fields:
 * <ul>
 * <li> id int mandatory
 * <li> hospital_name string(50) optional
 * <li> city string(50) optional
 * <li> doctor_id int optional
 * </ul>
 *
 * @package Bitrix\Doctors
 **/

class HospitalTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'otus_doctors_hospital';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return [
			'ID' => (new IntegerField('id',
					[]
				))->configureTitle(Loc::getMessage('HOSPITAL_ENTITY_ID_FIELD'))
						->configurePrimary(true)
						->configureAutocomplete(true)
			,
			'HOSPITAL_NAME' => (new StringField('hospital_name',
					[
						'validation' => function()
						{
							return[
								new LengthValidator(null, 50),
							];
						},
					]
				))->configureTitle(Loc::getMessage('HOSPITAL_ENTITY_HOSPITAL_NAME_FIELD'))
			,
			'CITY' => (new StringField('city',
					[
						'validation' => function()
						{
							return[
								new LengthValidator(null, 50),
							];
						},
					]
				))->configureTitle(Loc::getMessage('HOSPITAL_ENTITY_CITY_FIELD'))
			,
			'DOCTOR_ID' => (new IntegerField('doctor_id',
					[]
				))->configureTitle(Loc::getMessage('HOSPITAL_ENTITY_DOCTOR_ID_FIELD'))
			,
		];
	}
}