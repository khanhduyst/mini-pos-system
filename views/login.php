<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Hệ Thống Mini POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-8 col-md-6 col-lg-4">
                <div class="card border-0 shadow-lg rounded-4 p-4">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-primary mb-1">MINI POS</h2>
                            <p class="text-muted small">Hệ thống quản lý kho & bán hàng tại quầy</p>
                        </div>
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger text-center py-2 small mb-3 rounded-3" role="alert">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        <form action="/auth/login" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label font-monospace small fw-bold text-secondary">TÊN ĐĂNG NHẬP</label>
                                <input type="text" class="form-control form-control-lg bg-light border-0 rounded-3 shadow-none fs-6" id="username" name="username" placeholder="Nhập tài khoản..." required>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label font-monospace small fw-bold text-secondary">MẬT KHẨU</label>
                                <input type="password" class="form-control form-control-lg bg-light border-0 rounded-3 shadow-none fs-6" id="password" name="password" placeholder="Nhập mật khẩu..." required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold rounded-3 shadow-sm py-2fs-6">ĐĂNG NHẬP</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>