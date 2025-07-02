<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class testlist_userfield extends CModule
{
    public $MODULE_ID = 'testlist.userfield';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;

    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__ . '/version.php');

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage('TESTLIST_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('TESTLIST_MODULE_DESCRIPTION');

        $this->PARTNER_NAME = Loc::getMessage('TESTLIST_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('TESTLIST_PARTNER_URI');
    }

    public function InstallEvents()
    {
        RegisterModuleDependences('main', 'OnUserTypeBuildList', $this->MODULE_ID, '\TestList\UserField\TestListWithDescriptionType', 'getUserTypeDescription');
        return true;
    }

    public function UnInstallEvents()
    {
        UnRegisterModuleDependences('main', 'OnUserTypeBuildList', $this->MODULE_ID, '\TestList\UserField\TestListWithDescriptionType', 'getUserTypeDescription');
        return true;
    }

    public function InstallFiles()
    {
        // Копируем JS и CSS файлы
        CopyDirFiles(__DIR__ . '/js/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/js/' . $this->MODULE_ID . '/', true, true);
        CopyDirFiles(__DIR__ . '/css/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/js/' . $this->MODULE_ID . '/', true, true);
        
        // Копируем шаблоны компонентов
        CopyDirFiles(__DIR__ . '/templates/system.field.view/', $_SERVER['DOCUMENT_ROOT'] . '/local/templates/.default/components/bitrix/system.field.view/', true, true);
        CopyDirFiles(__DIR__ . '/templates/system.field.edit/', $_SERVER['DOCUMENT_ROOT'] . '/local/templates/.default/components/bitrix/system.field.edit/', true, true);
        
        return true;
    }

    public function UnInstallFiles()
    {
        // Удаляем JS и CSS
        DeleteDirFilesEx('/bitrix/js/' . $this->MODULE_ID);

        // Удаляем шаблоны компонентов
        DeleteDirFilesEx('/local/templates/.default/components/bitrix/system.field.view/testlist_with_description/');
        DeleteDirFilesEx('/local/templates/.default/components/bitrix/system.field.edit/testlist_with_description/');

        return true;
    }

    public function DoInstall()
    {
        global $APPLICATION;

        if (!ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            $this->InstallFiles();
            $this->InstallEvents();
            ModuleManager::registerModule($this->MODULE_ID);
        }

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage('TESTLIST_INSTALL_TITLE'),
            __DIR__ . '/step.php'
        );
    }

    public function DoUninstall()
    {
        global $APPLICATION;

        $this->UnInstallEvents();
        $this->UnInstallFiles();
        ModuleManager::unRegisterModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage('TESTLIST_UNINSTALL_TITLE'),
            __DIR__ . '/unstep.php'
        );
    }
} 