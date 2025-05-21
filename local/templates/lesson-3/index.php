<?php
    require ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    require ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
    /** @global $APPLICATION */
    $APPLICATION->SetTitle('Lesson-3');
    $templatePath = \Bitrix\Main\Application::getDocumentRoot() . __DIR__;
    // echo $templatePath . PHP_EOL;
    // echo DOMAIN_PORTAL;
    $APPLICATION->SetAdditionalCSS('assets/css/index.css');

    use Bitrix\Main\Web\HttpClient;

    // Создаем экземпляр HttpClient
    $httpClient = new HttpClient();

    // Устанавливаем параметры (необязательно)
    $httpClient->setTimeout(30); // Таймаут в секундах
    $httpClient->setStreamTimeout(60); // Таймаут потока в секундах
    $httpClient->setHeader('Content-Type', 'application/json', true); // Устанавливаем заголовок
    // $httpClient->setAuthorization(LOGIN_PORTAL, PASSWORD_PORTAL);

    $response = $httpClient->get('https://' . DOMAIN_PORTAL . '/local/templates/lesson-3/api/doctors/getDoctors.php');

    if ($httpClient->getStatus() == 200) {
        $dataDoctors = json_decode($httpClient->getResult(), true);
    } else {
        $dataDoctors = json_encode([
            'error' => true,
            'status' => $httpClient->getStatus(),
            'message' => 'Ошибка при запросе данных',
            'details' => $httpClient->getError()
        ], JSON_UNESCAPED_UNICODE);
    }

    // echo $_SERVER["DOCUMENT_ROOT"] . "/local/templates/lesson-3/api/doctors/setDoctors.php";
    

    if(isset($_GET['doctorName']) && isset($_GET['doctorSpecialty'])) {

        $doctorSpecialty = trim($_GET['doctorSpecialty'] ?? '');
        $nameDoctor = trim($_GET['doctorName'] ?? '');

        echo $nameDoctor . PHP_EOL;
        echo $doctorSpecialty . PHP_EOL;

        $response = $httpClient->get('https://' . DOMAIN_PORTAL . "/local/templates/lesson-3/api/doctors/setDoctors.php?name={$nameDoctor}&specialty={$doctorSpecialty}");
        if ($httpClient->getStatus() == 200) {
            $setDoctors = json_decode($httpClient->getResult(), true);
            print_r($setDoctors);
        } else {
            $setDoctors = json_encode([
                'error' => true,
            ]);
        }

        // pr($setDoctors);
        header("Location: " . $_SERVER['PHP_SELF']);
    }

    if(isset($_GET['idProcedureName']) && isset($_GET['idDoctor'])) {
        $idProcedure = trim($_GET['idProcedureName'] ?? '');
        $idDoctor = trim($_GET['idDoctor'] ?? '');
        $response = $httpClient->get('https://' . DOMAIN_PORTAL . "/local/templates/lesson-3/api/doctors/setSpecialization.php?idProcedure={$idProcedure}&idDoctor={$idDoctor}");
        
        if ($httpClient->getStatus() == 200) {
            $setDoctors = json_decode($httpClient->getResult(), true);
        } else {
            $setDoctors = json_encode([
                'error' => true,
            ]);
        }

        // pr($setDoctors);
        header("Location: " . $_SERVER['PHP_SELF']);

    }


?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Врачи</title>
</head>
<script>
    window.doctors = <?=json_encode($dataDoctors, JSON_UNESCAPED_UNICODE)?>;
</script>
<link rel="stylesheet" href="<?= 'assets/css/index.css' ?>">
<body>
    <div class="container">
        
        <!-- Форма добавления врача -->
        <div class="form-section">
            <h2>Добавить врача</h2>
            <form id="addDoctorForm" action="index.php" method="get">
                <div class="form-group">
                    <label for="doctorName">ФИО врача:</label>
                    <input type="text" id="doctorName" name="doctorName" required>
                </div>
                <div class="form-group">
                    <label for="doctorSpecialty">Специальность:</label>
                    <input type="text" id="doctorSpecialty" name="doctorSpecialty" required>
                </div>
                <button type="text" class="btn">Добавить врача</button>
            </form>
        </div>
        <!-- <?php if (!empty($_GET['message'])): ?>
            <div class="alert <?= strpos($_GET['message'], 'Ошибка') === false ? 'alert-success' : 'alert-error' ?>">
                <?= htmlspecialchars(urldecode($_GET['message'])) ?>
            </div>
        <?php endif; ?>  -->
        
        <!-- Форма добавления процедуры -->
        <div class="form-section">
            <h2>Добавить процедуру</h2>
            <form id="addProcedureForm" action="index.php" method="get">
                <div class="form-group">
                    <label for="procedureName">Название процедуры:</label>

                    <select id="procedureName" name="idProcedureName" required>
                        <option value="">-- Выберите процедуру --</option>
                    </select>

                </div>
                <div class="form-group">
                    <label for="procedureDoctor">Ответственный врач:</label>
                    <select id="procedureDoctor" name="idDoctor" required>
                        <option value="">-- Выберите врача --</option>
                    </select>
                </div>
                <button type="submit" class="btn">Добавить процедуру</button>
            </form>
        </div>
    </div>


    <h1 style="text-align: center; margin-bottom: 40px; color: var(--primary-color);">Наши специалисты</h1>
    
    <div class="doctors-container" id="doctorsContainer">
        <!-- Данные будут вставлены через JavaScript -->
    </div>

    <script>
        const doctorsData = window.doctors;
        function renderBloksDoctors() {
            const container = document.getElementById('doctorsContainer');
            
            if (!doctorsData.doctors || Object.keys(doctorsData.doctors).length === 0) {
                container.innerHTML = '<div class="no-doctors">Врачи не найдены</div>';
                return;
            }
            
            let html = '';
            
            for (const [id, doctor] of Object.entries(doctorsData.doctors)) {
                html += `
                    <div class="doctor-card" onclick="toggleProcedures(this)">
                        <div class="doctor-header">
                            <div class="doctor-name">${doctor.doctor}</div>
                            <div class="doctor-specialization">${doctor.specialization}</div>
                        </div>
                        <div class="doctor-procedures">
                            <ul class="procedure-list">
                                ${doctor.procedure && doctor.procedure.map(proc => `<li class="procedure-item">${proc}</li>`).join('')}
                            </ul>
                        </div>
                    </div>
                `;
            }
            container.innerHTML = html;
        }
        
        function toggleProcedures(element) {
            const target = element.querySelector(".doctor-procedures");
            console.log(target);
            if(target.style.display == 'block') {
                target.style.display = 'none';
            } else {
                target.style.display = 'block';
            }
        }

        function renderSelectOptions(data, targetElementId, optionTextTemplate) {
            const selectElement = document.getElementById(targetElementId);
            selectElement.innerHTML = '';

            for (const id in data) {
                const item = data[id];
                console.log(item);
                const option = document.createElement('option');
                option.value = id;
                
                option.textContent = optionTextTemplate(item);
                selectElement.appendChild(option);
            }
        }

        renderSelectOptions(
            doctorsData.procedure, 
            'procedureName', 
            (procedure) => `${procedure}`
        );

        renderSelectOptions(
            doctorsData.doctors, 
            'procedureDoctor', 
            (doctor) => `${doctor.doctor} (${doctor.specialization})`
        );
        
        document.addEventListener('DOMContentLoaded', function() {
            [renderBloksDoctors].forEach(func => func());
        });

    </script>
</body>
</html>
