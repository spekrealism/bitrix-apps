<?php
use Bitrix\Main\Localization\Loc;

if (!check_bitrix_sessid()) return;

echo CAdminMessage::ShowNote(Loc::getMessage('TESTLIST_UNINSTALL_SUCCESS'));
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
    <input type="hidden" name="lang" value="<?echo LANG?>">
    <input type="submit" name="" value="<?echo Loc::getMessage('TESTLIST_BACK_TO_LIST')?>">
</form> 