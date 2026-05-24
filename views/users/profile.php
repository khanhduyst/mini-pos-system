<?php require_once 'views/layout/header.php'; ?>

<div class="row g-4">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
            <div class="card-body p-4 text-center d-flex flex-column align-items-center justify-content-center">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mb-3 fw-bold font-monospace shadow-sm" style="width: 80px; height: 80px; font-size: 32px; background-color: #3c50e0 !important;">
                    <?php 
                    $display_name = isset($user['username']) ? $user['username'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'US');
                    echo strtoupper(substr($display_name, 0, 2)); 
                    ?>
                </div>
                <h4 class="fw-bold text-dark mb-1">
                    <?php echo htmlspecialchars(isset($user['full_name']) ? $user['full_name'] : (isset($user['username']) ? $user['username'] : 'Người dùng')); ?>
                </h4>
                <span class="badge bg-primary-subtle text-primary px-3 py-1.5 fw-semibold mb-4" style="font-size: 13px;">
                    <i class="bi bi-shield-lock me-1"></i> Chức vụ: <?php echo htmlspecialchars(isset($user['role_name']) ? $user['role_name'] : 'Nhân viên'); ?>
                </span>

                <div class="w-100 text-start border-top pt-3 small text-secondary">
                    <div class="mb-2.5 d-flex justify-content-between">
                        <span>Tên đăng nhập:</span>
                        <strong class="text-dark font-monospace">
                            <?php echo htmlspecialchars(isset($user['username']) ? $user['username'] : '---'); ?>
                        </strong>
                    </div>
                    <div class="mb-2.5 d-flex justify-content-between">
                        <span>Số điện thoại:</span>
                        <strong class="text-dark font-monospace">
                            <?php echo htmlspecialchars(isset($user['phone']) ? $user['phone'] : '---'); ?>
                        </strong>
                    </div>
                    <div class="mb-0 d-flex justify-content-between">
                        <span>Trạng thái hoạt động:</span>
                        <span class="text-success fw-bold"><i class="bi bi-circle-fill font-monospace me-1" style="font-size:9px;"></i>Đang trực tuyến</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
            <div class="card-header bg-white border-bottom p-4">
                <h5 class="fw-bold text-dark mb-1" style="font-size: 16px;"><i class="bi bi-key text-primary me-1"></i> Thiết lập mật khẩu an toàn</h5>
                <p class="text-muted small mb-0">Nên thay đổi mật khẩu định kỳ để bảo mật dữ liệu doanh thu quầy POS</p>
            </div>
            <div class="card-body p-4">
                <form id="changePasswordForm" novalidate>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Mật khẩu hiện tại</label>
                        <input type="password" class="form-control" name="current_password" required placeholder="••••••••">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Mật khẩu mới</label>
                        <input type="password" class="form-control" name="new_password" required placeholder="Tối thiểu 6 ký tự...">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-secondary">Xác nhận mật khẩu mới</label>
                        <input type="password" class="form-control" name="confirm_password" required placeholder="Nhập lại mật khẩu mới...">
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary fw-semibold px-4 shadow-none" id="btnSubmitChangePass" style="background-color: #3c50e0; border-color: #3c50e0;">
                            Cập nhật mật khẩu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/users/profile.js"></script>

<?php require_once 'views/layout/footer.php'; ?>