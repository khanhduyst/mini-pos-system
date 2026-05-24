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
</script>
<script src="/assets/js/supplier/createOrder.js"></script>

<?php require_once 'views/layout/footer.php'; ?>