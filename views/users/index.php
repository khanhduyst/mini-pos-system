<?php require_once 'views/layout/header.php'; ?>

<div class="card card-custom bg-white border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-4">
        <form action="/user/index" method="GET" class="row g-3 align-items-end" novalidate>
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-secondary">Tìm kiếm nhân viên</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control bg-light border-start-0 shadow-none" name="search"
                        placeholder="Nhập tên hoặc mã nhân viên..."
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-secondary">Chức vụ</label>
                <select class="form-select bg-light shadow-none" name="role_id">
                    <option value="">Tất cả chức vụ</option>
                    <option value="1"
                        <?php echo (isset($_GET['role_id']) && $_GET['role_id'] == '1') ? 'selected' : ''; ?>>Chủ cửa
                        hàng</option>
                    <option value="2"
                        <?php echo (isset($_GET['role_id']) && $_GET['role_id'] == '2') ? 'selected' : ''; ?>>Thu ngân
                    </option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-secondary">Trạng thái</label>
                <select class="form-select bg-light shadow-none" name="status">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1"
                        <?php echo (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : ''; ?>>Hoạt động
                    </option>
                    <option value="0"
                        <?php echo (isset($_GET['status']) && $_GET['status'] == '0') ? 'selected' : ''; ?>>Đang khóa
                    </option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-semibold shadow-none"
                    style="background-color: #3c50e0; border-color: #3c50e0;">Lọc</button>
                <a href="/user/index" class="btn btn-light border w-100 fw-semibold shadow-none">Xóa</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom bg-white">
    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold text-dark mb-1" style="font-size: 18px;">Quản lý nhân viên</h4>
            <p class="text-muted small mb-0" style="font-size: 13px;">Xem danh sách, phân quyền và trạng thái hoạt động
                của nhân viên tại quầy</p>
        </div>
        <button class="btn btn-primary fw-semibold px-3 py-2 rounded-2 d-flex align-items-center gap-2 shadow-none"
            style="background-color: #3c50e0; border-color: #3c50e0; font-size: 14px;" data-bs-toggle="modal"
            data-bs-target="#addUserModal">
            <i class="bi bi-plus-lg"></i> Thêm nhân viên
        </button>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary font-monospace small border-bottom"
                    style="background-color: #f8fafc;">
                    <tr>
                        <th class="ps-4 py-3 text-secondary" style="font-size: 12px;">MÃ NV</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">HỌ VÀ TÊN</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">TÀI KHOẢN</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">EMAIL</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">CHỨC VỤ</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">TRẠNG THÁI</th>
                        <th class="text-end pe-4 py-3 text-secondary" style="font-size: 12px;">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody class="text-dark" style="font-size: 14px;">
                    <?php if (isset($users) && is_array($users) && count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;" id="user-row-<?php echo $user['id']; ?>">
                                <td class="ps-4 fw-bold text-primary"><?php echo $user['user_code']; ?></td>
                                <td class="fw-semibold text-dark"><?php echo $user['full_name']; ?></td>
                                <td><?php echo $user['username']; ?></td>
                                <td class="text-secondary"><?php echo $user['email']; ?></td>
                                <td>
                                    <span class="badge rounded-1 px-2 py-1"
                                        style="<?php echo $user['role_name'] == 'admin' ? 'background-color: #fde8e8; color: #e02424; font-size: 12px;' : 'background-color: #e1effe; color: #1e429f; font-size: 12px;'; ?>">
                                        <?php echo $user['role_name'] == 'admin' ? 'Chủ cửa hàng' : 'Thu ngân'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge rounded-1 px-2 py-1"
                                        style="<?php echo $user['status'] == 1 ? 'background-color: #def7ec; color: #03543f; font-size: 12px;' : 'background-color: #f3f4f6; color: #4b5563; font-size: 12px;'; ?>">
                                        <?php echo $user['status'] == 1 ? 'Hoạt động' : 'Đang khóa'; ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-sm btn-light text-secondary border rounded-2 px-2 shadow-none"
                                            data-bs-toggle="modal" data-bs-target="#viewUserModal<?php echo $user['id']; ?>"><i
                                                class="bi bi-eye"></i></button>
                                        <button class="btn btn-sm btn-light text-primary border rounded-2 px-2 shadow-none"
                                            data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $user['id']; ?>"><i
                                                class="bi bi-pencil"></i></button>
                                        <button type="button" class="btn btn-sm btn-outline-warning px-2 shadow-none" title ="Cấp lại mật khẩu"
                                            onclick="resetEmployeePassword(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['full_name'], ENT_QUOTES); ?>')">
                                            <i class="bi bi-arrow-counterclockwise"></i> 
                                        </button>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <button
                                                class="btn btn-sm border rounded-2 px-2 shadow-none <?php echo $user['status'] == 1 ? 'btn-light text-danger' : 'btn-light text-success'; ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#toggleStatusModal<?php echo $user['id']; ?>"><i
                                                    class="bi <?php echo $user['status'] == 1 ? 'bi-lock' : 'bi-unlock'; ?>"></i></button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-light text-muted border rounded-2 px-2 shadow-none"
                                                disabled><i class="bi bi-person-check-fill"></i></button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center p-5 text-secondary">
                                <i class="bi bi-person-x d-block mb-2" style="font-size: 40px; color: #cbd5e1;"></i>
                                Không tìm thấy nhân viên nào phù hợp với bộ lọc!
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
                            $base_url = "/user/index?" . http_build_query($query_str) . "&page=";
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
    <?php if (isset($users) && is_array($users) && count($users) > 0): ?>
        <?php foreach ($users as $user): ?>
            <div class="modal fade" id="viewUserModal<?php echo $user['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow rounded-3">
                        <div class="modal-header p-4 border-bottom bg-white">
                            <h5 class="modal-title fw-bold text-dark">Hồ sơ nhân viên: <?php echo $user['user_code']; ?></h5>
                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4 bg-white">
                            <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                                <img src="<?php echo isset($user['avatar']) && $user['avatar'] ? $user['avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['full_name']) . '&background=3c50e0&color=fff&rounded=true'; ?>"
                                    width="50" height="50">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1"><?php echo $user['full_name']; ?></h6>
                                    <small class="text-muted">Tài khoản: <?php echo $user['username']; ?></small>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-6"><span class="small text-secondary font-monospace d-block">EMAIL</span> <span
                                        class="fw-semibold text-dark"><?php echo $user['email']; ?></span></div>
                                <div class="col-6"><span class="small text-secondary font-monospace d-block">SỐ ĐIỆN
                                        THOẠI</span> <span
                                        class="fw-semibold text-dark"><?php echo $user['phone'] ? $user['phone'] : 'N/A'; ?></span>
                                </div>
                                <div class="col-6"><span class="small text-secondary font-monospace d-block">GIỚI TÍNH</span>
                                    <span
                                        class="fw-semibold text-dark"><?php echo $user['gender'] == 'male' ? 'Nam' : ($user['gender'] == 'female' ? 'Nữ' : 'Khác'); ?></span>
                                </div>
                                <div class="col-6"><span class="small text-secondary font-monospace d-block">NGÀY SINH</span>
                                    <span
                                        class="fw-semibold text-dark"><?php echo $user['date_of_birth'] ? date('d/m/Y', strtotime($user['date_of_birth'])) : 'N/A'; ?></span>
                                </div>
                                <div class="col-6"><span class="small text-secondary font-monospace d-block">CHỨC VỤ</span>
                                    <span
                                        class="fw-semibold text-dark"><?php echo $user['role_name'] == 'admin' ? 'Chủ cửa hàng' : 'Thu ngân'; ?></span>
                                </div>
                                <div class="col-6"><span class="small text-secondary font-monospace d-block">NGÀY GIA
                                        NHẬP</span> <span
                                        class="fw-semibold text-dark"><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></span>
                                </div>
                                <div class="col-12"><span class="small text-secondary font-monospace d-block">ĐỊA CHỈ</span>
                                    <span
                                        class="fw-semibold text-dark"><?php echo $user['address'] ? $user['address'] : 'N/A'; ?></span>
                                </div>
                                <div class="col-12"><span class="small text-secondary font-monospace d-block">GHI CHÚ</span>
                                    <span
                                        class="fw-semibold text-muted"><?php echo $user['note'] ? $user['note'] : 'Không có'; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top p-3 bg-white">
                            <button type="button" class="btn btn-light fw-semibold rounded-2 px-4 shadow-none"
                                data-bs-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editUserModal<?php echo $user['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow rounded-3">
                        <div class="modal-header p-4 border-bottom bg-white">
                            <h5 class="modal-title fw-bold text-dark">Cập nhật thông tin nhân viên</h5>
                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="/user/edit" method="POST" novalidate>
                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                            <div class="modal-body p-4 bg-white">
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Mã nhân
                                            viên</label><input type="text" class="form-control bg-light" name="user_code"
                                            value="<?php echo htmlspecialchars($user['user_code']); ?>" readonly></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Tên đăng
                                            nhập</label><input type="text" class="form-control bg-light"
                                            value="<?php echo htmlspecialchars($user['username']); ?>" readonly></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Họ và
                                            tên</label><input type="text" class="form-control" name="full_name"
                                            value="<?php echo htmlspecialchars($user['full_name']); ?>" required></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Địa chỉ
                                            Email</label><input type="email" class="form-control" name="email"
                                            value="<?php echo htmlspecialchars($user['email']); ?>" required></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Số điện
                                            thoại</label><input type="text" class="form-control" name="phone"
                                            value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"></div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-secondary">Giới tính</label>
                                        <select class="form-select" name="gender">
                                            <option value="male" <?php echo $user['gender'] == 'male' ? 'selected' : ''; ?>>Nam
                                            </option>
                                            <option value="female" <?php echo $user['gender'] == 'female' ? 'selected' : ''; ?>>
                                                Nữ</option>
                                            <option value="other" <?php echo $user['gender'] == 'other' ? 'selected' : ''; ?>>
                                                Khác</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Ngày
                                            sinh</label><input type="date" class="form-control" name="date_of_birth"
                                            value="<?php echo $user['date_of_birth']; ?>"></div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-secondary">Vai trò phân quyền</label>
                                        <select class="form-select" name="role_id">
                                            <option value="2" <?php echo $user['role_id'] == 2 ? 'selected' : ''; ?>>Thu ngân
                                                (Staff)</option>
                                            <option value="1" <?php echo $user['role_id'] == 1 ? 'selected' : ''; ?>>Chủ cửa
                                                hàng (Admin)</option>
                                        </select>
                                    </div>
                                    <div class="col-12"><label class="form-label small fw-semibold text-secondary">Địa
                                            chỉ</label><input type="text" class="form-control" name="address"
                                            value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>"></div>
                                    <div class="col-12"><label class="form-label small fw-semibold text-secondary">Ghi
                                            chú</label><textarea class="form-control" name="note"
                                            rows="2"><?php echo htmlspecialchars($user['note'] ?? ''); ?></textarea></div>
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

            <div class="modal fade" id="toggleStatusModal<?php echo $user['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content border-0 shadow-lg rounded-3">
                        <div class="modal-body p-4 text-center bg-white rounded-3">
                            <?php if ($user['status'] == 1): ?>
                                <div class="text-danger mb-3"><i class="bi bi-exclamation-triangle-fill fs-1"></i></div>
                                <h5 class="fw-bold text-dark mb-2">Khóa tài khoản?</h5>
                                <p class="text-secondary small mb-4">Nhân viên <strong
                                        class="text-dark"><?php echo $user['full_name']; ?></strong> sẽ không thể đăng nhập sau khi
                                    bị khóa.</p>
                            <?php else: ?>
                                <div class="text-success mb-3"><i class="bi bi-info-circle-fill fs-1"></i></div>
                                <h5 class="fw-bold text-dark mb-2">Mở khóa?</h5>
                                <p class="text-secondary small mb-4">Kích hoạt lại quyền truy cập cho nhân viên <strong
                                        class="text-dark"><?php echo $user['full_name']; ?></strong>.</p>
                            <?php endif; ?>
                            <div class="d-flex gap-2 justify-content-center">
                                <button type="button" class="btn btn-light fw-semibold rounded-2 px-3 small"
                                    data-bs-dismiss="modal">Hủy</button>
                                <button type="button"
                                    data-url="/user/toggle?id=<?php echo $user['id']; ?>&status=<?php echo $user['status']; ?>"
                                    class="btn btn-toggle-confirm fw-semibold rounded-2 px-4 small text-white <?php echo $user['status'] == 1 ? 'btn-danger' : 'btn-success'; ?>">Xác
                                    nhận</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-1 shadow rounded-3">
            <div class="modal-header p-4 border-bottom bg-white">
                <h5 class="modal-title fw-bold text-dark">Khởi tạo nhân viên mới</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form action="/user/add" method="POST" novalidate>
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Mã nhân viên</label>
                            <div class="input-group">
                                <input type="text" class="form-control rounded-start-2 shadow-none" id="add_user_code"
                                    name="user_code" placeholder="NV001" required>
                                <button class="btn btn-outline-secondary rounded-end-2 px-3" type="button"
                                    id="btnRandomCode" title="Tạo mã ngẫu nhiên">
                                    <i class="bi bi-shuffle"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Tên đăng
                                nhập</label><input type="text" class="form-control" name="username"
                                placeholder="username" required></div>
                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Họ và
                                tên</label><input type="text" class="form-control" name="full_name"
                                placeholder="Nguyễn Văn A" required></div>
                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Địa chỉ
                                Email</label><input type="email" class="form-control" name="email"
                                placeholder="name@example.com" required></div>
                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Số điện
                                thoại</label><input type="text" class="form-control" name="phone"
                                placeholder="0901234..."></div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Giới tính</label>
                            <select class="form-select" name="gender">
                                <option value="male">Nam</option>
                                <option value="female">Nữ</option>
                                <option value="other" selected>Khác</option>
                            </select>
                        </div>
                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Ngày
                                sinh</label><input type="date" class="form-control" name="date_of_birth"></div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Vai trò phân quyền</label>
                            <select class="form-select" name="role_id">
                                <option value="2" selected>Thu ngân (Staff)</option>
                                <option value="1">Chủ cửa hàng (Admin)</option>
                            </select>
                        </div>
                        <div class="col-12"><label class="form-label small fw-semibold text-secondary">Địa
                                chi</label><input type="text" class="form-control" name="address"
                                placeholder="Số nhà, tên đường..."></div>
                        <div class="col-12"><label class="form-label small fw-semibold text-secondary">Ghi
                                chú</label><textarea class="form-control" name="note" rows="2"
                                placeholder="Hệ thống tự động sinh mật khẩu ngẫu nhiên và gửi về Email trên..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-white">
                    <button type="button" class="btn btn-light fw-semibold rounded-2 px-3"
                        data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-semibold rounded-2 px-4 btn-submit-user"
                        style="background-color: #3c50e0; border-color: #3c50e0;">Lưu lại</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script src="/assets/js/users/index.js"></script>

<?php require_once 'views/layout/footer.php'; ?>