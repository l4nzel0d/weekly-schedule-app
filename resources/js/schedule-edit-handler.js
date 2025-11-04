import axios from 'axios';
import { Modal } from 'bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const editModalElement = document.getElementById('editScheduleEntryModal');
    if (!editModalElement) return;

    const editModal = new Modal(editModalElement);
    const editForm = document.getElementById('editScheduleEntryForm');
    const errorContainer = document.getElementById('edit-error-container');
    let currentEntryId = null;
    let currentEntryData = null; // Переменная для хранения данных текущей записи

    // Заполнение формы при открытии модального окна
    editModalElement.addEventListener('show.bs.modal', function (event) {
        // Заполняем форму, только если модал был открыт кликом по строке
        if (event.relatedTarget) {
            const row = event.relatedTarget;
            currentEntryData = JSON.parse(row.dataset.entry);
            currentEntryId = currentEntryData.id;
            fillEditForm(currentEntryData);
        }
    });

    function fillEditForm(entry) {
        // Заполняем поля
        editForm.querySelector('#edit-title').value = entry.title;
        editForm.querySelector('#edit-description').value = entry.description || '';
        editForm.querySelector('#edit-day_of_week').value = entry.day_of_week;
        editForm.querySelector('#edit-start_time').value = entry.start_time.substring(0, 5);
        editForm.querySelector('#edit-end_time').value = entry.end_time.substring(0, 5);

        // Устанавливаем action для формы
        editForm.action = `/schedule-entries/${entry.id}`;
    }

    // Отправка формы редактирования
    editForm.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(this);
        // Преобразуем FormData в обычный JavaScript-объект для отправки как JSON
        const data = Object.fromEntries(formData.entries());

        // Удаляем лишние поля, которые не должны быть в теле PUT-запроса
        delete data._token;
        delete data._method;

        // Отправляем PUT-запрос с данными в формате JSON
        axios.put(this.action, data)
            .then(response => {
                window.location.reload(); // Перезагружаем для простоты
            })
            .catch(handleError);
    });

    // Логика удаления
    const deleteButton = document.getElementById('delete-button');
    const deleteConfirmModalElement = document.getElementById('deleteConfirmModal');
    const deleteConfirmModal = new Modal(deleteConfirmModalElement);
    const confirmDeleteButton = document.getElementById('confirm-delete-button');
    let isDeleting = false; // Флаг для отслеживания процесса удаления

    deleteButton.addEventListener('click', () => {
        isDeleting = false; // Сбрасываем флаг при каждом открытии
        deleteConfirmModal.show();
    });

    // Скрываем модал редактирования перед показом модала подтверждения
    deleteConfirmModalElement.addEventListener('show.bs.modal', () => {
        editModal.hide();
    });

    // Возвращаем модал редактирования, если удаление было отменено
    deleteConfirmModalElement.addEventListener('hidden.bs.modal', () => {
        if (!isDeleting) {
            // Показываем модал и заново заполняем его сохраненными данными
            editModal.show();
            if(currentEntryData) {
                fillEditForm(currentEntryData);
            }
        }
    });

    confirmDeleteButton.addEventListener('click', () => {
        if (!currentEntryId) return;
        isDeleting = true; // Устанавливаем флаг, что мы начали удаление

        axios.delete(`/schedule-entries/${currentEntryId}`)
            .then(response => {
                // Выполняем редирект, чтобы сервер корректно перерисовал таблицу
                if (response.data && response.data.redirectUrl) {
                    window.location.href = response.data.redirectUrl;
                } else {
                    window.location.reload();
                }
            })
            .catch(handleError);
    });

    // Очистка при закрытии
    editModalElement.addEventListener('hidden.bs.modal', function () {
        // Очищаем, только если мы не в процессе открытия модала подтверждения
        if (!deleteConfirmModalElement.classList.contains('show')) {
            errorContainer.classList.add('d-none');
            errorContainer.innerHTML = '';
            editForm.reset();
            currentEntryId = null;
            currentEntryData = null;
        }
    });

    function handleError(error) {
        console.error('Ошибка:', error.response);
        if (error.response && error.response.status === 422) {
            const errors = error.response.data.errors;
            let errorMessages = '<ul>';
            for (const field in errors) {
                errors[field].forEach(message => {
                    errorMessages += `<li>${message}</li>`;
                });
            }
            errorMessages += '</ul>';
            errorContainer.innerHTML = errorMessages;
            errorContainer.classList.remove('d-none');
        } else {
            const status = error.response ? error.response.status : 'N/A';
            errorContainer.innerHTML = `Произошла непредвиденная ошибка. Код: ${status}`;
            errorContainer.classList.remove('d-none');
        }
    }
});
