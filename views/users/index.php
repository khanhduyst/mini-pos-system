<?php require_once 'views/layout/header.php'; ?>

<div class="card card-custom bg-white">
    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold text-dark mb-1" style="font-size: 18px;">Quản lý nhân viên</h4>
            <p class="text-muted small mb-0" style="font-size: 13px;">Xem danh sách, phân quyền và trạng thái hoạt động của nhân viên tại quầy</p>
        </div>
        <button class="btn btn-primary fw-semibold px-3 py-2 rounded-2 d-flex align-items-center gap-2 shadow-none" style="background-color: #3c50e0; border-color: #3c50e0; font-size: 14px;" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-plus-lg"></i> Thêm nhân viên
        </button>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary font-monospace small border-bottom" style="background-color: #f8fafc;">
                    <tr>
                        <th class="ps-4 py-3 text-secondary" style="font-size: 12px; letter-spacing: 0.5px;">MÃ NV</th>
                        <th class="py-3 text-secondary" style="font-size: 12px; letter-spacing: 0.5px;">HỌ VÀ TÊN</th>
                        <th class="py-3 text-secondary" style="font-size: 12px; letter-spacing: 0.5px;">TÀI KHOẢN</th>
                        <th class="py-3 text-secondary" style="font-size: 12px; letter-spacing: 0.5px;">EMAIL</th>
                        <th class="py-3 text-secondary" style="font-size: 12px; letter-spacing: 0.5px;">CHỨC VỤ</th>
                        <th class="py-3 text-secondary" style="font-size: 12px; letter-spacing: 0.5px;">TRẠNG THÁI</th>
                        <th class="text-end pe-4 py-3 text-secondary" style="font-size: 12px; letter-spacing: 0.5px;">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody class="text-dark" style="font-size: 14px;">
                    <?php if (isset($users) && is_array($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td class="ps-4 fw-bold text-primary"><?php echo $user['user_code']; ?></td>
                                <td class="fw-semibold text-dark"><?php echo $user['full_name']; ?></td>
                                <td><?php echo $user['username']; ?></td>
                                <td class="text-secondary"><?php echo $user['email']; ?></td>
                                <td>
                                    <span class="badge rounded-1 px-2 py-1" style="<?php echo $user['role'] == 'admin' ? 'background-color: #fde8e8; color: #e02424; font-size: 12px;' : 'background-color: #e1effe; color: #1e429f; font-size: 12px;'; ?>">
                                        <?php echo $user['role'] == 'admin' ? 'Chủ cửa hàng' : 'Thu ngân'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge rounded-1 px-2 py-1" style="<?php echo $user['status'] == 1 ? 'background-color: #def7ec; color: #03543f; font-size: 12px;' : 'background-color: #f3f4f6; color: #4b5563; font-size: 12px;'; ?>">
                                        <?php echo $user['status'] == 1 ? 'Hoạt động' : 'Đang khóa'; ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-sm btn-light text-secondary border rounded-2 px-2 shadow-none" title="Xem chi tiết" data-bs-toggle="modal" data-bs-target="#viewUserModal<?php echo $user['id']; ?>">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        <button class="btn btn-sm btn-light text-primary border rounded-2 px-2 shadow-none" title="Sửa thông tin" data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $user['id']; ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <a href="/user/toggle?id=<?php echo $user['id']; ?>&status=<?php echo $user['status']; ?>" class="btn btn-sm border rounded-2 px-2 shadow-none <?php echo $user['status'] == 1 ? 'btn-light text-danger' : 'btn-light text-success'; ?>" title="<?php echo $user['status'] == 1 ? 'Khóa tài khoản' : 'Mở khóa tài khoản'; ?>">
                                                <i class="bi <?php echo $user['status'] == 1 ? 'bi-lock' : 'bi-unlock'; ?>"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-light text-muted border rounded-2 px-2 shadow-none" disabled title="Tài khoản của bạn">
                                                <i class="bi bi-person-check-fill"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>

                            <div class="modal fade" id="viewUserModal<?php echo $user['id']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow rounded-3">
                                        <div class="modal-header p-4 border-bottom bg-white">
                                            <h5 class="modal-title fw-bold text-dark">Hồ sơ nhân viên: <?php echo $user['user_code']; ?></h5>
                                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4 bg-white">
                                            <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['full_name']); ?>&background=3c50e0&color=fff&rounded=true" width="50" height="50">
                                                <div>
                                                    <h6 class="fw-bold text-dark mb-1"><?php echo $user['full_name']; ?></h6>
                                                    <small class="text-muted">Tài khoản: <?php echo $user['username']; ?></small>
                                                </div>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-6"><span class="small text-secondary font-monospace d-block">EMAIL</span> <span class="fw-semibold text-dark"><?php echo $user['email']; ?></span></div>
                                                <div class="col-6"><span class="small text-secondary font-monospace d-block">SỐ ĐIỆN THOẠI</span> <span class="fw-semibold text-dark"><?php echo $user['phone'] ? $user['phone'] : 'Chưa cập nhật'; ?></span></div>
                                                <div class="col-6"><span class="small text-secondary font-monospace d-block">CHỨC VỤ</span> <span class="fw-semibold text-dark"><?php echo $user['role'] == 'admin' ? 'Chủ cửa hàng' : 'Thu ngân'; ?></span></div>
                                                <div class="col-6"><span class="small text-secondary font-monospace d-block">NGÀY GIA NHẬP</span> <span class="fw-semibold text-dark"><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></span></div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top p-3 bg-white">
                                            <button type="button" class="btn btn-light fw-semibold rounded-2 px-4 shadow-none" data-bs-dismiss="modal">Đóng</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="editUserModal<?php echo $user['id']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow rounded-3">
                                        <div class="modal-header p-4 border-bottom bg-white">
                                            <h5 class="modal-title fw-bold text-dark">Cập nhật thông tin nhân viên</h5>
                                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="/user/edit" method="POST">
                                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                            <div class="modal-body p-4 bg-white">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold text-secondary">Mã nhân viên</label>
                                                        <input type="text" class="form-control border rounded-2 p-2 shadow-none bg-light" name="user_code" value="<?php echo $user['user_code']; ?>" readonly>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold text-secondary">Tên đăng nhập</label>
                                                        <input type="text" class="form-control border rounded-2 p-2 shadow-none bg-light" value="<?php echo $user['username']; ?>" readonly>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label small fw-semibold text-secondary">Họ và tên</label>
                                                        <input type="text" class="form-control border rounded-2 p-2 shadow-none" name="full_name" value="<?php echo $user['full_name']; ?>" required>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label small fw-semibold text-secondary">Địa chỉ Email</label>
                                                        <input type="email" class="form-control border rounded-2 p-2 shadow-none" name="email" value="<?php echo $user['email']; ?>" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold text-secondary">Số điện thoại</label>
                                                        <input type="text" class="form-control border rounded-2 p-2 shadow-none" name="phone" value="<?php echo $user['phone']; ?>">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold text-secondary">Vai trò phân quyền</label>
                                                        <select class="form-select border rounded-2 p-2 shadow-none" name="role">
                                                            <option value="staff" <?php echo $user['role'] == 'staff' ? 'selected' : ''; ?>>Thu ngân (Staff)</option>
                                                            <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Chủ cửa hàng (Admin)</option>
                                                        </select>
                                                    </div>
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

                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-1 shadow rounded-3">
            <div class="modal-header p-4 border-bottom bg-white">
                <h5 class="modal-title fw-bold text-dark">Khởi tạo nhân viên mới</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/user/add" method="POST">
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Mã nhân viên</label>
                            <input type="text" class="form-control border rounded-2 p-2 shadow-none" name="user_code" placeholder="NV001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Tên đăng nhập</label>
                            <input type="text" class="form-control border rounded-2 p-2 shadow-none" name="username" placeholder="username" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Mật khẩu</label>
                            <input type="password" class="form-control border rounded-2 p-2 shadow-none" name="password" placeholder="••••••••" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Họ và tên</label>
                            <input type="text" class="form-control border rounded-2 p-2 shadow-none" name="full_name" placeholder="Nguyễn Văn A" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Địa chỉ Email</label>
                            <input type="email" class="form-control border rounded-2 p-2 shadow-none" name="email" placeholder="name@example.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Số điện thoại</label>
                            <input type="text" class="form-control border rounded-2 p-2 shadow-none" name="phone" placeholder="0901234...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Vai trò phân quyền</label>
                            <select class="form-select border rounded-2 p-2 shadow-none" name="role">
                                <option value="staff">Thu ngân (Staff)</option>
                                <option value="admin">Chủ cửa hàng (Admin)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-white">
                    <button type="button" class="btn btn-light fw-semibold rounded-2 px-3" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-semibold rounded-2 px-4" style="background-color: #3c50e0; border-color: #3c50e0;">Lưu lại</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>