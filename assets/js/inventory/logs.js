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