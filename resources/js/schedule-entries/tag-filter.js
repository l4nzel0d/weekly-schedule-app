document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('schedule-filter-form');
    if (!form) return;

    const tagInputContainer = document.getElementById('tag-input-container');
    const tagSelectionDropdown = document.getElementById('tag-selection-dropdown');
    const hiddenInputsContainer = document.getElementById('tag-hidden-inputs');
    const dropdownToggleButton = tagInputContainer.querySelector('[data-bs-toggle="dropdown"]');

    if (!window.scheduleFilter || !tagInputContainer || !tagSelectionDropdown || !hiddenInputsContainer) {
        return;
    }

    const allTags = window.scheduleFilter.allTags;
    const initialTagIds = window.scheduleFilter.initialTagIds.map(id => parseInt(id, 10));

    let selectedTagIds = [];
    let availableTagIds = Object.keys(allTags).map(id => parseInt(id, 10));

    function rerender() {
        // 1. Очистка
        hiddenInputsContainer.innerHTML = '';
        tagSelectionDropdown.innerHTML = '';
        tagInputContainer.querySelectorAll('.badge').forEach(b => b.remove());

        // 2. Рендеринг выбранных тегов (бейджи)
        selectedTagIds.forEach(tagId => {
            const tag = allTags[tagId];
            if (!tag) return;

            const badge = document.createElement('span');
            const bsClass = window.colorMaps.colorToBsClass[tag.color] || 'secondary';
            badge.className = `badge text-bg-${bsClass} me-1 fw-normal d-inline-flex align-items-center`;
            badge.textContent = tag.name;

            const closeButton = document.createElement('span');
            closeButton.innerHTML = '&times;';
            closeButton.className = 'ms-2';
            closeButton.style.cursor = 'pointer';
            closeButton.onclick = (e) => {
                e.stopPropagation(); // Предотвращаем открытие дропдауна
                deselectTag(tagId);
            };

            badge.appendChild(closeButton);
            tagInputContainer.insertBefore(badge, dropdownToggleButton.parentNode);
        });

        // 3. Рендеринг доступных тегов (в дропдауне)
        if (availableTagIds.length === 0) {
            tagSelectionDropdown.innerHTML = '<span class="dropdown-item-text text-muted">Все теги выбраны</span>';
        } else {
            availableTagIds.forEach(tagId => {
                const tag = allTags[tagId];
                if (!tag) return;

                const item = document.createElement('a');
                item.className = 'dropdown-item';
                item.href = '#';
                const bsClass = window.colorMaps.colorToBsClass[tag.color] || 'secondary';
                item.innerHTML = `<span class="badge text-bg-${bsClass} me-1">${tag.name}</span>`;
                item.onclick = (e) => {
                    e.preventDefault();
                    selectTag(tagId);
                };
                tagSelectionDropdown.appendChild(item);
            });
        }

        // 4. Рендеринг скрытых инпутов для отправки формы
        selectedTagIds.forEach(tagId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'tags[]';
            input.value = tagId;
            hiddenInputsContainer.appendChild(input);
        });
    }

    function selectTag(tagId) {
        if (!selectedTagIds.includes(tagId)) {
            selectedTagIds.push(tagId);
            availableTagIds = availableTagIds.filter(id => id !== tagId);
            rerender();
        }
    }

    function deselectTag(tagId) {
        if (!availableTagIds.includes(tagId)) {
            availableTagIds.push(tagId);
            selectedTagIds = selectedTagIds.filter(id => id !== tagId);
            rerender();
        }
    }

    // --- Инициализация ---
    initialTagIds.forEach(id => {
        if (availableTagIds.includes(id)) {
            selectTag(id);
        }
    });

    rerender();
});
