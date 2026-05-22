<?php
require_once 'models/SupplierModel.php';
require_once 'models/ProductModel.php';

class SupplierController
{
    private $db;
    private $supplierModel;
    private $productModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->supplierModel = new SupplierModel($this->db);
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
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $suppliers = $this->supplierModel->getAllSuppliers($search);

        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'suppliers' => $suppliers]);
            exit();
        }

        require_once 'views/supplier/index.php';
    }

    public function addSupplier()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $code = trim($_POST['supplier_code'] ?? '');
            $name = trim($_POST['supplier_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $address = trim($_POST['address'] ?? '');

            if ($this->supplierModel->isSupplierCodeOrPhoneExists($code, $phone)) {
                echo json_encode(['success' => false, 'message' => 'Mã nhà cung cấp hoặc số điện thoại đã tồn tại!']);
                exit();
            }

            if ($this->supplierModel->createSupplier($code, $name, $phone, $email, $address)) {
                echo json_encode(['success' => true, 'message' => 'Thêm nhà cung cấp thành công!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể thêm nhà cung cấp!']);
            }
            exit();
        }
    }

    public function editSupplier()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = (int)$_POST['id'];
            $name = trim($_POST['supplier_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $address = trim($_POST['address'] ?? '');

            if ($this->supplierModel->isSupplierCodeOrPhoneExists('', $phone, $id)) {
                echo json_encode(['success' => false, 'message' => 'Số điện thoại này đã được sử dụng ở nhà cung cấp khác!']);
                exit();
            }

            if ($this->supplierModel->updateSupplier($id, $name, $phone, $email, $address)) {
                echo json_encode(['success' => true, 'message' => 'Cập nhật nhà cung cấp thành công!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Cập nhật thất bại!']);
            }
            exit();
        }
    }

    public function toggleSupplier()
    {
        header('Content-Type: application/json');
        if (isset($_GET['id']) && isset($_GET['status'])) {
            $id = (int)$_GET['id'];
            $status = (int)$_GET['status'];
            if ($this->supplierModel->toggleStatus($id, $status)) {
                echo json_encode(['success' => true, 'message' => 'Thay đổi trạng thái hợp tác thành công!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Thao tác thất bại!']);
            }
            exit();
        }
    }

    public function orders()
    {
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
        $end_date = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';

        $orders = $this->supplierModel->getAllPurchaseOrders($search, $start_date, $end_date);

        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'orders' => $orders]);
            exit();
        }

        require_once 'views/supplier/orders.php';
    }

    public function createOrder()
    {
        $suppliers = $this->supplierModel->getAllSuppliers();
        $variants = $this->productModel->getAllVariantsForPos();
        require_once 'views/supplier/create_order.php';
    }

    public function orderDetail()
    {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $sheet = $this->supplierModel->getPurchaseOrderById($id);
            $details = $this->supplierModel->getPurchaseOrderDetails($id);
            header('Content-Type: application/json');
            echo json_encode(['sheet' => $sheet, 'details' => $details]);
            exit();
        }
    }

    public function addOrder()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $purchase_code = 'NH' . rand(10000, 99999);
                $supplier_id = (int)$_POST['supplier_id'];
                $user_id = $_SESSION['user_id'];
                $note = $_POST['note'] ?? '';

                $variant_ids = $_POST['variant_id'] ?? [];
                $quantities = $_POST['quantity'] ?? [];
                $import_prices = $_POST['import_price'] ?? [];

                $items = [];
                for ($i = 0; $i < count($variant_ids); $i++) {
                    if (empty($quantities[$i]) || empty($import_prices[$i])) continue;
                    $items[] = [
                        'variant_id' => (int)$variant_ids[$i],
                        'quantity' => (int)$quantities[$i],
                        'import_price' => (float)$import_prices[$i]
                    ];
                }

                if (empty($items)) {
                    echo json_encode(['success' => false, 'message' => 'Đơn nhập hàng phải có ít nhất một mặt hàng hợp lệ!']);
                    exit();
                }

                if ($this->supplierModel->createPurchaseOrder($purchase_code, $supplier_id, $user_id, $items, $note)) {
                    echo json_encode(['success' => true, 'message' => 'Tạo đơn nhập hàng thành công, đang chờ nhập kho thực tế!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Không thể tạo đơn nhập hàng!']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
            }
            exit();
        }
    }

    public function approveOrder()
    {
        header('Content-Type: application/json');
        if (isset($_GET['id'])) {
            try {
                $id = (int)$_GET['id'];
                $user_id = $_SESSION['user_id'];

                if ($this->supplierModel->approvePurchaseOrder($id, $user_id)) {
                    echo json_encode(['success' => true, 'message' => 'Đã duyệt nhập kho, số lượng hàng hóa và giá vốn đã được cập nhật tự động!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Duyệt đơn thất bại hoặc đơn này đã được xử lý trước đó!']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
            }
            exit();
        }
    }

    public function deleteOrder()
    {
        header('Content-Type: application/json');
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            if ($this->supplierModel->deletePurchaseOrder($id)) {
                echo json_encode(['success' => true, 'message' => 'Đã hủy đơn nhập hàng nháp thành công!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa đơn hàng đã duyệt!']);
            }
            exit();
        }
    }
}