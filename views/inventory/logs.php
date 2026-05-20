<?php require_once 'views/layout/header.php'; $logs = $logs ?? []; ?>

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
                                <td class="ps-4 font-monospace text-muted small"><?php echo $l['created_at']; ?></td>
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

<?php require_once 'views/layout/footer.php'; ?>