<?php
require_once 'views/layout/header.php';
$suppliers = $suppliers ?? [];
$variants = $variants ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1" style="font-size: 22px; letter-spacing: -0.5px;">Lập đơn nhập hàng hóa mới</h4>
        <p class="text-muted small mb-0" style="font-size: 14px;">Chọn đối tác cung ứng và gõ tìm kiếm sản phẩm phân tầng để khởi tạo giá vốn nhập</p>
    </div>
    <a href="/supplier/orders" class="btn btn-light border fw-semibold px-3 py-2 rounded-2 shadow-none small" style="font-size: 14px;"><i class="bi bi-arrow-left me-1"></i> Quay lại danh sách đơn</a>
</div>

<form action="/supplier/addOrder" method="POST" id="createPurchaseOrderForm">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-2">1. Chọn nhà cung cấp đối tác</label>
                    <select class="form-select bg-light shadow-none" name="supplier_id" required>
                        <option value="">-- Chọn đối tác cấp hàng --</option>
                        <?php foreach ($suppliers as $s): ?>
                            <?php if ($s['status'] == 1): ?>
                                <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['supplier_name']); ?> (<?php echo $s['supplier_code']; ?>)</option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="position-relative mb-3">
                    <label class="form-label small fw-bold text-dark mb-2">2. Gõ tìm kiếm mặt hàng</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control bg-light border-start-0 shadow-none" id="txtSearchOrderVariant" placeholder="Nhập tên, mã gốc hoặc mã vạch..." autocomplete="off">
                    </div>
                    <div class="position-absolute w-100 bg-white border rounded-3 shadow-lg mt-1 d-none" id="orderSearchDropdown" style="z-index: 1050; max-height: 350px; overflow-y: auto;">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-2">3. Ghi chú chứng từ đơn</label>
                    <textarea class="form-control bg-light shadow-none small" name="note" rows="3" placeholder="Ghi chú điều khoản thanh toán, số hóa đơn đỏ, ghi chú giao nhận..." style="font-size:13px;"></textarea>
                </div>

                <div class="p-3 bg-light rounded-3 border">
                    <div class="text-secondary small fw-semibold mb-1">TỔNG GIÁ TRỊ ĐƠN NHẬP</div>
                    <h3 class="fw-bold m-0 font-monospace text-primary" id="txtDisplayTotalAmount">0đ</h3>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white border-bottom p-3 fw-bold text-dark" style="font-size: 15px;">
                    <i class="bi bi-cart-plus text-primary me-1"></i> Danh sách mặt hàng, quy cách khai báo nhập kho
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="min-height: 300px;">
                        <table class="table align-middle table-hover mb-0" id="tblPurchaseItems">
                            <thead class="table-light text-center small fw-bold font-monospace border-bottom">
                                <tr>
                                    <th class="text-start ps-4 py-3">Sản phẩm quy cách</th>
                                    <th style="width: 110px;">Số lượng</th>
                                    <th style="width: 160px;">Đơn giá nhập</th>
                                    <th style="width: 150px;">Thành tiền</th>
                                    <th style="width: 60px;">Xóa</th>
                                </tr>
                            </thead>
                            <tbody id="purchaseItemsTableBody">
                                <tr id="trOrderEmptyMsg">
                                    <td colspan="5" class="text-center p-5 text-muted">
                                        <i class="bi bi-box-seam d-block mb-2 fs-2 text-secondary" style="opacity: 0.5;"></i> Đơn hàng trống. Hãy tìm chọn mặt hàng ở khung bên trái để thêm vào danh sách nhập!
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top p-3 text-end">
                    <a href="/supplier/orders" class="btn btn-light border fw-semibold rounded-2 px-4 shadow-none me-2">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary fw-semibold rounded-2 px-5 shadow-none btn-submit-save-order" style="background-color: #3c50e0; border-color: #3c50e0;">Lưu đơn nhập</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    const allOrderVariants = <?php echo json_encode($variants); ?>;
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
</script>

<?php require_once 'views/layout/footer.php'; ?>