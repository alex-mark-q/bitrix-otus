<?php

    namespace Otus\Models;

    use Bitrix\Main\Localization\Loc;
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Fields\BooleanField;
    use Bitrix\Main\ORM\Fields\DatetimeField;
    use Bitrix\Main\ORM\Fields\FloatField;
    use Bitrix\Main\ORM\Fields\IntegerField;
    use Bitrix\Main\ORM\Fields\StringField;
    use Bitrix\Main\ORM\Fields\Validators\LengthValidator;

    /**
     * Class CurrencyTable
     * 
     * Fields:
     * <ul>
     * <li> CURRENCY string(3) mandatory
     * <li> AMOUNT_CNT int optional default 1
     * <li> AMOUNT double optional
     * <li> SORT int optional default 100
     * <li> DATE_UPDATE datetime mandatory
     * <li> NUMCODE string(3) optional
     * <li> BASE bool ('N', 'Y') optional default 'N'
     * <li> CREATED_BY int optional
     * <li> DATE_CREATE datetime optional
     * <li> MODIFIED_BY int optional
     * <li> CURRENT_BASE_RATE double optional
     * </ul>
     *
     * @package Bitrix\Catalog
     **/

    class CurrencyTable extends DataManager
    {
        /**
         * Returns DB table name for entity.
         *
         * @return string
         */
        public static function getTableName()
        {
            return 'b_catalog_currency';
        }

        /**
         * Returns entity map definition.
         *
         * @return array
         */
        public static function getMap()
        {
            return [
                'CURRENCY' => (new StringField('CURRENCY',
                        [
                            'validation' => function()
                            {
                                return[
                                    new LengthValidator(null, 3),
                                ];
                            },
                        ]
                    ))->configureTitle(Loc::getMessage('CURRENCY_ENTITY_CURRENCY_FIELD'))
                            ->configurePrimary(true)
                ,
                'AMOUNT_CNT' => (new IntegerField('AMOUNT_CNT',
                        []
                    ))->configureTitle(Loc::getMessage('CURRENCY_ENTITY_AMOUNT_CNT_FIELD'))
                            ->configureDefaultValue(1)
                ,
                'AMOUNT' => (new FloatField('AMOUNT',
                        []
                    ))->configureTitle(Loc::getMessage('CURRENCY_ENTITY_AMOUNT_FIELD'))
                ,
                'SORT' => (new IntegerField('SORT',
                        []
                    ))->configureTitle(Loc::getMessage('CURRENCY_ENTITY_SORT_FIELD'))
                            ->configureDefaultValue(100)
                ,
                'DATE_UPDATE' => (new DatetimeField('DATE_UPDATE',
                        []
                    ))->configureTitle(Loc::getMessage('CURRENCY_ENTITY_DATE_UPDATE_FIELD'))
                            ->configureRequired(true)
                ,
                'NUMCODE' => (new StringField('NUMCODE',
                        [
                            'validation' => function()
                            {
                                return[
                                    new LengthValidator(null, 3),
                                ];
                            },
                        ]
                    ))->configureTitle(Loc::getMessage('CURRENCY_ENTITY_NUMCODE_FIELD'))
                ,
                'BASE' => (new BooleanField('BASE',
                        []
                    ))->configureTitle(Loc::getMessage('CURRENCY_ENTITY_BASE_FIELD'))
                            ->configureValues('N', 'Y')
                            ->configureDefaultValue('N')
                ,
                'CREATED_BY' => (new IntegerField('CREATED_BY',
                        []
                    ))->configureTitle(Loc::getMessage('CURRENCY_ENTITY_CREATED_BY_FIELD'))
                ,
                'DATE_CREATE' => (new DatetimeField('DATE_CREATE',
                        []
                    ))->configureTitle(Loc::getMessage('CURRENCY_ENTITY_DATE_CREATE_FIELD'))
                ,
                'MODIFIED_BY' => (new IntegerField('MODIFIED_BY',
                        []
                    ))->configureTitle(Loc::getMessage('CURRENCY_ENTITY_MODIFIED_BY_FIELD'))
                ,
                'CURRENT_BASE_RATE' => (new FloatField('CURRENT_BASE_RATE',
                        []
                    ))->configureTitle(Loc::getMessage('CURRENCY_ENTITY_CURRENT_BASE_RATE_FIELD'))
                ,
            ];
        }
    }