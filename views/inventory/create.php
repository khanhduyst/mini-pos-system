<?php
require_once 'views/layout/header.php';
$variants = $variants ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1" style="font-size: 22px; letter-spacing: -0.5px;">Lập phiếu kiểm kê hàng hóa mới</h4>
        <p class="text-muted small mb-0" style="font-size: 14px;">Tìm kiếm sản phẩm phân tầng hoặc quét mã vạch để đối soát chênh lệch</p>
    </div>
    <a href="/inventory/index" class="btn btn-light border fw-semibold px-3 py-2 rounded-2 shadow-none small" style="font-size: 14px;"><i class="bi bi-arrow-left me-1"></i> Quay lại danh sách</a>
</div>

<form action="/inventory/add" method="POST" id="createInventorySheetForm">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                <div class="position-relative">
                    <label class="form-label small fw-bold text-dark mb-2">1. Gõ tìm kiếm hoặc quét Barcode</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control bg-light border-start-0 shadow-none" id="txtSearchVariant" placeholder="Nhập tên sản phẩm, mã gốc..." autocomplete="off">
                    </div>
                    <div class="position-absolute w-100 bg-white border rounded-3 shadow-lg mt-1 d-none" id="searchResultDropdown" style="z-index: 1050; max-height: 400px; overflow-y: auto;">
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label small fw-bold text-dark mb-2">2. Ghi chú phiếu kiểm (Nếu có)</label>
                    <textarea class="form-control bg-light shadow-none small" name="note" rows="3" placeholder="Nhập lý do chênh lệch thừa/thiếu, ghi chú nội bộ khi đi đếm kho..." style="font-size:13px;"></textarea>
                </div>

                <div class="p-3 mt-4 rounded-3 small text-secondary bg-light-subtle border">
                    <h6 class="fw-bold text-dark mb-2" style="font-size: 13px;"><i class="bi bi-info-circle text-primary me-1"></i> Hướng dẫn thao tác nhanh:</h6>
                    <ul class="ps-3 mb-0 gap-1 d-flex flex-column" style="font-size: 12px;">
                        <li>Gõ tên mặt hàng chung hoặc mã vạch.</li>
                        <li>Bấm vào quy cách con thích hợp từ danh sách xổ ra để chèn dòng vào phiếu kiểm bên cạnh.</li>
                        <li>Nhập số lượng thực tế đếm được tại quầy vào ô trống.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white border-bottom p-3 fw-bold text-dark" style="font-size: 15px;">
                    <i class="bi bi-list-task text-primary me-1"></i> Danh sách mặt hàng nằm trong phiếu kiểm kê
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="min-height: 250px;">
                        <table class="table align-middle table-hover mb-0" id="tblCheckItems">
                            <thead class="table-light text-center small fw-bold font-monospace border-bottom">
                                <tr>
                                    <th class="text-start ps-4 py-3">Mặt hàng quy cách</th>
                                    <th>Mã vạch</th>
                                    <th style="width: 120px;">Tồn máy</th>
                                    <th style="width: 180px;">Thực tế đếm</th>
                                    <th style="width: 70px;">Xóa</th>
                                </tr>
                            </thead>
                            <tbody id="checkItemsTableBody">
                                <tr id="trEmptyMessage">
                                    <td colspan="5" class="text-center p-5 text-muted">
                                        <i class="bi bi-upc-scan d-block mb-2 fs-2 text-secondary" style="opacity: 0.5;"></i> Phiếu kiểm chưa có sản phẩm. Hãy tìm và chọn sản phẩm ở khung bên trái để thêm vào danh sách!
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top p-3 text-end">
                    <a href="/inventory/index" class="btn btn-light border fw-semibold rounded-2 px-4 shadow-none me-2">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary fw-semibold rounded-2 px-5 shadow-none btn-submit-save-sheet" style="background-color: #3c50e0; border-color: #3c50e0;">Lưu phiếu kiểm kho</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    const allVariants = <?php echo json_encode($variants); ?>;
</script>
<script src="/assets/js/inventory/create.js"></script>


<?php require_once 'views/layout/footer.php'; ?>