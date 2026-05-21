<?php
require_once 'models/ProductModel.php';

class PosController
{
    private $db;
    private $productModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->productModel = new ProductModel($this->db);

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header("Location: /auth/login");
            exit();
        }
    }

    public function index()
    {
        $variants = $this->productModel->getAllVariantsForPos();

        $stmt_c = $this->db->prepare("SELECT id, full_name, phone, debt FROM customers WHERE status = 1");
        $stmt_c->execute();
        $customers = $stmt_c->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/pos/index.php';
    }

    public function checkout()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['cart'])) {
            echo json_encode(['success' => false, 'message' => 'Giỏ hàng trống rỗng!']);
            exit();
        }

        $customer_id   = (int)$input['customer_id'];
        $pay_method     = $input['pay_method'];
        $customer_paid  = (float)($input['customer_paid'] ?? 0);
        $cart           = $input['cart'];
        $user_id        = $_SESSION['user_id'];

        try {
            $this->db->beginTransaction();

            $total_bill = 0;
            $ref_code = 'HD-' . time();

            // Bước 1: Tính toán tổng tiền hóa đơn thực tế từ giỏ hàng
            foreach ($cart as $variant_id => $item) {
                $qty_buy = (int)$item['qty'];
                $variant_id = (int)$variant_id;

                $v_data = $this->productModel->getVariantStockForUpdate($variant_id);
                if (!$v_data || $v_data['stock_qty'] < $qty_buy) {
                    throw new Exception("Mặt hàng [" . $item['name'] . "] không đủ tồn kho trên kệ!");
                }
                $total_bill += (float)$item['price'] * $qty_buy;
            }

            // Bước 2: INSERT thông tin tổng quan của hóa đơn vào bảng orders (Lưu lịch sử mua hàng)
            $stmt_order = $this->db->prepare("INSERT INTO orders (order_code, customer_id, user_id, total_amount, customer_paid, pay_method, created_at) 
                                              VALUES (:code, :c_id, :u_id, :total, :paid, :method, NOW())");
            $stmt_order->bindParam(':code', $ref_code);
            $stmt_order->bindParam(':c_id', $customer_id, PDO::PARAM_INT);
            $stmt_order->bindParam(':u_id', $user_id, PDO::PARAM_INT);
            $stmt_order->bindParam(':total', $total_bill);
            $stmt_order->bindParam(':paid', $customer_paid);
            $stmt_order->bindParam(':method', $pay_method);
            $stmt_order->execute();

            $order_id = $this->db->lastInsertId();

            // Bước 3: Duyệt giỏ hàng để trừ kho, ghi log thẻ kho và lưu chi tiết từng món vào order_details
            foreach ($cart as $variant_id => $item) {
                $qty_buy = (int)$item['qty'];
                $variant_id = (int)$variant_id;
                $price = (float)$item['price'];

                $v_data = $this->productModel->getVariantStockForUpdate($variant_id);
                $new_stock = $v_data['stock_qty'] - $qty_buy;

                // Cập nhật số lượng tồn kho mới
                $this->productModel->updateVariantStock($variant_id, $new_stock);

                // Ghi nhật ký biến động kho (Thẻ kho)
                $change_qty = -$qty_buy;
                $this->productModel->logStockChange($variant_id, $user_id, $ref_code, $v_data['stock_qty'], $change_qty, $new_stock);

                // Lưu vết chi tiết sản phẩm của hóa đơn này
                $stmt_detail = $this->db->prepare("INSERT INTO order_details (order_id, variant_id, quantity, price) 
                                                   VALUES (:order_id, :v_id, :qty, :price)");
                $stmt_detail->bindParam(':order_id', $order_id, PDO::PARAM_INT);
                $stmt_detail->bindParam(':v_id', $variant_id, PDO::PARAM_INT);
                $stmt_detail->bindParam(':qty', $qty_buy, PDO::PARAM_INT);
                $stmt_detail->bindParam(':price', $price);
                $stmt_detail->execute();
            }

            // Bước 4: Xử lý công nợ khách hàng nếu là khách quen cụ thể
            if ($customer_id > 0) {
                $stmt_c = $this->db->prepare("SELECT debt, total_spent, points FROM customers WHERE id = :id FOR UPDATE");
                $stmt_c->bindParam(':id', $customer_id, PDO::PARAM_INT);
                $stmt_c->execute();
                $c_data = $stmt_c->fetch(PDO::FETCH_ASSOC);

                if ($c_data) {
                    $new_spent = $c_data['total_spent'] + $total_bill;
                    $new_points = $c_data['points'] + floor($total_bill / 10000);
                    $new_debt = $c_data['debt'];

                    // Tính toán số nợ mới phát sinh dựa trên tiền hàng và tiền thực trả
                    $debt_amount = 0;
                    if ($pay_method === 'debt') {
                        $debt_amount = $total_bill;
                    } else {
                        $diff = $total_bill - $customer_paid;
                        if ($diff > 0) {
                            $debt_amount = $diff; // Khách trả thiếu tiền mặt khi chọn Khách thanh toán
                        }
                    }

                    // Nếu có phát sinh nợ mới -> Ghi nhận vào bảng nhật ký công nợ customer_debts
                    if ($debt_amount > 0) {
                        $new_debt = $c_data['debt'] + $debt_amount;

                        $stmt_d_log = $this->db->prepare("INSERT INTO customer_debts (customer_id, user_id, type, amount, balance_after, note, created_at) 
                                                          VALUES (:c_id, :u_id, 'increase', :amount, :balance, 'Khách nợ đơn hàng POS từ mã đơn ' || :ref, NOW())");
                        $stmt_d_log->bindParam(':c_id', $customer_id, PDO::PARAM_INT);
                        $stmt_d_log->bindParam(':u_id', $user_id, PDO::PARAM_INT);
                        $stmt_d_log->bindParam(':amount', $debt_amount);
                        $stmt_d_log->bindParam(':balance', $new_debt);
                        $stmt_d_log->bindParam(':ref', $ref_code);
                        $stmt_d_log->execute();
                    }

                    // Cập nhật tổng chi tiêu, điểm tích lũy và số dư nợ mới vào bảng customers
                    $stmt_c_u = $this->db->prepare("UPDATE customers SET total_spent = :ts, points = :pts, debt = :debt WHERE id = :id");
                    $stmt_c_u->bindParam(':ts', $new_spent);
                    $stmt_c_u->bindParam(':pts', $new_points, PDO::PARAM_INT);
                    $stmt_c_u->bindParam(':debt', $new_debt);
                    $stmt_c_u->bindParam(':id', $customer_id, PDO::PARAM_INT);
                    $stmt_c_u->execute();
                }
            }

            $this->db->commit();
            echo json_encode(['success' => true, 'message' => 'Hóa đơn xuất kho thành công!']);
        } catch (Exception $e) {
            $this->db->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    
}