import axios from 'axios';
import { Modal } from 'bootstrap';
import { handleResponseError } from '../helpers/responseErrorHandler.js';

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

        // Сбрасываем все чекбоксы тегов
        editForm.querySelectorAll('#edit-tags-container input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });

        // Устанавливаем чекбоксы для тегов, связанных с записью
        if (entry.tags) {
            entry.tags.forEach(tag => {
                const checkbox = editForm.querySelector(`#edit_tag_${tag.id}`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
        }
    }
    // Отправка формы редактирования
    editForm.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(this);
        // Преобразуем FormData в обычный JavaScript-объект для отправки как JSON
        const data = Object.fromEntries(formData.entries());

        // Собираем выбранные теги
        const selectedTags = [];
        editForm.querySelectorAll('#edit-tags-container input[type="checkbox"]:checked').forEach(checkbox => {
            selectedTags.push(checkbox.value);
        });
        data.tags = selectedTags;

        // Удаляем лишние поля, которые не должны быть в теле PUT-запроса
        delete data._token;
        delete data._method;

        // Отправляем PUT-запрос с данными в формате JSON
        axios.put(this.action, data)
            .then(response => {
                // Выполняем редирект, чтобы сервер корректно перерисовал таблицу
                if (response.data?.payload?.redirectUrl) {
                    window.location.href = response.data.payload.redirectUrl;
                } else {
                    window.location.reload();
                }
            })
            .catch(error => handleResponseError(error, errorContainer));
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
                if (response.data?.payload?.redirectUrl) {
                    window.location.href = response.data.payload.redirectUrl;
                } else {
                    window.location.reload();
                }
            })
            .catch(error => handleResponseError(error, document.getElementById('delete-error-container')));
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

});
