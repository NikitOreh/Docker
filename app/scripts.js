function showEditForm(table, id) {
    document.getElementById(`edit-form-${table}-${id}`).style.display = 'block';
}

function hideEditForm(table, id) {
    document.getElementById(`edit-form-${table}-${id}`).style.display = 'none';
}

function submitEditForm(table, id) {
    const form = document.getElementById(`form-${table}-${id}`);
    const formData = new FormData(form);
    formData.append('id', id);
    formData.append('table', table);

    fetch('update.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            switch (table) {
                case 'client':
                    document.getElementById(`client-email-${id}`).textContent = formData.get('client_email') || '—';
                    document.getElementById(`client-phone-${id}`).textContent = formData.get('client_phone');
                    document.getElementById(`client-address-${id}`).textContent = formData.get('client_address');
                    document.getElementById(`client-company-${id}`).textContent = formData.get('client_company_or_full_name');
                    break;
                case 'employee':
                    document.getElementById(`employee-full-name-${id}`).textContent = formData.get('employee_full_name') || '—';
                    document.getElementById(`employee-phone-${id}`).textContent = formData.get('employee_phone');
                    break;
                case 'master':
                    document.getElementById(`master-full-name-${id}`).textContent = formData.get('master_full_name') || '—';
                    document.getElementById(`master-phone-${id}`).textContent = formData.get('master_phone');
                    break;
                case 'equipment_instance':
                    document.getElementById(`equipment-name-${id}`).textContent = formData.get('equipment_instance_name');
                    document.getElementById(`equipment-employee-${id}`).textContent = formData.get('employee_id') || '—';
                    document.getElementById(`equipment-status-${id}`).textContent = formData.get('equipment_instance_status');
                    document.getElementById(`equipment-price-${id}`).textContent = formData.get('equipment_instance_price');
                    break;
                case 'maintenance':
                    const [equipCode, maintNum] = id.split('-');
                    document.getElementById(`maintenance-master-${id}`).textContent = formData.get('master_id');
                    document.getElementById(`maintenance-price-${id}`).textContent = formData.get('maintenance_price');
                    document.getElementById(`maintenance-status-${id}`).textContent = formData.get('maintenance_status');
                    document.getElementById(`maintenance-date-${id}`).textContent = formData.get('maintenance_date');
                    break;
                case 'product':
                    document.getElementById(`product-name-${id}`).textContent = formData.get('product_name');
                    document.getElementById(`product-price-${id}`).textContent = formData.get('price');
                    document.getElementById(`product-quantity-${id}`).textContent = formData.get('stock_quantity');
                    document.getElementById(`product-unit-${id}`).textContent = formData.get('product_unit_of_measurement');
                    break;
                case 'shipping':
                    document.getElementById(`shipping-date-${id}`).textContent = formData.get('shipping_date');
                    document.getElementById(`shipping-status-${id}`).textContent = formData.get('shipment_status');
                    document.getElementById(`shipping-employee-${id}`).textContent = formData.get('employee_id');
                    document.getElementById(`shipping-client-${id}`).textContent = formData.get('client_id');
                    break;
                case 'supply':
                    document.getElementById(`supply-status-${id}`).textContent = formData.get('supply_status');
                    document.getElementById(`supply-supplier-id-${id}`).textContent = formData.get('supplier_id');
                    break;
                case 'supplier':
                    document.getElementById(`supplier-company-${id}`).textContent = formData.get('supplier_company_or_full_name');
                    document.getElementById(`supplier-email-${id}`).textContent = formData.get('supplier_email') || '—';
                    document.getElementById(`supplier-phone-${id}`).textContent = formData.get('supplier_phone');
                    document.getElementById(`supplier-address-${id}`).textContent = formData.get('supplier_address');
                    break;
            }
            hideEditForm(table, id);
            document.getElementById(`error-${table}-${id}`).textContent = '';
        } else {
            document.getElementById(`error-${table}-${id}`).textContent = data.error;
        }
    })
    .catch(error => {
        document.getElementById(`error-${table}-${id}`).textContent = 'Ошибка: ' + error.message;
    });
}   