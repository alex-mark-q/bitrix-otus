<?php
namespace Otus\Customtab\Data;

use Bitrix\Main\SystemException;
use Bitrix\Main\Result;
use Otus\Customtab\Orm\HospitalTable;

class TestDataInstaller
{
    public static function addHospital(): void
    {
        $hospitals = [
            [
                'hospital_name' => 'Боткинская больница',
                'city' => 'Москва',
                'doctor_id' => 29
            ],
            [
                'hospital_name' => 'Городская клиническая больница №1',
                'city' => 'Москва',
                'doctor_id' => 34
            ],
            [
                'hospital_name' => 'Скандинавский медицинский центр',
                'city' => 'Санкт-Петербург',
                'doctor_id' => 42
            ],
            [
                'hospital_name' => 'Клиническая больница №122',
                'city' => 'Санкт-Петербург',
                'doctor_id' => 56
            ],
            [
                'hospital_name' => 'Центральная клиническая больница',
                'city' => 'Новосибирск',
                'doctor_id' => 18
            ]
        ];

        foreach ($hospitals as $hospitalData) {
            HospitalTable::add($hospitalData);
        }
    }
    public static function addTest():void {}
}