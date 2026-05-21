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
                    <tr style="border-bottom: 1px solid #f1f5f9;" id="customer-row-<?php echo $customer['id']; ?>">
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
                                    class="btn btn-sm btn-outline-danger rounded-2 px-2 py-1 small fw-bold shadow-none me-1 btn-paydebt-trigger"
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

        <div id="pagination-container">
            <?php
            $page = isset($current_page) ? $current_page : 1;
            $pages = isset($total_pages) ? $total_pages : 1;
            if ($pages > 1):
            ?>
            <div class="card-footer bg-white border-top p-4 d-flex justify-content-between align-items-center">
                <div class="text-secondary small">
                    Hiển thị trang <strong class="current-page-txt"><?php echo $page; ?></strong> / <strong
                        class="total-pages-txt"><?php echo $pages; ?></strong> trang
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0 gap-1">
                        <?php
                            $query_str = $_GET;
                            unset($query_str['page']);
                            $base_url = "/customer/index?" . http_build_query($query_str) . "&page=";
                            ?>
                        <li class="page-item page-prev-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link border rounded-2 px-3 py-2 text-dark shadow-none"
                                href="<?php echo $base_url . ($page - 1); ?>" data-page="<?php echo ($page - 1); ?>"><i
                                    class="bi bi-chevron-left"></i></a>
                        </li>
                        <?php for ($i = 1; $i <= $pages; $i++): ?>
                        <li class="page-item page-number-item <?php echo ($page == $i) ? 'active' : ''; ?>"
                            data-page-num="<?php echo $i; ?>">
                            <a class="page-link border rounded-2 px-3 py-2 shadow-none fw-semibold <?php echo ($page == $i) ? 'text-white' : 'text-dark bg-white'; ?>"
                                style="<?php echo ($page == $i) ? 'background-color: #3c50e0; border-color: #3c50e0;' : ''; ?>"
                                href="<?php echo $base_url . $i; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>
                        <li class="page-item page-next-item <?php echo ($page >= $pages) ? 'disabled' : ''; ?>">
                            <a class="page-link border rounded-2 px-3 py-2 text-dark shadow-none"
                                href="<?php echo $base_url . ($page + 1); ?>" data-page="<?php echo ($page + 1); ?>"><i
                                    class="bi bi-chevron-right"></i></a>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="dynamic-modals-container">
    <?php if (isset($customers) && is_array($customers) && count($customers) > 0): ?>
    <?php foreach ($customers as $customer): ?>
    <div class="modal fade" id="viewCustomerModal<?php echo $customer['id']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow rounded-3">
                <div class="modal-header p-4 border-bottom bg-white">
                    <h5 class="modal-title fw-bold text-dark">Sổ nợ & Thông tin: <span
                            class="modal-title-cust-name"><?php echo $customer['full_name']; ?></span></h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3 mb-4 text-center">
                        <div class="col-4 border-end"><span class="small text-secondary font-monospace d-block">TỔNG
                                MUA</span><span
                                class="fw-bold text-dark fs-5 modal-txt-spent"><?php echo number_format((float)$customer['total_spent']); ?>đ</span>
                        </div>
                        <div class="col-4 border-end"><span class="small text-secondary font-monospace d-block">ĐIỂM
                                TÍCH LŨY</span><span
                                class="fw-bold text-success fs-5 modal-txt-points"><?php echo number_format($customer['points']); ?></span>
                        </div>
                        <div class="col-4"><span class="small text-secondary font-monospace d-block">ĐANG NỢ</span><span
                                class="fw-bold text-danger fs-5 modal-txt-debt"><?php echo number_format((float)$customer['debt']); ?>đ</span>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-journal-text"></i> Nhật ký
                        lịch sử công nợ</h6>
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
                            <tbody class="modal-table-history">
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
                                    <td colspan="6" class="text-muted p-3 text-center">Không có lịch sử biến động công
                                        nợ!</td>
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
                    <h6 class="modal-title fw-bold text-dark"><i class="bi bi-cash-coin text-danger"></i> Tiến hành thu
                        nợ khách</h6>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <form action="/customer/payDebt" method="POST">
                    <input type="hidden" name="customer_id" value="<?php echo $customer['id']; ?>">
                    <div class="modal-body p-3 bg-white">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Dư nợ hiện tại</label>
                            <input type="text" class="form-control bg-light fw-bold text-danger modal-input-debt-val"
                                value="<?php echo number_format((float)$customer['debt']); ?>đ" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Số tiền thu thực tế</label>
                            <input type="number" class="form-control modal-input-amount-field" name="amount" min="1"
                                max="<?php echo $customer['debt']; ?>" placeholder="Nhập số tiền mặt nhận..." required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-semibold text-secondary">Ghi chú thu nợ</label>
                            <textarea class="form-control small" name="note" rows="2"
                                placeholder="Khách trả nợ tiền mua tạp hóa..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top p-2 bg-white">
                        <button type="submit" class="btn btn-danger w-100 fw-semibold py-2 small">Xác nhận thu
                            nợ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

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
                                <input type="text" class="form-control" name="phone"
                                    value="<?php echo $customer['phone']; ?>" required>
                            </div>
                            <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Địa chỉ
                                    Email</label><input type="email" class="form-control" name="email"
                                    value="<?php echo $customer['email']; ?>"></div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Giới tính</label>
                                <select class="form-select" name="gender">
                                    <option value="male" <?php echo $customer['gender'] == 'male' ? 'selected' : ''; ?>>
                                        Nam</option>
                                    <option value="female"
                                        <?php echo $customer['gender'] == 'female' ? 'selected' : ''; ?>>Nữ</option>
                                    <option value="other"
                                        <?php echo $customer['gender'] == 'other' ? 'selected' : ''; ?>>Khác</option>
                                </select>
                            </div>
                            <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Ngày
                                    sinh</label><input type="date" class="form-control" name="date_of_birth"
                                    value="<?php echo $customer['date_of_birth']; ?>"></div>
                            <div class="col-12"><label class="form-label small fw-semibold text-secondary">Địa chỉ
                                    thường trú</label><input type="text" class="form-control" name="address"
                                    value="<?php echo $customer['address']; ?>"></div>
                            <div class="col-12"><label class="form-label small fw-semibold text-secondary">Ghi chú đặc
                                    điểm khách</label><textarea class="form-control" name="note"
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
                    <h5 class="fw-bold text-dark mb-2 modal-toggle-title">Ngừng theo dõi?</h5>
                    <p class="text-secondary small mb-4 modal-toggle-desc">Hệ thống tạm ẩn khách <strong
                            class="text-dark toggle-cust-name"><?php echo $customer['full_name']; ?></strong> khỏi màn
                        hình POS bán hàng.</p>
                    <?php else: ?>
                    <div class="text-success mb-3"><i class="bi bi-eye-fill fs-1"></i></div>
                    <h5 class="fw-bold text-dark mb-2 modal-toggle-title">Theo dõi lại?</h5>
                    <p class="text-secondary small mb-4 modal-toggle-desc">Kích hoạt hiển thị lại thông tin khách hàng
                        <strong class="text-dark toggle-cust-name"><?php echo $customer['full_name']; ?></strong>.</p>
                    <?php endif; ?>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light fw-semibold rounded-2 px-3 small"
                            data-bs-dismiss="modal">Hủy</button>
                        <button type="button"
                            data-url="/customer/toggle?id=<?php echo $customer['id']; ?>&status=<?php echo $customer['status']; ?>"
                            class="btn btn-toggle-cust-confirm fw-semibold rounded-2 px-4 small text-white <?php echo $customer['status'] == 1 ? 'btn-danger' : 'btn-success'; ?>">Xác
                            nhận</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

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
                                    id="add_customer_code" name="customer_code" placeholder="KH001" required>
                                <button class="btn btn-outline-secondary rounded-end-2 px-3" type="button"
                                    id="btnRandomCustCode"><i class="bi bi-shuffle"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Họ và tên khách
                                hàng</label><input type="text" class="form-control" name="full_name"
                                placeholder="Nguyễn Văn B" required></div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Số điện thoại liên hệ</label>
                            <input type="text" class="form-control" name="phone" placeholder="09xxxxxxxx" required>
                        </div>
                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Địa chỉ
                                Email</label><input type="email" class="form-control" name="email"
                                placeholder="khachhang@gmail.com"></div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Giới tính</label>
                            <select class="form-select" name="gender">
                                <option value="male">Nam</option>
                                <option value="female">Nữ</option>
                                <option value="other" selected>Khác</option>
                            </select>
                        </div>
                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Ngày sinh
                                nhật</label><input type="date" class="form-control" name="date_of_birth"></div>
                        <div class="col-12"><label class="form-label small fw-semibold text-secondary">Địa chỉ thường
                                trú</label><input type="text" class="form-control" name="address"
                                placeholder="Số nhà, tên đường..."></div>
                        <div class="col-12"><label class="form-label small fw-semibold text-secondary">Ghi chú thông tin
                                phụ</label><textarea class="form-control" name="note" rows="2"
                                placeholder="Khách hàng quen, đối tác VIP..."></textarea></div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-white">
                    <button type="button" class="btn btn-light fw-semibold rounded-2 px-3"
                        data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-semibold rounded-2 px-4 btn-submit-cust"
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

    function showToast(message, type = 'success') {
        const oldToast = document.getElementById('pos-custom-toast');
        if (oldToast) oldToast.remove();

        const toastDiv = document.createElement('div');
        toastDiv.id = 'pos-custom-toast';

        let bgColor = '#10b981';
        let icon = '<i class="bi bi-check-circle-fill me-2"></i>';
        if (type === 'error') {
            bgColor = '#ef4444';
            icon = '<i class="bi bi-exclamation-circle-fill me-2"></i>';
        }

        toastDiv.style.cssText = `
            position: fixed;
            top: 24px;
            right: 24px;
            background-color: ${bgColor};
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            z-index: 99999;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease;
        `;

        toastDiv.innerHTML = icon + message;
        document.body.appendChild(toastDiv);

        setTimeout(() => {
            toastDiv.style.opacity = '1';
            toastDiv.style.transform = 'translateY(0)';
        }, 50);

        setTimeout(() => {
            toastDiv.style.opacity = '0';
            toastDiv.style.transform = 'translateY(-20px)';
            setTimeout(() => toastDiv.remove(), 300);
        }, 4000);
    }

    function setBtnLoading(btn, isLoading, originalText = 'Lưu lại') {
        if (btn) {
            if (isLoading) {
                btn.disabled = true;
                btn.innerHTML =
                    `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Đang xử lý...`;
            } else {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }
    }

    function validateCustomerForm(formEl) {
        let isValid = true;
        const inputs = formEl.querySelectorAll('input, select, textarea');

        inputs.forEach(input => {
            if (input.hasAttribute('readonly') || input.type === 'hidden' || ['note', 'address',
                    'date_of_birth', 'search', 'email', 'amount'
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

    function loadCustomersData(page = 1) {
        const filterForm = document.querySelector('form[action="/customer/index"]');
        const formData = new FormData(filterForm);
        const params = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
            params.append(key, value);
        }
        params.append('page', page);
        params.append('ajax', '1');

        fetch('/customer/index?' + params.toString())
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderTableRows(data.customers);
                    renderPagination(data.total_pages, data.current_page);
                    updateModalContainers(data.customers);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function renderTableRows(customers) {
        const tbody = document.querySelector('table tbody');
        tbody.innerHTML = '';

        if (!customers || customers.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center p-5 text-secondary">
                        <i class="bi bi-person-exclamation d-block mb-2" style="font-size: 40px; color: #cbd5e1;"></i>
                        Không tìm thấy dữ liệu khách hàng nào phù hợp!
                    </td>
                </tr>`;
            return;
        }

        customers.forEach(customer => {
            const formattedSpent = parseFloat(customer.total_spent).toLocaleString('en-US') + 'đ';
            const formattedPoints = parseInt(customer.points).toLocaleString('en-US');

            let debtTd = '<span class="text-secondary">0đ</span>';
            let payDebtBtn = '';
            if (parseFloat(customer.debt) > 0) {
                const formattedDebt = parseFloat(customer.debt).toLocaleString('en-US') + 'đ';
                debtTd =
                    `<span class="badge rounded-1 px-2 py-1 fw-bold text-danger" style="background-color: #fde8e8; font-size: 12px;">Nợ: ${formattedDebt}</span>`;
                payDebtBtn =
                    `<button class="btn btn-sm btn-outline-danger rounded-2 px-2 py-1 small fw-bold shadow-none me-1 btn-paydebt-trigger" data-bs-toggle="modal" data-bs-target="#payDebtModal${customer.id}"><i class="bi bi-cash-coin"></i> Thu nợ</button>`;
            }

            const statusStyle = customer.status == 1 ?
                'background-color: #def7ec; color: #03543f; font-size: 12px;' :
                'background-color: #f3f4f6; color: #4b5563; font-size: 12px;';
            const statusTxt = customer.status == 1 ? 'Hoạt động' : 'Ngừng theo dõi';
            const toggleIcon = customer.status == 1 ? 'bi-eye-slash' : 'bi-eye';
            const toggleClass = customer.status == 1 ? 'btn-light text-danger' :
                'btn-light text-success';

            const tr = document.createElement('tr');
            tr.style.borderBottom = '1px solid #f1f5f9';
            tr.id = `customer-row-${customer.id}`;
            tr.innerHTML = `
                <td class="ps-4 fw-bold text-primary">${customer.customer_code}</td>
                <td class="fw-semibold text-dark">${customer.full_name}</td>
                <td class="font-monospace">${customer.phone}</td>
                <td class="fw-bold text-success">${formattedPoints}</td>
                <td class="fw-semibold">${formattedSpent}</td>
                <td>${debtTd}</td>
                <td><span class="badge rounded-1 px-2 py-1" style="${statusStyle}">${statusTxt}</span></td>
                <td class="text-end pe-4">
                    <div class="d-flex justify-content-end gap-1">
                        ${payDebtBtn}
                        <button class="btn btn-sm btn-light text-secondary border rounded-2 px-2 shadow-none" data-bs-toggle="modal" data-bs-target="#viewCustomerModal${customer.id}"><i class="bi bi-clock-history"></i></button>
                        <button class="btn btn-sm btn-light text-primary border rounded-2 px-2 shadow-none" data-bs-toggle="modal" data-bs-target="#editCustomerModal${customer.id}"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm border rounded-2 px-2 shadow-none ${toggleClass}" data-bs-toggle="modal" data-bs-target="#toggleStatusModal${customer.id}"><i class="bi ${toggleIcon}"></i></button>
                    </div>
                </td>`;
            tbody.appendChild(tr);
        });
    }

    function renderPagination(totalPages, currentPage) {
        const container = document.getElementById('pagination-container');
        if (totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        let numItems = '';
        for (let i = 1; i <= totalPages; i++) {
            const activeClass = currentPage == i ? 'active' : '';
            const linkStyle = currentPage == i ? 'style="background-color: #3c50e0; border-color: #3c50e0;"' :
                '';
            const textClass = currentPage == i ? 'text-white' : 'text-dark bg-white';
            numItems += `
                <li class="page-item page-number-item ${activeClass}" data-page-num="${i}">
                    <a class="page-link border rounded-2 px-3 py-2 shadow-none fw-semibold ${textClass}" ${linkStyle} href="#" data-page="${i}">${i}</a>
                </li>`;
        }

        const prevDisabled = currentPage <= 1 ? 'disabled' : '';
        const nextDisabled = currentPage >= totalPages ? 'disabled' : '';

        container.innerHTML = `
            <div class="card-footer bg-white border-top p-4 d-flex justify-content-between align-items-center">
                <div class="text-secondary small">
                    Hiển thị trang <strong class="current-page-txt">${currentPage}</strong> / <strong class="total-pages-txt">${totalPages}</strong> trang
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0 gap-1">
                        <li class="page-item page-prev-item ${prevDisabled}">
                            <a class="page-link border rounded-2 px-3 py-2 text-dark shadow-none" href="#" data-page="${currentPage - 1}"><i class="bi bi-chevron-left"></i></a>
                        </li>
                        ${numItems}
                        <li class="page-item page-next-item ${nextDisabled}">
                            <a class="page-link border rounded-2 px-3 py-2 text-dark shadow-none" href="#" data-page="${currentPage + 1}"><i class="bi bi-chevron-right"></i></a>
                        </li>
                    </ul>
                </nav>
            </div>`;
    }

    function updateModalContainers(customers) {
        const modalContainer = document.getElementById('dynamic-modals-container');
        modalContainer.innerHTML = '';

        customers.forEach(customer => {
            let historyRows = '';
            if (customer.history && customer.history.length > 0) {
                customer.history.forEach(log => {
                    const dateObj = new Date(log.created_at);
                    const formattedDate = ('0' + dateObj.getDate()).slice(-2) + '/' + ('0' + (
                            dateObj.getMonth() + 1)).slice(-2) + '/' + dateObj.getFullYear() +
                        ' ' + ('0' + dateObj.getHours()).slice(-2) + ':' + ('0' + dateObj
                            .getMinutes()).slice(-2);
                    const badgeClass = log.type === 'increase' ? 'bg-light-danger text-danger' :
                        'bg-light-success text-success';
                    const typeText = log.type === 'increase' ? 'Nợ thêm' : 'Trả nợ';
                    historyRows += `
                        <tr>
                            <td class="font-monospace">${formattedDate}</td>
                            <td><span class="badge rounded-1 ${badgeClass}">${typeText}</span></td>
                            <td class="fw-bold">${parseFloat(log.amount).toLocaleString('en-US')}đ</td>
                            <td class="font-monospace fw-semibold">${parseFloat(log.balance_after).toLocaleString('en-US')}đ</td>
                            <td>${log.staff_name}</td>
                            <td class="text-start text-secondary">${log.note}</td>
                        </tr>`;
                });
            } else {
                historyRows =
                    `<tr><td colspan="6" class="text-muted p-3 text-center">Không có lịch sử biến động công nợ!</td></tr>`;
            }

            let payDebtModalHtml = '';
            if (parseFloat(customer.debt) > 0) {
                payDebtModalHtml = `
                <div class="modal fade" id="payDebtModal${customer.id}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content border-0 shadow rounded-3">
                            <div class="modal-header p-3 border-bottom bg-white">
                                <h6 class="modal-title fw-bold text-dark"><i class="bi bi-cash-coin text-danger"></i> Tiến hành thu nợ khách</h6>
                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="/customer/payDebt" method="POST">
                                <input type="hidden" name="customer_id" value="${customer.id}">
                                <div class="modal-body p-3 bg-white">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary">Dư nợ hiện tại</label>
                                        <input type="text" class="form-control bg-light fw-bold text-danger modal-input-debt-val" value="${parseFloat(customer.debt).toLocaleString('en-US')}đ" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary">Số tiền thu thực tế</label>
                                        <input type="number" class="form-control modal-input-amount-field" name="amount" min="1" max="${customer.debt}" placeholder="Nhập số tiền mặt nhận..." required>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label small fw-semibold text-secondary">Ghi chú thu nợ</label>
                                        <textarea class="form-control small" name="note" rows="2" placeholder="Khách trả nợ tiền mua tạp hóa..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-top p-2 bg-white">
                                    <button type="submit" class="btn btn-danger w-100 fw-semibold py-2 small">Xác nhận thu nợ</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>`;
            }

            const toggleTitle = customer.status == 1 ? 'Ngừng theo dõi?' : 'Theo dõi lại?';
            const toggleDesc = customer.status == 1 ?
                `Hệ thống tạm ẩn khách <strong class="text-dark">${customer.full_name}</strong> khỏi màn hình POS bán hàng.` :
                `Kích hoạt hiển thị lại thông tin khách hàng <strong class="text-dark">${customer.full_name}</strong>.`;
            const toggleBtnClass = customer.status == 1 ? 'btn-danger' : 'btn-success';

            modalContainer.innerHTML += `
                <div class="modal fade" id="viewCustomerModal${customer.id}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow rounded-3">
                            <div class="modal-header p-4 border-bottom bg-white">
                                <h5 class="modal-title fw-bold text-dark">Sổ nợ & Thông tin: <span class="modal-title-cust-name">${customer.full_name}</span></h5>
                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4 bg-white">
                                <div class="row g-3 mb-4 text-center">
                                    <div class="col-4 border-end"><span class="small text-secondary font-monospace d-block">TỔNG MUA</span><span class="fw-bold text-dark fs-5 modal-txt-spent">${parseFloat(customer.total_spent).toLocaleString('en-US')}đ</span></div>
                                    <div class="col-4 border-end"><span class="small text-secondary font-monospace d-block">ĐIỂM TÍCH LŨY</span><span class="fw-bold text-success fs-5 modal-txt-points">${parseInt(customer.points).toLocaleString('en-US')}</span></div>
                                    <div class="col-4"><span class="small text-secondary font-monospace d-block">ĐANG NỢ</span><span class="fw-bold text-danger fs-5 modal-txt-debt">${parseFloat(customer.debt).toLocaleString('en-US')}đ</span></div>
                                </div>
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-journal-text"></i> Nhật ký lịch sử công nợ</h6>
                                <div class="table-responsive style-scroll" style="max-height: 250px; overflow-y: auto;">
                                    <table class="table table-sm table-bordered align-middle small text-center mb-0">
                                        <thead class="table-light text-secondary">
                                            <tr><th>Thời gian</th><th>Hành động</th><th>Số tiền</th><th>Nợ sau giao dịch</th><th>Nhân viên</th><th>Ghi chú</th></tr>
                                        </thead>
                                        <tbody class="modal-table-history">${historyRows}</tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                ${payDebtModalHtml}

                <div class="modal fade" id="editCustomerModal${customer.id}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow rounded-3">
                            <div class="modal-header p-4 border-bottom bg-white">
                                <h5 class="modal-title fw-bold text-dark">Chỉnh sửa thông tin khách hàng</h5>
                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="/customer/edit" method="POST" novalidate>
                                <input type="hidden" name="id" value="${customer.id}">
                                <div class="modal-body p-4 bg-white">
                                    <div class="row g-3">
                                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Mã khách hàng</label><input type="text" class="form-control bg-light" name="customer_code" value="${customer.customer_code}" readonly></div>
                                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Họ và tên khách</label><input type="text" class="form-control" name="full_name" value="${customer.full_name}" required></div>
                                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Số điện thoại</label><input type="text" class="form-control" name="phone" value="${customer.phone}" required></div>
                                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Địa chỉ Email</label><input type="email" class="form-control" name="email" value="${customer.email || ''}"></div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-secondary">Giới tính</label>
                                            <select class="form-select" name="gender">
                                                <option value="male" ${customer.gender == 'male' ? 'selected' : ''}>Nam</option>
                                                <option value="female" ${customer.gender == 'female' ? 'selected' : ''}>Nữ</option>
                                                <option value="other" ${customer.gender == 'other' ? 'selected' : ''}>Khác</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Ngày sinh</label><input type="date" class="form-control" name="date_of_birth" value="${customer.date_of_birth || ''}"></div>
                                        <div class="col-12"><label class="form-label small fw-semibold text-secondary">Địa chỉ thường trú</label><input type="text" class="form-control" name="address" value="${customer.address || ''}"></div>
                                        <div class="col-12"><label class="form-label small fw-semibold text-secondary">Ghi chú đặc điểm khách</label><textarea class="form-control" name="note" rows="2">${customer.note || ''}</textarea></div>
                                    </div>
                                </div>
                                <div class="modal-footer border-top p-3 bg-white">
                                    <button type="button" class="btn btn-light fw-semibold rounded-2 px-3" data-bs-dismiss="modal">Hủy</button>
                                    <button type="submit" class="btn btn-primary fw-semibold rounded-2 px-4" style="background-color: #3c50e0; border-color: #3c50e0;">Cập nhật</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="toggleStatusModal${customer.id}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content border-0 shadow-lg rounded-3">
                            <div class="modal-body p-4 text-center bg-white rounded-3">
                                <div class="${customer.status == 1 ? 'text-danger' : 'text-success'} mb-3"><i class="bi ${customer.status == 1 ? 'bi-eye-slash-fill' : 'bi-eye-fill'} fs-1"></i></div>
                                <h5 class="fw-bold text-dark mb-2 modal-toggle-title">${toggleTitle}</h5>
                                <p class="text-secondary small mb-4 modal-toggle-desc">${toggleDesc}</p>
                                <div class="d-flex gap-2 justify-content-center">
                                    <button type="button" class="btn btn-light fw-semibold rounded-2 px-3 small" data-bs-dismiss="modal">Hủy</button>
                                    <button type="button" data-url="/customer/toggle?id=${customer.id}&status=${customer.status}" class="btn btn-toggle-cust-confirm fw-semibold rounded-2 px-4 small text-white ${toggleBtnClass}">Xác nhận</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
        });
    }

    const filterForm = document.querySelector('form[action="/customer/index"]');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            loadCustomersData(1);
        });

        const btnReset = filterForm.querySelector('a[href="/customer/index"]');
        if (btnReset) {
            btnReset.addEventListener('click', function(e) {
                e.preventDefault();
                filterForm.reset();
                filterForm.querySelectorAll('input').forEach(i => i.value = '');
                filterForm.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
                loadCustomersData(1);
            });
        }
    }

    document.getElementById('pagination-container').addEventListener('click', function(e) {
        const targetLink = e.target.closest('a[data-page]');
        if (targetLink) {
            e.preventDefault();
            const parentLi = targetLink.parentNode;
            if (parentLi.classList.contains('disabled') || parentLi.classList.contains('active')) {
                return;
            }
            const targetPage = parseInt(targetLink.getAttribute('data-page'));
            loadCustomersData(targetPage);
        }
    });

    const addForm = document.querySelector('#addCustomerModal form');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!validateCustomerForm(addForm)) {
                return;
            }

            const btnSubmit = addForm.querySelector('.btn-submit-cust');
            setBtnLoading(btnSubmit, true, 'Lưu lại');

            const formData = new FormData(addForm);
            fetch('/customer/add', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    setBtnLoading(btnSubmit, false, 'Lưu lại');

                    const phoneInput = addForm.querySelector('input[name="phone"]');
                    phoneInput.classList.remove('is-invalid');
                    const oldFeedback = phoneInput.parentNode.querySelector('.invalid-feedback');
                    if (oldFeedback) oldFeedback.remove();

                    if (data.success) {
                        const modalEl = document.getElementById('addCustomerModal');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();
                        addForm.reset();

                        loadCustomersData(1);
                        showToast(data.message, 'success');
                    } else {
                        if (data.error_type === 'phone') {
                            phoneInput.classList.add('is-invalid');
                            const feedbackDiv = document.createElement('div');
                            feedbackDiv.className =
                                'invalid-feedback fw-semibold small mt-1 d-block';
                            feedbackDiv.innerText = data.message;
                            phoneInput.parentNode.appendChild(feedbackDiv);
                        } else {
                            showToast(data.message, 'error');
                        }
                    }
                })
                .catch(error => {
                    setBtnLoading(btnSubmit, false, 'Lưu lại');
                    console.error('Error:', error);
                    showToast('Có lỗi hệ thống xảy ra!', 'error');
                });
        });
    }

    document.addEventListener('submit', function(e) {
        const form = e.target.closest('form[action="/customer/edit"]');
        if (form) {
            e.preventDefault();
            if (!validateCustomerForm(form)) {
                return;
            }

            const btnSubmit = form.querySelector('button[type="submit"]');
            setBtnLoading(btnSubmit, true, 'Cập nhật');

            const formData = new FormData(form);
            fetch('/customer/edit', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    setBtnLoading(btnSubmit, false, 'Cập nhật');

                    const phoneInput = form.querySelector('input[name="phone"]');
                    phoneInput.classList.remove('is-invalid');
                    const oldFeedback = phoneInput.parentNode.querySelector('.invalid-feedback');
                    if (oldFeedback) oldFeedback.remove();

                    if (data.success) {
                        const modalEl = form.closest('.modal');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();

                        const activePageItem = document.querySelector('.page-number-item.active');
                        const currentPage = activePageItem ? parseInt(activePageItem.getAttribute(
                            'data-page-num')) : 1;
                        loadCustomersData(currentPage);
                        showToast(data.message, 'success');
                    } else {
                        if (data.error_type === 'phone') {
                            phoneInput.classList.add('is-invalid');
                            const feedbackDiv = document.createElement('div');
                            feedbackDiv.className =
                                'invalid-feedback fw-semibold small mt-1 d-block';
                            feedbackDiv.innerText = data.message;
                            phoneInput.parentNode.appendChild(feedbackDiv);
                        } else {
                            showToast(data.message, 'error');
                        }
                    }
                })
                .catch(error => {
                    setBtnLoading(btnSubmit, false, 'Cập nhật');
                    console.error('Error:', error);
                    showToast('Có lỗi hệ thống xảy ra!', 'error');
                });
        }
    });

    document.addEventListener('submit', function(e) {
        const form = e.target.closest('form[action="/customer/payDebt"]');
        if (form) {
            e.preventDefault();

            const btnSubmit = form.querySelector('button[type="submit"]');
            const originalText = btnSubmit.innerHTML;
            setBtnLoading(btnSubmit, true, originalText);

            const formData = new FormData(form);
            fetch('/customer/payDebt', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    setBtnLoading(btnSubmit, false, originalText);
                    if (data.success) {
                        const modalEl = form.closest('.modal');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();

                        const activePageItem = document.querySelector('.page-number-item.active');
                        const currentPage = activePageItem ? parseInt(activePageItem.getAttribute(
                            'data-page-num')) : 1;
                        loadCustomersData(currentPage);
                        showToast(data.message, 'success');
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    setBtnLoading(btnSubmit, false, originalText);
                    console.error('Error:', error);
                    showToast('Có lỗi hệ thống xảy ra!', 'error');
                });
        }
    });

    document.getElementById('dynamic-modals-container').addEventListener('click', function(e) {
        const confirmBtn = e.target.closest('.btn-toggle-cust-confirm');
        if (confirmBtn) {
            e.preventDefault();
            const targetUrl = confirmBtn.getAttribute('data-url');

            const originalTxt = confirmBtn.innerHTML;
            confirmBtn.disabled = true;
            confirmBtn.innerHTML =
                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;

            fetch(targetUrl)
                .then(response => response.json())
                .then(data => {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = originalTxt;
                    if (data.success) {
                        const modalEl = confirmBtn.closest('.modal');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();

                        const activePageItem = document.querySelector('.page-number-item.active');
                        const currentPage = activePageItem ? parseInt(activePageItem.getAttribute(
                            'data-page-num')) : 1;
                        loadCustomersData(currentPage);
                        showToast(data.message, 'success');
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = originalTxt;
                    console.error('Error:', error);
                    showToast('Có lỗi hệ thống xảy ra!', 'error');
                });
        }
    });
});
</script>

<?php require_once 'views/layout/footer.php'; ?>