
    const txtSearch = document.getElementById('txtSearchVariant');
    const dropdown = document.getElementById('searchResultDropdown');
    const tableBody = document.getElementById('checkItemsTableBody');
    const emptyMessage = document.getElementById('trEmptyMessage');

    txtSearch.addEventListener('input', function() {
        const value = this.value.trim().toLowerCase();
        if (value.length < 1) {
            dropdown.classList.add('d-none');
            return;
        }

        const filtered = allVariants.filter(v =>
            v.product_name.toLowerCase().includes(value) ||
            v.product_code.toLowerCase().includes(value) ||
            (v.barcode && v.barcode.toLowerCase().includes(value)) ||
            v.variant_name.toLowerCase().includes(value)
        );

        if (filtered.length === 0) {
            dropdown.innerHTML = '<div class="p-3 text-center text-muted small">Không tìm thấy sản phẩm này trên kệ!</div>';
        } else {
            const groupedProducts = {};
            filtered.forEach(v => {
                if (!groupedProducts[v.product_code]) {
                    groupedProducts[v.product_code] = {
                        product_name: v.product_name,
                        product_code: v.product_code,
                        variants: []
                    };
                }
                groupedProducts[v.product_code].variants.push(v);
            });

            let html = '<div class="font-monospace small bg-white">';
            for (const code in groupedProducts) {
                const p = groupedProducts[code];
                html += `
                <div class="p-2 bg-light border-bottom fw-bold text-primary d-flex justify-content-between align-items-center" style="font-size: 12px; background-color:#f1f5f9 !important;">
                    <span><i class="bi bi-box-seam me-1"></i> ${p.product_name}</span>
                    <span class="badge bg-secondary font-monospace">${p.product_code}</span>
                </div>
                <div class="list-group list-group-flush">
                `;

                p.variants.forEach(v => {
                    html += `
                    <button type="button" class="list-group-item list-group-item-action py-2 px-4 text-start border-bottom d-flex justify-content-between align-items-center bg-white" onclick="addVariantToTable(${v.variant_id})">
                        <div class="ps-1">
                            <span class="text-dark fw-semibold d-inline-block">— ${v.variant_name}</span>
                            <span class="text-muted small ms-2">(Barcode: ${v.barcode ? v.barcode : 'Trống'})</span>
                        </div>
                        <span class="text-muted font-monospace">Tồn: <strong class="text-dark">${v.stock_qty}</strong></span>
                    </button>
                    `;
                });
                html += `</div>`;
            }
            html += '</div>';
            dropdown.innerHTML = html;
        }
        dropdown.classList.remove('d-none');
    });

    document.addEventListener('click', function(e) {
        if (!txtSearch.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('d-none');
        }
    });

    function addVariantToTable(variantId) {
        dropdown.classList.add('d-none');
        txtSearch.value = '';

        if (document.getElementById('tr_item_' + variantId)) {
            document.getElementById('input_act_' + variantId).focus();
            return;
        }

        const item = allVariants.find(v => v.variant_id == variantId);
        if (!item) return;

        if (emptyMessage && tableBody.contains(emptyMessage)) {
            emptyMessage.remove();
        }

        const rowHtml = `
        <tr id="tr_item_${item.variant_id}" style="border-bottom: 1px solid #f1f5f9;">
            <td class="ps-4">
                <input type="hidden" name="variant_id[]" value="${item.variant_id}">
                <span class="fw-bold text-dark" style="font-size:14px;">${item.product_name}</span> 
                <small class="text-secondary d-block font-monospace" style="font-size:12px;">${item.variant_name}</small>
            </td>
            <td class="text-center font-monospace text-muted small">${item.barcode ? item.barcode : '---'}</td>
            <td class="text-center font-monospace bg-light fw-bold text-secondary">${item.stock_qty}
                <input type="hidden" name="system_qty[]" value="${item.stock_qty}">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm text-center shadow-none border-primary fw-bold" id="input_act_${item.variant_id}" name="actual_qty[]" min="0" required placeholder="Gõ SL đếm thực tế...">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm text-danger border-0 shadow-none" onclick="removeRow(${item.variant_id})"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
        `;
        tableBody.insertAdjacentHTML('beforeend', rowHtml);
        document.getElementById('input_act_' + item.variant_id).focus();
    }

    function removeRow(variantId) {
        document.getElementById('tr_item_' + variantId).remove();
        if (tableBody.querySelectorAll('tr:not(#trEmptyMessage)').length === 0) {
            tableBody.appendChild(emptyMessage);
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
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
                position: fixed;
                top: 24px;
                right: 24px;
                background-color: ${bgColor};
                color: #ffffff;
                padding: 12px 24px;
                border-radius: 8px;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                z-index: 99999;
                font-weight: 600;
                font-size: 14px;
                display: flex;
                align-items: center;
                opacity: 0;
                transform: translateY(-20px);
                transition: all 0.3s ease;
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

        const createSheetForm = document.getElementById('createInventorySheetForm');
        if (createSheetForm) {
            createSheetForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const addedItems = tableBody.querySelectorAll('tr:not(#trEmptyMessage)');
                if (addedItems.length === 0) {
                    showToast('Lỗi: Phiếu kiểm kho phải có ít nhất một mặt hàng!', 'error');
                    return;
                }

                const btnSubmit = createSheetForm.querySelector('.btn-submit-save-sheet');
                if (btnSubmit) {
                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Đang xử lý...`;
                }

                const formData = new FormData(createSheetForm);
                fetch('/inventory/add', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = '/inventory/index';
                        }, 1000);
                    } else {
                        if (btnSubmit) {
                            btnSubmit.disabled = false;
                            btnSubmit.innerText = 'Lưu phiếu kiểm kho';
                        }
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    if (btnSubmit) {
                        btnSubmit.disabled = false;
                        btnSubmit.innerText = 'Lưu phiếu kiểm kho';
                    }
                    console.error('Error:', error);
                    showToast('Có lỗi hệ thống xảy ra!', 'error');
                });
            });
        }
    });