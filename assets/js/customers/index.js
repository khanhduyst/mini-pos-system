
document.addEventListener("DOMContentLoaded", function() {
    const btnRandom = document.getElementById('btnRandomCustCode');
    const inputCode = document.getElementById('add_customer_code');
    if (btnRandom && inputCode) {
        btnRandom.addEventListener('click', function() {
            const randomNum = Math.floor(1000 + Math.random() * 9000);
            inputCode.value = 'KH' + randomNum;
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
                btn.innerHTML =
                    `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Đang xử lý...`;
            } else {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }
    }

    function validateCustomerForm(formEl) {
        let isValid = true;
        const inputs = formEl.querySelectorAll('input, select, textarea');

        inputs.forEach(input => {
            if (input.hasAttribute('readonly') || input.type === 'hidden' || ['note', 'address',
                    'date_of_birth', 'search', 'email', 'amount'
                ].includes(input.name)) {
                return;
            }

            input.classList.remove('is-invalid');

            let container = input.parentNode;
            if (input.id === 'add_customer_code') {
                container = input.parentNode.parentNode;
            }

            const oldFeedback = container.querySelector('.invalid-feedback');
            if (oldFeedback) oldFeedback.remove();

            let hasError = false;
            let errorMsg = "";

            if (!input.value.trim()) {
                hasError = true;
                errorMsg = "Trường thông tin này bắt buộc nhập!";
            } else if (input.name === 'phone') {
                const phonePattern = /^0[0-9]{9}$/;
                if (!phonePattern.test(input.value.trim())) {
                    hasError = true;
                    errorMsg = "Số điện thoại không đúng! Phải bắt đầu bằng số 0 và đủ 10 số.";
                }
            }

            if (hasError) {
                isValid = false;
                input.classList.add('is-invalid');

                const feedbackDiv = document.createElement('div');
                feedbackDiv.className = 'invalid-feedback fw-semibold small mt-1 d-block';
                feedbackDiv.innerText = errorMsg;
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

    function loadCustomersData(page = 1) {
        const filterForm = document.querySelector('form[action="/customer/index"]');
        const formData = new FormData(filterForm);
        const params = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
            params.append(key, value);
        }
        params.append('page', page);
        params.append('ajax', '1');

        fetch('/customer/index?' + params.toString())
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderTableRows(data.customers);
                    renderPagination(data.total_pages, data.current_page);
                    updateModalContainers(data.customers);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function renderTableRows(customers) {
        const tbody = document.querySelector('table tbody');
        tbody.innerHTML = '';

        if (!customers || customers.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center p-5 text-secondary">
                        <i class="bi bi-person-exclamation d-block mb-2" style="font-size: 40px; color: #cbd5e1;"></i>
                        Không tìm thấy dữ liệu khách hàng nào phù hợp!
                    </td>
                </tr>`;
            return;
        }

        customers.forEach(customer => {
            const formattedSpent = parseFloat(customer.total_spent).toLocaleString('en-US') + 'đ';
            const formattedPoints = parseInt(customer.points).toLocaleString('en-US');

            let debtTd = '<span class="text-secondary">0đ</span>';
            let payDebtBtn = '';
            if (parseFloat(customer.debt) > 0) {
                const formattedDebt = parseFloat(customer.debt).toLocaleString('en-US') + 'đ';
                debtTd =
                    `<span class="badge rounded-1 px-2 py-1 fw-bold text-danger" style="background-color: #fde8e8; font-size: 12px;">Nợ: ${formattedDebt}</span>`;
                payDebtBtn =
                    `<button class="btn btn-sm btn-outline-danger rounded-2 px-2 py-1 small fw-bold shadow-none me-1 btn-paydebt-trigger" data-bs-toggle="modal" data-bs-target="#payDebtModal${customer.id}"><i class="bi bi-cash-coin"></i> Thu nợ</button>`;
            }

            const statusStyle = customer.status == 1 ?
                'background-color: #def7ec; color: #03543f; font-size: 12px;' :
                'background-color: #f3f4f6; color: #4b5563; font-size: 12px;';
            const statusTxt = customer.status == 1 ? 'Hoạt động' : 'Ngừng theo dõi';
            const toggleIcon = customer.status == 1 ? 'bi-eye-slash' : 'bi-eye';
            const toggleClass = customer.status == 1 ? 'btn-light text-danger' :
                'btn-light text-success';

            const tr = document.createElement('tr');
            tr.style.borderBottom = '1px solid #f1f5f9';
            tr.id = `customer-row-${customer.id}`;
            tr.innerHTML = `
                <td class="ps-4 fw-bold text-primary">${customer.customer_code}</td>
                <td class="fw-semibold text-dark">${customer.full_name}</td>
                <td class="font-monospace">${customer.phone}</td>
                <td class="fw-bold text-success">${formattedPoints}</td>
                <td class="fw-semibold">${formattedSpent}</td>
                <td>${debtTd}</td>
                <td><span class="badge rounded-1 px-2 py-1" style="${statusStyle}">${statusTxt}</span></td>
                <td class="text-end pe-4">
                    <div class="d-flex justify-content-end gap-1">
                        ${payDebtBtn}
                        <button class="btn btn-sm btn-light text-secondary border rounded-2 px-2 shadow-none" data-bs-toggle="modal" data-bs-target="#viewCustomerModal${customer.id}"><i class="bi bi-clock-history"></i></button>
                        <button class="btn btn-sm btn-light text-primary border rounded-2 px-2 shadow-none" data-bs-toggle="modal" data-bs-target="#editCustomerModal${customer.id}"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm border rounded-2 px-2 shadow-none ${toggleClass}" data-bs-toggle="modal" data-bs-target="#toggleStatusModal${customer.id}"><i class="bi ${toggleIcon}"></i></button>
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
            const linkStyle = currentPage == i ? 'style="background-color: #3c50e0; border-color: #3c50e0;"' :
                '';
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

    function updateModalContainers(customers) {
        const modalContainer = document.getElementById('dynamic-modals-container');
        modalContainer.innerHTML = '';

        customers.forEach(customer => {
            let historyRows = '';
            if (customer.history && customer.history.length > 0) {
                customer.history.forEach(log => {
                    const dateObj = new Date(log.created_at);
                    const formattedDate = ('0' + dateObj.getDate()).slice(-2) + '/' + ('0' + (
                            dateObj.getMonth() + 1)).slice(-2) + '/' + dateObj.getFullYear() +
                        ' ' + ('0' + dateObj.getHours()).slice(-2) + ':' + ('0' + dateObj
                            .getMinutes()).slice(-2);
                    const badgeClass = log.type === 'increase' ? 'bg-light-danger text-danger' :
                        'bg-light-success text-success';
                    const typeText = log.type === 'increase' ? 'Nợ thêm' : 'Trả nợ';
                    historyRows += `
                        <tr>
                            <td class="font-monospace">${formattedDate}</td>
                            <td><span class="badge rounded-1 ${badgeClass}">${typeText}</span></td>
                            <td class="fw-bold">${parseFloat(log.amount).toLocaleString('en-US')}đ</td>
                            <td class="font-monospace fw-semibold">${parseFloat(log.balance_after).toLocaleString('en-US')}đ</td>
                            <td>${log.staff_name}</td>
                            <td class="text-start text-secondary">${log.note}</td>
                        </tr>`;
                });
            } else {
                historyRows =
                    `<tr><td colspan="6" class="text-muted p-3 text-center">Không có lịch sử biến động công nợ!</td></tr>`;
            }

            let payDebtModalHtml = '';
            if (parseFloat(customer.debt) > 0) {
                payDebtModalHtml = `
                <div class="modal fade" id="payDebtModal${customer.id}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content border-0 shadow rounded-3">
                            <div class="modal-header p-3 border-bottom bg-white">
                                <h6 class="modal-title fw-bold text-dark"><i class="bi bi-cash-coin text-danger"></i> Tiến hành thu nợ khách</h6>
                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="/customer/payDebt" method="POST">
                                <input type="hidden" name="customer_id" value="${customer.id}">
                                <div class="modal-body p-3 bg-white">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary">Dư nợ hiện tại</label>
                                        <input type="text" class="form-control bg-light fw-bold text-danger modal-input-debt-val" value="${parseFloat(customer.debt).toLocaleString('en-US')}đ" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary">Số tiền thu thực tế</label>
                                        <input type="number" class="form-control modal-input-amount-field" name="amount" min="1" max="${customer.debt}" placeholder="Nhập số tiền mặt nhận..." required>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label small fw-semibold text-secondary">Ghi chú thu nợ</label>
                                        <textarea class="form-control small" name="note" rows="2" placeholder="Khách trả nợ tiền mua tạp hóa..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-top p-2 bg-white">
                                    <button type="submit" class="btn btn-danger w-100 fw-semibold py-2 small">Xác nhận thu nợ</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>`;
            }

            const toggleTitle = customer.status == 1 ? 'Ngừng theo dõi?' : 'Theo dõi lại?';
            const toggleDesc = customer.status == 1 ?
                `Hệ thống tạm ẩn khách <strong class="text-dark">${customer.full_name}</strong> khỏi màn hình POS bán hàng.` :
                `Kích hoạt hiển thị lại thông tin khách hàng <strong class="text-dark">${customer.full_name}</strong>.`;
            const toggleBtnClass = customer.status == 1 ? 'btn-danger' : 'btn-success';

            modalContainer.innerHTML += `
                <div class="modal fade" id="viewCustomerModal${customer.id}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow rounded-3">
                            <div class="modal-header p-4 border-bottom bg-white">
                                <h5 class="modal-title fw-bold text-dark">Sổ nợ & Thông tin: <span class="modal-title-cust-name">${customer.full_name}</span></h5>
                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4 bg-white">
                                <div class="row g-3 mb-4 text-center">
                                    <div class="col-4 border-end"><span class="small text-secondary font-monospace d-block">TỔNG MUA</span><span class="fw-bold text-dark fs-5 modal-txt-spent">${parseFloat(customer.total_spent).toLocaleString('en-US')}đ</span></div>
                                    <div class="col-4 border-end"><span class="small text-secondary font-monospace d-block">ĐIỂM TÍCH LŨY</span><span class="fw-bold text-success fs-5 modal-txt-points">${parseInt(customer.points).toLocaleString('en-US')}</span></div>
                                    <div class="col-4"><span class="small text-secondary font-monospace d-block">ĐANG NỢ</span><span class="fw-bold text-danger fs-5 modal-txt-debt">${parseFloat(customer.debt).toLocaleString('en-US')}đ</span></div>
                                </div>
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-journal-text"></i> Nhật ký lịch sử công nợ</h6>
                                <div class="table-responsive style-scroll" style="max-height: 250px; overflow-y: auto;">
                                    <table class="table table-sm table-bordered align-middle small text-center mb-0">
                                        <thead class="table-light text-secondary">
                                            <tr><th>Thời gian</th><th>Hành động</th><th>Số tiền</th><th>Nợ sau giao dịch</th><th>Nhân viên</th><th>Ghi chú</th></tr>
                                        </thead>
                                        <tbody class="modal-table-history">${historyRows}</tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                ${payDebtModalHtml}

                <div class="modal fade" id="editCustomerModal${customer.id}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow rounded-3">
                            <div class="modal-header p-4 border-bottom bg-white">
                                <h5 class="modal-title fw-bold text-dark">Chỉnh sửa thông tin khách hàng</h5>
                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="/customer/edit" method="POST" novalidate>
                                <input type="hidden" name="id" value="${customer.id}">
                                <div class="modal-body p-4 bg-white">
                                    <div class="row g-3">
                                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Mã khách hàng</label><input type="text" class="form-control bg-light" name="customer_code" value="${customer.customer_code}" readonly></div>
                                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Họ và tên khách</label><input type="text" class="form-control" name="full_name" value="${customer.full_name}" required></div>
                                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Số điện thoại</label><input type="text" class="form-control" name="phone" value="${customer.phone}" required></div>
                                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Địa chỉ Email</label><input type="email" class="form-control" name="email" value="${customer.email || ''}"></div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-secondary">Giới tính</label>
                                            <select class="form-select" name="gender">
                                                <option value="male" ${customer.gender == 'male' ? 'selected' : ''}>Nam</option>
                                                <option value="female" ${customer.gender == 'female' ? 'selected' : ''}>Nữ</option>
                                                <option value="other" ${customer.gender == 'other' ? 'selected' : ''}>Khác</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Ngày sinh</label><input type="date" class="form-control" name="date_of_birth" value="${customer.date_of_birth || ''}"></div>
                                        <div class="col-12"><label class="form-label small fw-semibold text-secondary">Địa chỉ thường trú</label><input type="text" class="form-control" name="address" value="${customer.address || ''}"></div>
                                        <div class="col-12"><label class="form-label small fw-semibold text-secondary">Ghi chú đặc điểm khách</label><textarea class="form-control" name="note" rows="2">${customer.note || ''}</textarea></div>
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

                <div class="modal fade" id="toggleStatusModal${customer.id}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content border-0 shadow-lg rounded-3">
                            <div class="modal-body p-4 text-center bg-white rounded-3">
                                <div class="${customer.status == 1 ? 'text-danger' : 'text-success'} mb-3"><i class="bi ${customer.status == 1 ? 'bi-eye-slash-fill' : 'bi-eye-fill'} fs-1"></i></div>
                                <h5 class="fw-bold text-dark mb-2 modal-toggle-title">${toggleTitle}</h5>
                                <p class="text-secondary small mb-4 modal-toggle-desc">${toggleDesc}</p>
                                <div class="d-flex gap-2 justify-content-center">
                                    <button type="button" class="btn btn-light fw-semibold rounded-2 px-3 small" data-bs-dismiss="modal">Hủy</button>
                                    <button type="button" data-url="/customer/toggle?id=${customer.id}&status=${customer.status}" class="btn btn-toggle-cust-confirm fw-semibold rounded-2 px-4 small text-white ${toggleBtnClass}">Xác nhận</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
        });
    }

    const filterForm = document.querySelector('form[action="/customer/index"]');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            loadCustomersData(1);
        });

        const btnReset = filterForm.querySelector('a[href="/customer/index"]');
        if (btnReset) {
            btnReset.addEventListener('click', function(e) {
                e.preventDefault();
                filterForm.reset();
                filterForm.querySelectorAll('input').forEach(i => i.value = '');
                filterForm.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
                loadCustomersData(1);
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
            loadCustomersData(targetPage);
        }
    });

    const addForm = document.querySelector('#addCustomerModal form');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!validateCustomerForm(addForm)) {
                return;
            }

            const btnSubmit = addForm.querySelector('.btn-submit-cust');
            setBtnLoading(btnSubmit, true, 'Lưu lại');

            const formData = new FormData(addForm);
            fetch('/customer/add', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    setBtnLoading(btnSubmit, false, 'Lưu lại');

                    const phoneInput = addForm.querySelector('input[name="phone"]');
                    phoneInput.classList.remove('is-invalid');
                    const oldFeedback = phoneInput.parentNode.querySelector('.invalid-feedback');
                    if (oldFeedback) oldFeedback.remove();

                    if (data.success) {
                        const modalEl = document.getElementById('addCustomerModal');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();
                        addForm.reset();

                        loadCustomersData(1);
                        showToast(data.message, 'success');
                    } else {
                        if (data.error_type === 'phone') {
                            phoneInput.classList.add('is-invalid');
                            const feedbackDiv = document.createElement('div');
                            feedbackDiv.className =
                                'invalid-feedback fw-semibold small mt-1 d-block';
                            feedbackDiv.innerText = data.message;
                            phoneInput.parentNode.appendChild(feedbackDiv);
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
        const form = e.target.closest('form[action="/customer/edit"]');
        if (form) {
            e.preventDefault();
            if (!validateCustomerForm(form)) {
                return;
            }

            const btnSubmit = form.querySelector('button[type="submit"]');
            setBtnLoading(btnSubmit, true, 'Cập nhật');

            const formData = new FormData(form);
            fetch('/customer/edit', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    setBtnLoading(btnSubmit, false, 'Cập nhật');

                    const phoneInput = form.querySelector('input[name="phone"]');
                    phoneInput.classList.remove('is-invalid');
                    const oldFeedback = phoneInput.parentNode.querySelector('.invalid-feedback');
                    if (oldFeedback) oldFeedback.remove();

                    if (data.success) {
                        const modalEl = form.closest('.modal');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();

                        const activePageItem = document.querySelector('.page-number-item.active');
                        const currentPage = activePageItem ? parseInt(activePageItem.getAttribute(
                            'data-page-num')) : 1;
                        loadCustomersData(currentPage);
                        showToast(data.message, 'success');
                    } else {
                        if (data.error_type === 'phone') {
                            phoneInput.classList.add('is-invalid');
                            const feedbackDiv = document.createElement('div');
                            feedbackDiv.className =
                                'invalid-feedback fw-semibold small mt-1 d-block';
                            feedbackDiv.innerText = data.message;
                            phoneInput.parentNode.appendChild(feedbackDiv);
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

    document.addEventListener('submit', function(e) {
        const form = e.target.closest('form[action="/customer/payDebt"]');
        if (form) {
            e.preventDefault();

            const btnSubmit = form.querySelector('button[type="submit"]');
            const originalText = btnSubmit.innerHTML;
            setBtnLoading(btnSubmit, true, originalText);

            const formData = new FormData(form);
            fetch('/customer/payDebt', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    setBtnLoading(btnSubmit, false, originalText);
                    if (data.success) {
                        const modalEl = form.closest('.modal');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();

                        const activePageItem = document.querySelector('.page-number-item.active');
                        const currentPage = activePageItem ? parseInt(activePageItem.getAttribute(
                            'data-page-num')) : 1;
                        loadCustomersData(currentPage);
                        showToast(data.message, 'success');
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    setBtnLoading(btnSubmit, false, originalText);
                    console.error('Error:', error);
                    showToast('Có lỗi hệ thống xảy ra!', 'error');
                });
        }
    });

    document.getElementById('dynamic-modals-container').addEventListener('click', function(e) {
        const confirmBtn = e.target.closest('.btn-toggle-cust-confirm');
        if (confirmBtn) {
            e.preventDefault();
            const targetUrl = confirmBtn.getAttribute('data-url');

            const originalTxt = confirmBtn.innerHTML;
            confirmBtn.disabled = true;
            confirmBtn.innerHTML =
                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;

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
                        const currentPage = activePageItem ? parseInt(activePageItem.getAttribute(
                            'data-page-num')) : 1;
                        loadCustomersData(currentPage);
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