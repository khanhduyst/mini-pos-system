<?php 
require_once 'views/layout/header.php'; 
$logs = $logs ?? [];
?>

<div class="card card-custom bg-white border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-4">
        <form action="/inventory/logs" method="GET" id="filterStockLogsForm" class="row g-3 align-items-end" novalidate>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-secondary">Tìm kiếm hàng hóa</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control bg-light border-start-0 shadow-none" name="search" id="logSearchInput" placeholder="Nhập tên, mã gốc hoặc mã vạch...">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-secondary">Hành động biến động</label>
                <select class="form-select bg-light shadow-none" name="action_type" id="logActionSelect">
                    <option value="">Tất cả hành động</option>
                    <option value="ADJUST">Cân bằng kho</option>
                    <option value="SALE">Bán quầy</option>
                    <option value="EXPORT">Xuất kho</option>
                    <option value="IMPORT">Nhập kho</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold text-secondary">Từ ngày</label>
                <input type="date" class="form-control bg-light shadow-none" name="start_date" id="logStartDate">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold text-secondary">Đến ngày</label>
                <input type="date" class="form-control bg-light shadow-none" name="end_date" id="logEndDate">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-semibold shadow-none" style="background-color: #3c50e0; border-color: #3c50e0;">Lọc</button>
                <button type="button" id="btnResetLogFilter" class="btn btn-light border w-100 fw-semibold shadow-none">Xóa</button>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom bg-white mb-4">
    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold text-dark mb-1" style="font-size: 18px;">Nhật ký biến động kho (Thẻ kho gốc)</h4>
            <p class="text-muted small mb-0" style="font-size: 13px;">Theo dõi chi tiết lịch sử mọi hành động gây thay đổi tăng/giảm số lượng hàng hóa</p>
        </div>
        <a href="/inventory/index" class="btn btn-light border fw-semibold px-3 py-2 rounded-2 shadow-none small" style="font-size: 14px;"><i class="bi bi-arrow-left me-1"></i> Quay về trang kiểm kho</a>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary font-monospace small border-bottom" style="background-color: #f8fafc;">
                    <tr>
                        <th class="ps-4 py-3 text-secondary" style="font-size: 12px;">THỜI GIAN</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">SẢN PHẨM QUY CÁCH</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">HÀNH ĐỘNG</th>
                        <th class="py-3 text-secondary" style="font-size: 12px;">MÃ CHỨNG TỪ REFS</th>
                        <th class="py-3 text-center text-secondary" style="font-size: 12px;">TỒN CŨ</th>
                        <th class="py-3 text-center text-secondary" style="font-size: 12px;">BIẾN ĐỘNG</th>
                        <th class="py-3 text-center text-secondary" style="font-size: 12px;">TỒN MỚI</th>
                        <th class="pe-4 py-3 text-end text-secondary" style="font-size: 12px;">NGƯỜI THỰC HIỆN</th>
                    </tr>
                </thead>
                <tbody class="text-dark" style="font-size: 14px;">
                    <?php if (!empty($logs)): ?>
                        <?php foreach ($logs as $l): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td class="ps-4 font-monospace text-muted small">
                                    <?php echo date('d-m-Y H:i:s', strtotime($l['created_at'])); ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo $l['product_name']; ?></div>
                                    <small class="text-secondary font-monospace"><?php echo $l['variant_name']; ?></small>
                                </td>
                                <td>
                                    <?php if ($l['action_type'] === 'ADJUST'): ?>
                                        <span class="badge bg-warning-subtle text-warning fw-semibold px-2 py-1" style="font-size: 11px;">Cân bằng kho</span>
                                    <?php elseif ($l['action_type'] === 'SALE'): ?>
                                        <span class="badge bg-danger-subtle text-danger fw-semibold px-2 py-1" style="font-size: 11px;">Bán quầy</span>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success fw-semibold px-2 py-1" style="font-size: 11px;"><?php echo $l['action_type']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="font-monospace fw-bold text-secondary small"><?php echo !empty($l['reference_code']) ? $l['reference_code'] : '---'; ?></td>
                                <td class="text-center font-monospace text-secondary"><?php echo $l['old_qty']; ?></td>
                                <td class="text-center font-monospace fw-bold">
                                    <?php if ($l['change_qty'] > 0): ?>
                                        <span class="text-success">++<?php echo $l['change_qty']; ?></span>
                                    <?php elseif ($l['change_qty'] < 0): ?>
                                        <span class="text-danger"><?php echo $l['change_qty']; ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center font-monospace fw-bold text-primary bg-light-subtle"><?php echo $l['new_qty']; ?></td>
                                <td class="pe-4 text-end text-dark font-monospace small fw-semibold"><?php echo $l['fullname']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center p-5 text-secondary"><i class="bi bi-clock-history d-block mb-2" style="font-size: 40px; color: #cbd5e1;"></i>Thẻ kho trống rỗng, chưa phát sinh bất kỳ hoạt động biến động số lượng nào!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const filterForm = document.getElementById('filterStockLogsForm');
    
    function formatDateTime(dateString) {
        if (!dateString) return '---';
        const dateObj = new Date(dateString);
        if (isNaN(dateObj.getTime())) return dateString;
        
        const day = ('0' + dateObj.getDate()).slice(-2);
        const month = ('0' + (dateObj.getMonth() + 1)).slice(-2);
        const year = dateObj.getFullYear();
        const hours = ('0' + dateObj.getHours()).slice(-2);
        const minutes = ('0' + dateObj.getMinutes()).slice(-2);
        const seconds = ('0' + dateObj.getSeconds()).slice(-2);
        
        return `${day}-${month}-${year} ${hours}:${minutes}:${seconds}`;
    }

    function loadStockLogs() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams();
        
        for (const [key, value] of formData.entries()) {
            params.append(key, value);
        }
        params.append('ajax', '1');

        fetch('/inventory/logs?' + params.toString())
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderLogTableRows(data.logs);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function renderLogTableRows(logs) {
        const tbody = document.querySelector('table tbody');
        tbody.innerHTML = '';

        if (!logs || logs.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center p-5 text-secondary"><i class="bi bi-clock-history d-block mb-2" style="font-size: 40px; color: #cbd5e1;"></i>Không tìm thấy dữ liệu biến động kho phù hợp!</td></tr>`;
            return;
        }

        logs.forEach(l => {
            let actionBadge = '';
            if (l.action_type === 'ADJUST') {
                actionBadge = `<span class="badge bg-warning-subtle text-warning fw-semibold px-2 py-1" style="font-size: 11px;">Cân bằng kho</span>`;
            } else if (l.action_type === 'SALE') {
                actionBadge = `<span class="badge bg-danger-subtle text-danger fw-semibold px-2 py-1" style="font-size: 11px;">Bán quầy</span>`;
            } else {
                actionBadge = `<span class="badge bg-success-subtle text-success fw-semibold px-2 py-1" style="font-size: 11px;">${l.action_type}</span>`;
            }

            const refCode = l.reference_code ? l.reference_code : '---';
            
            let changeText = '';
            if (parseInt(l.change_qty) > 0) {
                changeText = `<span class="text-success">++${l.change_qty}</span>`;
            } else if (parseInt(l.change_qty) < 0) {
                changeText = `<span class="text-danger">${l.change_qty}</span>`;
            } else {
                changeText = `<span class="text-muted">0</span>`;
            }

            const tr = document.createElement('tr');
            tr.style.borderBottom = '1px solid #f1f5f9';
            tr.innerHTML = `
                <td class="ps-4 font-monospace text-muted small">${formatDateTime(l.created_at)}</td>
                <td>
                    <div class="fw-bold text-dark">${l.product_name}</div>
                    <small class="text-secondary font-monospace">${l.variant_name}</small>
                </td>
                <td>${actionBadge}</td>
                <td class="font-monospace fw-bold text-secondary small">${refCode}</td>
                <td class="text-center font-monospace text-secondary">${l.old_qty}</td>
                <td class="text-center font-monospace fw-bold">${changeText}</td>
                <td class="text-center font-monospace fw-bold text-primary bg-light-subtle">${l.new_qty}</td>
                <td class="pe-4 text-end text-dark font-monospace small fw-semibold">${l.fullname}</td>`;
            tbody.appendChild(tr);
        });
    }

    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            loadStockLogs();
        });
    }

    const btnReset = document.getElementById('btnResetLogFilter');
    if (btnReset) {
        btnReset.addEventListener('click', function(e) {
            e.preventDefault();
            filterForm.reset();
            filterForm.querySelectorAll('input').forEach(i => i.value = '');
            filterForm.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
            loadStockLogs();
        });
    }
});
</script>

<?php require_once 'views/layout/footer.php'; ?>