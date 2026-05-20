<?php
require_once 'models/UserModel.php';
require_once 'helpers/MailHelper.php';

class UserController {
    private $db;
    private $userModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->userModel = new UserModel($this->db);
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header("Location: /auth/login");
            exit();
        }
    }

    public function index() {
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $role_id = (isset($_GET['role_id']) && $_GET['role_id'] !== '') ? (int)$_GET['role_id'] : null;
        $status = (isset($_GET['status']) && $_GET['status'] !== '') ? (int)$_GET['status'] : null;

        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($current_page < 1) $current_page = 1;
        $limit = 10;
        $offset = ($current_page - 1) * $limit;

        $total_users = $this->userModel->countUsersWithFilter($search, $role_id, $status);
        $total_pages = ceil($total_users / $limit);

        $users = $this->userModel->getUsersWithFilter($search, $role_id, $status, $limit, $offset);

        require_once 'views/users/index.php';
    }

    private function generateRandomPassword($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($chars), 0, $length);
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_code = $_POST['user_code'];
            $username  = $_POST['username'];
            $full_name = $_POST['full_name'];
            $email     = $_POST['email'];
            $phone     = $_POST['phone'];
            $gender    = $_POST['gender'];
            $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
            $address   = $_POST['address'];
            $role_id   = $_POST['role_id'];
            $note      = $_POST['note'];

            $random_password = $this->generateRandomPassword();

            if ($this->userModel->createUser($user_code, $username, $random_password, $full_name, $email, $phone, $gender, $date_of_birth, $address, $role_id, $note)) {
                $subject = "Thông tin tài khoản nhân viên mới - MINI POS";
                $body = "
                    <h3>Chào mừng $full_name gia nhập đội ngũ!</h3>
                    <p>Tài khoản truy cập hệ thống phần mềm POS tại quầy của bạn đã được khởi tạo thành công:</p>
                    <table border='0' cellpadding='5'>
                        <tr><td><strong>Đường dẫn:</strong></td><td>http://localhost:8000/auth/login</td></tr>
                        <tr><td><strong>Tài khoản:</strong></td><td><span style='color:#3c50e0;font-weight:bold;'>$username</span></td></tr>
                        <tr><td><strong>Mật khẩu:</strong></td><td><span style='color:#e02424;font-weight:bold;'>$random_password</span></td></tr>
                    </table>
                    <p><i>Lưu ý: Vui lòng không chia sẻ email này cho bất kỳ ai để đảm bảo tính bảo mật.</i></p>
                ";

                MailHelper::send($email, $full_name, $subject, $body);

                $_SESSION['flash_success'] = "Khởi tạo tài khoản và gửi email mật khẩu thành công!";
            } else {
                $_SESSION['flash_error'] = "Lỗi: Không thể thêm nhân viên mới!";
            }
            header("Location: /user/index");
            exit();
        }
    }

    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id        = $_POST['id'];
            $full_name = $_POST['full_name'];
            $email     = $_POST['email'];
            $phone     = $_POST['phone'];
            $gender    = $_POST['gender'];
            $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
            $address   = $_POST['address'];
            $role_id   = $_POST['role_id'];
            $note      = $_POST['note'];

            if ($this->userModel->updateUser($id, $full_name, $email, $phone, $gender, $date_of_birth, $address, $role_id, $note)) {
                $_SESSION['flash_success'] = "Cập nhật thông tin nhân viên thành công!";
            } else {
                $_SESSION['flash_error'] = "Lỗi: Hệ thống không thể lưu thông tin chỉnh sửa!";
            }
            header("Location: /user/index");
            exit();
        }
    }

    public function toggle() {
        if (isset($_GET['id']) && isset($_GET['status'])) {
            $id     = $_GET['id'];
            $status = $_GET['status'];
            if ($this->userModel->toggleStatus($id, $status)) {
                $action_text = ($status == 1) ? "Khóa" : "Mở khóa";
                $_SESSION['flash_success'] = $action_text . " tài khoản nhân viên thành công!";
            } else {
                $_SESSION['flash_error'] = "Lỗi: Thao tác thất bại!";
            }
        }
        header("Location: /user/index");
        exit();
    }
}