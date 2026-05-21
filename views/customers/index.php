<?php require_once 'views/layout/header.php'; ?>

<div class="card card-custom bg-white border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-4">
        <form action="/customer/index" method="GET" class="row g-3 align-items-end" novalidate>
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-secondary">Tìm kiếm khách hàng</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control bg-light border-start-0 shadow-none" name="search"
                        placeholder="Nhập tên, mã hoặc số điện thoại khách hàng..."
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-secondary">Trạng thái theo dõi</label>
                <select class="form-select bg-light shadow-none" name="status">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1"
                        <?php echo (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : ''; ?>>Hoạt động
                    </option>
                    <option value="0"
                        <?php echo (isset($_GET['status']) && $_GET['status'] == '0') ? 'selected' : ''; ?>>Ngừng theo
                        dõi</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-semibold shadow-none"
                    style="background-color: #3c50e0; border-color: #3c50e0;">Lọc</button>
                <a href="/customer/index" class="btn btn-light border w-100 fw-semibold shadow-none">Xóa</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom bg-white">
    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold text-dark mb-1" style="font-size: 18px;">Quản lý khách hàng & Công nợ</h4>
            <p class="text-muted small mb-0" style="font-size: 13px;">Theo dõi thông tin khách, tích điểm thành viên và
                đối soát lịch sử ghi nợ/trả nợ chi tiết</p>
        </div>
        <button class="btn btn-primary fw-semibold px-3 py-2 rounded-2 d-flex align-items-center gap-2 shadow-none"
            style="background-color: #3c50e0; border-color: #3c50e0; font-size: 14px;" data-bs-toggle="modal"
            data-bs-target="#addCustomerModal">
            <i class="bi bi-plus-lg"></i> Thêm khách hàng
        </button>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary font-monospace small border-bottom"
                    style="background-color: #f8fafc;">
                    <tr>
                        <th class="ps-4 py-3 text-secondary" style="font-size: 12px;">MÃ KH</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">HỌ VÀ TÊN</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">SỐ ĐIỆN THOẠI</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">ĐIỂM TÍCH LŨY</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">TỔNG CHI TIÊU</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">CÔNG NỢ HIỆN TẠI</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">TRẠNG THÁI</th>
                        <th class="text-end pe-4 py-3 text-secondary" style="font-size: 12px;">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody class="text-dark" style="font-size: 14px;">
                    <?php if (isset($customers) && is_array($customers) && count($customers) > 0): ?>
                    <?php foreach ($customers as $customer): ?>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td class="ps-4 fw-bold text-primary"><?php echo $customer['customer_code']; ?></td>
                        <td class="fw-semibold text-dark"><?php echo $customer['full_name']; ?></td>
                        <td class="font-monospace"><?php echo $customer['phone']; ?></td>
                        <td class="fw-bold text-success"><?php echo number_format($customer['points']); ?></td>
                        <td class="fw-semibold">
                            <?php echo number_format((float)$customer['total_spent'], 0, '.', ','); ?>đ</td>
                        <td>
                            <?php if ((float)$customer['debt'] > 0): ?>
                            <span class="badge rounded-1 px-2 py-1 fw-bold text-danger"
                                style="background-color: #fde8e8; font-size: 12px;">
                                Nợ: <?php echo number_format((float)$customer['debt'], 0, '.', ','); ?>đ
                            </span>
                            <?php else: ?>
                            <span class="text-secondary">0đ</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge rounded-1 px-2 py-1"
                                style="<?php echo $customer['status'] == 1 ? 'background-color: #def7ec; color: #03543f; font-size: 12px;' : 'background-color: #f3f4f6; color: #4b5563; font-size: 12px;'; ?>">
                                <?php echo $customer['status'] == 1 ? 'Hoạt động' : 'Ngừng theo dõi'; ?>
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-1">
                                <?php if ((float)$customer['debt'] > 0): ?>
                                <button
                                    class="btn btn-sm btn-outline-danger rounded-2 px-2 py-1 small fw-bold shadow-none me-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#payDebtModal<?php echo $customer['id']; ?>"><i
                                        class="bi bi-cash-coin"></i> Thu nợ</button>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-light text-secondary border rounded-2 px-2 shadow-none"
                                    data-bs-toggle="modal"
                                    data-bs-target="#viewCustomerModal<?php echo $customer['id']; ?>"><i
                                        class="bi bi-clock-history"></i></button>
                                <button class="btn btn-sm btn-light text-primary border rounded-2 px-2 shadow-none"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editCustomerModal<?php echo $customer['id']; ?>"><i
                                        class="bi bi-pencil"></i></button>
                                <button
                                    class="btn btn-sm border rounded-2 px-2 shadow-none <?php echo $customer['status'] == 1 ? 'btn-light text-danger' : 'btn-light text-success'; ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#toggleStatusModal<?php echo $customer['id']; ?>"><i
                                        class="bi <?php echo $customer['status'] == 1 ? 'bi-eye-slash' : 'bi-eye'; ?>"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center p-5 text-secondary">
                            <i class="bi bi-person-exclamation d-block mb-2"
                                style="font-size: 40px; color: #cbd5e1;"></i>
                            Không tìm thấy dữ liệu khách hàng nào phù hợp!
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php
        $page = isset($current_page) ? $current_page : 1;
        $pages = isset($total_pages) ? $total_pages : 1;
        if ($pages > 1):
        ?>
        <div class="card-footer bg-white border-top p-4 d-flex justify-content-between align-items-center">
            <div class="text-secondary small">
                Hiển thị trang <strong><?php echo $page; ?></strong> / <strong><?php echo $pages; ?></strong> trang
            </div>
            <nav>
                <ul class="pagination pagination-sm mb-0 gap-1">
                    <?php
                        $query_str = $_GET;
                        unset($query_str['page']);
                        $base_url = "/customer/index?" . http_build_query($query_str) . "&page=";
                        ?>
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link border rounded-2 px-3 py-2 text-dark shadow-none"
                            href="<?php echo $base_url . ($page - 1); ?>"><i class="bi bi-chevron-left"></i></a>
                    </li>
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                        <a class="page-link border rounded-2 px-3 py-2 shadow-none fw-semibold <?php echo ($page == $i) ? 'text-white' : 'text-dark bg-white'; ?>"
                            style="<?php echo ($page == $i) ? 'background-color: #3c50e0; border-color: #3c50e0;' : ''; ?>"
                            href="<?php echo $base_url . $i; ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo ($page >= $pages) ? 'disabled' : ''; ?>">
                        <a class="page-link border rounded-2 px-3 py-2 text-dark shadow-none"
                            href="<?php echo $base_url . ($page + 1); ?>"><i class="bi bi-chevron-right"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($customers) && is_array($customers) && count($customers) > 0): ?>
<?php foreach ($customers as $customer): ?>
<div class="modal fade" id="viewCustomerModal<?php echo $customer['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-3">
            <div class="modal-header p-4 border-bottom bg-white">
                <h5 class="modal-title fw-bold text-dark">Sổ nợ & Thông tin: <?php echo $customer['full_name']; ?></h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <div class="row g-3 mb-4 text-center">
                    <div class="col-4 border-end"><span class="small text-secondary font-monospace d-block">TỔNG
                            MUA</span><span
                            class="fw-bold text-dark fs-5"><?php echo number_format((float)$customer['total_spent']); ?>đ</span>
                    </div>
                    <div class="col-4 border-end"><span class="small text-secondary font-monospace d-block">ĐIỂM TÍCH
                            LŨY</span><span
                            class="fw-bold text-success fs-5"><?php echo number_format($customer['points']); ?></span>
                    </div>
                    <div class="col-4"><span class="small text-secondary font-monospace d-block">ĐANG NỢ</span><span
                            class="fw-bold text-danger fs-5"><?php echo number_format((float)$customer['debt']); ?>đ</span>
                    </div>
                </div>

                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-journal-text"></i> Nhật ký lịch sử
                    công nợ</h6>
                <div class="table-responsive style-scroll" style="max-height: 250px; overflow-y: auto;">
                    <table class="table table-sm table-bordered align-middle small text-center mb-0">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th>Thời gian</th>
                                <th>Hành động</th>
                                <th>Số tiền</th>
                                <th>Nợ sau giao dịch</th>
                                <th>Nhân viên</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($customer['history'])): ?>
                            <?php foreach ($customer['history'] as $log): ?>
                            <tr>
                                <td class="font-monospace">
                                    <?php echo date('d/m/Y H:i', strtotime($log['created_at'])); ?></td>
                                <td>
                                    <span
                                        class="badge rounded-1 <?php echo $log['type'] == 'increase' ? 'bg-light-danger text-danger' : 'bg-light-success text-success'; ?>">
                                        <?php echo $log['type'] == 'increase' ? 'Nợ thêm' : 'Trả nợ'; ?>
                                    </span>
                                </td>
                                <td class="fw-bold"><?php echo number_format((float)$log['amount']); ?>đ</td>
                                <td class="font-monospace fw-semibold">
                                    <?php echo number_format((float)$log['balance_after']); ?>đ</td>
                                <td><?php echo $log['staff_name']; ?></td>
                                <td class="text-start text-secondary"><?php echo $log['note']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-muted p-3 text-center">Không có lịch sử biến động công nợ!
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ((float)$customer['debt'] > 0): ?>
<div class="modal fade" id="payDebtModal<?php echo $customer['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow rounded-3">
            <div class="modal-header p-3 border-bottom bg-white">
                <h6 class="modal-title fw-bold text-dark"><i class="bi bi-cash-coin text-danger"></i> Tiến hành thu nợ
                    khách</h6>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form action="/customer/payDebt" method="POST">
                <input type="hidden" name="customer_id" value="<?php echo $customer['id']; ?>">
                <div class="modal-body p-3 bg-white">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Dư nợ hiện tại</label>
                        <input type="text" class="form-control bg-light fw-bold text-danger"
                            value="<?php echo number_format((float)$customer['debt']); ?>đ" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Số tiền thu thực tế</label>
                        <input type="number" class="form-control" name="amount" min="1"
                            max="<?php echo $customer['debt']; ?>" placeholder="Nhập số tiền mặt nhận..." required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-semibold text-secondary">Ghi chú thu nợ</label>
                        <textarea class="form-control small" name="note" rows="2"
                            placeholder="Khách trả nợ tiền mua tạp hóa..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top p-2 bg-white">
                    <button type="submit" class="btn btn-danger w-100 fw-semibold py-2 small">Xác nhận thu nợ</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
        $is_edit_error = (isset($_SESSION['error_edit_phone_id']) && $_SESSION['error_edit_phone_id'] == $customer['id']);
        ?>
<div class="modal fade" id="editCustomerModal<?php echo $customer['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-3">
            <div class="modal-header p-4 border-bottom bg-white">
                <h5 class="modal-title fw-bold text-dark">Chỉnh sửa thông tin khách hàng</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form action="/customer/edit" method="POST" novalidate>
                <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Mã khách
                                hàng</label><input type="text" class="form-control bg-light" name="customer_code"
                                value="<?php echo $customer['customer_code']; ?>" readonly></div>
                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Họ và tên
                                khách</label><input type="text" class="form-control" name="full_name"
                                value="<?php echo $customer['full_name']; ?>" required></div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Số điện thoại</label>
                            <input type="text" class="form-control <?php echo $is_edit_error ? 'is-invalid' : ''; ?>"
                                name="phone" value="<?php echo $customer['phone']; ?>" required>
                            <?php if ($is_edit_error): ?>
                            <div class="invalid-feedback fw-semibold small mt-1 d-block">
                                <?php echo $_SESSION['error_edit_phone_msg']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Địa chỉ
                                Email</label><input type="email" class="form-control" name="email"
                                value="<?php echo $customer['email']; ?>"></div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Giới tính</label>
                            <select class="form-select" name="gender">
                                <option value="male" <?php echo $customer['gender'] == 'male' ? 'selected' : ''; ?>>Nam
                                </option>
                                <option value="female" <?php echo $customer['gender'] == 'female' ? 'selected' : ''; ?>>
                                    Nữ</option>
                                <option value="other" <?php echo $customer['gender'] == 'other' ? 'selected' : ''; ?>>
                                    Khác</option>
                            </select>
                        </div>
                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Ngày
                                sinh</label><input type="date" class="form-control" name="date_of_birth"
                                value="<?php echo $customer['date_of_birth']; ?>"></div>
                        <div class="col-12"><label class="form-label small fw-semibold text-secondary">Địa chỉ thường
                                trú</label><input type="text" class="form-control" name="address"
                                value="<?php echo $customer['address']; ?>"></div>
                        <div class="col-12"><label class="form-label small fw-semibold text-secondary">Ghi chú đặc điểm
                                khách</label><textarea class="form-control" name="note"
                                rows="2"><?php echo $customer['note']; ?></textarea></div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-white">
                    <button type="button" class="btn btn-light fw-semibold rounded-2 px-3"
                        data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-semibold rounded-2 px-4"
                        style="background-color: #3c50e0; border-color: #3c50e0;">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="toggleStatusModal<?php echo $customer['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-body p-4 text-center bg-white rounded-3">
                <?php if ($customer['status'] == 1): ?>
                <div class="text-danger mb-3"><i class="bi bi-eye-slash-fill fs-1"></i></div>
                <h5 class="fw-bold text-dark mb-2">Ngừng theo dõi?</h5>
                <p class="text-secondary small mb-4">Hệ thống tạm ẩn khách <strong
                        class="text-dark"><?php echo $customer['full_name']; ?></strong> khỏi màn hình POS bán hàng.</p>
                <?php else: ?>
                <div class="text-success mb-3"><i class="bi bi-eye-fill fs-1"></i></div>
                <h5 class="fw-bold text-dark mb-2">Theo dõi lại?</h5>
                <p class="text-secondary small mb-4">Kích hoạt hiển thị lại thông tin khách hàng <strong
                        class="text-dark"><?php echo $customer['full_name']; ?></strong>.</p>
                <?php endif; ?>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light fw-semibold rounded-2 px-3 small"
                        data-bs-dismiss="modal">Hủy</button>
                    <a href="/customer/toggle?id=<?php echo $customer['id']; ?>&status=<?php echo $customer['status']; ?>"
                        class="btn fw-semibold rounded-2 px-4 small text-white <?php echo $customer['status'] == 1 ? 'btn-danger' : 'btn-success'; ?>">Xác
                        nhận</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<?php
$is_add_error = isset($_SESSION['error_add_phone']);
$old_add = $is_add_error ? $_SESSION['old_add_data'] : [];
?>
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-1 shadow rounded-3">
            <div class="modal-header p-4 border-bottom bg-white">
                <h5 class="modal-title fw-bold text-dark">Thêm thông tin khách hàng mới</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form action="/customer/add" method="POST" novalidate>
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Mã khách hàng</label>
                            <div class="input-group">
                                <input type="text" class="form-control rounded-start-2 shadow-none"
                                    id="add_customer_code" name="customer_code" placeholder="KH001"
                                    value="<?php echo isset($old_add['customer_code']) ? htmlspecialchars($old_add['customer_code']) : ''; ?>"
                                    required>
                                <button class="btn btn-outline-secondary rounded-end-2 px-3" type="button"
                                    id="btnRandomCustCode">
                                    <i class="bi bi-shuffle"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Họ và tên khách
                                hàng</label><input type="text" class="form-control" name="full_name"
                                placeholder="Nguyễn Văn B"
                                value="<?php echo isset($old_add['full_name']) ? htmlspecialchars($old_add['full_name']) : ''; ?>"
                                required></div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Số điện thoại liên hệ</label>
                            <input type="text" class="form-control <?php echo $is_add_error ? 'is-invalid' : ''; ?>"
                                name="phone" placeholder="09xxxxxxxx"
                                value="<?php echo isset($old_add['phone']) ? htmlspecialchars($old_add['phone']) : ''; ?>"
                                required>
                            <?php if ($is_add_error): ?>
                            <div class="invalid-feedback fw-semibold small mt-1 d-block">
                                <?php echo $_SESSION['error_add_phone']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Địa chỉ
                                Email</label><input type="email" class="form-control" name="email"
                                placeholder="khachhang@gmail.com"
                                value="<?php echo isset($old_add['email']) ? htmlspecialchars($old_add['email']) : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Giới tính</label>
                            <select class="form-select" name="gender">
                                <option value="male"
                                    <?php echo (isset($old_add['gender']) && $old_add['gender'] == 'male') ? 'selected' : ''; ?>>
                                    Nam</option>
                                <option value="female"
                                    <?php echo (isset($old_add['gender']) && $old_add['gender'] == 'female') ? 'selected' : ''; ?>>
                                    Nữ</option>
                                <option value="other"
                                    <?php echo (!isset($old_add['gender']) || $old_add['gender'] == 'other') ? 'selected' : ''; ?>>
                                    Khác</option>
                            </select>
                        </div>
                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Ngày sinh
                                nhật</label><input type="date" class="form-control" name="date_of_birth"
                                value="<?php echo isset($old_add['date_of_birth']) ? htmlspecialchars($old_add['date_of_birth']) : ''; ?>">
                        </div>
                        <div class="col-12"><label class="form-label small fw-semibold text-secondary">Địa chỉ thường
                                trú</label><input type="text" class="form-control" name="address"
                                placeholder="Số nhà, tên đường..."
                                value="<?php echo isset($old_add['address']) ? htmlspecialchars($old_add['address']) : ''; ?>">
                        </div>
                        <div class="col-12"><label class="form-label small fw-semibold text-secondary">Ghi chú thông tin
                                phụ</label><textarea class="form-control" name="note" rows="2"
                                placeholder="Khách hàng quen, đối tác VIP..."><?php echo isset($old_add['note']) ? htmlspecialchars($old_add['note']) : ''; ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-white">
                    <button type="button" class="btn btn-light fw-semibold rounded-2 px-3"
                        data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-semibold rounded-2 px-4"
                        style="background-color: #3c50e0; border-color: #3c50e0;">Lưu lại</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
document.addEventListener("DOMContentLoaded", function() {
    const btnRandom = document.getElementById('btnRandomCustCode');
    const inputCode = document.getElementById('add_customer_code');
    if (btnRandom && inputCode) {
        btnRandom.addEventListener('click', function() {
            const randomNum = Math.floor(1000 + Math.random() * 9000);
            inputCode.value = 'KH' + randomNum;
            inputCode.classList.remove('is-invalid');
            const feedback = inputCode.parentNode.parentNode.querySelector('.invalid-feedback');
            if (feedback) feedback.remove();
        });
    }

    function validateCustomerForm(formEl) {
        let isValid = true;
        const inputs = formEl.querySelectorAll('input, select, textarea');

        inputs.forEach(input => {
            if (input.hasAttribute('readonly') || input.type === 'hidden' || ['note', 'address',
                    'date_of_birth', 'search', 'email'
                ].includes(input.name)) {
                return;
            }

            input.classList.remove('is-invalid');

            let container = input.parentNode;
            if (input.id === 'add_customer_code') {
                container = input.parentNode.parentNode;
            }

            const oldFeedback = container.querySelector('.invalid-feedback');
            if (oldFeedback) oldFeedback.remove();

            let hasError = false;
            let errorMsg = "";

            if (!input.value.trim()) {
                hasError = true;
                errorMsg = "Trường thông tin này bắt buộc nhập!";
            } else if (input.name === 'phone') {
                const phonePattern = /^0[0-9]{9}$/;
                if (!phonePattern.test(input.value.trim())) {
                    hasError = true;
                    errorMsg = "Số điện thoại không đúng! Phải bắt đầu bằng số 0 và đủ 10 số.";
                }
            }

            if (hasError) {
                isValid = false;
                input.classList.add('is-invalid');

                const feedbackDiv = document.createElement('div');
                feedbackDiv.className = 'invalid-feedback fw-semibold small mt-1 d-block';
                feedbackDiv.innerText = errorMsg;
                container.appendChild(feedbackDiv);
            }

            input.addEventListener('input', function() {
                input.classList.remove('is-invalid');
                const feedback = container.querySelector('.invalid-feedback');
                if (feedback) feedback.remove();
            });
        });

        return isValid;
    }

    const addForm = document.querySelector('#addCustomerModal form');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            if (!validateCustomerForm(addForm)) {
                e.preventDefault();
            }
        });
    }

    const editForms = document.querySelectorAll('form[action="/customer/edit"]');
    editForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateCustomerForm(form)) {
                e.preventDefault();
            }
        });
    });

    <?php if (isset($_SESSION['error_add_phone'])): ?>
    const addModal = new bootstrap.Modal(document.getElementById('addCustomerModal'));
    addModal.show();
    <?php unset($_SESSION['error_add_phone']);
            unset($_SESSION['old_add_data']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_edit_phone_id'])): ?>
    const editModalId = 'editCustomerModal' + '<?php echo $_SESSION['error_edit_phone_id']; ?>';
    const editModal = new bootstrap.Modal(document.getElementById(editModalId));
    editModal.show();
    <?php unset($_SESSION['error_edit_phone_id']);
            unset($_SESSION['error_edit_phone_msg']); ?>
    <?php endif; ?>
});
</script>

<?php require_once 'views/layout/footer.php'; ?>