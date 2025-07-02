<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */

\Bitrix\Main\Page\Asset::getInstance()->addJs('/bitrix/js/testlist.userfield/testlist_userfield.js');
\Bitrix\Main\Page\Asset::getInstance()->addCss('/bitrix/js/testlist.userfield/testlist_userfield.css');

$values = $arResult['value'];

// Компонент всегда оборачивает значение в массив.
// Если поле не множественное и не имеет значения, массив будет пустым или содержать пустую строку.
// Добавим один пустой элемент, чтобы цикл отрисовал хотя бы одно поле для заполнения.
if ($arParams['arUserField']['MULTIPLE'] === 'N') {
    if (empty($values) || (isset($values[0]) && empty($values[0]))) {
        $values = [''];
    }
}
// Если поле множественное и пустое, так же добавим один пустой элемент,
// чтобы было что клонировать для добавления новых значений.
if ($arParams['arUserField']['MULTIPLE'] === 'Y' && empty($values)) {
    $values[] = '';
}
?>
<div class="testlist-field-wrapper">
    <?php foreach ($values as $key => $value): ?>
        <?php
        $data = json_decode($value, true);
        $selectedOption = $data['option'] ?? '';
        $description = $data['description'] ?? '';

        // Формируем имя поля в зависимости от того, множественное оно или нет
        $fieldName = $arParams['arUserField']['FIELD_NAME'];
        $currentFieldName = ($arParams['arUserField']['MULTIPLE'] === 'Y') ? ($fieldName . '[' . $key . ']') : $fieldName;
        ?>
        <div class="testlist-field-container" style="margin-bottom: 10px;">
            <select name="<?= $currentFieldName ?>_option" class="testlist-select">
                <option value="">Выберите пункт</option>
                <option value="option1"<?= $selectedOption === 'option1' ? ' selected' : '' ?>>Пункт 1</option>
                <option value="option2"<?= $selectedOption === 'option2' ? ' selected' : '' ?>>Пункт 2</option>
                <option value="option3"<?= $selectedOption === 'option3' ? ' selected' : '' ?>>Пункт 3</option>
            </select>

            <input type="text"
                   name="<?= $currentFieldName ?>_description"
                   class="testlist-description"
                   placeholder="Введите описание..."
                   value="<?= htmlspecialcharsbx($description) ?>"
                   style="margin-left: 10px; display: <?= in_array($selectedOption, ['option1', 'option2']) ? 'inline-block' : 'none' ?>;" />

            <input type="hidden" name="<?= $currentFieldName ?>" value="<?= htmlspecialcharsbx($value) ?>" class="testlist-hidden-value" />
        </div>
    <?php endforeach; ?>
</div>

<script>
    BX.ready(function() {
        if(typeof TestListUserField !== 'undefined') {
            TestListUserField.init();
        }
    });
</script> 