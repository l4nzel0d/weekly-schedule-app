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

        // Подготавливаем и вставляем детали записи в модальное окно подтверждения
        const deleteEntryDetails = document.getElementById('delete-entry-details');
        if (currentEntryData && deleteEntryDetails) {
            // Словарь для преобразования номера дня недели в название
            const daysOfWeek = {
                1: 'Понедельник',
                2: 'Вторник',
                3: 'Среда',
                4: 'Четверг',
                5: 'Пятница',
                6: 'Суббота',
                7: 'Воскресенье'
            };
            const dayName = daysOfWeek[currentEntryData.day_of_week] || 'Неизвестный день';
            const startTime = currentEntryData.start_time.substring(0, 5);
            const endTime = currentEntryData.end_time.substring(0, 5);

            // Формируем и вставляем HTML
            deleteEntryDetails.innerHTML = `
                <p class="mb-1"><strong>День:</strong> ${dayName}</p>
                <p class="mb-1"><strong>Время:</strong> ${startTime} - ${endTime}</p>
                <p class="mb-0"><strong>Название:</strong> ${currentEntryData.title}</p>
            `;
        }
    });

    // Возвращаем модал редактирования, если удаление было отменено
    deleteConfirmModalElement.addEventListener('hidden.bs.modal', () => {
        // Очищаем контейнер с ошибками и деталями при закрытии
        const errorContainer = document.getElementById('delete-error-container');
        if(errorContainer) {
            errorContainer.classList.add('d-none');
            errorContainer.textContent = '';
        }
        const deleteEntryDetails = document.getElementById('delete-entry-details');
        if(deleteEntryDetails) {
            deleteEntryDetails.innerHTML = '';
        }

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
