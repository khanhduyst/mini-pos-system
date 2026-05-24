   function previewAddImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('add_preview_img').src = e.target.result;
                document.getElementById('add_preview_img').classList.remove('d-none');
                document.getElementById('add_icon_place').classList.add('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewEditImage(input, id) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('edit_preview_img' + id).src = e.target.result;
                document.getElementById('edit_preview_img' + id).classList.remove('d-none');
                var icon = document.getElementById('edit_icon_place' + id);
                if (icon) icon.classList.add('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function addEditRow(id) {
        const area = document.getElementById('editVariantArea' + id);
        if(area) {
            const html = `
                <div class="row g-2 align-items-end mb-2 variant-row text-center">
                    <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="v_name[]" placeholder="Đơn vị..." required></div>
                    <div class="col-md-3"><input type="text" class="form-control form-control-sm" name="v_barcode[]" placeholder="Mã vạch..."></div>
                    <div class="col-md-2"><input type="number" class="form-control form-control-sm text-end" name="v_cost[]" value="0" min="0" required></div>
                    <div class="col-md-2"><input type="number" class="form-control form-control-sm text-end" name="v_sale[]" value="0" min="0" required></div>
                    <div class="col-md-1"><input type="number" class="form-control form-control-sm text-center" name="v_limit[]" value="10" min="0" required></div>
                    <div class="col-md-1"><input type="number" class="form-control form-control-sm text-center" name="v_stock[]" value="0" min="0" required></div>
                    <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger w-100 btn-remove-row" style="padding: 4px;"><i class="bi bi-trash"></i></button></div>
                </div>`;
            area.insertAdjacentHTML('beforeend', html);
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const btnRandom = document.getElementById('btnRandomProdCode');
        const inputCode = document.getElementById('add_product_code');
        if (btnRandom && inputCode) {
            btnRandom.addEventListener('click', function() {
                inputCode.value = 'SP' + Math.floor(1000 + Math.random() * 9000);
                inputCode.classList.remove('is-invalid');
                const feedback = inputCode.parentNode.parentNode.querySelector('.invalid-feedback');
                if (feedback) feedback.remove();
            });
        }

        function getRowHtml() {
            return `
                <div class="row g-2 align-items-end mb-2 variant-row text-center">
                    <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="v_name[]" placeholder="Đơn vị..." required></div>
                    <div class="col-md-3"><input type="text" class="form-control form-control-sm" name="v_barcode[]" placeholder="Mã vạch..."></div>
                    <div class="col-md-2"><input type="number" class="form-control form-control-sm text-end" name="v_cost[]" value="0" min="0" required></div>
                    <div class="col-md-2"><input type="number" class="form-control form-control-sm text-end" name="v_sale[]" value="0" min="0" required></div>
                    <div class="col-md-1"><input type="number" class="form-control form-control-sm text-center" name="v_limit[]" value="10" min="0" required></div>
                    <div class="col-md-1"><input type="number" class="form-control form-control-sm text-center" name="v_stock[]" value="0" min="0" required></div>
                    <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger w-100 btn-remove-row" style="padding: 4px;"><i class="bi bi-trash"></i></button></div>
                </div>`;
        }

        const btnAddRowAddForm = document.getElementById('btnAddRowAddForm');
        const addVariantArea = document.getElementById('addVariantArea');
        if (btnAddRowAddForm && addVariantArea) {
            btnAddRowAddForm.addEventListener('click', function() {
                addVariantArea.insertAdjacentHTML('beforeend', getRowHtml());
            });
        }

        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-remove-row')) {
                const btn = e.target.closest('.btn-remove-row');
                const row = btn.closest('.variant-row');
                const container = row.parentNode;
                if (container.id === 'addVariantArea' && container.querySelectorAll('.variant-row').length <= 1) {
                    return;
                }
                row.remove();
            }
        });

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
            if (inputEl.id === 'add_product_code') {
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

        function validateProductForm(formEl) {
            let isValid = true;
            const inputs = formEl.querySelectorAll('input, select, textarea');

            inputs.forEach(input => {
                if (input.hasAttribute('readonly') || input.type === 'hidden' || 
                    ['short_description', 'search', 'category_id', 'v_barcode[]'].includes(input.name)) {
                    return;
                }

                input.classList.remove('is-invalid');

                let container = input.parentNode;
                if (input.id === 'add_product_code') {
                    container = input.parentNode.parentNode;
                }

                const oldFeedback = container.querySelector('.invalid-feedback');
                if (oldFeedback) oldFeedback.remove();

                if (input.hasAttribute('required') && !input.value.trim()) {
                    isValid = false;
                    input.classList.add('is-invalid');

                    const feedbackDiv = document.createElement('div');
                    feedbackDiv.className = 'invalid-feedback fw-semibold small mt-1 d-block';
                    feedbackDiv.innerText = "Bắt buộc nhập!";
                    container.appendChild(feedbackDiv);
                }
            });

            return isValid;
        }

        function loadProductsData(page = 1) {
            const filterForm = document.querySelector('form[action="/product/index"]');
            const formData = new FormData(filterForm);
            const params = new URLSearchParams();
            
            for (const [key, value] of formData.entries()) {
                params.append(key, value);
            }
            params.append('page', page);
            params.append('ajax', '1');

            fetch('/product/index?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderTableRows(data.products);
                        renderPagination(data.total_pages, data.current_page);
                        updateProductModalContainers(data.products);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function renderTableRows(products) {
            const tbody = document.querySelector('table tbody');
            tbody.innerHTML = '';

            if (!products || products.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center p-5 text-secondary"><i class="bi bi-box-seam d-block mb-2" style="font-size: 40px; color: #cbd5e1;"></i>Không tìm thấy mặt hàng nào!</td>
                    </tr>`;
                return;
            }

            products.forEach(prod => {
                const imgUrl = prod.image ? prod.image : 'https://res.cloudinary.com/dnjbvgejr/image/upload/v1779205656/09b31927-1b26-4980-9463-77b005a9cd38_e5l0iy.png';
                const descText = prod.short_description ? `<small class="text-secondary d-flex align-items-center gap-1 mt-1"><i class="bi bi-info-circle small text-muted"></i><em class="text-truncate" style="max-width: 250px;">${prod.short_description}</em></small>` : `<small class="text-muted d-block mt-1" style="font-size: 11px;"><em>Chưa có mô tả ghi chú</em></small>`;
                
                let priceText = '';
                if (parseFloat(prod.min_price) == parseFloat(prod.max_price)) {
                    priceText = parseFloat(prod.min_price).toLocaleString('en-US') + 'đ';
                } else {
                    priceText = parseFloat(prod.min_price).toLocaleString('en-US') + 'đ - ' + parseFloat(prod.max_price).toLocaleString('en-US') + 'đ';
                }

                let stockTd = '';
                if (parseInt(prod.alert_count) > 0) {
                    stockTd = `<span class="badge font-monospace rounded-1 px-2 py-1 fw-bold text-danger" style="background-color: #fde8e8; font-size: 12px;"><i class="bi bi-exclamation-circle me-1"></i>Sắp hết hàng (${prod.total_stock})</span>`;
                } else {
                    stockTd = `<span class="fw-bold font-monospace text-dark">${parseInt(prod.total_stock).toLocaleString('en-US')}</span>`;
                }

                const statusStyle = prod.status == 1 ? 'background-color: #def7ec; color: #03543f; font-size: 11px;' : 'background-color: #f3f4f6; color: #4b5563; font-size: 11px;';
                const statusTxt = prod.status == 1 ? 'Đang bán' : 'Ngừng bán';
                const toggleIcon = prod.status == 1 ? 'bi-toggle-on' : 'bi-toggle-off';

                const tr = document.createElement('tr');
                tr.style.borderBottom = '1px solid #f1f5f9';
                tr.id = `product-row-${prod.id}`;
                tr.innerHTML = `
                    <td class="ps-4"><img src="${imgUrl}" class="rounded-2 object-cover border shadow-sm" style="width: 45px; height: 45px; background-color: #f8fafc;"></td>
                    <td class="fw-bold text-secondary font-monospace">${prod.product_code}</td>
                    <td>
                        <div class="fw-bold text-dark" style="font-size: 15px;">${prod.product_name}</div>
                        ${descText}
                    </td>
                    <td><span class="badge bg-light text-primary border border-primary-subtle px-2 py-1" style="font-size: 12px;">${prod.category_name}</span></td>
                    <td><span class="badge bg-secondary rounded-pill px-2 py-1">${prod.total_variants} quy cách</span></td>
                    <td class="font-monospace text-success fw-bold">${priceText}</td>
                    <td>${stockTd}</td>
                    <td><span class="badge rounded-1 px-2 py-1 fw-semibold" style="${statusStyle}">${statusTxt}</span></td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-1">
                            <button class="btn btn-sm btn-light border text-secondary px-2" data-bs-toggle="modal" data-bs-target="#viewVariantsModal${prod.id}"><i class="bi bi-eye"></i></button>
                            <button class="btn btn-sm btn-light border text-primary px-2" data-bs-toggle="modal" data-bs-target="#editProductModal${prod.id}"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm border px-2 ${prod.status == 1 ? 'btn-light text-warning' : 'btn-light text-success'}" data-bs-toggle="modal" data-bs-target="#toggleStatusModal${prod.id}"><i class="bi ${toggleIcon}"></i></button>
                            <button class="btn btn-sm btn-light border text-danger px-2" data-bs-toggle="modal" data-bs-target="#deleteProductModal${prod.id}"><i class="bi bi-trash"></i></button>
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
                const textClass = currentPage == i ? 'text-white bg-primary border-primary' : 'text-dark bg-white';
                const styleAttr = currentPage == i ? 'style="background-color: #3c50e0; border-color: #3c50e0;"' : '';
                numItems += `
                    <li class="page-item page-number-item ${activeClass}" data-page-num="${i}">
                        <a class="page-link border rounded-2 px-3 py-2 shadow-none fw-semibold ${textClass}" ${styleAttr} href="#" data-page="${i}">${i}</a>
                    </li>`;
            }

            const prevDisabled = currentPage <= 1 ? 'disabled' : '';
            const nextDisabled = currentPage >= totalPages ? 'disabled' : '';

            container.innerHTML = `
                <div class="card-footer bg-white border-top p-4 d-flex justify-content-between align-items-center">
                    <div class="text-secondary small">Hiển thị trang <strong class="current-page-txt">${currentPage}</strong> / <strong class="total-pages-txt">${totalPages}</strong> trang</div>
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

        function updateProductModalContainers(products) {
            const modalContainer = document.getElementById('dynamic-modals-container');
            modalContainer.innerHTML = '';

            // Đọc danh mục có sẵn ở form add để đồng bộ qua form edit động
            const categorySelect = document.querySelector('#addProductModal select[name="category_id"]');
            let categoryOptionsHtml = '';
            if (categorySelect) {
                const options = categorySelect.querySelectorAll('option');
                options.forEach(opt => {
                    if (opt.value) {
                        categoryOptionsHtml += `<option value="${opt.value}">${opt.text}</option>`;
                    }
                });
            }

            products.forEach(prod => {
                const mainImg = prod.image ? prod.image : 'https://res.cloudinary.com/dnjbvgejr/image/upload/v1779205656/09b31927-1b26-4980-9463-77b005a9cd38_e5l0iy.png';
                const descText = prod.short_description ? prod.short_description : 'Mặt hàng này chưa được thiết lập nội dung mô tả chi tiết hoặc vị trí kệ hàng.';
                
                let variantTrs = '';
                let editRowsHtml = '';

                if (prod.variants && prod.variants.length > 0) {
                    prod.variants.forEach(v => {
                        const barcodeVal = v.barcode ? v.barcode : 'Trống';
                        let stockBadge = '';
                        if (parseInt(v.stock_qty) <= parseInt(v.low_stock_threshold)) {
                            stockBadge = `<span class="badge font-monospace rounded-1 px-2 py-1 fw-bold text-danger" style="background-color: #fde8e8; font-size: 12px;"><i class="bi bi-exclamation-triangle me-1"></i>Chạm sàn: ${v.stock_qty}</span>`;
                        } else {
                            stockBadge = `<span class="font-monospace fw-bold text-success bg-light px-2 py-1 rounded border" style="font-size: 13px;">${v.stock_qty}</span>`;
                        }

                        variantTrs += `
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td class="ps-4 fw-bold text-dark" style="font-size: 15px;">${v.variant_name}</td>
                                <td class="font-monospace text-secondary" style="font-size: 13px;">
                                    <span class="bg-light px-2 py-1 rounded border text-muted small"><i class="bi bi-upc-scan me-1"></i>${barcodeVal}</span>
                                </td>
                                <td class="text-end font-monospace text-muted">${parseFloat(v.cost_price).toLocaleString('en-US')}đ</td>
                                <td class="text-end font-monospace fw-bold" style="color: #3c50e0; font-size: 15px;">${parseFloat(v.sale_price).toLocaleString('en-US')}đ</td>
                                <td class="text-center font-monospace text-secondary fw-semibold">${v.low_stock_threshold}</td>
                                <td class="text-center pe-4">${stockBadge}</td>
                            </tr>`;

                        editRowsHtml += `
                            <div class="row g-2 align-items-end mb-2 variant-row text-center">
                                <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="v_name[]" value="${v.variant_name}" required></div>
                                <div class="col-md-3"><input type="text" class="form-control form-control-sm" name="v_barcode[]" value="${v.barcode || ''}"></div>
                                <div class="col-md-2"><input type="number" class="form-control form-control-sm text-end" name="v_cost[]" value="${parseInt(v.cost_price)}" required></div>
                                <div class="col-md-2"><input type="number" class="form-control form-control-sm text-end" name="v_sale[]" value="${parseInt(v.sale_price)}" required></div>
                                <div class="col-md-1"><input type="number" class="form-control form-control-sm text-center" name="v_limit[]" value="${v.low_stock_threshold || 10}" required></div>
                                <div class="col-md-1"><input type="number" class="form-control form-control-sm text-center" name="v_stock[]" value="${v.stock_qty}" required></div>
                                <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger w-100 btn-remove-row" style="padding: 4px;"><i class="bi bi-trash"></i></button></div>
                            </div>`;
                    });
                }

                modalContainer.innerHTML += `
                    <div class="modal fade" id="viewVariantsModal${prod.id}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                            <div class="modal-content border-0 shadow-lg rounded-4" style="overflow: hidden;">
                                <div class="modal-header p-4 border-bottom-0 bg-white d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="${mainImg}" class="rounded-3 border object-cover shadow-sm animate-preview-box" style="width: 60px; height: 60px; background-color: #f8fafc;">
                                        <div>
                                            <span class="badge rounded-2 px-2 py-1 mb-1 font-monospace fw-bold" style="background-color: #e0e4fd; color: #3c50e0; font-size: 11px;">MÃ SẢN PHẨM: ${prod.product_code}</span>
                                            <h5 class="modal-title fw-bold text-dark" style="font-size: 20px; letter-spacing: -0.3px;">${prod.product_name}</h5>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close shadow-none bg-light p-2 rounded-circle" data-bs-dismiss="modal" style="font-size: 12px;"></button>
                                </div>
                                <div class="modal-body p-4 pt-0">
                                    <div class="p-3 mb-4 rounded-3 small d-flex align-items-start gap-2" style="background-color: #f0f4ff; border-left: 4px solid #3c50e0;">
                                        <i class="bi bi-chat-left-text-fill text-primary mt-0.5" style="font-size: 14px;"></i>
                                        <div>
                                            <span class="d-block text-dark fw-bold mb-0.5" style="font-size: 13px;">Mô tả mặt hàng & Ghi chú quầy kho</span>
                                            <span class="text-secondary" style="font-size: 13px;">${descText}</span>
                                        </div>
                                    </div>
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-4">
                                            <div class="p-3 rounded-3 border-0 text-start" style="background-color: #f0fdf4;">
                                                <span class="d-block text-secondary small fw-semibold mb-1" style="color: #15803d !important;">Tổng tồn kho quầy</span>
                                                <h3 class="fw-bold m-0 font-monospace" style="color: #166534; font-size: 22px;">${parseFloat(prod.total_stock).toLocaleString('en-US')} <span class="small fw-normal" style="font-size: 13px;">cái</span></h3>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-3 rounded-3 border-0 text-start" style="background-color: #fefff0;">
                                                <span class="d-block text-secondary small fw-semibold mb-1" style="color: #a16207 !important;">Giá bán thấp nhất</span>
                                                <h3 class="fw-bold m-0 font-monospace" style="color: #854d0e; font-size: 22px;">${parseFloat(prod.min_price).toLocaleString('en-US')}đ</h3>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-3 rounded-3 border-0 text-start" style="background-color: #fef2f2;">
                                                <span class="d-block text-secondary small fw-semibold mb-1" style="color: #b91c1c !important;">Giá bán cao nhất</span>
                                                <h3 class="fw-bold m-0 font-monospace" style="color: #991b1b; font-size: 22px;">${parseFloat(prod.max_price).toLocaleString('en-US')}đ</h3>
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
                                            <tbody>${variantTrs}</tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer p-3 bg-light border-top-0 d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-white border fw-semibold rounded-2 px-4 shadow-none small" style="font-size: 14px;" data-bs-dismiss="modal">Đóng lại</button>
                                    <button type="button" class="btn btn-primary fw-semibold rounded-2 px-4 shadow-none small shadow-none" style="background-color: #3c50e0; border-color: #3c50e0; font-size: 14px;" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editProductModal${prod.id}"><i class="bi bi-pencil me-1"></i> Sửa nhanh</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="editProductModal${prod.id}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                            <div class="modal-content border-0 shadow rounded-3">
                                <div class="modal-header p-4 border-bottom bg-white">
                                    <h5 class="modal-title fw-bold text-dark">Chỉnh sửa quy cách sản phẩm</h5>
                                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="/product/edit" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="${prod.id}">
                                    <input type="hidden" name="current_image" value="${prod.image || ''}">
                                    <div class="modal-body p-4 bg-white">
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-3">
                                                <label class="form-label small fw-semibold text-secondary">Ảnh sản phẩm</label>
                                                <div class="d-flex flex-column align-items-center gap-2 border p-2 rounded-2 bg-light">
                                                    <div id="edit_img_preview_box${prod.id}">
                                                        <img src="${mainImg}" id="edit_preview_img${prod.id}" class="rounded border object-cover" style="width: 70px; height: 70px; background-color: #f8fafc;">
                                                    </div>
                                                    <input type="file" class="form-control form-control-sm d-none edit-file-input-el" id="edit_file_input${prod.id}" data-prod-id="${prod.id}" name="product_image" accept="image/*" onchange="previewEditImage(this, ${prod.id})">
                                                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small" style="font-size: 11px;" onclick="document.getElementById('edit_file_input${prod.id}').click()">Thay ảnh</button>
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="row g-2">
                                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Mã sản phẩm gốc</label><input type="text" class="form-control form-control-sm bg-light" name="product_code" value="${prod.product_code}" readonly></div>
                                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Tên mặt hàng / Sản phẩm (Chung)</label><input type="text" class="form-control form-control-sm" name="product_name" value="${prod.product_name}" required></div>
                                                    <div class="col-12">
                                                        <label class="form-label small fw-semibold text-secondary">Danh mục nhóm</label>
                                                        <select class="form-select form-select-sm" name="category_id" required>${categoryOptionsHtml}</select>
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
                                        <div class="variant-container mb-3 editVariantAreaClass" id="editVariantArea${prod.id}">${editRowsHtml}</div>
                                        <button type="button" class="btn btn-sm btn-outline-primary mb-3 fw-semibold shadow-none" onclick="addEditRow(${prod.id})"><i class="bi bi-plus"></i> Thêm quy cách quy đổi mới</button>
                                        <div class="mb-0">
                                            <label class="form-label small fw-semibold text-secondary">Mô tả ngắn đặc điểm / Vị trí kệ kho</label>
                                            <textarea class="form-control form-control-sm" name="short_description" rows="2" placeholder="Ví dụ: Kệ A1, hàng dễ vỡ, bảo quản nhiệt độ thường...">${prod.short_description || ''}</textarea>
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

                    <div class="modal fade" id="toggleStatusModal${prod.id}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-sm">
                            <div class="modal-content border-0 shadow rounded-3">
                                <div class="modal-body p-4 text-center">
                                    <div class="mb-3 ${prod.status == 1 ? 'text-warning' : 'text-success'}"><i class="bi bi-exclamation-circle fs-1"></i></div>
                                    <h5 class="fw-bold text-dark mb-2">${prod.status == 1 ? 'Tạm ngừng kinh doanh?' : 'Kinh doanh trở lại?'}</h5>
                                    <p class="text-secondary small mb-4">Hệ thống sẽ cập nhật lại trạng thái hiển thị của mặt hàng này trên quầy thu tiền POS.</p>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button type="button" class="btn btn-light px-3 fw-semibold rounded-2 small" data-bs-dismiss="modal">Hủy</button>
                                        <button type="button" data-url="/product/toggle?id=${prod.id}&status=${prod.status}" class="btn btn-toggle-prod-confirm px-4 fw-semibold rounded-2 text-white ${prod.status == 1 ? 'btn-warning bg-warning' : 'btn-success bg-success'}">Xác nhận</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="deleteProductModal${prod.id}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-sm">
                            <div class="modal-content border-0 shadow rounded-3">
                                <div class="modal-body p-4 text-center">
                                    <div class="text-danger mb-3"><i class="bi bi-trash-fill fs-1"></i></div>
                                    <h5 class="fw-bold text-dark mb-1">Xóa sản phẩm vĩnh viễn?</h5>
                                    <p class="text-muted small mb-4">Hành động này sẽ xóa sạch thông tin mặt hàng <strong class="text-dark">${prod.product_name}</strong> và các đơn vị tính con.</p>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button type="button" class="btn btn-light px-3 fw-semibold rounded-2 small" data-bs-dismiss="modal">Hủy</button>
                                        <button type="button" data-url="/product/delete?id=${prod.id}" class="btn btn-delete-prod-confirm btn-danger bg-danger px-4 fw-semibold rounded-2 text-white">Xác nhận Xóa</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;

                // Thiết lập giá trị select danh mục cho modal vừa tạo
                setTimeout(() => {
                    const createdModal = document.getElementById(`editProductModal${prod.id}`);
                    if (createdModal) {
                        const sel = createdModal.querySelector('select[name="category_id"]');
                        if (sel) sel.value = prod.category_id;
                    }
                }, 20);
            });
        }

        const filterForm = document.querySelector('form[action="/product/index"]');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                loadProductsData(1);
            });
            
            const btnReset = filterForm.querySelector('a[href="/product/index"]');
            if (btnReset) {
                btnReset.addEventListener('click', function(e) {
                    e.preventDefault();
                    filterForm.reset();
                    filterForm.querySelectorAll('input').forEach(i => i.value = '');
                    filterForm.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
                    loadProductsData(1);
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
                loadProductsData(targetPage);
            }
        });

        const addForm = document.querySelector('#addProductModal form');
        if (addForm) {
            addForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (!validateProductForm(addForm)) {
                    return;
                }

                const btnSubmit = addForm.querySelector('.btn-save-add-prod');
                setBtnLoading(btnSubmit, true, 'Lưu thông tin');

                const formData = new FormData(addForm);
                fetch('/product/add', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    setBtnLoading(btnSubmit, false, 'Lưu thông tin');
                    
                    const codeInput = addForm.querySelector('input[name="product_code"]');
                    if (codeInput) codeInput.classList.remove('is-invalid');

                    if (data.success) {
                        const modalEl = document.getElementById('addProductModal');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();
                        
                        addForm.reset();
                        document.getElementById('add_preview_img').classList.add('d-none');
                        document.getElementById('add_icon_place').classList.remove('d-none');
                        
                        loadProductsData(1);
                        showToast(data.message, 'success');
                    } else {
                        if (data.error_type === 'duplicate') {
                            showInputError(codeInput, data.message);
                        } else {
                            showToast(data.message, 'error');
                        }
                    }
                })
                .catch(error => {
                    setBtnLoading(btnSubmit, false, 'Lưu thông tin');
                    console.error('Error:', error);
                    showToast('Có lỗi hệ thống xảy ra!', 'error');
                });
            });
        }

        document.addEventListener('submit', function(e) {
            const form = e.target.closest('form[action="/product/edit"]');
            if (form) {
                e.preventDefault();
                if (!validateProductForm(form)) {
                    return;
                }

                const btnSubmit = form.querySelector('.btn-save-edit-prod');
                setBtnLoading(btnSubmit, true, 'Lưu cập nhật');

                const formData = new FormData(form);
                fetch('/product/edit', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    setBtnLoading(btnSubmit, false, 'Lưu cập nhật');
                    
                    if (data.success) {
                        const modalEl = form.closest('.modal');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();
                        
                        const activePageItem = document.querySelector('.page-number-item.active');
                        const currentPage = activePageItem ? parseInt(activePageItem.getAttribute('data-page-num')) : 1;
                        loadProductsData(currentPage);
                        showToast(data.message, 'success');
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    setBtnLoading(btnSubmit, false, 'Lưu cập nhật');
                    console.error('Error:', error);
                    showToast('Có lỗi hệ thống xảy ra!', 'error');
                });
            }
        });

        document.getElementById('dynamic-modals-container').addEventListener('click', function(e) {
            const confirmToggleBtn = e.target.closest('.btn-toggle-prod-confirm');
            if (confirmToggleBtn) {
                e.preventDefault();
                const targetUrl = confirmToggleBtn.getAttribute('data-url');
                
                const originalTxt = confirmToggleBtn.innerHTML;
                confirmToggleBtn.disabled = true;
                confirmToggleBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;

                fetch(targetUrl)
                .then(response => response.json())
                .then(data => {
                    confirmToggleBtn.disabled = false;
                    confirmToggleBtn.innerHTML = originalTxt;
                    if (data.success) {
                        const modalEl = confirmToggleBtn.closest('.modal');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();
                        
                        const activePageItem = document.querySelector('.page-number-item.active');
                        const currentPage = activePageItem ? parseInt(activePageItem.getAttribute('data-page-num')) : 1;
                        loadProductsData(currentPage);
                        showToast(data.message, 'success');
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    confirmToggleBtn.disabled = false;
                    confirmToggleBtn.innerHTML = originalTxt;
                    console.error('Error:', error);
                    showToast('Có lỗi hệ thống xảy ra!', 'error');
                });
            }

            const confirmDeleteBtn = e.target.closest('.btn-delete-prod-confirm');
            if (confirmDeleteBtn) {
                e.preventDefault();
                const targetUrl = confirmDeleteBtn.getAttribute('data-url');
                
                const originalTxt = confirmDeleteBtn.innerHTML;
                confirmDeleteBtn.disabled = true;
                confirmDeleteBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;

                fetch(targetUrl)
                .then(response => response.json())
                .then(data => {
                    confirmDeleteBtn.disabled = false;
                    confirmDeleteBtn.innerHTML = originalTxt;
                    if (data.success) {
                        const modalEl = confirmDeleteBtn.closest('.modal');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();
                        
                        loadProductsData(1);
                        showToast(data.message, 'success');
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    confirmDeleteBtn.disabled = false;
                    confirmDeleteBtn.innerHTML = originalTxt;
                    console.error('Error:', error);
                    showToast('Có lỗi hệ thống xảy ra!', 'error');
                });
            }
        });
    });