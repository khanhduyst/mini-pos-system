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

        foreach ($customers as &$customer) {
            $customer['history'] = $this->customerModel->getDebtHistory($customer['id']);
        }

        require_once 'views/customers/index.php';
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $customer_code = $_POST['customer_code'];
            $full_name     = $_POST['full_name'];
            $phone         = trim($_POST['phone']);
            $email         = !empty($_POST['email']) ? $_POST['email'] : null;
            $gender        = $_POST['gender'];
            $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
            $address       = !empty($_POST['address']) ? $_POST['address'] : null;
            $note          = !empty($_POST['note']) ? $_POST['note'] : null;

            if ($this->customerModel->isPhoneExists($phone)) {
                $_SESSION['error_add_phone'] = "Số điện thoại này đã được đăng ký cho một khách hàng khác!";
                $_SESSION['old_add_data'] = $_POST;
                header("Location: /customer/index");
                exit();
            }

            if ($this->customerModel->createCustomer($customer_code, $full_name, $phone, $email, $gender, $date_of_birth, $address, $note)) {
                $_SESSION['flash_success'] = "Thêm mới khách hàng thành công!";
            } else {
                $_SESSION['flash_error'] = "Lỗi: Không thể thêm khách hàng!";
            }
            header("Location: /customer/index");
            exit();
        }
    }

    public function edit()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id            = $_POST['id'];
            $full_name     = $_POST['full_name'];
            $phone         = trim($_POST['phone']);
            $email         = !empty($_POST['email']) ? $_POST['email'] : null;
            $gender        = $_POST['gender'];
            $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
            $address       = !empty($_POST['address']) ? $_POST['address'] : null;
            $note          = !empty($_POST['note']) ? $_POST['note'] : null;

            if ($this->customerModel->isPhoneExists($phone, $id)) {
                $_SESSION['error_edit_phone_id'] = $id;
                $_SESSION['error_edit_phone_msg'] = "Số điện thoại cập nhật đã tồn tại trên hệ thống!";
                header("Location: /customer/index");
                exit();
            }

            if ($this->customerModel->updateCustomer($id, $full_name, $phone, $email, $gender, $date_of_birth, $address, $note)) {
                $_SESSION['flash_success'] = "Cập nhật thông tin khách hàng thành công!";
            } else {
                $_SESSION['flash_error'] = "Lỗi: Thao tác thất bại!";
            }
            header("Location: /customer/index");
            exit();
        }
    }

    public function payDebt()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $customer_id = $_POST['customer_id'];
            $amount      = $_POST['amount'];
            $note        = !empty($_POST['note']) ? $_POST['note'] : "Khách thanh toán tiền nợ tại quầy";
            $user_id     = $_SESSION['user_id'];

            if ($this->customerModel->payDebt($customer_id, $user_id, $amount, $note)) {
                $_SESSION['flash_success'] = "Thu nợ khách hàng thành công!";
            } else {
                $_SESSION['flash_error'] = "Lỗi: Quá trình trừ nợ thất bại!";
            }
            header("Location: /customer/index");
            exit();
        }
    }

    public function toggle()
    {
        if (isset($_GET['id']) && isset($_GET['status'])) {
            $id     = $_GET['id'];
            $status = $_GET['status'];
            if ($this->customerModel->toggleStatus($id, $status)) {
                $action_text = ($status == 1) ? "Ngừng theo dõi" : "Kích hoạt lại";
                $_SESSION['flash_success'] = $action_text . " khách hàng thành công!";
            } else {
                $_SESSION['flash_error'] = "Lỗi: Thao tác thất bại!";
            }
        }
        header("Location: /customer/index");
        exit();
    }
}