 const txtOrderSearch = document.getElementById('txtSearchOrderVariant');
    const orderDropdown = document.getElementById('orderSearchDropdown');
    const orderTableBody = document.getElementById('purchaseItemsTableBody');
    const orderEmptyMsg = document.getElementById('trOrderEmptyMsg');

    txtOrderSearch.addEventListener('input', function() {
        const value = this.value.trim().toLowerCase();
        if (value.length < 1) {
            orderDropdown.classList.add('d-none');
            return;
        }

        const filtered = allOrderVariants.filter(v =>
            v.product_name.toLowerCase().includes(value) ||
            v.product_code.toLowerCase().includes(value) ||
            (v.barcode && v.barcode.toLowerCase().includes(value)) ||
            v.variant_name.toLowerCase().includes(value)
        );

        if (filtered.length === 0) {
            orderDropdown.innerHTML = '<div class="p-3 text-center text-muted small">Không tìm thấy sản phẩm quy cách này!</div>';
        } else {
            const grouped = {};
            filtered.forEach(v => {
                if (!grouped[v.product_id]) {
                    grouped[v.product_id] = {
                        product_name: v.product_name,
                        variants: []
                    };
                }
                grouped[v.product_id].variants.push(v);
            });

            let html = '<div class="font-monospace small bg-white">';
            for (const pid in grouped) {
                const p = grouped[pid];
                html += `
                <div class="p-2 bg-light border-bottom fw-bold text-dark d-flex align-items-center" style="font-size: 12px; background-color:#f1f5f9 !important;">
                    <span><i class="bi bi-box me-1 text-secondary"></i> ${p.product_name}</span>
                </div>
                <div class="list-group list-group-flush">`;

                p.variants.forEach(v => {
                    html += `
                    <button type="button" class="list-group-item list-group-item-action py-2 px-4 text-start border-bottom d-flex justify-content-between align-items-center bg-white" onclick="addVariantToOrderTable(${v.id})">
                        <div>
                            <span class="text-primary fw-semibold">— ${v.variant_name}</span>
                            <span class="text-muted small ms-1">(Mã vạch: ${v.barcode ? v.barcode : '---'})</span>
                        </div>
                        <span class="badge bg-light text-dark border font-monospace">Giá vốn cũ: ${parseFloat(v.cost_price).toLocaleString('en-US')}đ</span>
                    </button>`;
                });
                html += `</div>`;
            }
            html += '</div>';
            orderDropdown.innerHTML = html;
        }
        orderDropdown.classList.remove('d-none');
    });

    document.addEventListener('click', function(e) {
        if (!txtOrderSearch.contains(e.target) && !orderDropdown.contains(e.target)) {
            orderDropdown.classList.add('d-none');
        }
    });

    function addVariantToOrderTable(variantId) {
        orderDropdown.classList.add('d-none');
        txtOrderSearch.value = '';

        if (document.getElementById('tr_po_' + variantId)) {
            document.getElementById('input_po_qty_' + variantId).focus();
            return;
        }

        const item = allOrderVariants.find(v => v.id == variantId);
        if (!item) return;

        if (orderEmptyMsg && orderTableBody.contains(orderEmptyMsg)) {
            orderEmptyMsg.remove();
        }

        const rowHtml = `
        <tr id="tr_po_${item.id}" class="po-item-row" style="border-bottom: 1px solid #f1f5f9;">
            <td class="ps-4">
                <input type="hidden" name="variant_id[]" value="${item.id}">
                <span class="fw-bold text-dark" style="font-size:14px;">${item.product_name}</span> 
                <small class="text-secondary d-block font-monospace" style="font-size:12px;">${item.variant_name}</small>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm text-center shadow-none po-calc-trigger po-qty-input" id="input_po_qty_${item.id}" name="quantity[]" min="1" value="1" required>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm text-end shadow-none po-calc-trigger po-price-input" id="input_po_price_${item.id}" name="import_price[]" min="0" value="${parseInt(item.cost_price)}" required>
            </td>
            <td class="text-end font-monospace fw-bold text-primary pe-3 row-total-amount-txt">
                ${parseInt(item.cost_price).toLocaleString('en-US')}đ
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm text-danger border-0 shadow-none" onclick="removeOrderRow(${item.id})"><i class="bi bi-trash"></i></button>
            </td>
        </tr>`;
        
        orderTableBody.insertAdjacentHTML('beforeend', rowHtml);
        calculateOrderTotal();
        document.getElementById('input_po_qty_${item.id}').focus();
    }

    function removeOrderRow(variantId) {
        document.getElementById('tr_po_' + variantId).remove();
        if (orderTableBody.querySelectorAll('tr:not(#trOrderEmptyMsg)').length === 0) {
            orderTableBody.appendChild(orderEmptyMsg);
        }
        calculateOrderTotal();
    }

    function calculateOrderTotal() {
        let orderTotal = 0;
        const rows = orderTableBody.querySelectorAll('.po-item-row');
        
        rows.forEach(row => {
            const qty = parseInt(row.querySelector('.po-qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.po-price-input').value) || 0;
            const rowAmount = qty * price;
            orderTotal += rowAmount;
            
            row.querySelector('.row-total-amount-txt').innerText = rowAmount.toLocaleString('en-US') + 'đ';
        });

        document.getElementById('txtDisplayTotalAmount').innerText = orderTotal.toLocaleString('en-US') + 'đ';
    }

    orderTableBody.addEventListener('input', function(e) {
        if (e.target.classList.contains('po-calc-trigger')) {
            calculateOrderTotal();
        }
    });

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

        const formOrder = document.getElementById('createPurchaseOrderForm');
        if (formOrder) {
            formOrder.addEventListener('submit', function(e) {
                e.preventDefault();

                const rows = orderTableBody.querySelectorAll('.po-item-row');
                if (rows.length === 0) {
                    showToast('Lỗi: Đơn nhập hàng hóa phải có ít nhất một mặt hàng!', 'error');
                    return;
                }

                const btnSubmit = formOrder.querySelector('.btn-submit-save-order');
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Xử lý...`;

                const formData = new FormData(formOrder);
                fetch('/supplier/addOrder', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = '/supplier/orders';
                        }, 1000);
                    } else {
                        btnSubmit.disabled = false;
                        btnSubmit.innerText = 'Lưu đơn nhập';
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    btnSubmit.disabled = false;
                    btnSubmit.innerText = 'Lưu đơn nhập';
                    console.error('Error:', error);
                    showToast('Có lỗi kết nối hệ thống xảy ra!', 'error');
                });
            });
        }
    });