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

<script src="/assets/js/dashboard/index.js"></script>

<?php require_once 'views/layout/footer.php'; ?>