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

<script src="/assets/js/supplier/orders.js"></script>


<?php require_once 'views/layout/footer.php'; ?>