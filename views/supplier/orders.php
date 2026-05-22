<?php
require_once 'views/layout/header.php';
$orders = $orders ?? [];
?>

<div class="card card-custom bg-white border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-4">
        <form action="/supplier/orders" method="GET" id="filterPurchaseOrdersForm" class="row g-3 align-items-end" novalidate>
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-secondary">Tìm kiếm đơn hàng</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control bg-light border-start-0 shadow-none" name="search" placeholder="Nhập mã đơn hàng hoặc tên nhà cung cấp...">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-secondary">Từ ngày</label>
                <input type="date" class="form-control bg-light shadow-none" name="start_date">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-secondary">Đến ngày</label>
                <input type="date" class="form-control bg-light shadow-none" name="end_date">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-semibold shadow-none" style="background-color: #3c50e0; border-color: #3c50e0;">Lọc đơn</button>
                <button type="button" id="btnResetOrderFilter" class="btn btn-light border w-100 fw-semibold shadow-none">Xóa lọc</button>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom bg-white mb-4">
    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold text-dark mb-1" style="font-size: 18px;">Lịch sử đơn nhập hàng hóa</h4>
            <p class="text-muted small mb-0" style="font-size: 13px;">Quản lý danh sách đơn mua từ nhà cung cấp, đối soát tổng tiền và duyệt cộng tồn kho thực tế vào thẻ kho</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/supplier/index" class="btn btn-light border fw-semibold px-3 py-2 rounded-2 shadow-none small" style="font-size: 14px;"><i class="bi bi-building me-1"></i> Danh sách nhà cung cấp</a>
            <a href="/supplier/createOrder" class="btn btn-primary fw-semibold px-3 py-2 rounded-2 d-flex align-items-center gap-2 shadow-none" style="background-color: #3c50e0; border-color: #3c50e0; font-size: 14px;">
                <i class="bi bi-plus-lg"></i> Lập đơn nhập hàng
            </a>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tblPurchaseOrders">
                <thead class="table-light text-secondary font-monospace small border-bottom" style="background-color: #f8fafc;">
                    <tr>
                        <th class="ps-4 py-3 text-secondary" style="font-size: 12px;">MÃ ĐƠN HÀNG</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">NHÀ CUNG CẤP</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">TỔNG TIỀN ĐƠN</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">NGƯỜI LẬP</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">NGÀY KHỞI TẠO</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">TRẠNG THÁI</th>
                        <th class="text-end pe-4 py-3 text-secondary" style="font-size: 12px;">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody class="text-dark" style="font-size: 14px;">
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $o): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;" id="order-row-<?php echo $o['id']; ?>">
                                <td class="ps-4 fw-bold font-monospace text-secondary"><?php echo $o['purchase_code']; ?></td>
                                <td class="fw-bold text-dark"><?php echo htmlspecialchars($o['supplier_name']); ?></td>
                                <td class="font-monospace fw-bold text-primary"><?php echo number_format($o['total_amount'], 0, '.', ','); ?>đ</td>
                                <td class="text-secondary"><?php echo htmlspecialchars($o['username']); ?></td>
                                <td class="text-muted font-monospace small"><?php echo date('d-m-Y H:i:s', strtotime($o['created_at'])); ?></td>
                                <td>
                                    <span class="badge rounded-1 px-2 py-1 fw-semibold status-badge-el" style="<?php echo $o['status'] == 1 ? 'background-color: #def7ec; color: #03543f; font-size: 11px;' : 'background-color: #fef2f2; color: #9b1c1c; font-size: 11px;'; ?>">
                                        <?php echo $o['status'] == 1 ? 'Đã nhập kho' : 'Chờ nhập kho'; ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1 btn-container-el">
                                        <button class="btn btn-sm btn-light border text-secondary px-2 shadow-none" onclick="viewOrderDetail(<?php echo $o['id']; ?>)"><i class="bi bi-eye"></i> Chi tiết</button>
                                        <?php if ($o['status'] == 0): ?>
                                            <button type="button" class="btn btn-sm btn-success text-white bg-success px-2 shadow-none btn-approve-order-trigger" data-id="<?php echo $o['id']; ?>"><i class="bi bi-check-lg"></i> Duyệt kho</button>
                                            <button type="button" class="btn btn-sm btn-outline-danger px-2 shadow-none btn-delete-order-trigger" data-id="${o.id}" data-code="${o.purchase_code}"><i class="bi bi-trash"></i> Hủy đơn</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center p-5 text-secondary"><i class="bi bi-receipt d-block mb-2" style="font-size: 40px; color: #cbd5e1;"></i>Chưa có đơn nhập hàng nào được lập!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg rounded-4" style="overflow: hidden;">
            <div class="modal-header p-4 border-bottom-0 bg-white">
                <div>
                    <span class="badge rounded-2 px-2 py-1 mb-1 font-monospace fw-bold" id="lblOrderCode" style="background-color: #e0e4fd; color: #3c50e0; font-size: 11px;"></span>
                    <h5 class="modal-title fw-bold text-dark" style="font-size: 20px;">Chi tiết đơn hàng nhập quầy kho</h5>
                </div>
                <button type="button" class="btn-close shadow-none bg-light p-2 rounded-circle" data-bs-dismiss="modal" style="font-size: 12px;"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <div class="p-3 mb-4 rounded-3 small text-secondary d-flex flex-wrap justify-content-between align-items-center bg-light border">
                    <div><i class="bi bi-building text-primary"></i> <strong>Nhà cung ứng:</strong> <span id="lblSupplier" class="text-dark fw-bold"></span></div>
                    <div><i class="bi bi-person text-primary"></i> <strong>Người lập đơn:</strong> <span id="lblUser"></span></div>
                    <div><i class="bi bi-clock text-primary"></i> <strong>Thời gian:</strong> <span id="lblTime"></span></div>
                    <div><i class="bi bi-cash text-primary"></i> <strong>Tổng tiền hóa đơn:</strong> <span id="lblTotalAmount" class="text-success fw-bold font-monospace"></span></div>
                </div>
                <div class="mb-3 d-none" id="noteContainer">
                    <label class="form-label small fw-bold text-secondary mb-1">Ghi chú chứng từ:</label>
                    <div class="p-2 border rounded bg-light small" id="lblNote"></div>
                </div>
                <div class="border rounded-3 overflow-hidden shadow-none">
                    <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                        <thead class="table-light border-bottom" style="background-color: #f8fafc;">
                            <tr>
                                <th class="ps-4 py-3 text-secondary fw-semibold font-monospace" style="font-size: 12px;">TÊN SẢN PHẨM CHÍNH & QUY CÁCH</th>
                                <th class="py-3 text-center text-secondary fw-semibold font-monospace" style="font-size: 12px;">MÃ VẠCH BARCODE</th>
                                <th class="py-3 text-center text-secondary fw-semibold font-monospace" style="font-size: 12px;">SỐ LƯỢNG NHẬP</th>
                                <th class="py-3 text-end text-secondary fw-semibold font-monospace" style="font-size: 12px;">ĐƠN GIÁ VỐN</th>
                                <th class="py-3 text-end pe-4 text-secondary fw-semibold font-monospace" style="font-size: 12px;">THÀNH TIỀN DÒNG</th>
                            </tr>
                        </thead>
                        <tbody id="orderDetailTableBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer p-3 bg-light border-top-0 d-flex justify-content-end">
                <button type="button" class="btn btn-white border fw-semibold rounded-2 px-4 shadow-none small" style="font-size: 14px;" data-bs-dismiss="modal">Đóng lại</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteOrderConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow rounded-3">
            <div class="modal-body p-4 text-center">
                <div class="text-danger mb-3"><i class="bi bi-trash fs-1"></i></div>
                <h5 class="fw-bold text-dark mb-1">Hủy đơn nhập hàng?</h5>
                <p class="text-muted small mb-4">Bạn chắc chắn muốn hủy bỏ và xóa vĩnh viễn đơn nhập nháp <strong class="text-dark font-monospace" id="delOrderCode"></strong> chứ?</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light border px-3 fw-semibold rounded-2 small shadow-none" data-bs-dismiss="modal">Không</button>
                    <button type="button" id="btnDoDeleteOrder" class="btn btn-danger bg-danger px-4 fw-semibold rounded-2 text-white shadow-none">Xác nhận Hủy</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
</script>

<?php require_once 'views/layout/footer.php'; ?>