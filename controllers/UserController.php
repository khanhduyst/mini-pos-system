<?php
require_once 'models/UserModel.php';
require_once 'helpers/MailHelper.php';

class UserController
{
    private $db;
    private $userModel;

    public function __construct()
    {
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

    public function index()
    {
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

        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'users' => $users,
                'total_pages' => $total_pages,
                'current_page' => $current_page,
                'current_user_id' => $_SESSION['user_id']
            ]);
            exit();
        }

        $stmt_r = $this->db->prepare("SELECT id, role_name FROM roles");
        $stmt_r->execute();
        $roles = $stmt_r->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/users/index.php';
    }

    private function generateRandomPassword($length = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($chars), 0, $length);
    }

    public function add()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_code = $_POST['user_code'] ?? '';
            $username  = trim($_POST['username'] ?? '');
            $full_name = $_POST['full_name'] ?? '';
            $email     = trim($_POST['email'] ?? '');
            $phone     = $_POST['phone'] ?? '';
            $gender    = $_POST['gender'] ?? '';
            $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
            $address   = $_POST['address'] ?? '';
            $role_id   = $_POST['role_id'] ?? '';
            $note      = $_POST['note'] ?? '';

            // Bắt lỗi trùng mã nhân viên
            if ($this->userModel->isFieldExists('user_code', $user_code)) {
                echo json_encode(['success' => false, 'message' => 'Mã nhân viên này đã tồn tại trên hệ thống!']);
                exit();
            }

            // Bắt lỗi trùng tên đăng nhập
            if ($this->userModel->isFieldExists('username', $username)) {
                echo json_encode(['success' => false, 'message' => 'Tên đăng nhập (username) này đã được sử dụng!']);
                exit();
            }

            // Bắt lỗi trùng Email nhân viên
            if ($this->userModel->isFieldExists('email', $email)) {
                echo json_encode(['success' => false, 'message' => 'Địa chỉ Email này đã tồn tại trên hệ thống!']);
                exit();
            }

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

                echo json_encode(['success' => true, 'message' => 'Khởi tạo tài khoản và gửi email mật khẩu thành công!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể thêm nhân viên mới vào cơ sở dữ liệu!']);
            }
            exit();
        }
    }

    public function edit()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id        = $_POST['id'] ?? 0;
            $full_name = $_POST['full_name'] ?? '';
            $email     = trim($_POST['email'] ?? '');
            $phone     = $_POST['phone'] ?? '';
            $gender    = $_POST['gender'] ?? '';
            $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
            $address   = $_POST['address'] ?? '';
            $role_id   = $_POST['role_id'] ?? '';
            $note      = $_POST['note'] ?? '';

            // Bắt lỗi trùng Email khi chỉnh sửa (loại trừ ID hiện tại)
            if ($this->userModel->isFieldExists('email', $email, $id)) {
                echo json_encode(['success' => false, 'message' => 'Địa chỉ Email cập nhật đã tồn tại trên hệ thống!']);
                exit();
            }

            if ($this->userModel->updateUser($id, $full_name, $email, $phone, $gender, $date_of_birth, $address, $role_id, $note)) {
                echo json_encode(['success' => true, 'message' => 'Cập nhật thông tin nhân viên thành công!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Hệ thống không thể lưu thông tin chỉnh sửa!']);
            }
            exit();
        }
    }

    public function toggle()
    {
        header('Content-Type: application/json');
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $status = isset($_GET['status']) ? (int)$_GET['status'] : 0;

        if ($id > 0) {
            $new_status = $this->userModel->toggleStatus($id, $status);
            if ($new_status !== false) {
                echo json_encode([
                    'success' => true,
                    'new_status' => $new_status,
                    'message' => 'Thay đổi trạng thái tài khoản nhân viên thành công!'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Thao tác cập nhật trạng thái thất bại!']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Mã nhân viên không hợp lệ!']);
        }
        exit();
    }
}