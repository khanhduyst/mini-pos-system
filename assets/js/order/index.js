function removeVietnameseTones(str) {
    if (!str) return '';
    str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
    str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
    str = str.replace(/ò|ó|ọ|ỏ|ã|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
    str = str.replace(/đ/g, "d");
    str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, "A");
    str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/g, "E");
    str = str.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, "I");
    str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/g, "O");
    str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, "U");
    str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g, "Y");
    str = str.replace(/Đ/g, "D");
    str = str.replace(/\u0300|\u0301|\u0303|\u0309|\u0323/g, "");
    str = str.replace(/\u02C6|\u0306|\u031B/g, "");
    return str.toLowerCase().trim();
}

function filterOrders() {
    const rawSearch = document.getElementById('search-order').value;
    const searchKeyword = removeVietnameseTones(rawSearch);
    const methodFilter = document.getElementById('filter-method').value;
    const rows = document.querySelectorAll('.order-row');

    rows.forEach(row => {
        const dataSearch = row.getAttribute('data-search') || '';
        const dataMethod = row.getAttribute('data-method') || '';

        const textMatch = dataSearch.includes(searchKeyword);
        const methodMatch = (methodFilter === 'all') || (dataMethod === methodFilter);

        if (textMatch && methodMatch) {
            row.style.setProperty('display', '', 'important');
        } else {
            row.style.setProperty('display', 'none', 'important');
        }
    });
}

function viewOrderDetail(orderId, orderCode) {
    document.getElementById('md-order-code').innerText = orderCode;
    const tbody = document.getElementById('md-detail-body');
    tbody.innerHTML =
        '<tr><td colspan="5" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Đang tải dữ liệu...</td></tr>';

    let modal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
    modal.show();

    fetch('/order/detail?id=' + orderId)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.details.length > 0) {
                let html = '';
                data.details.forEach(item => {
                    let subtotal = parseFloat(item.price) * parseInt(item.quantity);
                    html += `
                    <tr>
                        <td class="ps-3 fw-bold text-dark text-start">${item.product_name} <span class="text-secondary fw-normal">(${item.variant_name})</span></td>
                        <td class="text-center font-monospace text-muted">${item.barcode ? item.barcode : '---'}</td>
                        <td class="text-center font-monospace">${new Intl.NumberFormat('vi-VN').format(item.price)}đ</td>
                        <td class="text-center fw-bold font-monospace">${item.quantity}</td>
                        <td class="text-end pe-3 font-monospace fw-bold text-primary">${new Intl.NumberFormat('vi-VN').format(subtotal)}đ</td>
                    </tr>`;
                });
                tbody.innerHTML = html;
            } else {
                tbody.innerHTML =
                    '<tr><td colspan="5" class="text-center text-danger py-4">Không có chi tiết mặt hàng hoặc lỗi dữ liệu!</td></tr>';
            }
        })
        .catch(err => {
            tbody.innerHTML =
                '<tr><td colspan="5" class="text-center text-danger py-4">Lỗi kết nối máy chủ!</td></tr>';
        });
}