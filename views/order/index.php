<?php
require_once 'views/layout/header.php';
?>

<div class="card card-custom bg-white mb-4">
    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold text-dark mb-1" style="font-size: 18px;">Quản lý danh sách đơn hàng</h4>
            <p class="text-muted small mb-0" style="font-size: 13px;">Tra cứu lịch sử hóa đơn xuất kho, trạng thái thanh
                toán và công nợ đơn hàng</p>
        </div>
    </div>

    <div class="card-body p-4 bg-light">
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4">
            <div class="row g-3 mb-4">
                <div class="col-md-6 col-lg-4">
                    <label class="form-label small fw-bold text-secondary">Tìm kiếm đơn hàng</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i
                                class="bi bi-search text-muted"></i></span>
                        <input type="text" id="search-order" class="form-control border-start-0 shadow-none"
                            placeholder="Nhập mã đơn, tên hoặc SĐT khách..." oninput="filterOrders()">
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label class="form-label small fw-bold text-secondary">Lọc theo hình thức</label>
                    <select id="filter-method" class="form-select shadow-none" onchange="filterOrders()">
                        <option value="all">-- Tất cả hình thức --</option>
                        <option value="cash">Tiền mặt</option>
                        <option value="debt">Ghi nợ</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="order-table">
                    <thead class="table-light text-secondary font-monospace small" style="font-size: 11px;">
                        <tr>
                            <th class="ps-3 py-3">MÃ ĐƠN HÀNG</th>
                            <th class="py-3">THỜI GIAN</th>
                            <th class="py-3">KHÁCH HÀNG</th>
                            <th class="text-end py-3">TỔNG TIỀN</th>
                            <th class="text-end py-3">KHÁCH TRẢ</th>
                            <th class="text-center py-3">HÌNH THỨC</th>
                            <th class="text-center py-3">TRẠNG THÁI ĐƠN</th>
                            <th class="text-end pe-3 py-3">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 13px;">
                        <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-secondary">
                                <i class="bi bi-folder-x display-6 d-block mb-2 text-muted"></i>
                                Chưa có đơn hàng nào được xuất kho!
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($orders as $order):
                                $diff = (float)$order['total_amount'] - (float)$order['customer_paid'];
                                $row_search = strtolower(removeVietnameseTones($order['order_code'] . ' ' . ($order['customer_name'] ?? 'khach le vang lai') . ' ' . ($order['customer_phone'] ?? '')));
                            ?>
                        <tr class="order-row" data-search="<?= $row_search ?>"
                            data-method="<?= $order['pay_method'] ?>">
                            <td class="ps-3 fw-bold font-monospace text-primary"><?= $order['order_code'] ?></td>
                            <td class="text-secondary"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                            <td>
                                <div class="fw-bold text-dark">
                                    <?= $order['customer_name'] ?? '<span class="text-muted fw-normal">Khách lẻ vãng lai</span>' ?>
                                </div>
                                <?php if (!empty($order['customer_phone'])): ?>
                                <small class="text-muted font-monospace"><i class="bi bi-telephone"></i>
                                    <?= $order['customer_phone'] ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-bold font-monospace">
                                <?= number_format($order['total_amount'], 0, ',', '.') ?>đ</td>
                            <td class="text-end font-monospace text-success">
                                <?= number_format($order['customer_paid'], 0, ',', '.') ?>đ</td>
                            <td class="text-center">
                                <?php if ($order['pay_method'] === 'cash'): ?>
                                <span class="badge bg-secondary-subtle text-secondary px-2 py-1">Tiền mặt</span>
                                <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger px-2 py-1">Ghi nợ</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($order['pay_method'] === 'debt'): ?>
                                <span class="badge bg-danger text-white px-2 py-1"><i
                                        class="bi bi-exclamation-circle me-1"></i>Nợ 100%</span>
                                <?php elseif ($diff > 0): ?>
                                <span class="badge bg-warning text-dark px-2 py-1"><i
                                        class="bi bi-clock-history me-1"></i>Thiếu
                                    <?= number_format($diff, 0, ',', '.') ?>đ</span>
                                <?php else: ?>
                                <span class="badge bg-success text-white px-2 py-1"><i class="bi bi-check-lg me-1"></i>
                                    Đã trả đủ</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-3">
                                <button class="btn btn-sm btn-outline-primary shadow-none px-2 py-1"
                                    onclick="viewOrderDetail(<?= $order['id'] ?>, '<?= $order['order_code'] ?>')">
                                    <i class="bi bi-eye"></i> Xem món
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header p-4 border-bottom">
                <h5 class="modal-title fw-bold text-dark" style="font-size: 16px;"><i
                        class="bi bi-box-seam text-primary me-2"></i>Chi tiết sản phẩm đơn hàng: <span
                        id="md-order-code" class="font-monospace text-primary"></span></h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light text-secondary font-monospace">
                            <tr>
                                <th class="ps-3 py-2">Tên sản phẩm / Quy cách</th>
                                <th class="text-center py-2">Mã vạch</th>
                                <th class="text-center py-2">Đơn giá</th>
                                <th class="text-center py-2" style="width: 80px;">SL mua</th>
                                <th class="text-end pe-3 py-2">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody id="md-detail-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
</script>

<?php
require_once 'views/layout/footer.php';

function removeVietnameseTones($str)
{
    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
    $str = preg_replace("/(đ)/", 'd', $str);
    return strtolower($str);
}
?>