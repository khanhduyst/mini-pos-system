<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Hệ Thống - MINI POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f1f5f9;
        font-family: 'Segoe UI', system-ui, sans-serif;
    }

    .card-login {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        background-color: #ffffff;
        max-width: 400px;
        width: 100%;
    }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100 p-3">

    <div class="card card-login p-4 p-sm-5">
        <div class="text-center mb-4">
            <h4 class="text-dark fw-bold mb-1 font-monospace" style="letter-spacing: 1px;">MINI <span
                    class="text-primary">POS</span></h4>
            <p class="text-muted small">Đăng nhập để vào màn hình quản lý quầy</p>
        </div>

        <form action="/auth/login" method="POST" novalidate>
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Tên đăng nhập</label>
                <input type="text" class="form-control p-2 shadow-none" name="username" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-semibold text-secondary">Mật khẩu</label>
                <input type="password" class="form-control p-2 shadow-none" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-semibold py-2"
                style="background-color: #3c50e0; border-color: #3c50e0;">Đăng nhập</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php if (isset($_SESSION['flash_login_success'])): ?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: 'success',
            title: 'Thành công',
            text: 'Đăng nhập hệ thống thành công!',
            timer: 2000,
            showConfirmButton: false,
            timerProgressBar: true,
            willClose: () => {
                window.location.href = "/dashboard/index";
            }
        });
    });
    </script>
    <?php
        unset($_SESSION['flash_login_success']);
    endif;
    ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: 'error',
            title: 'Thất bại',
            text: '<?php echo $_SESSION['flash_error']; ?>',
            timer: 2000,
            showConfirmButton: false,
            timerProgressBar: true
        });
    });
    </script>
    <?php
        unset($_SESSION['flash_error']);
    endif;
    ?>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const inputs = form.querySelectorAll('input');
            inputs.forEach(input => {
                input.classList.remove('is-invalid');
                const oldFeedback = input.parentNode.querySelector('.invalid-feedback');
                if (oldFeedback) oldFeedback.remove();

                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('is-invalid');
                    const feedbackDiv = document.createElement('div');
                    feedbackDiv.className = 'invalid-feedback fw-semibold small mt-1';
                    feedbackDiv.innerText = "Trường thông tin này không được để trống!";
                    input.parentNode.appendChild(feedbackDiv);
                }
            });
            if (!isValid) e.preventDefault();
        });
    });
    </script>
</body>

</html>