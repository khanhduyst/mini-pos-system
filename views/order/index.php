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

<script src="/assets/js/order/index.js"></script>


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