<?php

namespace TestList\UserField;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Класс пользовательского типа "Тестовый список с описанием"
 */
class TestListWithDescriptionType extends \CUserTypeString
{
    const USER_TYPE_ID = 'testlist_with_description';

    /**
     * Описание пользовательского типа
     */
    public static function getUserTypeDescription()
    {
        return [
            'USER_TYPE_ID' => self::USER_TYPE_ID,
            'CLASS_NAME' => __CLASS__,
            'DESCRIPTION' => Loc::getMessage('TESTLIST_USER_TYPE_NAME'),
            'BASE_TYPE' => 'string',
        ];
    }

    /**
     * Тип поля в БД
     */
    public static function GetDBColumnType($arUserField = [])
    {
        global $DB;
        switch (strtolower($DB->type)) {
            case 'mysql':
                return 'TEXT';
            case 'oracle':
                return 'CLOB';
            case 'mssql':
                return 'TEXT';
        }
        return 'TEXT';
    }

    /**
     * Подготовка настроек поля
     */
    public function PrepareSettings($arUserField)
    {
        return [
            'DEFAULT_VALUE' => $arUserField['SETTINGS']['DEFAULT_VALUE'] ?? '',
        ];
    }

    /**
     * HTML для настроек поля
     */
    function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm)
    {
        if ($bVarsFromForm) {
            $value = htmlspecialcharsbx($GLOBALS[$arHtmlControl['NAME']]['DEFAULT_VALUE']);
        } elseif (is_array($arUserField)) {
            $value = htmlspecialcharsbx($arUserField['SETTINGS']['DEFAULT_VALUE']);
        } else {
            $value = '';
        }

        return '
        <tr>
            <td>Значение по умолчанию:</td>
            <td>
                <select name="' . $arHtmlControl['NAME'] . '[DEFAULT_VALUE]">
                    <option value="">Не выбрано</option>
                    <option value="option1"' . ($value === 'option1' ? ' selected' : '') . '>Пункт 1</option>
                    <option value="option2"' . ($value === 'option2' ? ' selected' : '') . '>Пункт 2</option>
                    <option value="option3"' . ($value === 'option3' ? ' selected' : '') . '>Пункт 3</option>
                </select>
            </td>
        </tr>';
    }

    /**
     * HTML для редактирования в форме
     */
    function GetEditFormHTML($arUserField, $arHtmlControl)
    {
        $value = $arHtmlControl['VALUE'];
        $fieldName = $arHtmlControl['NAME'];
        
        // Парсим значение (JSON содержащий выбранный пункт и описание)
        $data = json_decode($value, true);
        $selectedOption = $data['option'] ?? '';
        $description = $data['description'] ?? '';

        // Подключаем необходимые ресурсы
        \CJSCore::Init(['jquery']);
        \Bitrix\Main\Page\Asset::getInstance()->addJs('/bitrix/js/testlist.userfield/testlist_userfield.js');
        \Bitrix\Main\Page\Asset::getInstance()->addCss('/bitrix/js/testlist.userfield/testlist_userfield.css');
        
        $html = '
        <div class="testlist-field-container" data-field="' . htmlspecialchars($fieldName) . '">
            <select name="' . $fieldName . '_option" class="testlist-select" data-field="' . htmlspecialchars($fieldName) . '">
                <option value="">Выберите пункт</option>
                <option value="option1"' . ($selectedOption === 'option1' ? ' selected' : '') . '>' . Loc::getMessage('TESTLIST_OPTION_1') . '</option>
                <option value="option2"' . ($selectedOption === 'option2' ? ' selected' : '') . '>' . Loc::getMessage('TESTLIST_OPTION_2') . '</option>
                <option value="option3"' . ($selectedOption === 'option3' ? ' selected' : '') . '>' . Loc::getMessage('TESTLIST_OPTION_3') . '</option>
            </select>
            <input type="text" 
                   name="' . $fieldName . '_description" 
                   class="testlist-description" 
                   placeholder="' . Loc::getMessage('TESTLIST_DESCRIPTION_PLACEHOLDER') . '" 
                   value="' . htmlspecialchars($description) . '"
                   style="margin-left: 10px; display: ' . (in_array($selectedOption, ['option1', 'option2']) ? 'inline-block' : 'none') . ';" />
            <input type="hidden" name="' . $fieldName . '" value="' . htmlspecialchars($value) . '" class="testlist-hidden-value" />
        </div>
        
        <script>
        BX.ready(function() {
            TestListUserField.init();
        });
        </script>';

        return $html;
    }

    /**
     * HTML для отображения в списке
     */
    function GetAdminListViewHTML($arUserField, $arHtmlControl)
    {
        $value = $arHtmlControl['VALUE'];
        $data = json_decode($value, true);
        
        if (!$data) {
            return '';
        }

        $optionText = '';
        switch ($data['option']) {
            case 'option1':
                $optionText = Loc::getMessage('TESTLIST_OPTION_1');
                break;
            case 'option2':
                $optionText = Loc::getMessage('TESTLIST_OPTION_2');
                break;
            case 'option3':
                $optionText = Loc::getMessage('TESTLIST_OPTION_3');
                break;
        }

        $result = $optionText;
        if (!empty($data['description']) && in_array($data['option'], ['option1', 'option2'])) {
            $result .= ' (' . htmlspecialchars($data['description']) . ')';
        }

        return $result;
    }

    /**
     * HTML для детального просмотра
     */
    function GetAdminListEditHTML($arUserField, $arHtmlControl)
    {
        return $this->GetEditFormHTML($arUserField, $arHtmlControl);
    }

    /**
     * Валидация значения при сохранении
     */
    function CheckFields($arUserField, $value)
    {
        $errors = [];
        
        if ($arUserField['MANDATORY'] == 'Y' && empty($value)) {
            $errors[] = 'Поле "' . $arUserField['FIELD_NAME'] . '" обязательно для заполнения';
        }

        return $errors;
    }

    /**
     * Подготовка значения перед сохранением в БД
     */
    function OnBeforeSave($arUserField, $value)
    {
        // Если пришли данные из формы
        if (isset($_POST[$arUserField['FIELD_NAME'] . '_option'])) {
            $option = $_POST[$arUserField['FIELD_NAME'] . '_option'];
            $description = $_POST[$arUserField['FIELD_NAME'] . '_description'] ?? '';
            
            // Для пункта 3 описание не нужно
            if ($option === 'option3') {
                $description = '';
            }
            
            $data = [
                'option' => $option,
                'description' => $description
            ];
            
            return json_encode($data, JSON_UNESCAPED_UNICODE);
        }

        return $value;
    }

    /**
     * Для множественных значений
     */
    function GetEditFormHTMLMulty($arUserField, $arHtmlControl)
    {
        $html = '';
        if (is_array($arHtmlControl['VALUE'])) {
            foreach ($arHtmlControl['VALUE'] as $i => $value) {
                $control = $arHtmlControl;
                $control['VALUE'] = $value;
                $control['NAME'] = $arHtmlControl['NAME'] . '[' . $i . ']';
                $html .= $this->GetEditFormHTML($arUserField, $control) . '<br>';
            }
        }
        
        // Добавляем пустое поле для нового значения
        $control = $arHtmlControl;
        $control['VALUE'] = '';
        $control['NAME'] = $arHtmlControl['NAME'] . '[n' . (count($arHtmlControl['VALUE']) ?: 0) . ']';
        $html .= $this->GetEditFormHTML($arUserField, $control);
        
        return $html;
    }
} 