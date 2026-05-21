<?php
require_once 'views/layout/header.php';
?>

<div class="card card-custom bg-white mb-4">
    <div class="card-header bg-white border-bottom p-4">
        <div>
            <h4 class="fw-bold text-dark mb-1" style="font-size: 18px;">Quầy bán hàng & Xuất hóa đơn POS</h4>
            <p class="text-muted small mb-0" style="font-size: 13px;">Gõ tìm kiếm tên hàng hoặc quét barcode để thêm
                nhanh mặt hàng vào phiếu xuất quầy</p>
        </div>
    </div>

    <div class="card-body p-4 bg-light">
        <div class="row g-4">
            <div class="col-lg-7 col-xl-8">
                <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4" style="min-height: 65vh;">
                    <div class="position-relative mb-4">
                        <label class="form-label small fw-bold text-secondary" style="font-size: 13px;">Tìm kiếm sản
                            phẩm xuất kho</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i
                                    class="bi bi-search text-muted"></i></span>
                            <input type="text" id="search-product"
                                class="form-control rounded-end-2 border-start-0 shadow-none"
                                placeholder="Nhập tên hàng hóa, quy cách hoặc quét mã barcode vạch..."
                                oninput="searchLiveProduct()">
                        </div>

                        <div id="search-results-dropdown"
                            class="position-absolute w-100 bg-white border shadow rounded-3 mt-1 d-none"
                            style="z-index: 9999; max-height: 280px; overflow-y: auto;">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 small" style="font-size: 13px;">
                                    <tbody id="search-results-body"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3" style="font-size: 15px;"><i
                            class="bi bi-receipt text-primary me-2"></i>Chi tiết danh sách hàng hóa xuất kho</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-secondary font-monospace small" style="font-size: 11px;">
                                <tr>
                                    <th class="ps-3 py-3">TÊN HÀNG HÓA QUY CÁCH</th>
                                    <th class="py-3 text-center">ĐƠN GIÁ</th>
                                    <th class="py-3 text-center" style="width: 140px;">SỐ LƯỢNG</th>
                                    <th class="py-3 text-center">THÀNH TIỀN</th>
                                    <th class="text-end pe-3 py-3">HÀNH ĐỘNG</th>
                                </tr>
                            </thead>
                            <tbody id="cart-table-body" style="font-size: 14px;">
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-secondary" id="cart-empty-row">
                                        <i class="bi bi-basket display-6 d-block mb-2 text-muted"></i>
                                        Phiếu xuất đang trống. Vui lòng tìm kiếm sản phẩm phía trên!
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-xl-4">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4 d-flex flex-column"
                    style="min-height: 65vh;">
                    <h5 class="fw-bold text-dark mb-4" style="font-size: 15px;"><i
                            class="bi bi-person-lines-fill text-warning me-2"></i>Thông tin hóa đơn & Khách nợ</h5>

                    <div class="mb-3 position-relative">
                        <label class="form-label small fw-bold text-secondary" style="font-size: 13px;">Khách hàng nhận
                            hàng</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i
                                    class="bi bi-person text-muted"></i></span>
                            <input type="text" id="search-customer"
                                class="form-control border-start-0 border-end-0 shadow-none fw-bold"
                                placeholder="Nhập tên khách hàng hoặc số điện thoại..." oninput="searchLiveCustomer()">
                            <input type="hidden" id="selected_customer_id" value="0">
                            <input type="hidden" id="selected_customer_debt" value="0">
                            <button class="btn btn-outline-primary shadow-none border d-flex align-items-center px-3"
                                type="button" onclick="openQuickCustomerModal()">
                                <i class="bi bi-plus-lg fs-5"></i>
                            </button>
                        </div>

                        <div id="customer-results-dropdown"
                            class="position-absolute w-100 bg-white border shadow rounded-3 mt-1 d-none"
                            style="z-index: 9999; max-height: 220px; overflow-y: auto;">
                            <div class="list-group list-group-flush small" id="customer-results-list"
                                style="font-size: 13px;"></div>
                        </div>
                    </div>

                    <div class="mb-4" id="block-walkin-active" style="display: none;">
                        <div class="p-3 rounded-3 text-success d-flex align-items-center justify-content-between small fw-semibold"
                            style="background-color: #def7ec; border-left: 4px solid #0e9f6e;">
                            <div><i class="bi bi-person-check-fill me-2"></i> Chế độ: Khách lẻ vãng lai</div>
                        </div>
                    </div>

                    <div class="mb-4" id="block-customer-badge" style="display: none;">
                        <div class="p-3 rounded-3 border bg-light position-relative">
                            <button type="button" class="btn-close position-absolute top-0 end-0 m-2 small shadow-none"
                                style="font-size: 10px;" onclick="resetToWalkInCustomer()"></button>
                            <div class="fw-bold text-dark fs-6 mb-1" id="badge-cust-name">---</div>
                            <div class="text-secondary small mb-2"><i class="bi bi-telephone-fill me-1"></i> SĐT: <span
                                    id="badge-cust-phone">---</span></div>
                            <div class="d-flex justify-content-between text-danger small fw-semibold">
                                <span>Công nợ cũ hiện tại:</span>
                                <span id="badge-cust-debt">0đ</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4" id="block-pay-method" style="display: none;">
                        <label class="form-label small fw-bold text-secondary" style="font-size: 13px;">Hình thức thanh
                            toán mặc định</label>
                        <div class="d-flex gap-2">
                            <input type="radio" class="btn-check" name="pay_method" id="pay_cash" value="cash" checked
                                onchange="togglePayInputs()">
                            <label class="btn btn-outline-secondary w-100 small py-2 fw-semibold shadow-none"
                                for="pay_cash" style="font-size: 13px;">
                                <i class="bi bi-cash-coin me-1"></i> Khách thanh toán
                            </label>

                            <input type="radio" class="btn-check" name="pay_method" id="pay_debt" value="debt"
                                onchange="togglePayInputs()">
                            <label class="btn btn-outline-danger w-100 small py-2 fw-semibold shadow-none"
                                for="pay_debt" style="font-size: 13px;">
                                <i class="bi bi-journal-bookmark me-1"></i> Khách xin nợ
                            </label>
                        </div>
                    </div>

                    <div class="mb-4" id="block-customer-paid" style="display: none;">
                        <label class="form-label small fw-bold text-secondary" style="font-size: 13px;">Số tiền khách
                            đưa (đ)</label>
                        <input type="number" id="customer_paid"
                            class="form-control font-monospace fw-bold shadow-none text-primary" value="0"
                            oninput="calculateDebtLogic()" style="font-size: 16px; background-color: #f0f7ff;">
                    </div>

                    <div class="rounded-3 p-3 mb-4 mt-2" style="background-color: #f8fafc;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary small" style="font-size: 13px;">Tổng tiền hóa đơn đơn
                                này:</span>
                            <span class="fw-bold font-monospace text-dark" id="txt-total"
                                style="font-size: 15px;">0đ</span>
                        </div>
                        <hr class="my-2 border-secondary-subtle">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-dark small fw-bold" style="font-size: 13px;">Tổng cộng đơn hàng:</span>
                            <span class="fw-bold font-monospace text-dark fs-5" id="txt-final-total">0đ</span>
                        </div>

                        <div class="d-flex justify-content-between my-2 pt-2 border-top border-dashed" id="row-new-debt"
                            style="display: none !important;">
                            <span class="small fw-bold" style="font-size: 13px;" id="lbl-debt-status">Tiền thừa thối
                                khách:</span>
                            <span class="fw-bold font-monospace" id="txt-new-debt" style="font-size: 15px;">0đ</span>
                        </div>
                    </div>

                    <button class="btn btn-primary w-100 fw-bold py-3 text-white rounded-3 shadow-none border-0 mt-auto"
                        id="btn-submit-pos" onclick="submitOrder()" disabled
                        style="background-color: #3c50e0; font-size: 14px;">
                        <i class="bi bi-check-circle me-1"></i>XÁC NHẬN XUẤT HÀNG & IN ĐƠN
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="quickCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header p-4 border-bottom-0">
                <h5 class="modal-title fw-bold text-dark" style="font-size: 18px;"><i
                        class="bi bi-person-plus text-primary me-2"></i>Thêm nhanh khách hàng mới</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <form id="quickCustomerForm">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Họ và tên khách hàng</label>
                        <input type="text" id="quick_full_name" class="form-control shadow-none" required
                            placeholder="Nhập tên khách hàng...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Số điện thoại</label>
                        <input type="text" id="quick_phone" class="form-control shadow-none" required
                            placeholder="Nhập số điện thoại...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Địa chỉ (Không bắt buộc)</label>
                        <input type="text" id="quick_address" class="form-control shadow-none"
                            placeholder="Nhập địa chỉ của khách...">
                    </div>
                </form>
            </div>
            <div class="modal-footer p-3 bg-light border-top-0">
                <button type="button" class="btn btn-white border fw-semibold rounded-2 px-3 shadow-none small"
                    data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="button" class="btn btn-primary fw-semibold rounded-2 px-4 shadow-none small"
                    onclick="submitQuickCustomer()" style="background-color: #3c50e0; border-color: #3c50e0;">Lưu khách
                    hàng</button>
            </div>
        </div>
    </div>
</div>

<script>
let cart = {};
let allProducts = <?php echo json_encode($variants ?? []); ?>;
let allCustomers = <?php echo json_encode($customers ?? []); ?>;
let globalTotalBill = 0;

function removeVietnameseTones(str) {
    str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
    str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
    str = str.replace(/ò|ó|ọ|ỏ|ã|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
    str = str.replace(/đ/g, "md");
    str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, "A");
    str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/g, "E");
    str = str.replace(/Ì|Í|Ị|||Ĩ/g, "I");
    str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/g, "O");
    str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, "U");
    str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g, "Y");
    str = str.replace(/Đ/g, "D");
    str = str.replace(/\u0300|\u0301|\u0303|\u0309|\u0323/g, "");
    str = str.replace(/\u02C6|\u0306|\u031B/g, "");
    return str;
}

function searchLiveProduct() {
    const rawKeyword = document.getElementById('search-product').value.trim().toLowerCase();
    const dropdown = document.getElementById('search-results-dropdown');
    const tbody = document.getElementById('search-results-body');

    if (rawKeyword === '') {
        dropdown.classList.add('d-none');
        return;
    }

    const keywordNoTone = removeVietnameseTones(rawKeyword);

    let filtered = allProducts.filter(p => {
        const prodNoTone = removeVietnameseTones(p.product_name.toLowerCase());
        const varNoTone = removeVietnameseTones(p.variant_name.toLowerCase());
        return prodNoTone.includes(keywordNoTone) ||
            varNoTone.includes(keywordNoTone) ||
            (p.barcode && p.barcode.toLowerCase().includes(rawKeyword));
    });

    if (filtered.length === 0) {
        tbody.innerHTML = '<tr><td class="text-center text-muted p-3">Không tìm thấy sản phẩm phù hợp</td></tr>';
    } else {
        let html = '';
        filtered.forEach(p => {
            html += `
                <tr style="cursor: pointer;" onclick="selectProductFromSearch(${p.id}, '${escapeHtml(p.product_name + ' - ' + p.variant_name)}', ${p.sale_price}, ${p.stock_qty})">
                    <td class="ps-3" style="width: 50px;"><img src="${p.image ? p.image : 'uploads/default.jpg'}" class="rounded" style="width: 35px; height: 35px; object-fit: cover;"></td>
                    <td>
                        <div class="fw-bold text-dark text-start">${p.product_name}</div>
                        <small class="text-muted font-monospace d-block text-start">${p.variant_name} | Barcode: ${p.barcode ? p.barcode : '---'}</small>
                    </td>
                    <td class="text-primary fw-bold font-monospace">${new Intl.NumberFormat('vi-VN').format(p.sale_price)}đ</td>
                    <td class="text-end pe-3 text-secondary">Tồn: <span class="badge ${p.stock_qty > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'}">${p.stock_qty}</span></td>
                </tr>`;
        });
        tbody.innerHTML = html;
    }
    dropdown.classList.remove('d-none');
}

function selectProductFromSearch(id, name, price, stock) {
    if (stock <= 0) {
        alert("Sản phẩm này đã hết hàng tồn trên kệ!");
        return;
    }
    if (cart[id]) {
        if (cart[id].qty >= stock) {
            alert("Không thể xuất quá số lượng tồn kho hiện tại!");
            return;
        }
        cart[id].qty++;
    } else {
        cart[id] = {
            name: name,
            price: price,
            qty: 1,
            max_stock: stock
        };
    }
    document.getElementById('search-product').value = '';
    document.getElementById('search-results-dropdown').classList.add('d-none');
    renderCartTable();
}

function updateQty(id, delta) {
    if (cart[id]) {
        cart[id].qty += delta;
        if (cart[id].qty > cart[id].max_stock) {
            alert("Vượt quá tồn kho hệ thống cho phép!");
            cart[id].qty = cart[id].max_stock;
        }
        if (cart[id].qty <= 0) {
            delete cart[id];
        }
        renderCartTable();
    }
}

function removeItem(id) {
    if (cart[id]) {
        delete cart[id];
        renderCartTable();
    }
}

function renderCartTable() {
    const tbody = document.getElementById('cart-table-body');
    let keys = Object.keys(cart);

    if (keys.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-5 text-secondary">
                    <i class="bi bi-basket display-6 d-block mb-2 text-muted"></i>
                    Phiếu xuất đang trống. Vui lòng tìm kiếm sản phẩm phía trên!
                </td>
            </tr>`;
        document.getElementById('btn-submit-pos').disabled = true;
        document.getElementById('txt-total').innerText = '0đ';
        globalTotalBill = 0;
        calculateDebtLogic();
        return;
    }

    document.getElementById('btn-submit-pos').disabled = false;
    let html = '';
    let total = 0;

    keys.forEach(id => {
        let item = cart[id];
        let subtotal = item.price * item.qty;
        total += subtotal;

        html += `
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td class="ps-3 fw-bold text-dark text-start">${item.name}</td>
                <td class="text-center font-monospace text-secondary">${new Intl.NumberFormat('vi-VN').format(item.price)}đ</td>
                <td class="text-center">
                    <div class="input-group input-group-sm m-auto" style="width: 110px;">
                        <button class="btn btn-light border shadow-none" onclick="updateQty(${id}, -1)">-</button>
                        <input type="text" class="form-control text-center bg-white border font-monospace fw-bold shadow-none" value="${item.qty}" readonly style="font-size: 13px;">
                        <button class="btn btn-light border shadow-none" onclick="updateQty(${id}, 1)">+</button>
                    </div>
                </td>
                <td class="text-center font-monospace fw-bold text-primary">${new Intl.NumberFormat('vi-VN').format(subtotal)}đ</td>
                <td class="text-end pe-3">
                    <button class="btn btn-sm btn-link text-danger shadow-none p-0 border-0 text-decoration-none small" onclick="removeItem(${id})"><i class="bi bi-trash3"></i> Xóa</button>
                </td>
            </tr>`;
    });

    tbody.innerHTML = html;
    document.getElementById('txt-total').innerText = new Intl.NumberFormat('vi-VN').format(total) + 'đ';
    globalTotalBill = total;

    const paidInput = document.getElementById('customer_paid');
    if (!document.getElementById('pay_debt').checked) {
        paidInput.value = globalTotalBill;
    }

    calculateDebtLogic();
}

function searchLiveCustomer() {
    const rawKeyword = document.getElementById('search-customer').value.trim().toLowerCase();
    const dropdown = document.getElementById('customer-results-dropdown');
    const listGroup = document.getElementById('customer-results-list');

    if (rawKeyword === '') {
        dropdown.classList.add('d-none');
        return;
    }

    const keywordNoTone = removeVietnameseTones(rawKeyword);

    let filtered = allCustomers.filter(c => {
        const nameNoTone = removeVietnameseTones(c.full_name.toLowerCase());
        return nameNoTone.includes(keywordNoTone) || c.phone.includes(rawKeyword);
    });

    let html = '';

    if (filtered.length > 0) {
        filtered.forEach(c => {
            html += `
                <button type="button" class="list-group-item list-group-item-action py-2 text-start" onclick="selectCustomer(${c.id}, '${escapeHtml(c.full_name)}', '${c.phone}', ${c.debt})">
                    <div class="fw-bold text-dark">${c.full_name}</div>
                    <small class="text-muted">SĐT: ${c.phone} | Nợ cũ: ${new Intl.NumberFormat('vi-VN').format(c.debt)}đ</small>
                </button>`;
        });
    } else {
        html = '<div class="p-3 text-center text-muted">Không tìm thấy khách hàng phù hợp</div>';
    }

    listGroup.innerHTML = html;
    dropdown.classList.remove('d-none');
}

function selectCustomer(id, name, phone, debt) {
    document.getElementById('selected_customer_id').value = id;
    document.getElementById('selected_customer_debt').value = debt;
    document.getElementById('customer-results-dropdown').classList.add('d-none');

    const blockWalkIn = document.getElementById('block-walkin-active');
    const blockCustBadge = document.getElementById('block-customer-badge');
    const methodBlock = document.getElementById('block-pay-method');
    const paidBlock = document.getElementById('block-customer-paid');

    if (id === 0) {
        document.getElementById('search-customer').value = '';
        blockWalkIn.setAttribute('style', 'display: block;');
        blockCustBadge.setAttribute('style', 'display: none;');
        methodBlock.setAttribute('style', 'display: none !important;');
        paidBlock.setAttribute('style', 'display: none !important;');
        document.getElementById('pay_cash').checked = true;
    } else {
        document.getElementById('search-customer').value = name;
        blockWalkIn.setAttribute('style', 'display: none;');
        blockCustBadge.setAttribute('style', 'display: block;');

        document.getElementById('badge-cust-name').innerText = name;
        document.getElementById('badge-cust-phone').innerText = phone;
        document.getElementById('badge-cust-debt').innerText = new Intl.NumberFormat('vi-VN').format(debt) + 'đ';

        methodBlock.setAttribute('style', 'display: block !important;');
        paidBlock.setAttribute('style', 'display: block !important;');
    }

    document.getElementById('pay_cash').checked = true;
    const paidInput = document.getElementById('customer_paid');
    paidInput.removeAttribute('readonly');
    paidInput.value = globalTotalBill;

    calculateDebtLogic();
}

function resetToWalkInCustomer() {
    selectCustomer(0, '', '', 0);
}

function togglePayInputs() {
    const paidInput = document.getElementById('customer_paid');

    if (document.getElementById('pay_debt').checked) {
        paidInput.value = 0;
        paidInput.setAttribute('readonly', 'true');
    } else {
        paidInput.removeAttribute('readonly');
        paidInput.value = globalTotalBill;
    }
    calculateDebtLogic();
}

function calculateDebtLogic() {
    const custId = document.getElementById('selected_customer_id').value;
    const paidInput = document.getElementById('customer_paid');

    document.getElementById('txt-final-total').innerText = new Intl.NumberFormat('vi-VN').format(globalTotalBill) + 'đ';

    const rowNewDebt = document.getElementById('row-new-debt');
    const txtNewDebt = document.getElementById('txt-new-debt');
    const lblDebtStatus = document.getElementById('lbl-debt-status');

    if (custId === "0") {
        rowNewDebt.setAttribute('style', 'display: none !important;');
        return;
    }

    let customerPaid = parseFloat(paidInput.value);
    if (isNaN(customerPaid)) customerPaid = 0;

    let diff = globalTotalBill - customerPaid;

    if (document.getElementById('pay_debt').checked) {
        rowNewDebt.setAttribute('style', 'display: flex !important;');
        lblDebtStatus.innerHTML =
            '<span class="text-danger fw-bold"><i class="bi bi-journal-arrow-up me-1"></i>Tính vào nợ mới:</span>';
        txtNewDebt.innerText = new Intl.NumberFormat('vi-VN').format(globalTotalBill) + 'đ';
        txtNewDebt.className = "fw-bold font-monospace text-danger";
    } else {
        if (diff > 0) {
            rowNewDebt.setAttribute('style', 'display: flex !important;');
            lblDebtStatus.innerHTML =
                '<span class="text-danger fw-bold"><i class="bi bi-exclamation-circle-fill me-1"></i>Thiếu (Tính thêm nợ):</span>';
            txtNewDebt.innerText = new Intl.NumberFormat('vi-VN').format(diff) + 'đ';
            txtNewDebt.className = "fw-bold font-monospace text-danger";
        } else if (diff < 0) {
            rowNewDebt.setAttribute('style', 'display: flex !important;');
            lblDebtStatus.innerHTML =
                '<span class="text-success fw-bold"><i class="bi bi-cash-stack me-1"></i>Tiền thừa thối khách:</span>';
            txtNewDebt.innerText = new Intl.NumberFormat('vi-VN').format(Math.abs(diff)) + 'đ';
            txtNewDebt.className = "fw-bold font-monospace text-success";
        } else {
            rowNewDebt.setAttribute('style', 'display: none !important;');
        }
    }
}

function openQuickCustomerModal() {
    document.getElementById('quickCustomerForm').reset();
    let modal = new bootstrap.Modal(document.getElementById('quickCustomerModal'));
    modal.show();
}

function submitQuickCustomer() {
    const fullName = document.getElementById('quick_full_name').value.trim();
    const phone = document.getElementById('quick_phone').value.trim();
    const address = document.getElementById('quick_address').value.trim();

    if (!fullName || !phone) {
        alert("Vui lòng điền đầy đủ Họ tên và Số điện thoại!");
        return;
    }

    fetch('/customer/add_quick', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `full_name=${encodeURIComponent(fullName)}&phone=${encodeURIComponent(phone)}&address=${encodeURIComponent(address)}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                allCustomers.push({
                    id: data.customer.id,
                    full_name: data.customer.full_name,
                    phone: data.customer.phone,
                    debt: 0
                });
                selectCustomer(data.customer.id, data.customer.full_name, data.customer.phone, 0);
                bootstrap.Modal.getInstance(document.getElementById('quickCustomerModal')).hide();
                alert("Đã thêm nhanh khách hàng thành công!");
            } else {
                alert("Lỗi: " + data.message);
            }
        });
}

function submitOrder() {
    const custId = document.getElementById('selected_customer_id').value;
    const payMethod = document.querySelector('input[name="pay_method"]:checked').value;
    const paidInput = document.getElementById('customer_paid');

    let customerPaid = payMethod === 'debt' ? 0 : (parseFloat(paidInput.value) || 0);

    const btnSubmit = document.getElementById('btn-submit-pos');
    btnSubmit.disabled = true;

    fetch('/pos/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                customer_id: custId,
                pay_method: payMethod,
                customer_paid: customerPaid,
                cart: cart
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showNotificationToast("Xuất kho hóa đơn thành công!");
                setTimeout(() => {
                    cart = {};
                    location.reload();
                }, 1000);
            } else {
                alert("Lỗi: " + data.message);
                btnSubmit.disabled = false;
            }
        })
        .catch(err => {
            alert("Lỗi kết nối hệ thống!");
            btnSubmit.disabled = false;
        });
}

function showNotificationToast(message) {
    let toastContainer = document.getElementById('toast-container-pos');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container-pos';
        toastContainer.setAttribute('style', 'position: fixed; top: 20px; right: 20px; z-index: 10000;');
        document.body.appendChild(toastContainer);
    }

    const toast = document.createElement('div');
    toast.setAttribute('style',
        'background-color: #0e9f6e; color: #fff; padding: 16px 24px; border-radius: 8px; margin-bottom: 10px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); font-weight: bold; display: flex; align-items: center; gap: 10px; transition: all 0.3s ease-in-out; opacity: 0; transform: translateY(-20px);'
    );
    toast.innerHTML = `<i class="bi bi-check-circle-fill"></i> <span>${message}</span>`;

    toastContainer.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
    }, 50);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-20px)';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

function escapeHtml(text) {
    return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g,
        "&#039;");
}

document.addEventListener('click', function(e) {
    const dropdownP = document.getElementById('search-results-dropdown');
    const searchP = document.getElementById('search-product');
    if (!dropdownP.contains(e.target) && e.target !== searchP) {
        dropdownP.classList.add('d-none');
    }

    const dropdownC = document.getElementById('customer-results-dropdown');
    const searchC = document.getElementById('search-customer');
    if (!dropdownC.contains(e.target) && e.target !== searchC) {
        dropdownC.classList.add('d-none');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    resetToWalkInCustomer();
});
</script>

<?php require_once 'views/layout/footer.php'; ?>