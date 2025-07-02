<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Loader;

Loader::registerAutoLoadClasses('testlist.userfield', [
    '\TestList\UserField\TestListWithDescriptionType' => 'lib/userfield/testlistwithdescriptiontype.php',
]); 