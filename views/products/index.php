<?php
require_once 'views/layout/header.php';
$view_products   = $products ?? [];
$view_categories = $categories ?? [];
$page_current    = $current_page ?? 1;
$page_total      = $total_pages ?? 1;
$is_add_error    = isset($_SESSION['error_add_prod']);
?>

<div class="card card-custom bg-white border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-4">
        <form action="/product/index" method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-semibold text-secondary">Tìm kiếm hàng hóa</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control bg-light border-start-0 shadow-none" name="search" placeholder="Nhập tên sản phẩm, mã gốc hoặc mã vạch..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-secondary">Lọc theo nhóm danh mục</label>
                <select class="form-select bg-light shadow-none" name="category_id">
                    <option value="">Tất cả danh mục</option>
                    <?php foreach ($view_categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo (isset($_GET['category_id']) && $_GET['category_id'] == $cat['id']) ? 'selected' : ''; ?>><?php echo $cat['category_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-semibold shadow-none" style="background-color: #3c50e0; border-color: #3c50e0;">Lọc dữ liệu</button>
                <a href="/product/index" class="btn btn-light border w-100 fw-semibold shadow-none">Xóa bộ lọc</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom bg-white">
    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold text-dark mb-1" style="font-size: 18px;">Danh sách sản phẩm đa đơn vị tính</h4>
            <p class="text-muted small mb-0" style="font-size: 13px;">Quản lý ảnh đại diện, quy cách đóng gói, định mức tồn kho và trạng thái bán hàng</p>
        </div>
        <button class="btn btn-primary fw-semibold px-3 py-2 rounded-2 d-flex align-items-center gap-2 shadow-none" style="background-color: #3c50e0; border-color: #3c50e0; font-size: 14px;" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-lg"></i> Thêm sản phẩm mới
        </button>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary font-monospace small border-bottom" style="background-color: #f8fafc;">
                    <tr>
                        <th class="ps-4 py-3 text-secondary" style="font-size: 12px;">HÌNH ẢNH</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">MÃ GỐC</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">TÊN SẢN PHẨM CHÍNH</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">DANH MỤC</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">SỐ PHÂN LOẠI</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">KHOẢNG GIÁ BÁN</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">TỔNG TỒN QUẦY</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">TRẠNG THÁI</th>
                        <th class="text-end pe-4 py-3 text-secondary" style="font-size: 12px;">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody class="text-dark" style="font-size: 14px;">
                    <?php if (!empty($view_products)): ?>
                        <?php foreach ($view_products as $prod): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;" id="product-row-<?php echo $prod['id']; ?>">
                                <td class="ps-4">
                                    <img src="<?php echo !empty($prod['image']) ? $prod['image'] : 'https://res.cloudinary.com/dnjbvgejr/image/upload/v1779205656/09b31927-1b26-4980-9463-77b005a9cd38_e5l0iy.png'; ?>"
                                        class="rounded-2 object-cover border shadow-sm item-img-render"
                                        style="width: 45px; height: 45px; background-color: #f8fafc;">
                                </td>
                                <td class="fw-bold text-secondary font-monospace"><?php echo $prod['product_code']; ?></td>
                                <td>
                                    <div class="fw-bold text-dark mb-prod-name-el" style="font-size: 15px;"><?php echo $prod['product_name']; ?></div>
                                    <?php if (!empty($prod['short_description'])): ?>
                                        <small class="text-secondary d-flex align-items-center gap-1 mt-1 mb-prod-desc-el" style="font-size: 12px;">
                                            <i class="bi bi-info-circle small text-muted"></i>
                                            <em class="text-truncate" style="max-width: 250px;"><?php echo $prod['short_description']; ?></em>
                                        </small>
                                    <?php else: ?>
                                        <small class="text-muted d-block mt-1 mb-prod-desc-el" style="font-size: 11px;"><em>Chưa có mô tả ghi chú</em></small>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-light text-primary border border-primary-subtle px-2 py-1" style="font-size: 12px;"><?php echo $prod['category_name']; ?></span></td>
                                <td><span class="badge bg-secondary rounded-pill px-2 py-1"><?php echo $prod['total_variants']; ?> quy cách</span></td>
                                <td class="font-monospace text-success fw-bold">
                                    <?php
                                    if ($prod['min_price'] == $prod['max_price']) {
                                        echo number_format($prod['min_price'], 0, '.', ',') . 'đ';
                                    } else {
                                        echo number_format($prod['min_price'], 0, '.', ',') . 'đ - ' . number_format($prod['max_price'], 0, '.', ',') . 'đ';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($prod['alert_count'] > 0): ?>
                                        <span class="badge font-monospace rounded-1 px-2 py-1 fw-bold text-danger" style="background-color: #fde8e8; font-size: 12px;"><i class="bi bi-exclamation-circle me-1"></i>Sắp hết hàng (<?php echo $prod['total_stock']; ?>)</span>
                                    <?php else: ?>
                                        <span class="fw-bold font-monospace text-dark"><?php echo number_format($prod['total_stock']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge rounded-1 px-2 py-1 fw-semibold" style="<?php echo $prod['status'] == 1 ? 'background-color: #def7ec; color: #03543f; font-size: 11px;' : 'background-color: #f3f4f6; color: #4b5563; font-size: 11px;'; ?>">
                                        <?php echo $prod['status'] == 1 ? 'Đang bán' : 'Ngừng bán'; ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-sm btn-light border text-secondary px-2" data-bs-toggle="modal" data-bs-target="#viewVariantsModal<?php echo $prod['id']; ?>"><i class="bi bi-eye"></i></button>
                                        <button class="btn btn-sm btn-light border text-primary px-2" data-bs-toggle="modal" data-bs-target="#editProductModal<?php echo $prod['id']; ?>"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm border px-2 <?php echo $prod['status'] == 1 ? 'btn-light text-warning' : 'btn-light text-success'; ?>" data-bs-toggle="modal" data-bs-target="#toggleStatusModal<?php echo $prod['id']; ?>"><i class="bi <?php echo $prod['status'] == 1 ? 'bi-toggle-on' : 'bi-toggle-off'; ?>"></i></button>
                                        <button class="btn btn-sm btn-light border text-danger px-2" data-bs-toggle="modal" data-bs-target="#deleteProductModal<?php echo $prod['id']; ?>"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center p-5 text-secondary"><i class="bi bi-box-seam d-block mb-2" style="font-size: 40px; color: #cbd5e1;"></i>Không tìm thấy mặt hàng nào!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div id="pagination-container">
            <?php if ($page_total > 1): ?>
                <div class="card-footer bg-white border-top p-4 d-flex justify-content-between align-items-center">
                    <div class="text-secondary small">Hiển thị trang <strong class="current-page-txt"><?php echo $page_current; ?></strong> / <strong class="total-pages-txt"><?php echo $page_total; ?></strong> trang</div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0 gap-1">
                            <?php
                            $query_str = $_GET;
                            unset($query_str['page']);
                            $base_url = "/product/index?" . http_build_query($query_str) . "&page=";
                            ?>
                            <li class="page-item page-prev-item <?php echo ($page_current <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link border rounded-2 px-3 py-2 text-dark shadow-none" href="<?php echo $base_url . ($page_current - 1); ?>" data-page="<?php echo ($page_current - 1); ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                            <?php for ($i = 1; $i <= $page_total; $i++): ?>
                                <li class="page-item page-number-item <?php echo ($page_current == $i) ? 'active' : ''; ?>" data-page-num="<?php echo $i; ?>">
                                    <a class="page-link border rounded-2 px-3 py-2 shadow-none fw-semibold <?php echo ($page_current == $i) ? 'text-white bg-primary border-primary' : 'text-dark bg-white'; ?>" style="<?php echo ($page_current == $i) ? 'background-color: #3c50e0; border-color: #3c50e0;' : ''; ?>" href="<?php echo $base_url . $i; ?>" data-page="<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item page-next-item <?php echo ($page_current >= $page_total) ? 'disabled' : ''; ?>">
                                <a class="page-link border rounded-2 px-3 py-2 text-dark shadow-none" href="<?php echo $base_url . ($page_current + 1); ?>" data-page="<?php echo ($page_current + 1); ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="dynamic-modals-container">
    <?php if (!empty($view_products)): foreach ($view_products as $prod): ?>
        <div class="modal fade" id="viewVariantsModal<?php echo $prod['id']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content border-0 shadow-lg rounded-4" style="overflow: hidden;">
                    <div class="modal-header p-4 border-bottom-0 bg-white d-flex align-items-start justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <img src="<?php echo !empty($prod['image']) ? $prod['image'] : 'https://res.cloudinary.com/dnjbvgejr/image/upload/v1779205656/09b31927-1b26-4980-9463-77b005a9cd38_e5l0iy.png'; ?>"
                                class="rounded-3 border object-cover shadow-sm dynamic-modal-main-img"
                                style="width: 60px; height: 60px; background-color: #f8fafc;">
                            <div>
                                <span class="badge rounded-2 px-2 py-1 mb-1 font-monospace fw-bold" style="background-color: #e0e4fd; color: #3c50e0; font-size: 11px;">MÃ SẢN PHẨM: <?php echo $prod['product_code']; ?></span>
                                <h5 class="modal-title fw-bold text-dark dynamic-modal-title-txt" style="font-size: 20px; letter-spacing: -0.3px;"><?php echo $prod['product_name']; ?></h5>
                            </div>
                        </div>
                        <button type="button" class="btn-close shadow-none bg-light p-2 rounded-circle" data-bs-dismiss="modal" style="font-size: 12px;"></button>
                    </div>

                    <div class="modal-body p-4 pt-0">
                        <div class="p-3 mb-4 rounded-3 small d-flex align-items-start gap-2" style="background-color: #f0f4ff; border-left: 4px solid #3c50e0;">
                            <i class="bi bi-chat-left-text-fill text-primary mt-0.5" style="font-size: 14px;"></i>
                            <div>
                                <span class="d-block text-dark fw-bold mb-0.5" style="font-size: 13px;">Mô tả mặt hàng & Ghi chú quầy kho</span>
                                <span class="text-secondary dynamic-modal-desc-txt" style="font-size: 13px;">
                                    <?php echo !empty($prod['short_description']) ? $prod['short_description'] : 'Mặt hàng này chưa được thiết lập nội dung mô tả chi tiết hoặc vị trí kệ hàng.'; ?>
                                </span>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="p-3 rounded-3 border-0 text-start" style="background-color: #f0fdf4;">
                                    <span class="d-block text-secondary small fw-semibold mb-1" style="color: #15803d !important;">Tổng tồn kho quầy</span>
                                    <h3 class="fw-bold m-0 font-monospace dynamic-modal-total-stock-txt" style="color: #166534; font-size: 22px;"><?php echo number_format($prod['total_stock']); ?> <span class="small fw-normal" style="font-size: 13px;">cái</span></h3>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded-3 border-0 text-start" style="background-color: #fefff0;">
                                    <span class="d-block text-secondary small fw-semibold mb-1" style="color: #a16207 !important;">Giá bán thấp nhất</span>
                                    <h3 class="fw-bold m-0 font-monospace dynamic-modal-min-price-txt" style="color: #854d0e; font-size: 22px;"><?php echo number_format($prod['min_price'], 0, '.', ','); ?>đ</h3>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded-3 border-0 text-start" style="background-color: #fef2f2;">
                                    <span class="d-block text-secondary small fw-semibold mb-1" style="color: #b91c1c !important;">Giá bán cao nhất</span>
                                    <h3 class="fw-bold m-0 font-monospace dynamic-modal-max-price-txt" style="color: #991b1b; font-size: 22px;"><?php echo number_format($prod['max_price'], 0, '.', ','); ?>đ</h3>
                                </div>
                            </div>
                        </div>

                        <div class="border rounded-3 overflow-hidden shadow-none">
                            <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                                <thead class="table-light border-bottom" style="background-color: #f8fafc;">
                                    <tr>
                                        <th class="ps-4 py-3 text-secondary fw-semibold font-monospace" style="font-size: 12px;">QUY CÁCH ĐÓNG GÓI</th>
                                        <th class="py-3 text-secondary fw-semibold font-monospace" style="font-size: 12px;">MÃ VẠCH BARCODE</th>
                                        <th class="py-3 text-end text-secondary fw-semibold font-monospace" style="font-size: 12px;">GIÁ VỐN NHẬP</th>
                                        <th class="py-3 text-end text-secondary fw-semibold font-monospace" style="font-size: 12px;">GIÁ BÁN NIÊM YẾT</th>
                                        <th class="py-3 text-center text-secondary fw-semibold font-monospace" style="font-size: 12px;">ĐỊNH MỨC TỒN TỐI THIỂU</th>
                                        <th class="py-3 text-center pe-4 text-secondary fw-semibold font-monospace" style="font-size: 12px;">SỐ LƯỢNG TỒN THỰC TẾ</th>
                                    </tr>
                                </thead>
                                <tbody class="dynamic-modal-variants-tbody">
                                    <?php foreach ($prod['variants'] as $v): ?>
                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                            <td class="ps-4 fw-bold text-dark" style="font-size: 15px;"><?php echo $v['variant_name']; ?></td>
                                            <td class="font-monospace text-secondary" style="font-size: 13px;">
                                                <span class="bg-light px-2 py-1 rounded border text-muted small"><i class="bi bi-upc-scan me-1"></i><?php echo !empty($v['barcode']) ? $v['barcode'] : 'Trống'; ?></span>
                                            </td>
                                            <td class="text-end font-monospace text-muted"><?php echo number_format($v['cost_price'], 0, '.', ','); ?>đ</td>
                                            <td class="text-end font-monospace fw-bold" style="color: #3c50e0; font-size: 15px;"><?php echo number_format($v['sale_price'], 0, '.', ','); ?>đ</td>
                                            <td class="text-center font-monospace text-secondary fw-semibold"><?php echo $v['low_stock_threshold']; ?></td>
                                            <td class="text-center pe-4">
                                                <?php if ($v['stock_qty'] <= $v['low_stock_threshold']): ?>
                                                    <span class="badge font-monospace rounded-1 px-2 py-1 fw-bold text-danger" style="background-color: #fde8e8; font-size: 12px;"><i class="bi bi-exclamation-triangle me-1"></i>Chạm sàn: <?php echo $v['stock_qty']; ?></span>
                                                <?php else: ?>
                                                    <span class="font-monospace fw-bold text-success bg-light px-2 py-1 rounded border" style="font-size: 13px;"><?php echo $v['stock_qty']; ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer p-3 bg-light border-top-0 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-white border fw-semibold rounded-2 px-4 shadow-none small" style="font-size: 14px;" data-bs-dismiss="modal">Đóng lại</button>
                        <button type="button" class="btn btn-primary fw-semibold rounded-2 px-4 shadow-none small btn-quick-edit-trigger" style="background-color: #3c50e0; border-color: #3c50e0; font-size: 14px;" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editProductModal<?php echo $prod['id']; ?>"><i class="bi bi-pencil me-1"></i> Sửa nhanh</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editProductModal<?php echo $prod['id']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content border-0 shadow rounded-3">
                    <div class="modal-header p-4 border-bottom bg-white">
                        <h5 class="modal-title fw-bold text-dark">Chỉnh sửa quy cách sản phẩm</h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="/product/edit" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $prod['id']; ?>">
                        <input type="hidden" name="current_image" value="<?php echo $prod['image']; ?>">
                        <div class="modal-body p-4 bg-white">
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold text-secondary">Ảnh sản phẩm</label>
                                    <div class="d-flex flex-column align-items-center gap-2 border p-2 rounded-2 bg-light">
                                        <div id="edit_img_preview_box<?php echo $prod['id']; ?>">
                                            <img src="<?php echo !empty($prod['image']) ? $prod['image'] : 'https://res.cloudinary.com/dnjbvgejr/image/upload/v1779205656/09b31927-1b26-4980-9463-77b005a9cd38_e5l0iy.png'; ?>"
                                                id="edit_preview_img<?php echo $prod['id']; ?>"
                                                class="rounded border object-cover"
                                                style="width: 70px; height: 70px; background-color: #f8fafc;">
                                        </div>
                                        <input type="file" class="form-control form-control-sm d-none edit-file-input-el" id="edit_file_input<?php echo $prod['id']; ?>" data-prod-id="<?php echo $prod['id']; ?>" name="product_image" accept="image/*" onchange="previewEditImage(this, <?php echo $prod['id']; ?>)">
                                        <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small" style="font-size: 11px;" onclick="document.getElementById('edit_file_input<?php echo $prod['id']; ?>').click()">Thay ảnh</button>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="row g-2">
                                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Mã sản phẩm gốc</label><input type="text" class="form-control form-control-sm bg-light" name="product_code" value="<?php echo $prod['product_code']; ?>" readonly></div>
                                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Tên mặt hàng / Sản phẩm (Chung)</label><input type="text" class="form-control form-control-sm" name="product_name" value="<?php echo htmlspecialchars($prod['product_name']); ?>" required></div>
                                        <div class="col-12">
                                            <label class="form-label small fw-semibold text-secondary">Danh mục nhóm</label>
                                            <select class="form-select form-select-sm" name="category_id" required>
                                                <?php foreach ($view_categories as $cat): ?>
                                                    <option value="<?php echo $cat['id']; ?>" <?php echo ($prod['category_id'] == $cat['id']) ? 'selected' : ''; ?>><?php echo $cat['category_name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-2">Thiết lập đơn vị tính quy đổi</h6>
                            <div class="row g-2 font-monospace small text-secondary fw-semibold mb-1 text-center d-none d-md-flex">
                                <div class="col-md-2 text-start">Tên đơn vị</div>
                                <div class="col-md-3 text-start">Mã vạch Barcode</div>
                                <div class="col-md-2">Giá vốn (đ)</div>
                                <div class="col-md-2">Giá bán (đ)</div>
                                <div class="col-md-1">Hạn mức</div>
                                <div class="col-md-1">Tồn kho</div>
                                <div class="col-md-1">Xóa</div>
                            </div>

                            <div class="variant-container mb-3 editVariantAreaClass" id="editVariantArea<?php echo $prod['id']; ?>">
                                <?php foreach ($prod['variants'] as $v): ?>
                                    <div class="row g-2 align-items-end mb-2 variant-row text-center">
                                        <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="v_name[]" value="<?php echo htmlspecialchars($v['variant_name']); ?>" required></div>
                                        <div class="col-md-3"><input type="text" class="form-control form-control-sm" name="v_barcode[]" value="<?php echo htmlspecialchars($v['barcode'] ?? ''); ?>"></div>
                                        <div class="col-md-2"><input type="number" class="form-control form-control-sm text-end" name="v_cost[]" value="<?php echo (int)$v['cost_price']; ?>" required></div>
                                        <div class="col-md-2"><input type="number" class="form-control form-control-sm text-end" name="v_sale[]" value="<?php echo (int)$v['sale_price']; ?>" required></div>
                                        <div class="col-md-1"><input type="number" class="form-control form-control-sm text-center" name="v_limit[]" value="<?php echo isset($v['low_stock_threshold']) ? $v['low_stock_threshold'] : 10; ?>" required></div>
                                        <div class="col-md-1"><input type="number" class="form-control form-control-sm text-center" name="v_stock[]" value="<?php echo $v['stock_qty']; ?>" required></div>
                                        <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger w-100 btn-remove-row" style="padding: 4px;"><i class="bi bi-trash"></i></button></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mb-3 fw-semibold shadow-none" onclick="addEditRow(<?php echo $prod['id']; ?>)"><i class="bi bi-plus"></i> Thêm quy cách quy đổi mới</button>

                            <div class="mb-0">
                                <label class="form-label small fw-semibold text-secondary">Mô tả ngắn đặc điểm / Vị trí kệ kho</label>
                                <textarea class="form-control form-control-sm" name="short_description" rows="2" placeholder="Ví dụ: Kệ A1, hàng dễ vỡ, bảo quản nhiệt độ thường..."><?php echo htmlspecialchars($prod['short_description'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-top p-3 bg-white">
                            <button type="button" class="btn btn-light fw-semibold rounded-2 px-3" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary fw-semibold rounded-2 px-4 btn-save-edit-prod" style="background-color: #3c50e0; border-color: #3c50e0;">Lưu cập nhật</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="toggleStatusModal<?php echo $prod['id']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow rounded-3">
                    <div class="modal-body p-4 text-center">
                        <div class="mb-3 <?php echo $prod['status'] == 1 ? 'text-warning' : 'text-success'; ?>"><i class="bi bi-exclamation-circle fs-1"></i></div>
                        <h5 class="fw-bold text-dark mb-2"><?php echo $prod['status'] == 1 ? 'Tạm ngừng kinh doanh?' : 'Kinh doanh trở lại?'; ?></h5>
                        <p class="text-secondary small mb-4">Hệ thống sẽ cập nhật lại trạng thái hiển thị của mặt hàng này trên quầy thu tiền POS.</p>
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-light px-3 fw-semibold rounded-2 small" data-bs-dismiss="modal">Hủy</button>
                            <button type="button" data-url="/product/toggle?id=${prod.id}&status=${prod.status}" class="btn btn-toggle-prod-confirm px-4 fw-semibold rounded-2 text-white <?php echo $prod['status'] == 1 ? 'btn-warning bg-warning' : 'btn-success bg-success'; ?>">Xác nhận</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteProductModal<?php echo $prod['id']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow rounded-3">
                    <div class="modal-body p-4 text-center">
                        <div class="text-danger mb-3"><i class="bi bi-trash-fill fs-1"></i></div>
                        <h5 class="fw-bold text-dark mb-1">Xóa sản phẩm vĩnh viễn?</h5>
                        <p class="text-muted small mb-4">Hành động này sẽ xóa sạch thông tin mặt hàng <strong class="text-dark toggle-prod-name-txt"><?php echo $prod['product_name']; ?></strong> và các đơn vị tính con.</p>
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-light px-3 fw-semibold rounded-2 small" data-bs-dismiss="modal">Hủy</button>
                            <button type="button" data-url="/product/delete?id=${prod.id}" class="btn btn-delete-prod-confirm btn-danger bg-danger px-4 fw-semibold rounded-2 text-white">Xác nhận Xóa</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>

<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-1 shadow rounded-3">
            <div class="modal-header p-4 border-bottom bg-white">
                <h5 class="modal-title fw-bold text-dark">Thêm mặt hàng sản phẩm đa quy cách</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form action="/product/add" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Ảnh sản phẩm</label>
                            <div class="d-flex flex-column align-items-center gap-2 border p-2 rounded-2 bg-light">
                                <div id="add_img_preview_box" class="text-muted">
                                    <i class="bi bi-image fs-2" id="add_icon_place"></i>
                                    <img src="" id="add_preview_img" class="rounded object-cover d-none" style="width: 70px; height: 70px;">
                                </div>
                                <input type="file" class="form-control form-control-sm d-none" id="add_file_input" name="product_image" accept="image/*" onchange="previewAddImage(this)">
                                <button type="button" class="btn btn-xs btn-outline-primary py-1 px-2 small" style="font-size: 11px;" onclick="document.getElementById('add_file_input').click()">Chọn hình ảnh</button>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <label class="form-label small fw-semibold text-secondary">Mã hàng hóa gốc</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" id="add_product_code" name="product_code" placeholder="SP001" required>
                                        <button class="btn btn-outline-secondary" type="button" id="btnRandomProdCode"><i class="bi bi-shuffle"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-7"><label class="form-label small fw-semibold text-secondary">Tên hàng hóa (Chung)</label><input type="text" class="form-control form-control-sm" name="product_name" placeholder="Ví dụ: Kem đánh răng Colgate" required></div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-secondary">Danh mục nhóm</label>
                                    <select class="form-select form-select-sm" name="category_id" required>
                                        <option value="">-- Chọn danh mục --</option>
                                        <?php foreach ($view_categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>"><?php echo $cat['category_name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-2">Thiết lập danh sách đơn vị định lượng hàng hóa</h6>
                    <div class="row g-2 font-monospace small text-secondary fw-semibold mb-1 text-center d-none d-md-flex">
                        <div class="col-md-2 text-start">Tên đơn vị</div>
                        <div class="col-md-3 text-start">Mã vạch Barcode quét quầy</div>
                        <div class="col-md-2">Giá vốn nhập (đ)</div>
                        <div class="col-md-2">Giá bán lẻ (đ)</div>
                        <div class="col-md-1">Hạn mức</div>
                        <div class="col-md-1">Tồn</div>
                        <div class="col-md-1">Xóa</div>
                    </div>

                    <div id="addVariantArea" class="mb-3">
                        <div class="row g-2 align-items-end mb-2 variant-row text-center">
                            <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="v_name[]" placeholder="Tuýp lẻ" required></div>
                            <div class="col-md-3"><input type="text" class="form-control form-control-sm" name="v_barcode[]" placeholder="Mã vạch..."></div>
                            <div class="col-md-2"><input type="number" class="form-control form-control-sm text-end" name="v_cost[]" value="0" min="0" required></div>
                            <div class="col-md-2"><input type="number" class="form-control form-control-sm text-end" name="v_sale[]" value="0" min="0" required></div>
                            <div class="col-md-1"><input type="number" class="form-control form-control-sm text-center" name="v_limit[]" value="10" min="0" required></div>
                            <div class="col-md-1"><input type="number" class="form-control form-control-sm text-center" name="v_stock[]" value="0" min="0" required></div>
                            <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger w-100 btn-remove-row" style="padding: 4px;" disabled><i class="bi bi-trash"></i></button></div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mb-3 fw-semibold shadow-none" id="btnAddRowAddForm"><i class="bi bi-plus"></i> Thêm quy cách quy đổi mới</button>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold text-secondary">Mô tả ngắn đặc điểm / Vị trí kệ kho</label>
                        <textarea class="form-control form-control-sm" name="short_description" rows="2" placeholder="Ví dụ: Kệ A1, hàng tiêu dùng nhanh, bảo quản nhiệt độ thường..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-white">
                    <button type="button" class="btn btn-light fw-semibold rounded-2 px-3" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-semibold rounded-2 px-4 btn-save-add-prod" style="background-color: #3c50e0; border-color: #3c50e0;">Lưu thông tin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/assets/js/products/index.js"></script>

<?php require_once 'views/layout/footer.php'; ?>