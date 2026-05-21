<?php
require_once 'models/CustomerModel.php';

class CustomerController
{
    private $db;
    private $customerModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->customerModel = new CustomerModel($this->db);
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
        $status = (isset($_GET['status']) && $_GET['status'] !== '') ? (int)$_GET['status'] : null;

        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($current_page < 1) $current_page = 1;
        $limit = 10;
        $offset = ($current_page - 1) * $limit;

        $total_customers = $this->customerModel->countCustomersWithFilter($search, $status);
        $total_pages = ceil($total_customers / $limit);

        $customers = $this->customerModel->getCustomersWithFilter($search, $status, $limit, $offset);

        foreach ($customers as $key => $customer) {
            $customers[$key]['history'] = $this->customerModel->getDebtHistory($customer['id']);
        }

        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'customers' => $customers,
                'total_pages' => $total_pages,
                'current_page' => $current_page
            ]);
            exit();
        }

        require_once 'views/customers/index.php';
    }

    public function add()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $customer_code = $_POST['customer_code'] ?? '';
            $full_name     = $_POST['full_name'] ?? '';
            $phone         = trim($_POST['phone'] ?? '');
            $email         = !empty($_POST['email']) ? $_POST['email'] : null;
            $gender        = $_POST['gender'] ?? 'other';
            $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
            $address       = !empty($_POST['address']) ? $_POST['address'] : null;
            $note          = !empty($_POST['note']) ? $_POST['note'] : null;

            if ($this->customerModel->isPhoneExists($phone)) {
                echo json_encode([
                    'success' => false,
                    'error_type' => 'phone',
                    'message' => 'Số điện thoại này đã được đăng ký cho một khách hàng khác!'
                ]);
                exit();
            }

            if ($this->customerModel->createCustomer($customer_code, $full_name, $phone, $email, $gender, $date_of_birth, $address, $note)) {
                echo json_encode(['success' => true, 'message' => 'Thêm mới khách hàng thành công!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi: Không thể thêm khách hàng vào cơ sở dữ liệu!']);
            }
            exit();
        }
    }

    public function edit()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id            = $_POST['id'] ?? 0;
            $full_name     = $_POST['full_name'] ?? '';
            $phone         = trim($_POST['phone'] ?? '');
            $email         = !empty($_POST['email']) ? $_POST['email'] : null;
            $gender        = $_POST['gender'] ?? 'other';
            $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
            $address       = !empty($_POST['address']) ? $_POST['address'] : null;
            $note          = !empty($_POST['note']) ? $_POST['note'] : null;

            if ($this->customerModel->isPhoneExists($phone, $id)) {
                echo json_encode([
                    'success' => false,
                    'error_type' => 'phone',
                    'message' => 'Số điện thoại cập nhật đã tồn tại trên hệ thống!'
                ]);
                exit();
            }

            if ($this->customerModel->updateCustomer($id, $full_name, $phone, $email, $gender, $date_of_birth, $address, $note)) {
                echo json_encode(['success' => true, 'message' => 'Cập nhật thông tin khách hàng thành công!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi: Thao tác cập nhật thất bại!']);
            }
            exit();
        }
    }

    public function payDebt()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $customer_id = $_POST['customer_id'] ?? 0;
            $amount      = $_POST['amount'] ?? 0;
            $note        = !empty($_POST['note']) ? $_POST['note'] : "Khách thanh toán tiền nợ tại quầy";
            $user_id     = $_SESSION['user_id'];

            if ($this->customerModel->payDebt($customer_id, $user_id, $amount, $note)) {
                echo json_encode(['success' => true, 'message' => 'Thu nợ khách hàng thành công!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi: Quá trình trừ nợ thất bại!']);
            }
            exit();
        }
    }

    public function toggle()
    {
        header('Content-Type: application/json');
        if (isset($_GET['id']) && isset($_GET['status'])) {
            $id     = (int)$_GET['id'];
            $status = (int)$_GET['status'];
            if ($this->customerModel->toggleStatus($id, $status)) {
                $action_text = ($status == 1) ? "Ngừng theo dõi" : "Kích hoạt lại";
                echo json_encode([
                    'success' => true,
                    'message' => $action_text . " khách hàng thành công!"
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi: Thao tác thay đổi trạng thái thất bại!']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ!']);
        }
        exit();
    }
}