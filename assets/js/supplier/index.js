    function openEditModal(id, name, phone, email, address) {
        document.getElementById('edit_supplier_id').value = id;
        document.getElementById('edit_supplier_name').value = name;
        document.getElementById('edit_supplier_phone').value = phone;
        document.getElementById('edit_supplier_email').value = email;
        document.getElementById('edit_supplier_address').value = address;

        const editModalEl = document.getElementById('editSupplierModal');
        let instance = bootstrap.Modal.getInstance(editModalEl);
        if (!instance) instance = new bootstrap.Modal(editModalEl);
        instance.show();
    }

    document.addEventListener("DOMContentLoaded", function() {
        const filterForm = document.getElementById('filterSupplierForm');
        const btnRandom = document.getElementById('btnRandomSupplierCode');
        const inputCode = document.getElementById('add_supplier_code');

        if (btnRandom && inputCode) {
            btnRandom.addEventListener('click', function() {
                inputCode.value = 'NCC' + Math.floor(1000 + Math.random() * 9000);
            });
        }

        function showToast(message, type = 'success') {
            const oldToast = document.getElementById('pos-custom-toast');
            if (oldToast) oldToast.remove();

            const toastDiv = document.createElement('div');
            toastDiv.id = 'pos-custom-toast';
            let bgColor = '#10b981';
            let icon = '<i class="bi bi-check-circle-fill me-2"></i>';
            if (type === 'error') {
                bgColor = '#ef4444';
                icon = '<i class="bi bi-exclamation-circle-fill me-2"></i>';
            }

            toastDiv.style.cssText = `
            position: fixed; top: 24px; right: 24px; background-color: ${bgColor}; color: #ffffff;
            padding: 12px 24px; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            z-index: 99999; font-weight: 600; font-size: 14px; display: flex; align-items: center;
            opacity: 0; transform: translateY(-20px); transition: all 0.3s ease;
        `;
            toastDiv.innerHTML = icon + message;
            document.body.appendChild(toastDiv);

            setTimeout(() => {
                toastDiv.style.opacity = '1';
                toastDiv.style.transform = 'translateY(0)';
            }, 50);
            setTimeout(() => {
                toastDiv.style.opacity = '0';
                toastDiv.style.transform = 'translateY(-20px)';
                setTimeout(() => toastDiv.remove(), 300);
            }, 4000);
        }

        function loadSuppliersData() {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams();
            for (const [key, value] of formData.entries()) {
                params.append(key, value);
            }
            params.append('ajax', '1');

            fetch('/supplier/index?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderTableRows(data.suppliers);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function renderTableRows(suppliers) {
            const tbody = document.querySelector('#tblSuppliers tbody');
            tbody.innerHTML = '';

            if (!suppliers || suppliers.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center p-5 text-secondary"><i class="bi bi-building d-block mb-2" style="font-size: 40px; color: #cbd5e1;"></i>Không tìm thấy nhà cung cấp nào phù hợp!</td></tr>`;
                return;
            }

            suppliers.forEach(s => {
                const badgeStyle = s.status == 1 ? 'background-color: #def7ec; color: #03543f; font-size: 11px;' : 'background-color: #f3f4f6; color: #4b5563; font-size: 11px;';
                const badgeTxt = s.status == 1 ? 'Đang hợp tác' : 'Ngừng hợp tác';
                const toggleIcon = s.status == 1 ? 'bi-toggle-on' : 'bi-toggle-off';
                const emailVal = s.email ? s.email : '---';
                const addressVal = s.address ? s.address : '---';

                const tr = document.createElement('tr');
                tr.style.borderBottom = '1px solid #f1f5f9';
                tr.id = `supplier-row-${s.id}`;
                tr.innerHTML = `
                <td class="ps-4 fw-bold font-monospace text-secondary">${s.supplier_code}</td>
                <td class="fw-bold text-dark supplier-name-text">${s.supplier_name}</td>
                <td class="font-monospace supplier-phone-text">${s.phone}</td>
                <td class="text-muted supplier-email-text">${emailVal}</td>
                <td class="text-secondary small supplier-address-text" style="max-width: 250px;">${addressVal}</td>
                <td><span class="badge rounded-1 px-2 py-1 fw-semibold status-badge-el" style="${badgeStyle}">${badgeTxt}</span></td>
                <td class="text-end pe-4">
                    <div class="d-flex justify-content-end gap-1">
                        <button class="btn btn-sm btn-light border text-primary px-2 shadow-none" onclick="openEditModal(${s.id}, '${s.supplier_name.replace(/'/g, "\\'")}', '${s.phone}', '${emailVal.replace(/'/g, "\\'")}', '${addressVal.replace(/'/g, "\\'")}')"><i class="bi bi-pencil"></i> Sửa</button>
                        <button type="button" class="btn btn-sm border px-2 shadow-none btn-toggle-status-trigger ${s.status == 1 ? 'btn-light text-warning' : 'btn-light text-success'}" data-id="${s.id}" data-status="${s.status}"><i class="bi ${toggleIcon}"></i></button>
                    </div>
                </td>`;
                tbody.appendChild(tr);
            });
        }

        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                loadSuppliersData();
            });
        }

        const btnReset = document.getElementById('btnResetSupplierFilter');
        if (btnReset) {
            btnReset.addEventListener('click', function(e) {
                e.preventDefault();
                filterForm.reset();
                loadSuppliersData();
            });
        }

        const addForm = document.getElementById('addSupplierForm');
        if (addForm) {
            addForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btnSubmit = addForm.querySelector('.btn-save-add-supplier');
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Xử lý...`;

                const formData = new FormData(addForm);
                fetch('/supplier/addSupplier', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        btnSubmit.disabled = false;
                        btnSubmit.innerText = 'Lưu thông tin';
                        if (data.success) {
                            const modalInstance = bootstrap.Modal.getInstance(document.getElementById('addSupplierModal'));
                            if (modalInstance) modalInstance.hide();
                            addForm.reset();
                            loadSuppliersData();
                            showToast(data.message, 'success');
                        } else {
                            showToast(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        btnSubmit.disabled = false;
                        btnSubmit.innerText = 'Lưu thông tin';
                        showToast('Có lỗi kết nối hệ thống xảy ra!', 'error');
                    });
            });
        }

        const editForm = document.getElementById('editSupplierForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btnSubmit = editForm.querySelector('.btn-save-edit-supplier');
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Xử lý...`;

                const formData = new FormData(editForm);
                fetch('/supplier/editSupplier', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        btnSubmit.disabled = false;
                        btnSubmit.innerText = 'Lưu cập nhật';
                        if (data.success) {
                            const modalInstance = bootstrap.Modal.getInstance(document.getElementById('editSupplierModal'));
                            if (modalInstance) modalInstance.hide();
                            loadSuppliersData();
                            showToast(data.message, 'success');
                        } else {
                            showToast(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        btnSubmit.disabled = false;
                        btnSubmit.innerText = 'Lưu cập nhật';
                        showToast('Có lỗi kết nối hệ thống xảy ra!', 'error');
                    });
            });
        }

        document.querySelector('#tblSuppliers tbody').addEventListener('click', function(e) {
            const toggleBtn = e.target.closest('.btn-toggle-status-trigger');
            if (toggleBtn) {
                e.preventDefault();
                const id = toggleBtn.getAttribute('data-id');
                const currentStatus = toggleBtn.getAttribute('data-status');
                toggleBtn.disabled = true;

                fetch(`/supplier/toggleSupplier?id=${id}&status=${currentStatus}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadSuppliersData();
                            showToast(data.message, 'success');
                        } else {
                            toggleBtn.disabled = false;
                            showToast(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        toggleBtn.disabled = false;
                        showToast('Có lỗi kết nối hệ thống xảy ra!', 'error');
                    });
            }
        });
    });