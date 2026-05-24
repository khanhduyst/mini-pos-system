let currentDeleteOrderId = null;

function confirmCancelOrder(id, code) {
    currentDeleteOrderId = id;
    document.getElementById('delOrderCode').innerText = code;
    const modalEl = document.getElementById('deleteOrderConfirmModal');
    let instance = bootstrap.Modal.getInstance(modalEl);
    if (!instance) instance = new bootstrap.Modal(modalEl);
    instance.show();
}

function formatDateTime(dateString) {
    if (!dateString) return '---';
    const dateObj = new Date(dateString);
    if (isNaN(dateObj.getTime())) return dateString;
    const day = ('0' + dateObj.getDate()).slice(-2);
    const month = ('0' + (dateObj.getMonth() + 1)).slice(-2);
    const year = dateObj.getFullYear();
    const hours = ('0' + dateObj.getHours()).slice(-2);
    const minutes = ('0' + dateObj.getMinutes()).slice(-2);
    const seconds = ('0' + dateObj.getSeconds()).slice(-2);
    return `${day}-${month}-${year} ${hours}:${minutes}:${seconds}`;
}

function viewOrderDetail(id) {
    fetch('/supplier/orderDetail?id=' + id)
        .then(response => response.json())
        .then(data => {
            document.getElementById('lblOrderCode').innerText = 'ĐƠN NHẬP: ' + data.sheet.purchase_code;
            document.getElementById('lblSupplier').innerText = data.sheet.supplier_name;
            document.getElementById('lblUser').innerText = data.sheet.username;
            document.getElementById('lblTime').innerText = formatDateTime(data.sheet.created_at);
            document.getElementById('lblTotalAmount').innerText = parseFloat(data.sheet.total_amount).toLocaleString('en-US') + 'đ';
            
            const noteBox = document.getElementById('noteContainer');
            if(data.sheet.note) {
                noteBox.classList.remove('d-none');
                document.getElementById('lblNote').innerText = data.sheet.note;
            } else {
                noteBox.classList.add('d-none');
            }

            let html = '';
            data.details.forEach(item => {
                const barcodeStr = item.barcode ? item.barcode : '---';
                html += `
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td class="ps-4 fw-bold text-dark">${item.product_name} <small class="text-muted d-block fw-normal font-monospace">${item.variant_name}</small></td>
                        <td class="text-center font-monospace small text-secondary">${barcodeStr}</td>
                        <td class="text-center font-monospace fw-bold text-dark">${parseInt(item.quantity).toLocaleString('en-US')}</td>
                        <td class="text-end font-monospace text-secondary">${parseFloat(item.import_price).toLocaleString('en-US')}đ</td>
                        <td class="text-end pe-4 font-monospace fw-bold text-primary">${parseFloat(item.amount).toLocaleString('en-US')}đ</td>
                    </tr>`;
            });
            document.getElementById('orderDetailTableBody').innerHTML = html;
            const detailModalEl = document.getElementById('orderDetailModal');
            let detailInstance = bootstrap.Modal.getInstance(detailModalEl);
            if (!detailInstance) detailInstance = new bootstrap.Modal(detailModalEl);
            detailInstance.show();
        });
}

document.addEventListener("DOMContentLoaded", function() {
    const filterForm = document.getElementById('filterPurchaseOrdersForm');

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
            toastDiv.style.opacity = '1'; toastDiv.style.transform = 'translateY(0)';
        }, 50);
        setTimeout(() => {
            toastDiv.style.opacity = '0'; toastDiv.style.transform = 'translateY(-20px)';
            setTimeout(() => toastDiv.remove(), 300);
        }, 4000);
    }

    function loadPurchaseOrders() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams();
        for (const [key, value] of formData.entries()) {
            params.append(key, value);
        }
        params.append('ajax', '1');

        fetch('/supplier/orders?' + params.toString())
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderOrderTableRows(data.orders);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function renderOrderTableRows(orders) {
        const tbody = document.querySelector('#tblPurchaseOrders tbody');
        tbody.innerHTML = '';

        if (!orders || orders.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center p-5 text-secondary"><i class="bi bi-receipt d-block mb-2" style="font-size: 40px; color: #cbd5e1;"></i>Không tìm thấy đơn nhập hàng nào phù hợp!</td></tr>`;
            return;
        }

        orders.forEach(o => {
            const badgeStyle = o.status == 1 ? 'background-color: #def7ec; color: #03543f; font-size: 11px;' : 'background-color: #fef2f2; color: #9b1c1c; font-size: 11px;';
            const badgeTxt = o.status == 1 ? 'Đã nhập kho' : 'Chờ nhập kho';
            
            let actionButtons = `<button class="btn btn-sm btn-light border text-secondary px-2 shadow-none" onclick="viewOrderDetail(${o.id})"><i class="bi bi-eye"></i> Chi tiết</button>`;
            if (o.status == 0) {
                actionButtons += `
                    <button type="button" class="btn btn-sm btn-success text-white bg-success px-2 shadow-none btn-approve-order-trigger" data-id="${o.id}"><i class="bi bi-check-lg"></i> Duyệt kho</button>
                    <button type="button" class="btn btn-sm btn-outline-danger px-2 shadow-none btn-delete-order-trigger" data-id="${o.id}" data-code="${o.purchase_code}"><i class="bi bi-trash"></i> Hủy đơn</button>`;
            }

            const tr = document.createElement('tr');
            tr.style.borderBottom = '1px solid #f1f5f9';
            tr.id = `order-row-${o.id}`;
            tr.innerHTML = `
                <td class="ps-4 fw-bold font-monospace text-secondary">${o.purchase_code}</td>
                <td class="fw-bold text-dark">${o.supplier_name}</td>
                <td class="font-monospace fw-bold text-primary">${parseFloat(o.total_amount).toLocaleString('en-US')}đ</td>
                <td class="text-secondary">${o.username}</td>
                <td class="text-muted font-monospace small">${formatDateTime(o.created_at)}</td>
                <td><span class="badge rounded-1 px-2 py-1 fw-semibold" style="${badgeStyle}">${badgeTxt}</span></td>
                <td class="text-end pe-4"><div class="d-flex justify-content-end gap-1 btn-container-el">${actionButtons}</div></td>`;
            tbody.appendChild(tr);
        });
    }

    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            loadPurchaseOrders();
        });
    }

    const btnReset = document.getElementById('btnResetOrderFilter');
    if (btnReset) {
        btnReset.addEventListener('click', function(e) {
            e.preventDefault();
            filterForm.reset();
            loadPurchaseOrders();
        });
    }

    document.querySelector('#tblPurchaseOrders tbody').addEventListener('click', function(e) {
        const approveBtn = e.target.closest('.btn-approve-order-trigger');
        if (approveBtn) {
            e.preventDefault();
            const id = approveBtn.getAttribute('data-id');
            const originalHtml = approveBtn.innerHTML;
            approveBtn.disabled = true;
            approveBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;

            fetch('/supplier/approveOrder?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success) { loadPurchaseOrders(); showToast(data.message, 'success'); } 
                    else { approveBtn.disabled = false; approveBtn.innerHTML = originalHtml; showToast(data.message, 'error'); }
                })
                .catch(error => { approveBtn.disabled = false; approveBtn.innerHTML = originalHtml; showToast('Có lỗi hệ thống xảy ra!', 'error'); });
        }

        const deleteBtn = e.target.closest('.btn-delete-order-trigger');
        if (deleteBtn) {
            const id = deleteBtn.getAttribute('data-id');
            const code = deleteBtn.getAttribute('data-code');
            confirmCancelOrder(id, code);
        }
    });

    document.getElementById('btnDoDeleteOrder').addEventListener('click', function(e) {
        e.preventDefault();
        if (!currentDeleteOrderId) return;
        const btnDoDel = e.target;
        btnDoDel.disabled = true;

        fetch('/supplier/deleteOrder?id=' + currentDeleteOrderId)
            .then(response => response.json())
            .then(data => {
                btnDoDel.disabled = false;
                const instance = bootstrap.Modal.getInstance(document.getElementById('deleteOrderConfirmModal'));
                if (instance) instance.hide();
                if (data.success) { loadPurchaseOrders(); showToast(data.message, 'success'); } 
                else { showToast(data.message, 'error'); }
                currentDeleteOrderId = null;
            })
            .catch(error => { btnDoDel.disabled = false; showToast('Có lỗi hệ thống xảy ra!', 'error'); currentDeleteOrderId = null; });
    });
});