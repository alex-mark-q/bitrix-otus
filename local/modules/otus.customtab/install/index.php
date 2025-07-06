<?php

use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\SystemException;
use Bitrix\Main\IO\InvalidPathException;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\LoaderException;
use Otus\Customtab\Orm\HospitalTable;
use Otus\Customtab\Data\TestDataInstaller;

Loc::getMessage(__FILE__);

class otus_customtab extends CModule
{
    public $MODULE_ID = 'otus.customtab';
    public $MODULE_SORT = 500;
    public $MODULE_VERSION;
    public $MODULE_DESCRIPTION;
    public $MODULE_VERSION_DATE;
    public $PARTNER_NAME;
    public $PARTNER_URI;

    public function __construct()
    {
        $arModuleVersion = [];
        include __DIR__ . '/version.php';
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_DESCRIPTION = Loc::getMessage('CRMOTUSTAB_INSTALL_MODULE_DESCRIPTION');
        $this->MODULE_NAME = Loc::getMessage('CRMOTUSTAB_INSTALL_MODULE_NAME');
        $this->PARTNER_NAME = Loc::getMessage('CRMOTUSTAB_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('CRMOTUSTAB_PARTNER_URI');
    }

    /**
     * @throws SystemException
     */
    public function DoInstall(): void
    {
        if ($this->isVersionD7()) {
            ModuleManager::registerModule($this->MODULE_ID);
            $this->InstallFiles();
            $this->InstallDB();
            $this->InstallEvents();

        } else {
            throw new SystemException(Loc::getMessage('CRMOTUSTAB_INSTALL_ERROR_VERSION'));
        }
    }

    /**
     * @throws SqlQueryException
     * @throws LoaderException
     * @throws InvalidPathException
     */
    public function DoUninstall(): void
    {
        $this->UnInstallFiles();
        $this->UnInstallDB();
        $this->UnInstallEvents();

        \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * @throws InvalidPathException
     */
    public function InstallFiles($params = []): void
    {
        $component_path = $this->getPath() . '/install/components';

        if (Directory::isDirectoryExists($component_path)) {
            CopyDirFiles($component_path, $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components', true, true);
        } else {
            throw new InvalidPathException($component_path);
        }
    }

    public function InstallDB(): void
    {
        Loader::includeModule($this->MODULE_ID);

        $entities = $this->getEntities();

        foreach ($entities as $entity) {
            // pr($entity::getTableName());
            if (!Application::getConnection($entity::getConnectionName())->isTableExists($entity::getTableName())) {
                Base::getInstance($entity)->createDbTable();
            }
        }
        $this->installManyToManyTable();

        foreach ($entities as $entity) {
            // pr($entity);
            try {
                $this->addEntityElements($entity);
            } catch (\Exception $e) {
                pr($e);
            }
            
        }
    }

    public function InstallEvents(): void
    {
        $eventManager = EventManager::getInstance();

        $eventManager->registerEventHandler(
            'crm',
            'onEntityDetailsTabsInitialized',
            $this->MODULE_ID,
            '\\Otus\\Customtab\\Crm\\Handlers',
            'updateTabs'
        );
    }

    public function UnInstallEvents(): void
    {
        $eventManager = EventManager::getInstance();

        $eventManager->unRegisterEventHandler(
            'crm',
            'onEntityDetailsTabsInitialized',
            $this->MODULE_ID,
            '\\Otus\\Customtab\\Crm\\Handlers',
            'updateTabs'
        );
    }

    /**
     * @throws SqlQueryException
     * @throws LoaderException
     */
    public function UnInstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        $connection = \Bitrix\Main\Application::getConnection();

        $entities = $this->getEntities();
        // pr($entities);
        $this->unInstallManyToManyTable();

        foreach ($entities as $entity) {
            // pr($entity::getTableName());
            if (Application::getConnection($entity::getConnectionName())->isTableExists($entity::getTableName())) {
                $connection->dropTable($entity::getTableName());
            }
        }
    }

    /**
     * Удаляет файлы, установленные компонентом
     * @throws InvalidPathException
     */
    public function UninstallFiles(): void
    {
        $component_path = $this->getPath() . '/install/components';

        if (Directory::isDirectoryExists($component_path)) {
            $installed_components = new \DirectoryIterator($component_path);
            foreach ($installed_components as $component) {
                if ($component->isDir() && !$component->isDot()) {
                    $target_path = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/' . $component->getFilename();
                    if (Directory::isDirectoryExists($target_path)) {
                        Directory::deleteDirectory($target_path);
                    }
                }
            }
        } else {
            throw new InvalidPathException($component_path);
        }
    }
    /**
     * Добавляет тестовые данные в указанную таблицу базы данных.
     * 
     * В зависимости от переданного класса сущности вызывает соответствующий метод
     * класса TestDataInstaller для добавления тестовых записей.
     * 
     * @param string $entityClass Полное имя класса таблицы сущности (включая namespace),
     *                            для которой нужно добавить тестовые данные.
     *                            Поддерживаемые классы: HospitalTable::class.
     * 
     * @return void
     * 
     * @example
     * addEntityElements(HospitalTable::class);  // Добавит тестовые данные - больницы
     */
    private function addEntityElements(string $entityClass): void
    {
        if ($entityClass === HospitalTable::class) {
            TestDataInstaller::addHospital();
        }
    }

    private function installManyToManyTable(): void
    {
        $connection = Application::getConnection();
        $tableName = 'otus_doctors_hospital';

        if (!$connection->isTableExists($tableName)) {
            $connection->queryExecute("
            create table {$tableName} (
                id INTEGER NOT NULL auto_increment PRIMARY KEY,
                hospital_name VARCHAR(50),
                city VARCHAR(50),
                doctor_id TINYINT(4),
                address VARCHAR(50)
            );
        ");
        }
    }

    /**
     * @throws SqlQueryException
     */
    private function unInstallManyToManyTable(): void
    {
        $connection = Application::getConnection();
        $tableName = 'otus_doctors_hospital';

        if ($connection->isTableExists($tableName)) {
            $connection->dropTable($tableName);
        }
    }

    private function getEntities(): array
    {
        return [
            HospitalTable::class,
        ];
    }

    public function getPath($notDocumentRoot = false): string
    {
        if ($notDocumentRoot) {
            return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
        } else {
            return dirname(__DIR__);
        }
    }

    public function isVersionD7()
    {
        return CheckVersion(ModuleManager::getVersion('main'), '20.00.00');
    }
}
