
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