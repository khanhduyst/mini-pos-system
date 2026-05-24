<?php
require_once 'views/layout/header.php';
?>

<div class="card card-custom bg-white mb-4">
    <div class="card-header bg-white border-bottom p-4">
        <div>
            <h4 class="fw-bold text-dark mb-1" style="font-size: 18px;">Quầy bán hàng & Xuất hóa đơn POS</h4>
            <p class="text-muted small mb-0" style="font-size: 13px;">Gõ tìm kiếm tên hàng hoặc quét barcode để thêm
                nhanh mặt hàng vào phiếu xuất quầy</p>
        </div>
    </div>

    <div class="card-body p-4 bg-light">
        <div class="row g-4">
            <div class="col-lg-7 col-xl-8">
                <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4" style="min-height: 65vh;">
                    <div class="position-relative mb-4">
                        <label class="form-label small fw-bold text-secondary" style="font-size: 13px;">Tìm kiếm sản
                            phẩm xuất kho</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i
                                    class="bi bi-search text-muted"></i></span>
                            <input type="text" id="search-product"
                                class="form-control rounded-end-2 border-start-0 shadow-none"
                                placeholder="Nhập tên hàng hóa, quy cách hoặc quét mã barcode vạch..."
                                oninput="searchLiveProduct()">
                        </div>

                        <div id="search-results-dropdown"
                            class="position-absolute w-100 bg-white border shadow rounded-3 mt-1 d-none"
                            style="z-index: 9999; max-height: 280px; overflow-y: auto;">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 small" style="font-size: 13px;">
                                    <tbody id="search-results-body"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3" style="font-size: 15px;"><i
                            class="bi bi-receipt text-primary me-2"></i>Chi tiết danh sách hàng hóa xuất kho</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-secondary font-monospace small" style="font-size: 11px;">
                                <tr>
                                    <th class="ps-3 py-3">TÊN HÀNG HÓA QUY CÁCH</th>
                                    <th class="py-3 text-center">ĐƠN GIÁ</th>
                                    <th class="py-3 text-center" style="width: 140px;">SỐ LƯỢNG</th>
                                    <th class="py-3 text-center">THÀNH TIỀN</th>
                                    <th class="text-end pe-3 py-3">HÀNH ĐỘNG</th>
                                </tr>
                            </thead>
                            <tbody id="cart-table-body" style="font-size: 14px;">
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-secondary" id="cart-empty-row">
                                        <i class="bi bi-basket display-6 d-block mb-2 text-muted"></i>
                                        Phiếu xuất đang trống. Vui lòng tìm kiếm sản phẩm phía trên!
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-xl-4">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4 d-flex flex-column"
                    style="min-height: 65vh;">
                    <h5 class="fw-bold text-dark mb-4" style="font-size: 15px;"><i
                            class="bi bi-person-lines-fill text-warning me-2"></i>Thông tin hóa đơn & Khách nợ</h5>

                    <div class="mb-3 position-relative">
                        <label class="form-label small fw-bold text-secondary" style="font-size: 13px;">Khách hàng nhận
                            hàng</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i
                                    class="bi bi-person text-muted"></i></span>
                            <input type="text" id="search-customer"
                                class="form-control border-start-0 border-end-0 shadow-none fw-bold"
                                placeholder="Nhập tên khách hàng hoặc số điện thoại..." oninput="searchLiveCustomer()">
                            <input type="hidden" id="selected_customer_id" value="0">
                            <input type="hidden" id="selected_customer_debt" value="0">
                            <button class="btn btn-outline-primary shadow-none border d-flex align-items-center px-3"
                                type="button" onclick="openQuickCustomerModal()">
                                <i class="bi bi-plus-lg fs-5"></i>
                            </button>
                        </div>

                        <div id="customer-results-dropdown"
                            class="position-absolute w-100 bg-white border shadow rounded-3 mt-1 d-none"
                            style="z-index: 9999; max-height: 220px; overflow-y: auto;">
                            <div class="list-group list-group-flush small" id="customer-results-list"
                                style="font-size: 13px;"></div>
                        </div>
                    </div>

                    <div class="mb-4" id="block-walkin-active" style="display: none;">
                        <div class="p-3 rounded-3 text-success d-flex align-items-center justify-content-between small fw-semibold"
                            style="background-color: #def7ec; border-left: 4px solid #0e9f6e;">
                            <div><i class="bi bi-person-check-fill me-2"></i> Chế độ: Khách lẻ vãng lai</div>
                        </div>
                    </div>

                    <div class="mb-4" id="block-customer-badge" style="display: none;">
                        <div class="p-3 rounded-3 border bg-light position-relative">
                            <button type="button" class="btn-close position-absolute top-0 end-0 m-2 small shadow-none"
                                style="font-size: 10px;" onclick="resetToWalkInCustomer()"></button>
                            <div class="fw-bold text-dark fs-6 mb-1" id="badge-cust-name">---</div>
                            <div class="text-secondary small mb-2"><i class="bi bi-telephone-fill me-1"></i> SĐT: <span
                                    id="badge-cust-phone">---</span></div>
                            <div class="d-flex justify-content-between text-danger small fw-semibold">
                                <span>Công nợ cũ hiện tại:</span>
                                <span id="badge-cust-debt">0đ</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4" id="block-pay-method" style="display: none;">
                        <label class="form-label small fw-bold text-secondary" style="font-size: 13px;">Hình thức thanh
                            toán mặc định</label>
                        <div class="d-flex gap-2">
                            <input type="radio" class="btn-check" name="pay_method" id="pay_cash" value="cash" checked
                                onchange="togglePayInputs()">
                            <label class="btn btn-outline-secondary w-100 small py-2 fw-semibold shadow-none"
                                for="pay_cash" style="font-size: 13px;">
                                <i class="bi bi-cash-coin me-1"></i> Khách thanh toán
                            </label>

                            <input type="radio" class="btn-check" name="pay_method" id="pay_debt" value="debt"
                                onchange="togglePayInputs()">
                            <label class="btn btn-outline-danger w-100 small py-2 fw-semibold shadow-none"
                                for="pay_debt" style="font-size: 13px;">
                                <i class="bi bi-journal-bookmark me-1"></i> Khách xin nợ
                            </label>
                        </div>
                    </div>

                    <div class="mb-4" id="block-customer-paid" style="display: none;">
                        <label class="form-label small fw-bold text-secondary" style="font-size: 13px;">Số tiền khách
                            đưa (đ)</label>
                        <input type="number" id="customer_paid"
                            class="form-control font-monospace fw-bold shadow-none text-primary" value="0"
                            oninput="calculateDebtLogic()" style="font-size: 16px; background-color: #f0f7ff;">
                    </div>

                    <div class="rounded-3 p-3 mb-4 mt-2" style="background-color: #f8fafc;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary small" style="font-size: 13px;">Tổng tiền hóa đơn đơn
                                này:</span>
                            <span class="fw-bold font-monospace text-dark" id="txt-total"
                                style="font-size: 15px;">0đ</span>
                        </div>
                        <hr class="my-2 border-secondary-subtle">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-dark small fw-bold" style="font-size: 13px;">Tổng cộng đơn hàng:</span>
                            <span class="fw-bold font-monospace text-dark fs-5" id="txt-final-total">0đ</span>
                        </div>

                        <div class="d-flex justify-content-between my-2 pt-2 border-top border-dashed" id="row-new-debt"
                            style="display: none !important;">
                            <span class="small fw-bold" style="font-size: 13px;" id="lbl-debt-status">Tiền thừa thối
                                khách:</span>
                            <span class="fw-bold font-monospace" id="txt-new-debt" style="font-size: 15px;">0đ</span>
                        </div>
                    </div>

                    <button class="btn btn-primary w-100 fw-bold py-3 text-white rounded-3 shadow-none border-0 mt-auto"
                        id="btn-submit-pos" onclick="submitOrder()" disabled
                        style="background-color: #3c50e0; font-size: 14px;">
                        <i class="bi bi-check-circle me-1"></i>XÁC NHẬN XUẤT HÀNG & IN ĐƠN
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="quickCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header p-4 border-bottom-0">
                <h5 class="modal-title fw-bold text-dark" style="font-size: 18px;"><i
                        class="bi bi-person-plus text-primary me-2"></i>Thêm nhanh khách hàng mới</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <form id="quickCustomerForm">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Họ và tên khách hàng</label>
                        <input type="text" id="quick_full_name" class="form-control shadow-none" required
                            placeholder="Nhập tên khách hàng...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Số điện thoại</label>
                        <input type="text" id="quick_phone" class="form-control shadow-none" required
                            placeholder="Nhập số điện thoại...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Địa chỉ (Không bắt buộc)</label>
                        <input type="text" id="quick_address" class="form-control shadow-none"
                            placeholder="Nhập địa chỉ của khách...">
                    </div>
                </form>
            </div>
            <div class="modal-footer p-3 bg-light border-top-0">
                <button type="button" class="btn btn-white border fw-semibold rounded-2 px-3 shadow-none small"
                    data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="button" class="btn btn-primary fw-semibold rounded-2 px-4 shadow-none small"
                    onclick="submitQuickCustomer()" style="background-color: #3c50e0; border-color: #3c50e0;">Lưu khách
                    hàng</button>
            </div>
        </div>
    </div>
</div>

<script>
    let cart = {};
    let allProducts = <?php echo json_encode($variants ?? []); ?>;
    let allCustomers = <?php echo json_encode($customers ?? []); ?>;
    let globalTotalBill = 0;
</script>
<script src="/assets/js/pos/index.js"></script>

<?php require_once 'views/layout/footer.php'; ?>