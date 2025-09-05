document.addEventListener('DOMContentLoaded', () => {
    console.log('Script.js: DOMContentLoaded fired');

    function showAddForm(tableName) {
        const form = document.getElementById(`add-form-${tableName}`);
        if (form) {
            form.style.display = 'block';
            console.log(`Show add form for ${tableName}`);
        } else {
            console.error(`Add form not found for ${tableName}`);
        }
    }

    function hideAddForm(tableName) {
        const form = document.getElementById(`add-form-${tableName}`);
        if (form) {
            form.style.display = 'none';
            console.log(`Hide add form for ${tableName}`);
        }
    }

    function showEditForm(tableName, rowData) {
        const data = JSON.parse(rowData);
        const form = document.getElementById(`edit-form-${tableName}`);
        if (form) {
            if (tableName === 'maintenance') {
                form.querySelector('input[name="equipment_instance_code"]').value = data.equipment_instance_code || '';
                form.querySelector('input[name="maintenance_number"]').value = data.maintenance_number || '';
            } else if (tableName === 'shipment_product') {
                form.querySelector('input[name="shipment_number"]').value = data.shipment_number || '';
                form.querySelector('input[name="product_code"]').value = data.product_code || '';
            } else {
                form.querySelector('input[name="id"]').value = data[Object.keys(data)[0]] || '';
            }
            Object.keys(data).forEach(key => {
                const input = form.querySelector(`input[name="${key}"], select[name="${key}"]`);
                if (input) input.value = data[key] || '';
            });
            form.style.display = 'block';
            console.log(`Show edit form for ${tableName}`, data);
        } else {
            console.error(`Edit form not found for ${tableName}`);
        }
    }

    function hideEditForm(tableName) {
        const form = document.getElementById(`edit-form-${tableName}`);
        if (form) {
            form.style.display = 'none';
            console.log(`Hide edit form for ${tableName}`);
        }
    }

    function addRecord(tableName, event) {
        event.preventDefault();
        const form = document.getElementById(`add-form-${tableName}`);
        if (!form) {
            console.error(`Add form not found for ${tableName}`);
            alert('Ошибка: форма не найдена');
            return;
        }

        const button = form.querySelector('button[type="submit"]');
        if (!button) {
            console.error(`Submit button not found in form for ${tableName}`);
            alert('Ошибка: кнопка отправки не найдена');
            return;
        }

        button.disabled = true;
        button.textContent = 'Сохранение...';

        const formData = new FormData(form);
        formData.append('table', tableName);

        fetch('add.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log(`Add response status for ${tableName}: ${response.status}`);
            return response.json();
        })
        .then(data => {
            button.disabled = false;
            button.textContent = 'Сохранить';
            if (data.success) {
                alert('Запись успешно добавлена!');
                location.reload(); // Обновляем страницу для отображения новой записи
                hideAddForm(tableName);
            } else {
                console.error(`Add error for ${tableName}:`, data.error);
                const errorDiv = document.getElementById(`error-add-${tableName}`) || document.createElement('div');
                errorDiv.id = `error-add-${tableName}`;
                errorDiv.className = 'alert alert-danger';
                errorDiv.textContent = data.error || 'Неизвестная ошибка';
                form.prepend(errorDiv);
            }
        })
        .catch(error => {
            button.disabled = false;
            button.textContent = 'Сохранить';
            console.error(`Add error for ${tableName}:`, error);
            const errorDiv = document.getElementById(`error-add-${tableName}`) || document.createElement('div');
            errorDiv.id = `error-add-${tableName}`;
            errorDiv.className = 'alert alert-danger';
            errorDiv.textContent = 'Ошибка: ' + error.message;
            form.prepend(errorDiv);
        });
    }

    function submitEditForm(tableName, id) {
        const form = document.getElementById(`form-${tableName}-${id}`);
        const formData = new FormData(form);
        formData.append('id', id);
        formData.append('table', tableName);

        fetch('update.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Обновление интерфейса (оставляем как есть)
                hideEditForm(tableName);
                document.getElementById(`error-${tableName}-${id}`).textContent = '';
            } else {
                document.getElementById(`error-${tableName}-${id}`).textContent = data.error;
            }
        })
        .catch(error => {
            document.getElementById(`error-${tableName}-${id}`).textContent = 'Ошибка: ' + error.message;
        });
    }

    function deleteRecord(tableName, id) {
        if (!confirm('Вы уверены, что хотите удалить эту запись?')) return;

        const formData = new FormData();
        formData.append('table', tableName);
        if (tableName === 'maintenance') {
            const [equipment_instance_code, maintenance_number] = id.split('-');
            formData.append('equipment_instance_code', equipment_instance_code);
            formData.append('maintenance_number', maintenance_number);
        } else if (tableName === 'shipment_product') {
            const [shipment_number, product_code] = id.split('-');
            formData.append('shipment_number', shipment_number);
            formData.append('product_code', product_code);
        } else {
            formData.append('id', id);
        }
        console.log(`Sending delete request for ${tableName}`, Object.fromEntries(formData));

        fetch('delete.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log(`Delete response status for ${tableName}: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log(`Delete response for ${tableName}:`, data);
            if (data.success) {
                alert('Запись удалена!');
                location.reload();
            } else {
                alert('Ошибка: ' + data.error);
            }
        })
        .catch(error => {
            console.error(`Delete error for ${tableName}:`, error);
            alert('Ошибка: ' + error.message);
        });
    }

    window.showAddForm = showAddForm;
    window.hideAddForm = hideAddForm;
    window.showEditForm = showEditForm;
    window.hideEditForm = hideEditForm;
    window.addRecord = addRecord;
    window.submitEditForm = submitEditForm;
    window.deleteRecord = deleteRecord;

    console.log('Script: Initialization complete');
});