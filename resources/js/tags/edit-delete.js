import axios from 'axios';
import { Modal } from 'bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const editDeleteModalElement = document.getElementById('editDeleteTagModal');
    if (!editDeleteModalElement) return;

    const editDeleteModal = new Modal(editDeleteModalElement);
    const editForm = document.getElementById('editTagForm');
    const errorContainer = document.getElementById('edit-tag-error-container');
    let currentTagId = null;
    let currentTagData = null; // Для сохранения данных тега при переходе к модалу удаления

    // Модал подтверждения удаления
    const deleteConfirmModalElement = document.getElementById('deleteConfirmTagModal');
    const deleteConfirmModal = new Modal(deleteConfirmModalElement);
    const confirmDeleteButton = document.getElementById('confirm-delete-tag-button');
    let isDeleting = false; // Флаг для отслеживания процесса удаления

    // Заполнение формы при открытии модального окна редактирования
    editDeleteModalElement.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; // Кнопка, которая открыла модал
        currentTagData = JSON.parse(button.dataset.tag);
        currentTagId = currentTagData.id;

        fillEditForm(currentTagData);
    });

    function fillEditForm(tag) {
        editForm.querySelector('#edit-tag-name').value = tag.name;

        // Преобразуем внутренний цвет (например, 'blue') в класс bootstrap (например, 'primary')
        const bsClass = window.colorMaps.colorToBsClass[tag.color];

        // Выбираем соответствующий радиобаттон цвета
        const colorRadio = editForm.querySelector(`input[name="bootstrap_color_class"][value="${bsClass}"]`);
        if (colorRadio) {
            colorRadio.checked = true;
        }

        // Устанавливаем action для формы
        editForm.action = `/tags/${tag.id}`;
    }

    // Отправка формы редактирования
    editForm.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(this);
        const bsClass = formData.get('bootstrap_color_class');
        const genericColor = window.colorMaps.bsClassToColor[bsClass];

        const payload = {
            name: formData.get('name'),
            color: genericColor,
            _method: 'PUT' // Не забываем метод для Laravel
        };

        axios.post(this.action, payload) // Используем post для обхода ограничений, Laravel поймет по _method
            .then(response => {
                if (response.data?.payload?.redirectUrl) {
                    window.location.href = response.data.payload.redirectUrl;
                } else {
                    window.location.reload();
                }
            })
            .catch(handleError);
    });

    // Логика удаления
    const deleteTagButton = document.getElementById('delete-tag-button');

    deleteTagButton.addEventListener('click', () => {
        if (!currentTagData) return;

        // Вставляем имя тега в модал подтверждения
        const tagNameElement = document.getElementById('tag-to-delete-name');
        if (tagNameElement) {
            tagNameElement.textContent = currentTagData.name;
        }

        isDeleting = false; // Сбрасываем флаг при каждом открытии
        deleteConfirmModal.show();
    });

    // Скрываем модал редактирования перед показом модала подтверждения
    deleteConfirmModalElement.addEventListener('show.bs.modal', () => {
        editDeleteModal.hide();
    });

    // Возвращаем модал редактирования, если удаление было отменено
    deleteConfirmModalElement.addEventListener('hidden.bs.modal', () => {
        if (!isDeleting) {
            editDeleteModal.show();
            if(currentTagData) {
                fillEditForm(currentTagData);
            }
        }
    });

    confirmDeleteButton.addEventListener('click', () => {
        if (!currentTagId) return;
        isDeleting = true; // Устанавливаем флаг, что мы начали удаление

        axios.delete(`/tags/${currentTagId}`)
            .then(response => {
                if (response.data?.payload?.redirectUrl) {
                    window.location.href = response.data.payload.redirectUrl;
                } else {
                    window.location.reload();
                }
            })
            .catch(handleError);
    });

    // Очистка при закрытии основного модала
    editDeleteModalElement.addEventListener('hidden.bs.modal', function () {
        // Очищаем, только если мы не в процессе открытия модала подтверждения
        if (!deleteConfirmModalElement.classList.contains('show')) {
            errorContainer.classList.add('d-none');
            errorContainer.innerHTML = '';
            editForm.reset();
            currentTagId = null;
            currentTagData = null;
        }
    });

    function handleError(error) {
        console.error('Ошибка при отправке формы тега:', error.response);
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
