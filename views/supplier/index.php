<?php
require_once 'views/layout/header.php';
$suppliers = $suppliers ?? [];
?>

<div class="card card-custom bg-white border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-4">
        <form id="filterSupplierForm" class="row g-3 align-items-end" novalidate>
            <div class="col-md-9">
                <label class="form-label small fw-semibold text-secondary">Tìm kiếm đối tác</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control bg-light border-start-0 shadow-none" id="supplierSearchInput" name="search" placeholder="Nhập tên nhà cung cấp, mã đối tác hoặc số điện thoại...">
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-semibold shadow-none" style="background-color: #3c50e0; border-color: #3c50e0;">Tìm kiếm</button>
                <button type="button" id="btnResetSupplierFilter" class="btn btn-light border w-100 fw-semibold shadow-none">Xóa lọc</button>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom bg-white mb-4">
    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold text-dark mb-1" style="font-size: 18px;">Quản lý nhà cung cấp đối tác</h4>
            <p class="text-muted small mb-0" style="font-size: 13px;">Lưu trữ thông tin liên hệ, số điện thoại, địa chỉ và quản lý trạng thái hợp tác của các đơn vị cấp hàng</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/supplier/orders" class="btn btn-light border fw-semibold px-3 py-2 rounded-2 shadow-none small" style="font-size: 14px;"><i class="bi bi-receipt me-1"></i> Quản lý đơn nhập hàng</a>
            <button class="btn btn-primary fw-semibold px-3 py-2 rounded-2 d-flex align-items-center gap-2 shadow-none" style="background-color: #3c50e0; border-color: #3c50e0; font-size: 14px;" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                <i class="bi bi-plus-lg"></i> Thêm nhà cung cấp
            </button>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tblSuppliers">
                <thead class="table-light text-secondary font-monospace small border-bottom" style="background-color: #f8fafc;">
                    <tr>
                        <th class="ps-4 py-3 text-secondary" style="font-size: 12px;">MÃ ĐỐI TÁC</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">TÊN NHÀ CUNG CẤP</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">SỐ ĐIỆN THOẠI</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">EMAIL</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">ĐỊA CHỈ TRỤ SỞ</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">TRẠNG THÁI</th>
                        <th class="text-end pe-4 py-3 text-secondary" style="font-size: 12px;">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody class="text-dark" style="font-size: 14px;">
                    <?php if (!empty($suppliers)): ?>
                        <?php foreach ($suppliers as $s): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;" id="supplier-row-<?php echo $s['id']; ?>">
                                <td class="ps-4 fw-bold font-monospace text-secondary"><?php echo $s['supplier_code']; ?></td>
                                <td class="fw-bold text-dark supplier-name-text"><?php echo htmlspecialchars($s['supplier_name']); ?></td>
                                <td class="font-monospace supplier-phone-text"><?php echo $s['phone']; ?></td>
                                <td class="text-muted supplier-email-text"><?php echo htmlspecialchars($s['email'] ? $s['email'] : '---'); ?></td>
                                <td class="text-secondary small supplier-address-text" style="max-width: 250px;">
                                    <div class="text-truncate" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($s['address'] ?? ''); ?>">
                                        <?php echo htmlspecialchars($s['address'] ? $s['address'] : '---'); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge rounded-1 px-2 py-1 fw-semibold status-badge-el" style="<?php echo $s['status'] == 1 ? 'background-color: #def7ec; color: #03543f; font-size: 11px;' : 'background-color: #f3f4f6; color: #4b5563; font-size: 11px;'; ?>">
                                        <?php echo $s['status'] == 1 ? 'Đang hợp tác' : 'Ngừng hợp tác'; ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-sm btn-light border text-primary px-2 shadow-none" onclick="openEditModal(<?php echo $s['id']; ?>, '<?php echo htmlspecialchars($s['supplier_name'], ENT_QUOTES); ?>', '<?php echo $s['phone']; ?>', '<?php echo htmlspecialchars($s['email'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($s['address'], ENT_QUOTES); ?>')"><i class="bi bi-pencil"></i> Sửa</button>
                                        <button type="button" class="btn btn-sm border px-2 shadow-none btn-toggle-status-trigger <?php echo $s['status'] == 1 ? 'btn-light text-warning' : 'btn-light text-success'; ?>" data-id="<?php echo $s['id']; ?>" data-status="<?php echo $s['status']; ?>"><i class="bi <?php echo $s['status'] == 1 ? 'bi-toggle-on' : 'bi-toggle-off'; ?>"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center p-5 text-secondary">
                                <i class="bi bi-building d-block mb-2" style="font-size: 40px; color: #cbd5e1;"></i>Chưa có thông tin nhà cung cấp nào!
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header p-4 border-bottom bg-white">
                <h5 class="modal-title fw-bold text-dark">Thêm đối tác cung cấp mới</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form action="/supplier/addSupplier" method="POST" id="addSupplierForm">
                <div class="modal-body p-4 bg-white">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Mã nhà cung cấp gốc</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="add_supplier_code" name="supplier_code" placeholder="NCC001" required>
                            <button class="btn btn-outline-secondary" type="button" id="btnRandomSupplierCode"><i class="bi bi-shuffle"></i></button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Tên nhà cung cấp / Tên công ty</label>
                        <input type="text" class="form-control form-control-sm" name="supplier_name" placeholder="Ví dụ: Công ty Cổ phần sữa Việt Nam" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Số điện thoại liên hệ</label>
                            <input type="text" class="form-control form-control-sm" name="phone" placeholder="028..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Email nhận đơn hàng</label>
                            <input type="email" class="form-control form-control-sm" name="email" placeholder="ncc@gmail.com">
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-semibold text-secondary">Địa chỉ văn phòng / Kho tổng</label>
                        <textarea class="form-control form-control-sm" name="address" rows="2" placeholder="Nhập địa chỉ chi tiết giao nhận..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-white">
                    <button type="button" class="btn btn-light fw-semibold rounded-2 px-3 shadow-none text-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-semibold rounded-2 px-4 btn-save-add-supplier shadow-none" style="background-color: #3c50e0; border-color: #3c50e0;">Lưu thông tin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header p-4 border-bottom bg-white">
                <h5 class="modal-title fw-bold text-dark">Chỉnh sửa thông tin đối tác</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form action="/supplier/editSupplier" method="POST" id="editSupplierForm">
                <input type="hidden" name="id" id="edit_supplier_id">
                <div class="modal-body p-4 bg-white">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Tên nhà cung cấp / Tên công ty</label>
                        <input type="text" class="form-control form-control-sm" name="supplier_name" id="edit_supplier_name" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Số điện thoại liên hệ</label>
                            <input type="text" class="form-control form-control-sm" name="phone" id="edit_supplier_phone" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Email nhận đơn hàng</label>
                            <input type="email" class="form-control form-control-sm" name="email" id="edit_supplier_email">
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-semibold text-secondary">Địa chỉ văn phòng / Kho tổng</label>
                        <textarea class="form-control form-control-sm" name="address" id="edit_supplier_address" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-white">
                    <button type="button" class="btn btn-light fw-semibold rounded-2 px-3 shadow-none text-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-semibold rounded-2 px-4 btn-save-edit-supplier shadow-none" style="background-color: #3c50e0; border-color: #3c50e0;">Lưu cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/assets/js/supplier/index.js"></script>

<?php require_once 'views/layout/footer.php'; ?>