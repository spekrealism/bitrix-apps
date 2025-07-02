/**
 * JavaScript для работы с пользовательским полем "Тестовый список с описанием"
 */
window.TestListUserField = {
    /**
     * Инициализация
     */
    init: function() {
        this.bindEvents();
        this.initExistingFields();
    },

    /**
     * Привязка обработчиков событий
     */
    bindEvents: function() {
        var self = this;

        // Используем делегирование событий, чтобы обработчики работали и для динамически добавленных элементов
        BX.bind(document, 'change', function(e) {
            var target = e.target || e.srcElement;
            if (BX.hasClass(target, 'testlist-select')) {
                self.handleSelectChange(target);
            }
        });

        BX.bind(document, 'input', function(e) {
            var target = e.target || e.srcElement;
            if (BX.hasClass(target, 'testlist-description')) {
                self.handleDescriptionChange(target);
            }
        });

        // Используем MutationObserver для отслеживания новых полей (современная замена DOMNodeInserted)
        if ('MutationObserver' in window) {
            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length) {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === 1) { // Убедимся, что это элемент
                                // Проверяем сам добавленный узел
                                if (BX.hasClass(node, 'testlist-field-container')) {
                                    var select = BX.findChild(node, {className: 'testlist-select'}, true);
                                    if(select) self.initField(select);
                                }
                                // Проверяем дочерние узлы
                                var containers = BX.findChildren(node, {className: 'testlist-field-container'}, true);
                                if (containers) {
                                    containers.forEach(function(container) {
                                        var select = BX.findChild(container, {className: 'testlist-select'}, true);
                                        if(select) self.initField(select);
                                    });
                                }
                            }
                        });
                    }
                });
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    },

    /**
     * Инициализация существующих полей на странице
     */
    initExistingFields: function() {
        var selects = BX.findChildren(document, {className: 'testlist-select'}, true);
        if (selects) {
            for (var i = 0; i < selects.length; i++) {
                this.initField(selects[i]);
            }
        }
    },

    /**
     * Инициализация конкретного поля
     */
    initField: function(selectElement) {
        this.updateFieldVisibility(selectElement);
        this.updateHiddenValue(selectElement);
    },

    /**
     * Обработчик изменения селекта
     */
    handleSelectChange: function(selectElement) {
        this.updateFieldVisibility(selectElement);
        this.updateHiddenValue(selectElement);
    },

    /**
     * Обработчик изменения текстового поля
     */
    handleDescriptionChange: function(descriptionElement) {
        var container = this.findContainer(descriptionElement);
        if (container) {
            var selectElement = BX.findChild(container, {className: 'testlist-select'});
            if (selectElement) {
                this.updateHiddenValue(selectElement);
            }
        }
    },

    /**
     * Обновление видимости текстового поля
     */
    updateFieldVisibility: function(selectElement) {
        var container = this.findContainer(selectElement);
        if (!container) return;

        var descriptionField = BX.findChild(container, {className: 'testlist-description'});
        if (!descriptionField) return;

        var selectedValue = selectElement.value;
        
        if (selectedValue === 'option1' || selectedValue === 'option2') {
            descriptionField.style.display = 'inline-block';
        } else {
            descriptionField.style.display = 'none';
            descriptionField.value = ''; // Очищаем значение если поле скрыто
        }
    },

    /**
     * Обновление значения скрытого поля
     */
    updateHiddenValue: function(selectElement) {
        var container = this.findContainer(selectElement);
        if (!container) return;

        var descriptionField = BX.findChild(container, {className: 'testlist-description'});
        var hiddenField = BX.findChild(container, {className: 'testlist-hidden-value'});
        
        if (!hiddenField) return;

        var selectedOption = selectElement.value;
        var description = descriptionField ? descriptionField.value : '';

        // Для пункта 3 описание не нужно
        if (selectedOption === 'option3') {
            description = '';
        }

        var data = {
            option: selectedOption,
            description: description
        };

        hiddenField.value = JSON.stringify(data);
    },

    /**
     * Поиск контейнера поля
     */
    findContainer: function(element) {
        return BX.findParent(element, {className: 'testlist-field-container'});
    }
};

// Инициализация при загрузке страницы
BX.ready(function() {
    TestListUserField.init();
}); 