<?php
/**
 * Демонстрационный скрипт для тестирования пользовательского поля
 * "Тестовый список с описанием"
 * 
 * Этот файл можно использовать для быстрого тестирования работы модуля
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Тест пользовательского поля");

// Проверяем, что модуль установлен
if (!\Bitrix\Main\Loader::includeModule('testlist.userfield')) {
    echo "<p style='color: red;'>Модуль testlist.userfield не установлен!</p>";
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
    exit;
}

echo "<h1>Тест пользовательского поля 'Тестовый список с описанием'</h1>";

// Создаем экземпляр класса для тестирования
$userFieldType = new \TestList\UserField\TestListWithDescriptionType();

// Тестовые данные
$arUserField = [
    'FIELD_NAME' => 'UF_TEST_LIST',
    'ENTITY_ID' => 'USER',
    'USER_TYPE_ID' => 'testlist_with_description',
    'MANDATORY' => 'N',
    'EDIT_FORM_LABEL' => 'Тестовое поле',
    'LIST_COLUMN_LABEL' => 'Тестовое поле',
    'LIST_FILTER_LABEL' => 'Тестовое поле',
    'SETTINGS' => []
];

// Тестовые значения
$testValues = [
    '',
    '{"option":"option1","description":"Описание для пункта 1"}',
    '{"option":"option2","description":"Описание для пункта 2"}',
    '{"option":"option3","description":""}',
];

echo "<h2>1. Информация о типе поля</h2>";
$description = $userFieldType::getUserTypeDescription();
echo "<pre>";
print_r($description);
echo "</pre>";

echo "<h2>2. Тест отображения поля в форме редактирования</h2>";
foreach ($testValues as $i => $value) {
    echo "<h3>Тест " . ($i + 1) . " (значение: " . ($value ?: 'пустое') . ")</h3>";
    
    $arHtmlControl = [
        'NAME' => 'UF_TEST_LIST',
        'VALUE' => $value
    ];
    
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
    echo $userFieldType->GetEditFormHTML($arUserField, $arHtmlControl);
    echo "</div>";
}

echo "<h2>3. Тест отображения в списке</h2>";
foreach ($testValues as $i => $value) {
    if (empty($value)) continue;
    
    echo "<h4>Значение " . ($i + 1) . ":</h4>";
    
    $arHtmlControl = [
        'NAME' => 'UF_TEST_LIST',
        'VALUE' => $value
    ];
    
    $listView = $userFieldType->GetAdminListViewHTML($arUserField, $arHtmlControl);
    echo "<div style='border: 1px solid #ddd; padding: 5px; background: #f9f9f9;'>";
    echo $listView ?: '<em>Пустое значение</em>';
    echo "</div>";
}

echo "<h2>4. Тест настроек поля</h2>";
$arHtmlControl = ['NAME' => 'SETTINGS'];
echo "<table border='1' cellpadding='5'>";
echo $userFieldType->GetSettingsHTML($arUserField, $arHtmlControl, false);
echo "</table>";

?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

h1 {
    color: #0066cc;
    border-bottom: 2px solid #0066cc;
    padding-bottom: 10px;
}

h2 {
    color: #333;
    margin-top: 30px;
}

h3, h4 {
    color: #666;
}

pre {
    background: #f5f5f5;
    border: 1px solid #ddd;
    padding: 10px;
    overflow-x: auto;
}

.testlist-field-container {
    background: #fff;
}
</style>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?> 