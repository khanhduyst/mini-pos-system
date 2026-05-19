<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MINI POS - Hệ thống quản lý bán hàng tại quầy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f1f5f9;
        font-family: 'Segoe UI', system-ui, sans-serif;
    }

    .sidebar {
        background-color: #ffffff;
        min-height: 100vh;
        width: 260px;
        position: fixed;
        top: 0;
        left: 0;
        box-shadow: 1px 0 5px rgba(0, 0, 0, 0.05);
        z-index: 1000;
    }

    .main-content {
        margin-left: 260px;
        min-height: 100vh;
        background-color: #f8fafc;
    }

    .navbar-custom {
        background-color: #ffffff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .nav-link {
        color: #475569 !important;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.2s;
    }

    .nav-link:hover {
        color: #3c50e0 !important;
        background-color: #f1f5f9;
    }

    .nav-link.active {
        color: #ffffff !important;
        background-color: #3c50e0 !important;
        font-weight: 600 !important;
    }

    .nav-link.active i {
        color: #ffffff !important;
    }

    .card-custom {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    [aria-expanded="true"] .submenu-arrow {
        transform: rotate(180deg);
    }

    .bg-light-primary {
        background-color: #f1f5f9;
    }

    .style-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .style-scroll::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .style-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    </style>
</head>

<body>

    <?php
    // Tự động lấy tên controller từ URL hiện tại nếu biến hệ thống không truyền xuống
    $current_uri = isset($_SERVER['REQUEST_URI']) ? strtolower($_SERVER['REQUEST_URI']) : '';

    $is_user_active = (isset($controller_name) && $controller_name == 'user') || (strpos($current_uri, '/user') !== false);
    $is_customer_active = (isset($controller_name) && $controller_name == 'customer') || (strpos($current_uri, '/customer') !== false);
    $is_pos_active = (isset($controller_name) && $controller_name == 'pos') || (strpos($current_uri, '/pos') !== false);

    $is_category_active = (strpos($current_uri, '/category') !== false);
    $is_product_active = (strpos($current_uri, '/product') !== false);
    $is_product_group = $is_category_active || $is_product_active || (isset($controller_name) && in_array($controller_name, ['category', 'product', 'hanghoa']));
    ?>

    <div class="sidebar d-flex flex-column p-3">
        <div class="text-center py-3 mb-4 border-bottom">
            <h4 class="text-dark fw-bold mb-0 font-monospace" style="letter-spacing: 1px;">MINI <span
                    class="text-primary">POS</span></h4>
        </div>

        <ul class="nav nav-pills flex-column gap-1 flex-grow-1">
            <li class="nav-item">
                <a href="/user/index"
                    class="nav-link d-flex align-items-center gap-3 px-3 py-2 rounded-2 <?php echo $is_user_active ? 'active' : ''; ?>">
                    <i class="bi bi-people" style="font-size: 16px;"></i>
                    <span>Quản lý nhân viên</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/customer/index"
                    class="nav-link d-flex align-items-center gap-3 px-3 py-2 rounded-2 <?php echo $is_customer_active ? 'active' : ''; ?>">
                    <i class="bi bi-person-vcard" style="font-size: 16px;"></i>
                    <span>Quản lý khách hàng</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center justify-content-between px-3 py-2 rounded-2 <?php echo ($is_product_group && !$is_category_active && !$is_product_active) ? 'active' : ''; ?>"
                    data-bs-toggle="collapse" href="#productSubmenu" role="button"
                    aria-expanded="<?php echo $is_product_group ? 'true' : 'false'; ?>"
                    style="color: #475569 !important; font-weight: 500 !important;">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-box-seam" style="font-size: 16px; color: #475569;"></i>
                        <span>Quản lý hàng hóa</span>
                    </div>
                    <i class="bi bi-chevron-down submenu-arrow"
                        style="font-size: 12px; transition: transform 0.2s; color: #475569;"></i>
                </a>

                <div class="collapse <?php echo $is_product_group ? 'show' : ''; ?>" id="productSubmenu">
                    <ul class="list-unstyled m-0 mt-1 d-flex flex-column gap-1" style="padding-left: 12px;">
                        <li>
                            <a href="/category/index"
                                class="nav-link d-flex align-items-center gap-3 py-2 px-3 rounded-2 <?php echo $is_category_active ? 'active' : ''; ?>">
                                <i class="bi bi-tag" style="font-size: 16px; opacity: 0.8;"></i>
                                <span>Danh mục sản phẩm</span>
                            </a>
                        </li>
                        <li>
                            <a href="/product/index"
                                class="nav-link d-flex align-items-center gap-3 py-2 px-3 rounded-2 <?php echo $is_product_active ? 'active' : ''; ?>">
                                <i class="bi bi-boxes" style="font-size: 16px; opacity: 0.8;"></i>
                                <span>Danh sách sản phẩm</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a href="/pos/index"
                    class="nav-link d-flex align-items-center gap-3 px-3 py-2 rounded-2 <?php echo $is_pos_active ? 'active' : ''; ?>">
                    <i class="bi bi-cart3" style="font-size: 16px;"></i>
                    <span>Màn hình bán hàng</span>
                </a>
            </li>
        </ul>

        <div class="mt-auto border-top pt-3">
            <a href="/auth/logout" class="nav-link d-flex align-items-center gap-3 px-3 py-2 rounded-2 text-danger">
                <i class="bi bi-box-arrow-left" style="font-size: 16px;"></i>
                <span>Đăng xuất tài khoản</span>
            </a>
        </div>
    </div>

    <div class="main-content d-flex flex-column">
        <nav class="navbar navbar-expand navbar-custom px-4 py-3 border-bottom sticky-top">
            <div class="container-fluid p-0 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center" style="max-width: 400px; width: 100%;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i
                                class="bi bi-search"></i></span>
                        <input type="text" class="form-control bg-light border-start-0 shadow-none ps-1"
                            style="font-size: 13px;" placeholder="Tìm kiếm tính năng...">
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="text-end d-none d-sm-block">
                        <h6 class="text-dark fw-bold mb-0 small">
                            <?php echo isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Quản Trị Viên'; ?></h6>
                        <small class="text-muted"
                            style="font-size: 11px;"><?php echo (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ? 'Chủ cửa hàng' : 'Thu ngân'; ?></small>
                    </div>
                    <div class="dropdown">
                        <button class="btn p-0 border-0 d-flex align-items-center gap-1 shadow-none" type="button"
                            data-bs-toggle="dropdown">
                            <span
                                class="badge rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold font-monospace"
                                style="width: 35px; height: 35px; font-size: 13px;">
                                <?php
                                if (isset($_SESSION['full_name'])) {
                                    $words = explode(' ', $_SESSION['full_name']);
                                    echo strtoupper(substr(end($words), 0, 2));
                                } else {
                                    echo 'AD';
                                }
                                ?>
                            </span>
                            <i class="bi bi-chevron-down text-muted ms-1" style="font-size: 10px;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border mt-2 small rounded-2">
                            <li><a class="dropdown-menu-item dropdown-item py-2" href="/profile/index"><i
                                        class="bi bi-person me-2 text-secondary"></i>Hồ sơ cá nhân</a></li>
                            <li><a class="dropdown-menu-item dropdown-item py-2" href="/profile/password"><i
                                        class="bi bi-shield-lock me-2 text-secondary"></i>Đổi mật khẩu</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-menu-item dropdown-item py-2 text-danger" href="/auth/logout"><i
                                        class="bi bi-box-arrow-left me-2"></i>Đăng xuất</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div class="p-4 flex-grow-1 style-scroll" style="overflow-y: auto;">