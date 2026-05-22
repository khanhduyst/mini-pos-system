<?php require_once 'views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1" style="font-size: 20px;">Bảng phân tích hoạt động kinh doanh</h4>
        <p class="text-muted small mb-0">Thống kê doanh số bán quầy, theo dõi công nợ khách hàng và cảnh báo biến động kho quầy POS</p>
    </div>
    <div style="width: 220px;">
        <select class="form-select bg-white border shadow-sm fw-semibold text-dark shadow-none" id="dashboardPeriodSelect" style="font-size: 14px;">
            <option value="7days" selected>7 ngày gần đây</option>
            <option value="today">Hôm nay</option>
            <option value="this_month">Trong tháng này</option>
            <option value="last_month">Tháng trước đã qua</option>
        </select>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white p-3 rounded-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="d-block text-muted small fw-semibold mb-1 txt-card-period-title">ĐƠN HÀNG (7 NGÀY)</span>
                    <h3 class="fw-bold text-dark m-0 font-monospace" id="card_today_orders">0</h3>
                </div>
                <div class="rounded-circle p-3 bg-primary-subtle text-primary"><i class="bi bi-cart-check fs-4"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white p-3 rounded-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="d-block text-muted small fw-semibold mb-1 txt-card-period-title">DOANH THU (7 NGÀY)</span>
                    <h3 class="fw-bold text-success m-0 font-monospace" id="card_today_revenue">0đ</h3>
                </div>
                <div class="rounded-circle p-3 bg-success-subtle text-success"><i class="bi bi-currency-dollar fs-4"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white p-3 rounded-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="d-block text-muted small fw-semibold mb-1">SẢN PHẨM SẮP HẾT HÀNG</span>
                    <h3 class="fw-bold text-danger m-0 font-monospace" id="card_alert_products">0</h3>
                </div>
                <div class="rounded-circle p-3 bg-danger-subtle text-danger"><i class="bi bi-exclamation-triangle fs-4"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white p-3 rounded-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="d-block text-muted small fw-semibold mb-1">KHÁCH HÀNG NỢ LỆCH</span>
                    <h3 class="fw-bold text-warning m-0 font-monospace" id="card_debt_customers">0</h3>
                </div>
                <div class="rounded-circle p-3 bg-warning-subtle text-warning"><i class="bi bi-person-badge fs-4"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
            <h5 class="fw-bold text-dark mb-1" style="font-size: 16px;"><i class="bi bi-graph-up-arrow text-primary me-1"></i> Biểu đồ diễn biến doanh thu chi tiết</h5>
            <div class="d-flex align-items-end gap-2 pt-4 border-bottom font-monospace small text-secondary justify-content-start" id="chartBarsContainer" style="height: 240px; overflow-x: auto; padding-bottom: 8px;">
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
            <div class="card-header bg-white border-bottom p-3 fw-bold text-dark" style="font-size: 15px;">
                <i class="bi bi-receipt text-primary me-1"></i> 5 giao dịch bán lẻ gần đây nhất
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tblRecentOrders">
                        <thead class="table-light text-secondary font-monospace small border-bottom">
                            <tr>
                                <th class="ps-3 py-2">MÃ ĐƠN</th>
                                <th class="py-2">KHÁCH HÀNG</th>
                                <th class="text-end pe-3 py-2">TỔNG TIỀN</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 13.5px;"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
            <div class="card-header bg-white border-bottom p-3 fw-bold text-dark" style="font-size: 15px;">
                <i class="bi bi-person-exclamation text-danger me-1"></i> Top khách hàng nợ quỹ nhiều nhất
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tblTopDebts">
                        <thead class="table-light text-secondary font-monospace small border-bottom">
                            <tr>
                                <th class="ps-3 py-2">MÃ KH</th>
                                <th class="py-2">HỌ VÀ TÊN</th>
                                <th class="py-2">SỐ ĐIỆN THOẠI</th>
                                <th class="text-end pe-3 py-2">DƯ NỢ HIỆN TẠI</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 13.5px;"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
            <div class="card-header bg-white border-bottom p-3 fw-bold text-dark" style="font-size: 15px;">
                <i class="bi bi-trophy text-warning me-1"></i> Top 5 sản phẩm bán chạy nhất
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="tblTopProducts">
                        <thead class="table-light text-secondary font-monospace small border-bottom">
                            <tr>
                                <th class="ps-3 py-2">TÊN SẢN PHẨM QUY CÁCH</th>
                                <th class="text-center py-2">SL BÁN</th>
                                <th class="text-end pe-3 py-2">TỔNG DOANH THU</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 13.5px;"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
            <div class="card-header bg-white border-bottom p-3 fw-bold text-dark" style="font-size: 15px;">
                <i class="bi bi-shield-exclamation text-danger me-1"></i> Cảnh báo hết hàng hạn mức quầy
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="tblStockAlerts">
                        <thead class="table-light text-secondary font-monospace small border-bottom">
                            <tr>
                                <th class="ps-3 py-2">SẢN PHẨM QUY CÁCH</th>
                                <th class="text-center py-2">HẠN MỨC</th>
                                <th class="text-center pe-3 py-2">TỒN THỰC TẾ</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 13.5px;"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const periodSelect = document.getElementById('dashboardPeriodSelect');

    function updateCardTitles(period) {
        let label = '(7 NGÀY)';
        if(period === 'today') label = '(HÔM NAY)';
        if(period === 'this_month') label = '(THÁNG NÀY)';
        if(period === 'last_month') label = '(THÁNG TRƯỚC)';
        
        document.querySelectorAll('.txt-card-period-title').forEach(el => {
            if(el.innerText.includes('ĐƠN HÀNG')) el.innerText = 'ĐƠN HÀNG ' + label;
            if(el.innerText.includes('DOANH THU')) el.innerText = 'DOANH THU ' + label;
        });
    }

    function loadDashboardData() {
        const period = periodSelect.value;
        updateCardTitles(period);

        fetch(`/dashboard/index?ajax=1&filter_type=${period}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('card_today_orders').innerText = parseInt(data.cards.today_orders).toLocaleString('en-US');
                    document.getElementById('card_today_revenue').innerText = parseFloat(data.cards.today_revenue).toLocaleString('en-US') + 'đ';
                    document.getElementById('card_alert_products').innerText = parseInt(data.cards.alert_products).toLocaleString('en-US');
                    document.getElementById('card_debt_customers').innerText = parseInt(data.cards.debt_customers).toLocaleString('en-US');

                    renderChart(data.chart);
                    renderRecentOrders(data.recent_orders);
                    renderTopDebts(data.top_debts);
                    renderTopProducts(data.top_products);
                    renderStockAlerts(data.stock_alerts);
                }
            })
            .catch(error => console.error('Error Fetch:', error));
    }

    function renderChart(chartData) {
        const container = document.getElementById('chartBarsContainer');
        container.innerHTML = '';
        
        if(!chartData || chartData.length === 0) {
            container.innerHTML = `<div class="w-100 text-center text-muted p-5"><i class="bi bi-bar-chart d-block mb-2 fs-3 text-secondary" style="opacity:0.5;"></i>Giai đoạn này chưa phát sinh dữ liệu hóa đơn nào!</div>`;
            return;
        }

        const maxRevenue = Math.max(...chartData.map(d => parseFloat(d.daily_revenue)), 1);

        chartData.forEach(d => {
            const rev = parseFloat(d.daily_revenue);
            const heightPercent = rev > 0 ? (rev / maxRevenue) * 130 : 8; 
            
            const barWrapper = document.createElement('div');
            barWrapper.className = 'd-flex flex-column align-items-center h-100 justify-content-end';
            barWrapper.style.cssText = 'flex: 1; max-width: 50px; min-width: 45px;';
            
            barWrapper.innerHTML = `
                <div class="fw-bold text-primary mb-1 text-nowrap font-monospace" style="font-size: 10px; color: #3c50e0 !important;">
                    ${rev > 0 ? (rev >= 1000000 ? (rev/1000000).toFixed(1) + 'M' : (rev/1000).toFixed(0) + 'k') : ''}
                </div>
                <div class="w-70 rounded-top shadow-sm" style="height: ${heightPercent}px; background: linear-gradient(180deg, #3c50e0 0%, #6875ed 100%); transition: height 0.6s ease;"></div>
                <div class="text-secondary mt-2 fw-semibold text-nowrap font-monospace" style="font-size: 11px; border-top: 2px solid #e2e8f0; width: 100%; text-align: center; padding-top: 4px;">
                    ${d.date_label}
                </div>`;
            container.appendChild(barWrapper);
        });
    }

    function renderRecentOrders(orders) {
        const tbody = document.querySelector('#tblRecentOrders tbody');
        tbody.innerHTML = '';
        if(!orders || orders.length === 0) {
            tbody.innerHTML = `<tr><td colspan="3" class="text-center p-4 text-muted">Hệ thống chưa ghi nhận đơn hàng nào!</td></tr>`;
            return;
        }
        orders.forEach(o => {
            const tr = document.createElement('tr');
            tr.style.borderBottom = '1px solid #f1f5f9';
            tr.innerHTML = `
                <td class="ps-3 font-monospace fw-bold text-secondary">${o.order_code}</td>
                <td><span class="fw-bold text-dark">${o.customer_name}</span><small class="text-muted d-block font-monospace" style="font-size:11px;">Bán bởi: ${o.username}</small></td>
                <td class="text-end pe-3 font-monospace fw-bold text-primary">${parseFloat(o.total_amount).toLocaleString('en-US')}đ</td>`;
            tbody.appendChild(tr);
        });
    }

    function renderTopDebts(debts) {
        const tbody = document.querySelector('#tblTopDebts tbody');
        tbody.innerHTML = '';
        if(!debts || debts.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center p-4 text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Tuyệt vời! Không có khách hàng nào nợ quỹ.</td></tr>`;
            return;
        }
        debts.forEach(d => {
            const tr = document.createElement('tr');
            tr.style.borderBottom = '1px solid #f1f5f9';
            tr.innerHTML = `
                <td class="ps-3 font-monospace text-muted" style="font-size:12px;">${d.customer_code}</td>
                <td class="fw-bold text-dark">${d.full_name}</td>
                <td class="font-monospace text-secondary">${d.phone}</td>
                <td class="text-end pe-3 font-monospace fw-bold text-danger">${parseFloat(d.debt).toLocaleString('en-US')}đ</td>`;
            tbody.appendChild(tr);
        });
    }

    function renderTopProducts(products) {
        const tbody = document.querySelector('#tblTopProducts tbody');
        tbody.innerHTML = '';
        if(!products || products.length === 0) {
            tbody.innerHTML = `<tr><td colspan="3" class="text-center p-4 text-muted">Chưa ghi nhận số liệu bán hàng!</td></tr>`;
            return;
        }
        products.forEach(p => {
            const tr = document.createElement('tr');
            tr.style.borderBottom = '1px solid #f1f5f9';
            tr.innerHTML = `
                <td class="ps-3 py-2.5"><strong>${p.product_name}</strong> <small class="text-secondary d-block">${p.variant_name}</small></td>
                <td class="text-center font-monospace fw-bold text-dark">${p.total_sold}</td>
                <td class="text-end pe-3 font-monospace fw-bold text-success">${parseFloat(p.total_revenue).toLocaleString('en-US')}đ</td>`;
            tbody.appendChild(tr);
        });
    }

    function renderStockAlerts(alerts) {
        const tbody = document.querySelector('#tblStockAlerts tbody');
        tbody.innerHTML = '';
        if(!alerts || alerts.length === 0) {
            tbody.innerHTML = `<tr><td colspan="3" class="text-center p-4 text-success fw-semibold"><i class="bi bi-check-circle me-1"></i>An toàn! Mọi quy cách đều đạt trên mức tối thiểu.</td></tr>`;
            return;
        }
        alerts.forEach(a => {
            const tr = document.createElement('tr');
            tr.style.borderBottom = '1px solid #f1f5f9';
            tr.innerHTML = `
                <td class="ps-3 py-2.5"><strong>${a.product_name}</strong> <small class="text-secondary d-block font-monospace">${a.variant_name}</small></td>
                <td class="text-center font-monospace text-secondary">${a.low_stock_threshold}</td>
                <td class="text-center pe-3"><span class="badge font-monospace bg-danger-subtle text-danger fw-bold rounded-1 px-2 py-1">${a.stock_qty}</span></td>`;
            tbody.appendChild(tr);
        });
    }

    periodSelect.addEventListener('change', loadDashboardData);
    loadDashboardData();
});
</script>

<?php require_once 'views/layout/footer.php'; ?>