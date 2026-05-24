<?php 
require_once 'views/layout/header.php'; 
$sheets = $sheets ?? [];
?>

<div class="card card-custom bg-white mb-4">
    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold text-dark mb-1" style="font-size: 18px;">Kiểm kê & Cân bằng kho quầy</h4>
            <p class="text-muted small mb-0" style="font-size: 13px;">Tạo phiếu đối soát chênh lệch giữa số lượng thực tế đếm được và số liệu máy hệ thống</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/inventory/logs" class="btn btn-light border fw-semibold px-3 py-2 rounded-2 shadow-none small" style="font-size: 14px;"><i class="bi bi-journal-text me-1"></i> Nhật ký biến động kho</a>
            <a href="/inventory/create" class="btn btn-primary fw-semibold px-3 py-2 rounded-2 d-flex align-items-center gap-2 shadow-none" style="background-color: #3c50e0; border-color: #3c50e0; font-size: 14px;">
                <i class="bi bi-plus-lg"></i> Tạo phiếu kiểm kho
            </a>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary font-monospace small border-bottom" style="background-color: #f8fafc;">
                    <tr>
                        <th class="ps-4 py-3 text-secondary" style="font-size: 12px;">MÃ PHIẾU</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">NGƯỜI KIỂM KÊ</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">NGÀY GIỜ KIỂM</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">TRẠNG THÁI</th>
                        <th class="text-end pe-4 py-3 text-secondary" style="font-size: 12px;">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody class="text-dark" style="font-size: 14px;">
                    <?php if (!empty($sheets)): ?>
                        <?php foreach ($sheets as $s): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;" id="sheet-row-<?php echo $s['id']; ?>">
                                <td class="ps-4 fw-bold font-monospace text-secondary"><?php echo $s['check_code']; ?></td>
                                <td class="fw-bold text-dark"><?php echo $s['fullname']; ?></td>
                                <td class="text-muted font-monospace"><?php echo $s['created_at']; ?></td>
                                <td>
                                    <span class="badge rounded-1 px-2 py-1 fw-semibold status-badge-el" style="<?php echo $s['status'] == 1 ? 'background-color: #def7ec; color: #03543f; font-size: 11px;' : 'background-color: #fef2f2; color: #9b1c1c; font-size: 11px;'; ?>">
                                        <?php echo $s['status'] == 1 ? 'Đã duyệt kho' : 'Chờ duyệt'; ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1 btn-action-container-el">
                                        <button class="btn btn-sm btn-light border text-secondary px-2 shadow-none" onclick="viewDetail(<?php echo $s['id']; ?>)"><i class="bi bi-eye"></i> Xem phiếu</button>
                                        <?php if ($s['status'] == 0): ?>
                                            <button type="button" class="btn btn-sm btn-success text-white bg-success px-2 shadow-none btn-approve-trigger" data-id="<?php echo $s['id']; ?>"><i class="bi bi-check-lg"></i> Duyệt kho</button>
                                            <button type="button" class="btn btn-sm btn-outline-danger px-2 shadow-none btn-delete-trigger" onclick="confirmDelete(<?php echo $s['id']; ?>, '<?php echo $s['check_code']; ?>')"><i class="bi bi-trash"></i> Xóa nháp</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center p-5 text-secondary"><i class="bi bi-clipboard-check d-block mb-2" style="font-size: 40px; color: #cbd5e1;"></i>Chưa có phiếu kiểm kho nào được tạo!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg rounded-4" style="overflow: hidden;">
            <div class="modal-header p-4 border-bottom-0 bg-white">
                <div>
                    <span class="badge rounded-2 px-2 py-1 mb-1 font-monospace fw-bold" id="lblCheckCode" style="background-color: #e0e4fd; color: #3c50e0; font-size: 11px;"></span>
                    <h5 class="modal-title fw-bold text-dark" style="font-size: 20px;">Chi tiết chênh lệch kiểm kho</h5>
                </div>
                <button type="button" class="btn-close shadow-none bg-light p-2 rounded-circle" data-bs-dismiss="modal" style="font-size: 12px;"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <div class="p-3 mb-4 rounded-3 small text-secondary d-flex justify-content-between align-items-center" style="background-color: #f8fafc; border-left: 4px solid #3c50e0;">
                    <div><i class="bi bi-person-fill text-primary"></i> <strong>Người đếm:</strong> <span id="lblUser"></span></div>
                    <div><i class="bi bi-clock-fill text-primary"></i> <strong>Thời gian tạo:</strong> <span id="lblTime"></span></div>
                    <div><i class="bi bi-pencil-square text-primary"></i> <strong>Ghi chú:</strong> <span id="lblNote" class="text-danger fw-semibold"></span></div>
                </div>
                <div class="border rounded-3 overflow-hidden shadow-none">
                    <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                        <thead class="table-light border-bottom" style="background-color: #f8fafc;">
                            <tr>
                                <th class="ps-4 py-3 text-secondary fw-semibold font-monospace" style="font-size: 12px;">TÊN HÀNG HÓA QUY CÁCH</th>
                                <th class="py-3 text-center text-secondary fw-semibold font-monospace" style="font-size: 12px;">MÃ BARCODE</th>
                                <th class="py-3 text-center text-secondary fw-semibold font-monospace" style="font-size: 12px;">TỒN TRÊN MÁY</th>
                                <th class="py-3 text-center text-secondary fw-semibold font-monospace" style="font-size: 12px;">THỰC TẾ ĐẾM</th>
                                <th class="py-3 text-center pe-4 text-secondary fw-semibold font-monospace" style="font-size: 12px;">CHÊNH LỆCH LỆCH</th>
                            </tr>
                        </thead>
                        <tbody id="detailTableBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer p-3 bg-light border-top-0 d-flex justify-content-end">
                <button type="button" class="btn btn-white border fw-semibold rounded-2 px-4 shadow-none small" style="font-size: 14px;" data-bs-dismiss="modal">Đóng lại</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow rounded-3">
            <div class="modal-body p-4 text-center">
                <div class="text-danger mb-3"><i class="bi bi-exclamation-octagon-fill fs-1"></i></div>
                <h5 class="fw-bold text-dark mb-1">Hủy & Xóa phiếu kiểm?</h5>
                <p class="text-muted small mb-4">Bạn có chắc chắn muốn hủy bỏ và xóa vĩnh viễn phiếu nháp <strong class="text-dark font-monospace" id="delSheetCode"></strong> không?</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light border px-3 fw-semibold rounded-2 small shadow-none" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="button" id="btnDoDelete" class="btn btn-danger bg-danger px-4 fw-semibold rounded-2 text-white shadow-none">Xác nhận Xóa</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/inventory/index.js"></script>

<?php require_once 'views/layout/footer.php'; ?>