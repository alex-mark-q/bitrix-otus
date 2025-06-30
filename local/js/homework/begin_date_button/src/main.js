console.log("include begin date homework");
// нашел пример https://github.com/Cleverscript/otus.gettstart/blob/main/install/js/otus/gettstart/src/gettstart.js
BX.namespace('Homework.BeginDateButton');

BX.Homework.BeginDateButton = {
    showMessage: function (message) {
        alert(message);
    },
    onStartWorkingDateAction: function (popupNodeId) {
        var popup = BX.PopupWindowManager.create("greeting-popup-message", BX(popupNodeId), {
            content: 'Вы хотите начать рабочий день?',
            width: 600, // ширина окна
            height: 400, // высота окна
            zIndex: 100, // z-index
            offsetTop: 0,
            offsetLeft: 600,
            closeIcon: {
                // объект со стилями для иконки закрытия, при null - иконки не будет
                opacity: 1
            },
            titleBar: 'Начало рабочего дня',
            closeByEsc: true, // закрытие окна по esc
            darkMode: false, // окно будет светлым или темным
            autoHide: true, // закрытие при клике вне окна
            draggable: true, // можно двигать или нет
            resizable: true, // можно ресайзить
            min_height: 100, // минимальная высота окна
            min_width: 100, // минимальная ширина окна
            lightShadow: true, // использовать светлую тень у окна
            angle: true, // появится уголок
            overlay: {
                backgroundColor: 'black',
                opacity: 500
            },
            buttons: [
                new BX.PopupWindowButton({
                    text: 'Начать', // текст кнопки
                    id: 'save-btn', // идентификатор
                    className: 'ui-btn ui-btn-success', // доп. классы
                    events: {
                        click: function() {
                            BX.Homework.BeginDateButton.startDate();
                            popup.close();
                        }
                    }
                }),
                new BX.PopupWindowButton({
                    text: 'Отмена',
                    id: 'copy-btn',
                    className: 'ui-btn ui-btn-primary',
                    events: {
                        click: function() {
                            BX.Homework.BeginDateButton.closeDate();
                            popup.close();
                        }
                    }
                })
            ],
            events: {
                onPopupShow: function() {
                    BX.Homework.BeginDateButton.showMessage('Вы открыли окно начала рабочего дня!');
                },
                onPopupClose: function() {
                    BX.Homework.BeginDateButton.showMessage('Вы закрыли окно начала рабочего дня!');
                }
            }
        });

        popup.show();
    },
    startDate: function () {
        BX.ajax.runAction('otus:customtab.TimemanActions.TimemanController.startDate', {
            data: {
                action: "init_timer_data",
                data: {
                    TIMER: false,
                    TASK: false,
                },
                module: "tasks"
            },
        });
    },
    closeDate: function () {
        BX.ajax.runAction('otus:customtab.TimemanActions.TimemanController.closeDate', {});
    }
};

BX.addCustomEvent('onPlannerInit', function () {
    console.log("begin_date_button", 'onPlannerInit');
    let timemanPopup = BX('timeman_main');

    if(timemanPopup) {
        let popupDiv = document.createElement('div');
        popupDiv.id = 'greeting-message-popup';
        document.body.appendChild(popupDiv);
        BX.Homework.BeginDateButton.onStartWorkingDateAction(popupDiv.id);
    }
});
