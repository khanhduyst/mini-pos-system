<?php
require_once 'models/UserModel.php';

class AuthController {
    private $db;
    private $userModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->userModel = new UserModel($this->db);
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login() {
        if (isset($_SESSION['user_id'])) {
            header("Location: /home");
            exit();
        }

        $error = "";
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $this->userModel->login($username);

            if ($user) {
                if ($user['status'] == 0) {
                    $error = "Tài khoản của bạn đã bị khóa!";
                } else if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_code'] = $user['user_code'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['role'] = $user['role'];
                    
                    header("Location: /home");
                    exit();
                } else {
                    $error = "Mật khẩu không chính xác!";
                }
            } else {
                $error = "Tên đăng nhập không tồn tại!";
            }
        }
        require_once 'views/login.php';
    }

    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header("Location: /auth/login");
        exit();
    }
}