<?php
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
    $APPLICATION->SetTitle('Lesson-4');


    use Otus\Models\ClientsTable as Clients;
    use Otus\Models\HospitalTable as Hospital;
    use Otus\Models\DoctorsTable as Doctors;

    // связь OneToOne
    $collectionClients = Clients::getList([
        'select' => [
            'id', 
            'first_name', 
            'doctor_id', 
            'CONTACT.*',
            'DOCTOR.*',
            'PROTSEDURA_M'=>'DOCTOR.PROTSEDURA_M.ELEMENT'
        ], 
        // 'limit'=>3
    ])->fetchCollection();

    foreach($collectionClients as $key => $record) {
        echo $record->getId().' '.$record->getFirstName().' '.$record->getDoctorId();
        // Получаем связанный контакт
        $contact = $record->get('CONTACT');
        if ($contact) {
            echo $contact->getName() . PHP_EOL; // Пример доступа к полю NAME контакта
        }
        $doctor = $record->get('DOCTOR');
        if($doctor) {
            echo $doctor->getName() . PHP_EOL;
        }

        foreach($record->getDoctor()->getProtseduraM()->getAll() as $prItem) {
            echo $prItem->getElement()->getName();
        }

    }
    // связь OneToOne END

    // связь OneToMany

    $collectionHospital = Hospital::getList([
        'select' => [
            'id',
            'hospital_name',
            'doctor_id'
        ]
    ])->fetchCollection();

    foreach ($collectionHospital as $key => $record) {
        echo "<pre>";
        echo "ID доктора: " . $record->getDoctorId() . " Название учереждения: " . $record->getHospitalName();
        echo "</pre>";
    }
    
    // связь OneToMany END

    // связь ManyToMany

    // $collectionDoctors = Clients::getList([
    //     'select' => [
    //         'id', 
    //         'doctor_id', 
    //         'hospitals' 
    //     ]
    // ])->fetchCollection();

    // foreach ($collectionDoctors as $doctor) {
    //     foreach ($doctor->getHospitals() as $hospital) {
    //         echo 'Доктор '.$doctor->getName(). ' работает в: '.$hospital->getHospitalName().'<br/>';
    //     }
    // }

    $hospitals = Hospital::getList([
        'select' => ['id', 'hospital_name', 'doctors']
    ])->fetchCollection();

    foreach ($hospitals as $hospital) {
        echo "Больница: " . $hospital->getHospitalName() . "<br>";
        
        foreach ($hospital->getDoctors() as $doctor) {
            echo "— Доктор: " . $doctor->getFirstName() . " " . $doctor->getLastName() . "<br>";
        }
    }

    // связь ManyToMany END
    