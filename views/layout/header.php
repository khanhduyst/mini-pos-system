<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ Thống Quản Lý MINI POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .sidebar {
            min-width: 280px;
            max-width: 280px;
            background-color: #ffffff;
            min-height: 100vh;
            border-right: 1px solid #e2e8f0;
        }

        .sidebar .nav-link {
            color: #64748b;
            padding: 12px 24px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 0px;
            margin: 0;
            border-left: 4px solid transparent;
        }

        .sidebar .nav-link:hover {
            background-color: #f8fafc;
            color: #3c50e0;
        }

        .sidebar .nav-link.active {
            background-color: #f0f2fe;
            color: #3c50e0;
            border-left-color: #3c50e0;

        }

        .main-content {
            width: 100%;
        }

        .top-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 15px 40px;
        }

        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            background-color: #ffffff;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <div class="sidebar d-flex flex-column flex-shrink-0 py-3">
            <div class="px-4 py-2 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-grid-fill text-primary fs-4"></i>
                <h4 class="text-dark fw-bold mb-0 font-monospace" style="letter-spacing: 1px;">MINI <span class="text-primary">POS</span></h4>
            </div>
            <div class="px-4 text-uppercase font-monospace text-secondary small fw-bold mb-2" style="letter-spacing: 0.5px; font-size: 11px;">MENU</div>
            <ul class="nav nav-pills flex-column mb-auto">
                <li>
                    <a href="/home" class="nav-link">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="/user/index" class="nav-link active">
                        <i class="bi bi-people"></i> Quản lý nhân viên
                    </a>
                </li>
                <li>
                    <a href="/customer/index" class="nav-link">
                        <i class="bi bi-people-fill"></i> Quản lý khách hàng
                    </a>
                </li>
                <li>
                    <a href="/category/index" class="nav-link">
                        <i class="bi bi-tags"></i> Danh mục sản phẩm
                    </a>
                </li>
                <li>
                    <a href="/product/index" class="nav-link">
                        <i class="bi bi-box-seam"></i> Quản lý hàng hóa
                    </a>
                </li>
                <li>
                    <a href="/pos/index" class="nav-link">
                        <i class="bi bi-cart3"></i> Màn hình bán hàng
                    </a>
                </li>
            </ul>
        </div>

        <div class="main-content d-flex flex-column">
            <div class="top-navbar d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center bg-light px-3 py-2 rounded-pill" style="width: 300px;">
                    <i class="bi bi-search text-secondary me-2"></i>
                    <input type="text" class="form-control bg-transparent border-0 p-0 shadow-none small" style="font-size: 14px;" placeholder="Tìm kiếm tính năng...">
                </div>
                <div class="dropdown">
                    <button class="btn btn-transparent dropdown-toggle d-flex align-items-center gap-3 border-0 p-0 shadow-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="text-end d-none d-sm-block">
                            <div class="fw-bold text-dark lh-1" style="font-size: 14px;"><?php echo $_SESSION['full_name']; ?></div>
                            <small class="text-muted" style="font-size: 12px;"><?php echo $_SESSION['role'] == 'admin' ? 'Chủ cửa hàng' : 'Thu ngân'; ?></small>
                        </div>
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['full_name']); ?>&background=3c50e0&color=fff&rounded=true" width="36" height="36" alt="User Avatar">
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm mt-2 rounded-3 border">
                        <li><a class="dropdown-item py-2 small d-flex align-items-center gap-2" href="/profile"><i class="bi bi-person"></i> Hồ sơ của tôi</a></li>
                        <li>
                            <hr class="dropdown-divider my-1">
                        </li>
                        <li><a class="dropdown-item py-2 small text-danger d-flex align-items-center gap-2" href="/auth/logout"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
            <div class="p-4 p-lg-5 container-fluid">