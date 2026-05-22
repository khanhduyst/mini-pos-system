<?php require_once 'views/layout/header.php'; ?>

<div class="card card-custom bg-white border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-4">
        <form action="/category/index" method="GET" class="row g-3 align-items-end" novalidate>
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-secondary">Tìm kiếm danh mục</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control bg-light border-start-0 shadow-none" name="search"
                        placeholder="Nhập tên hoặc mã danh mục sản phẩm..."
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-secondary">Trạng thái hiển thị</label>
                <select class="form-select bg-light shadow-none" name="status">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1"
                        <?php echo (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : ''; ?>>Đang kinh
                        doanh</option>
                    <option value="0"
                        <?php echo (isset($_GET['status']) && $_GET['status'] == '0') ? 'selected' : ''; ?>>Tạm ẩn
                    </option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-semibold shadow-none"
                    style="background-color: #3c50e0; border-color: #3c50e0;">Lọc</button>
                <a href="/category/index" class="btn btn-light border w-100 fw-semibold shadow-none">Xóa</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom bg-white">
    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold text-dark mb-1" style="font-size: 18px;">Danh mục sản phẩm</h4>
            <p class="text-muted small mb-0" style="font-size: 13px;">Phân loại nhóm hàng hóa giúp thu ngân dễ dàng tìm
                kiếm và tối ưu hóa bộ lọc tại quầy POS</p>
        </div>
        <button class="btn btn-primary fw-semibold px-3 py-2 rounded-2 d-flex align-items-center gap-2 shadow-none"
            style="background-color: #3c50e0; border-color: #3c50e0; font-size: 14px;" data-bs-toggle="modal"
            data-bs-target="#addCategoryModal">
            <i class="bi bi-plus-lg"></i> Thêm danh mục
        </button>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary font-monospace small border-bottom"
                    style="background-color: #f8fafc;">
                    <tr>
                        <th class="ps-4 py-3 text-secondary" style="font-size: 12px;">MÃ DANH MỤC</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">TÊN DANH MỤC</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">MÔ TẢ NGẮN</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">SỐ LƯỢNG SẢN PHẨM</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">TRẠNG THÁI</th>
                        <th class="text-end pe-4 py-3 text-secondary" style="font-size: 12px;">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody class="text-dark" style="font-size: 14px;">
                    <?php if (isset($categories) && is_array($categories) && count($categories) > 0): ?>
                    <?php foreach ($categories as $cat): ?>
                    <tr style="border-bottom: 1px solid #f1f5f9;" id="category-row-<?php echo $cat['id']; ?>">
                        <td class="ps-4 fw-bold text-primary"><?php echo $cat['category_code']; ?></td>
                        <td class="fw-semibold text-dark"><?php echo $cat['category_name']; ?></td>
                        <td class="text-secondary">
                            <?php echo !empty($cat['description']) ? $cat['description'] : '<em class="text-muted small">Không có mô tả</em>'; ?>
                        </td>
                        <td class="fw-bold text-success ps-5"><?php echo number_format($cat['total_products']); ?></td>
                        <td>
                            <span class="badge rounded-1 px-2 py-1"
                                style="<?php echo $cat['status'] == 1 ? 'background-color: #def7ec; color: #03543f; font-size: 12px;' : 'background-color: #f3f4f6; color: #4b5563; font-size: 12px;'; ?>">
                                <?php echo $cat['status'] == 1 ? 'Đang kinh doanh' : 'Tạm ẩn'; ?>
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-1">
                                <button class="btn btn-sm btn-light text-primary border rounded-2 px-2 shadow-none"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editCategoryModal<?php echo $cat['id']; ?>"><i
                                        class="bi bi-pencil"></i></button>
                                <button
                                    class="btn btn-sm border rounded-2 px-2 shadow-none <?php echo $cat['status'] == 1 ? 'btn-light text-danger' : 'btn-light text-success'; ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#toggleStatusModal<?php echo $cat['id']; ?>"><i
                                        class="bi <?php echo $cat['status'] == 1 ? 'bi-eye-slash' : 'bi-eye'; ?>"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center p-5 text-secondary">
                            <i class="bi bi-tags d-block mb-2" style="font-size: 40px; color: #cbd5e1;"></i>
                            Không tìm thấy dữ liệu danh mục nào phù hợp!
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
                    Hiển thị trang <strong class="current-page-txt"><?php echo $page; ?></strong> / <strong class="total-pages-txt"><?php echo $pages; ?></strong> trang
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0 gap-1">
                        <?php
                                $query_str = $_GET;
                                unset($query_str['page']);
                                $base_url = "/category/index?" . http_build_query($query_str) . "&page=";
                                ?>
                        <li class="page-item page-prev-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link border rounded-2 px-3 py-2 text-dark shadow-none"
                                href="<?php echo $base_url . ($page - 1); ?>" data-page="<?php echo ($page - 1); ?>"><i class="bi bi-chevron-left"></i></a>
                        </li>
                        <?php for ($i = 1; $i <= $pages; $i++): ?>
                        <li class="page-item page-number-item <?php echo ($page == $i) ? 'active' : ''; ?>" data-page-num="<?php echo $i; ?>">
                            <a class="page-link border rounded-2 px-3 py-2 shadow-none fw-semibold <?php echo ($page == $i) ? 'text-white' : 'text-dark bg-white'; ?>"
                                style="<?php echo ($page == $i) ? 'background-color: #3c50e0; border-color: #3c50e0;' : ''; ?>"
                                href="<?php echo $base_url . $i; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>
                        <li class="page-item page-next-item <?php echo ($page >= $pages) ? 'disabled' : ''; ?>">
                            <a class="page-link border rounded-2 px-3 py-2 text-dark shadow-none"
                                href="<?php echo $base_url . ($page + 1); ?>" data-page="<?php echo ($page + 1); ?>"><i class="bi bi-chevron-right"></i></a>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="dynamic-modals-container">
    <?php if (isset($categories) && is_array($categories) && count($categories) > 0): ?>
    <?php foreach ($categories as $cat): ?>
    <div class="modal fade" id="editCategoryModal<?php echo $cat['id']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-3">
                <div class="modal-header p-4 border-bottom bg-white">
                    <h5 class="modal-title fw-bold text-dark">Chỉnh sửa danh mục sản phẩm</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <form action="/category/edit" method="POST" novalidate>
                    <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                    <div class="modal-body p-4 bg-white">
                        <div class="row g-3">
                            <div class="col-12"><label class="form-label small fw-semibold text-secondary">Mã danh mục</label><input type="text" class="form-control bg-light" name="category_code" value="<?php echo $cat['category_code']; ?>" readonly></div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Tên danh mục sản phẩm</label>
                                <input type="text" class="form-control" name="category_name" value="<?php echo $cat['category_name']; ?>" required>
                            </div>
                            <div class="col-12"><label class="form-label small fw-semibold text-secondary">Mô tả ngắn</label><textarea class="form-control" name="description" rows="3"><?php echo $cat['description']; ?></textarea></div>
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

    <div class="modal fade" id="toggleStatusModal<?php echo $cat['id']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-body p-4 text-center bg-white rounded-3">
                    <div class="<?php echo $cat['status'] == 1 ? 'text-danger' : 'text-success'; ?> mb-3"><i class="bi <?php echo $cat['status'] == 1 ? 'bi-eye-slash-fill' : 'bi-eye-fill'; ?> fs-1"></i></div>
                    <h5 class="fw-bold text-dark mb-2">${cat.status == 1 ? 'Tạm ẩn danh mục?' : 'Kinh doanh lại?'}</h5>
                    <p class="text-secondary small mb-4">${cat.status == 1 ? 'Hệ thống sẽ tạm ẩn nhóm <strong class="text-dark">' + cat.category_name + '</strong> khỏi bộ lọc màn hình bán hàng.' : 'Kích hoạt hiển thị lại danh mục nhóm hàng <strong class="text-dark">' + cat.category_name + '</strong>.'}</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light fw-semibold rounded-2 px-3 small" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" data-url="/category/toggle?id=<?php echo $cat['id']; ?>&status=<?php echo $cat['status']; ?>" class="btn btn-toggle-cat-confirm fw-semibold rounded-2 px-4 small text-white <?php echo $cat['status'] == 1 ? 'btn-danger' : 'btn-success'; ?>">Xác nhận</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-1 shadow rounded-3">
            <div class="modal-header p-4 border-bottom bg-white">
                <h5 class="modal-title fw-bold text-dark">Thêm danh mục sản phẩm mới</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form action="/category/add" method="POST" novalidate>
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Mã danh mục</label>
                            <div class="input-group">
                                <input type="text" class="form-control rounded-start-2 shadow-none" id="add_category_code" name="category_code" placeholder="DM01" required>
                                <button class="btn btn-outline-secondary rounded-end-2 px-3" type="button" id="btnRandomCatCode"><i class="bi bi-shuffle"></i></button>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Tên danh mục sản phẩm</label>
                            <input type="text" class="form-control" name="category_name" placeholder="Ví dụ: Nước ngọt, Bánh kẹo..." required>
                        </div>
                        <div class="col-12"><label class="form-label small fw-semibold text-secondary">Mô tả đặc điểm nhóm hàng</label><textarea class="form-control" name="description" rows="3" placeholder="Ghi chú thêm..."></textarea></div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-white">
                    <button type="button" class="btn btn-light fw-semibold rounded-2 px-3" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-semibold rounded-2 px-4 btn-submit-cat" style="background-color: #3c50e0; border-color: #3c50e0;">Lưu lại</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const btnRandom = document.getElementById('btnRandomCatCode');
    const inputCode = document.getElementById('add_category_code');
    if (btnRandom && inputCode) {
        btnRandom.addEventListener('click', function() {
            const randomNum = Math.floor(100 + Math.random() * 900);
            inputCode.value = 'DM' + randomNum;
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
                btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Đang xử lý...`;
            } else {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }
    }

    function showInputError(inputEl, errorMsg) {
        if (!inputEl) return;
        inputEl.classList.add('is-invalid');
        
        let container = inputEl.parentNode;
        if (inputEl.id === 'add_category_code') {
            container = inputEl.parentNode.parentNode;
        }

        const oldFeedback = container.querySelector('.invalid-feedback');
        if (oldFeedback) oldFeedback.remove();

        const feedbackDiv = document.createElement('div');
        feedbackDiv.className = 'invalid-feedback fw-semibold small mt-1 d-block';
        feedbackDiv.innerText = errorMsg;
        container.appendChild(feedbackDiv);

        inputEl.addEventListener('input', function() {
            inputEl.classList.remove('is-invalid');
            const feedback = container.querySelector('.invalid-feedback');
            if (feedback) feedback.remove();
        });
    }

    function validateCategoryForm(formEl) {
        let isValid = true;
        const inputs = formEl.querySelectorAll('input, textarea');

        inputs.forEach(input => {
            if (input.hasAttribute('readonly') || input.type === 'hidden' || ['description', 'search']
                .includes(input.name)) {
                return;
            }

            input.classList.remove('is-invalid');

            let container = input.parentNode;
            if (input.id === 'add_category_code') {
                container = input.parentNode.parentNode;
            }

            const oldFeedback = container.querySelector('.invalid-feedback');
            if (oldFeedback) oldFeedback.remove();

            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('is-invalid');

                const feedbackDiv = document.createElement('div');
                feedbackDiv.className = 'invalid-feedback fw-semibold small mt-1 d-block';
                feedbackDiv.innerText = "Trường thông tin này bắt buộc nhập!";
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

    function loadCategoriesData(page = 1) {
        const fForm = document.querySelector('form[action="/category/index"]');
        const formData = new FormData(fForm);
        const params = new URLSearchParams();
        
        for (const [key, value] of formData.entries()) {
            params.append(key, value);
        }
        params.append('page', page);
        params.append('ajax', '1');

        fetch('/category/index?' + params.toString())
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderTableRows(data.categories);
                    renderPagination(data.total_pages, data.current_page);
                    updateCategoryModalContainers(data.categories);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function renderTableRows(categories) {
        const tbody = document.querySelector('table tbody');
        tbody.innerHTML = '';

        if (!categories || categories.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center p-5 text-secondary">
                        <i class="bi bi-tags d-block mb-2" style="font-size: 40px; color: #cbd5e1;"></i>
                        Không tìm thấy dữ liệu danh mục nào phù hợp!
                    </td>
                </tr>`;
            return;
        }

        categories.forEach(cat => {
            const descTxt = cat.description ? cat.description : '<em class="text-muted small">Không có mô tả</em>';
            const statusStyle = cat.status == 1 ? 'background-color: #def7ec; color: #03543f; font-size: 12px;' : 'background-color: #f3f4f6; color: #4b5563; font-size: 12px;';
            const statusTxt = cat.status == 1 ? 'Đang kinh doanh' : 'Tạm ẩn';
            const toggleIcon = cat.status == 1 ? 'bi-eye-slash' : 'bi-eye';
            const toggleClass = cat.status == 1 ? 'btn-light text-danger' : 'btn-light text-success';

            const tr = document.createElement('tr');
            tr.style.borderBottom = '1px solid #f1f5f9';
            tr.id = `category-row-${cat.id}`;
            tr.innerHTML = `
                <td class="ps-4 fw-bold text-primary">${cat.category_code}</td>
                <td class="fw-semibold text-dark">${cat.category_name}</td>
                <td class="text-secondary">${descTxt}</td>
                <td class="fw-bold text-success ps-5">${parseInt(cat.total_products).toLocaleString('en-US')}</td>
                <td><span class="badge rounded-1 px-2 py-1" style="${statusStyle}">${statusTxt}</span></td>
                <td class="text-end pe-4">
                    <div class="d-flex justify-content-end gap-1">
                        <button class="btn btn-sm btn-light text-primary border rounded-2 px-2 shadow-none" data-bs-toggle="modal" data-bs-target="#editCategoryModal${cat.id}"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm border rounded-2 px-2 shadow-none ${toggleClass}" data-bs-toggle="modal" data-bs-target="#toggleStatusModal${cat.id}"><i class="bi ${toggleIcon}"></i></button>
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
            const linkStyle = currentPage == i ? 'style="background-color: #3c50e0; border-color: #3c50e0;"' : '';
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

    function updateCategoryModalContainers(categories) {
        const modalContainer = document.getElementById('dynamic-modals-container');
        modalContainer.innerHTML = '';

        categories.forEach(cat => {
            const toggleTitle = cat.status == 1 ? 'Tạm ẩn danh mục?' : 'Kinh doanh lại?';
            const toggleDesc = cat.status == 1 
                ? `Hệ thống sẽ tạm ẩn nhóm <strong class="text-dark">${cat.category_name}</strong> khỏi bộ lọc màn hình bán hàng.`
                : `Kích hoạt hiển thị lại danh mục nhóm hàng <strong class="text-dark">${cat.category_name}</strong>.`;
            const toggleBtnClass = cat.status == 1 ? 'btn-danger' : 'btn-success';

            modalContainer.innerHTML += `
                <div class="modal fade" id="editCategoryModal${cat.id}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow rounded-3">
                            <div class="modal-header p-4 border-bottom bg-white">
                                <h5 class="modal-title fw-bold text-dark">Chỉnh sửa danh mục sản phẩm</h5>
                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="/category/edit" method="POST" novalidate>
                                <input type="hidden" name="id" value="${cat.id}">
                                <div class="modal-body p-4 bg-white">
                                    <div class="row g-3">
                                        <div class="col-12"><label class="form-label small fw-semibold text-secondary">Mã danh mục</label><input type="text" class="form-control bg-light" name="category_code" value="${cat.category_code}" readonly></div>
                                        <div class="col-12">
                                            <label class="form-label small fw-semibold text-secondary">Tên danh mục sản phẩm</label>
                                            <input type="text" class="form-control" name="category_name" value="${cat.category_name}" required>
                                        </div>
                                        <div class="col-12"><label class="form-label small fw-semibold text-secondary">Mô tả ngắn</label><textarea class="form-control" name="description" rows="3">${cat.description || ''}</textarea></div>
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

                <div class="modal fade" id="toggleStatusModal${cat.id}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content border-0 shadow-lg rounded-3">
                            <div class="modal-body p-4 text-center bg-white rounded-3">
                                <div class="${cat.status == 1 ? 'text-danger' : 'text-success'} mb-3"><i class="bi ${cat.status == 1 ? 'bi-eye-slash-fill' : 'bi-eye-fill'} fs-1"></i></div>
                                <h5 class="fw-bold text-dark mb-2">${toggleTitle}</h5>
                                <p class="text-secondary small mb-4">${toggleDesc}</p>
                                <div class="d-flex gap-2 justify-content-center">
                                    <button type="button" class="btn btn-light fw-semibold rounded-2 px-3 small" data-bs-dismiss="modal">Hủy</button>
                                    <button type="button" data-url="/category/toggle?id=${cat.id}&status=${cat.status}" class="btn btn-toggle-cat-confirm fw-semibold rounded-2 px-4 small text-white ${toggleBtnClass}">Xác nhận</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
        });
    }

    const mainFilterForm = document.querySelector('form[action="/category/index"]');
    if (mainFilterForm) {
        mainFilterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            loadCategoriesData(1);
        });
        
        const btnReset = mainFilterForm.querySelector('a[href="/category/index"]');
        if (btnReset) {
            btnReset.addEventListener('click', function(e) {
                e.preventDefault();
                mainFilterForm.reset();
                mainFilterForm.querySelectorAll('input').forEach(i => i.value = '');
                mainFilterForm.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
                loadCategoriesData(1);
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
            loadCategoriesData(targetPage);
        }
    });

    const addForm = document.querySelector('#addCategoryModal form');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!validateCategoryForm(addForm)) {
                return;
            }

            const btnSubmit = addForm.querySelector('.btn-submit-cat');
            setBtnLoading(btnSubmit, true, 'Lưu lại');

            const formData = new FormData(addForm);
            fetch('/category/add', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                setBtnLoading(btnSubmit, false, 'Lưu lại');
                
                const nameInput = addForm.querySelector('input[name="category_name"]');
                if (nameInput) nameInput.classList.remove('is-invalid');

                if (data.success) {
                    const modalEl = document.getElementById('addCategoryModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();
                    addForm.reset();
                    
                    loadCategoriesData(1);
                    showToast(data.message, 'success');
                } else {
                    if (data.error_type === 'category_name') {
                        showInputError(nameInput, data.message);
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
        const form = e.target.closest('form[action="/category/edit"]');
        if (form) {
            e.preventDefault();
            if (!validateCategoryForm(form)) {
                return;
            }

            const btnSubmit = form.querySelector('button[type="submit"]');
            setBtnLoading(btnSubmit, true, 'Cập nhật');

            const formData = new FormData(form);
            fetch('/category/edit', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                setBtnLoading(btnSubmit, false, 'Cập nhật');
                
                const nameInput = form.querySelector('input[name="category_name"]');
                if (nameInput) nameInput.classList.remove('is-invalid');

                if (data.success) {
                    const modalEl = form.closest('.modal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();
                    
                    const activePageItem = document.querySelector('.page-number-item.active');
                    const currentPage = activePageItem ? parseInt(activePageItem.getAttribute('data-page-num')) : 1;
                    loadCategoriesData(currentPage);
                    showToast(data.message, 'success');
                } else {
                    if (data.error_type === 'category_name') {
                        showInputError(nameInput, data.message);
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

    document.getElementById('dynamic-modals-container').addEventListener('click', function(e) {
        const confirmBtn = e.target.closest('.btn-toggle-cat-confirm');
        if (confirmBtn) {
            e.preventDefault();
            const targetUrl = confirmBtn.getAttribute('data-url');
            
            const originalTxt = confirmBtn.innerHTML;
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;

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
                    const currentPage = activePageItem ? parseInt(activePageItem.getAttribute('data-page-num')) : 1;
                    loadCategoriesData(currentPage);
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