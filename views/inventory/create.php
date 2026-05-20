<?php
require_once 'views/layout/header.php';
$variants = $variants ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1" style="font-size: 22px; letter-spacing: -0.5px;">Lập phiếu kiểm kê hàng hóa mới</h4>
        <p class="text-muted small mb-0" style="font-size: 14px;">Tìm kiếm sản phẩm phân tầng hoặc quét mã vạch để đối soát chênh lệch</p>
    </div>
    <a href="/inventory/index" class="btn btn-light border fw-semibold px-3 py-2 rounded-2 shadow-none small" style="font-size: 14px;"><i class="bi bi-arrow-left me-1"></i> Quay lại danh sách</a>
</div>

<form action="/inventory/add" method="POST">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                <div class="position-relative">
                    <label class="form-label small fw-bold text-dark mb-2">1. Gõ tìm kiếm hoặc quét Barcode</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control bg-light border-start-0 shadow-none" id="txtSearchVariant" placeholder="Nhập tên sản phẩm, mã gốc..." autocomplete="off">
                    </div>
                    <div class="position-absolute w-100 bg-white border rounded-3 shadow-lg mt-1 d-none" id="searchResultDropdown" style="z-index: 1050; max-height: 400px; overflow-y: auto;">
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label small fw-bold text-dark mb-2">2. Ghi chú phiếu kiểm (Nếu có)</label>
                    <textarea class="form-control bg-light shadow-none small" name="note" rows="3" placeholder="Nhập lý do chênh lệch thừa/thiếu, ghi chú nội bộ khi đi đếm kho..." style="font-size:13px;"></textarea>
                </div>

                <div class="p-3 mt-4 rounded-3 small text-secondary bg-light-subtle border">
                    <h6 class="fw-bold text-dark mb-2" style="font-size: 13px;"><i class="bi bi-info-circle text-primary me-1"></i> Hướng dẫn thao tác nhanh:</h6>
                    <ul class="ps-3 mb-0 gap-1 d-flex flex-column" style="font-size: 12px;">
                        <li>Gõ tên mặt hàng chung hoặc mã vạch.</li>
                        <li>Bấm vào quy cách con thích hợp từ danh sách xổ ra để chèn dòng vào phiếu kiểm bên cạnh.</li>
                        <li>Nhập số lượng thực tế đếm được tại quầy vào ô trống.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white border-bottom p-3 fw-bold text-dark" style="font-size: 15px;">
                    <i class="bi bi-list-task text-primary me-1"></i> Danh sách mặt hàng nằm trong phiếu kiểm kê
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="min-height: 250px;">
                        <table class="table align-middle table-hover mb-0" id="tblCheckItems">
                            <thead class="table-light text-center small fw-bold font-monospace border-bottom">
                                <tr>
                                    <th class="text-start ps-4 py-3">Mặt hàng quy cách</th>
                                    <th>Mã vạch</th>
                                    <th style="width: 120px;">Tồn máy</th>
                                    <th style="width: 180px;">Thực tế đếm</th>
                                    <th style="width: 70px;">Xóa</th>
                                </tr>
                            </thead>
                            <tbody id="checkItemsTableBody">
                                <tr id="trEmptyMessage">
                                    <td colspan="5" class="text-center p-5 text-muted">
                                        <i class="bi bi-upc-scan d-block mb-2 fs-2 text-secondary" style="opacity: 0.5;"></i> Phiếu kiểm chưa có sản phẩm. Hãy tìm và chọn sản phẩm ở khung bên trái để thêm vào danh sách!
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top p-3 text-end">
                    <a href="/inventory/index" class="btn btn-light border fw-semibold rounded-2 px-4 shadow-none me-2">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary fw-semibold rounded-2 px-5 shadow-none" style="background-color: #3c50e0; border-color: #3c50e0;">Lưu phiếu kiểm kho</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    const allVariants = <?php echo json_encode($variants); ?>;
    const txtSearch = document.getElementById('txtSearchVariant');
    const dropdown = document.getElementById('searchResultDropdown');
    const tableBody = document.getElementById('checkItemsTableBody');
    const emptyMessage = document.getElementById('trEmptyMessage');

    txtSearch.addEventListener('input', function() {
        const value = this.value.trim().toLowerCase();
        if (value.length < 1) {
            dropdown.classList.add('d-none');
            return;
        }

        const filtered = allVariants.filter(v =>
            v.product_name.toLowerCase().includes(value) ||
            v.product_code.toLowerCase().includes(value) ||
            (v.barcode && v.barcode.toLowerCase().includes(value)) ||
            v.variant_name.toLowerCase().includes(value)
        );

        if (filtered.length === 0) {
            dropdown.innerHTML = '<div class="p-3 text-center text-muted small">Không tìm thấy sản phẩm này trên kệ!</div>';
        } else {
            const groupedProducts = {};
            filtered.forEach(v => {
                if (!groupedProducts[v.product_code]) {
                    groupedProducts[v.product_code] = {
                        product_name: v.product_name,
                        product_code: v.product_code,
                        variants: []
                    };
                }
                groupedProducts[v.product_code].variants.push(v);
            });

            let html = '<div class="font-monospace small bg-white">';
            for (const code in groupedProducts) {
                const p = groupedProducts[code];
                html += `
                <div class="p-2 bg-light border-bottom fw-bold text-primary d-flex justify-content-between align-items-center" style="font-size: 12px; background-color:#f1f5f9 !important;">
                    <span><i class="bi bi-box-seam me-1"></i> ${p.product_name}</span>
                    <span class="badge bg-secondary font-monospace">${p.product_code}</span>
                </div>
                <div class="list-group list-group-flush">
            `;

                p.variants.forEach(v => {
                    html += `
                    <button type="button" class="list-group-item list-group-item-action py-2 px-4 text-start border-bottom d-flex justify-content-between align-items-center bg-white" onclick="addVariantToTable(${v.variant_id})">
                        <div class="ps-1">
                            <span class="text-dark fw-semibold d-inline-block">— ${v.variant_name}</span>
                            <span class="text-muted small ms-2">(Barcode: ${v.barcode ? v.barcode : 'Trống'})</span>
                        </div>
                        <span class="text-muted font-monospace">Tồn: <strong class="text-dark">${v.stock_qty}</strong></span>
                    </button>
                `;
                });
                html += `</div>`;
            }
            html += '</div>';
            dropdown.innerHTML = html;
        }
        dropdown.classList.remove('d-none');
    });

    document.addEventListener('click', function(e) {
        if (!txtSearch.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('d-none');
        }
    });

    function addVariantToTable(variantId) {
        dropdown.classList.add('d-none');
        txtSearch.value = '';

        if (document.getElementById('tr_item_' + variantId)) {
            document.getElementById('input_act_' + variantId).focus();
            return;
        }

        const item = allVariants.find(v => v.variant_id == variantId);
        if (!item) return;

        if (emptyMessage && tableBody.contains(emptyMessage)) {
            emptyMessage.remove();
        }

        const rowHtml = `
        <tr id="tr_item_${item.variant_id}" style="border-bottom: 1px solid #f1f5f9;">
            <td class="ps-4">
                <input type="hidden" name="variant_id[]" value="${item.variant_id}">
                <span class="fw-bold text-dark" style="font-size:14px;">${item.product_name}</span> 
                <small class="text-secondary d-block font-monospace" style="font-size:12px;">${item.variant_name}</small>
            </td>
            <td class="text-center font-monospace text-muted small">${item.barcode ? item.barcode : '---'}</td>
            <td class="text-center font-monospace bg-light fw-bold text-secondary">${item.stock_qty}
                <input type="hidden" name="system_qty[]" value="${item.stock_qty}">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm text-center shadow-none border-primary fw-bold" id="input_act_${item.variant_id}" name="actual_qty[]" min="0" required placeholder="Gõ SL đếm thực tế...">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm text-danger border-0 shadow-none" onclick="removeRow(${item.variant_id})"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
    `;
        tableBody.insertAdjacentHTML('beforeend', rowHtml);
        document.getElementById('input_act_' + item.variant_id).focus();
    }

    function removeRow(variantId) {
        document.getElementById('tr_item_' + variantId).remove();
        if (tableBody.querySelectorAll('tr').length === 0) {
            tableBody.appendChild(emptyMessage);
        }
    }
</script>

<?php require_once 'views/layout/footer.php'; ?>