<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
?>
<span class="fields string">
<?php
$first = true;
foreach ($arResult["~VALUE"] as $val)
{
    // Пропускаем пустые значения
    if ($val === '' || $val === null) continue;

    if (!$first)
    {
        echo '<span class="fields separator"></span>';
    }
    else
    {
        $first = false;
    }

    $data = json_decode($val, true);
    if (!$data) continue;

    // Определяем текст пункта
    switch ($data['option']) {
        case 'option1':
            $optionText = 'Пункт 1';
            break;
        case 'option2':
            $optionText = 'Пункт 2';
            break;
        case 'option3':
            $optionText = 'Пункт 3';
            break;
        default:
            $optionText = '';
    }

    if ($optionText === '') continue;

    $displayValue = $optionText;
    if (!empty($data['description']) && in_array($data['option'], ['option1', 'option2']))
    {
        $displayValue .= ' (' . htmlspecialcharsbx($data['description']) . ')';
    }

    echo '<span class="fields string">' . htmlspecialcharsbx($displayValue) . '</span>';
}
?>
</span> 