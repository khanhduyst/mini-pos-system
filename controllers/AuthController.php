<?php
require_once 'models/UserModel.php';

class AuthController
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
    }

    public function login()
    {
        if (isset($_SESSION['user_id'])) {
            header("Location: /user/index");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $this->userModel->login($username);

            if ($user && password_verify($password, $user['password'])) {
                if ($user['status'] == 0) {
                    $_SESSION['flash_error'] = "Tài khoản của bạn hiện đang bị khóa!";
                    header("Location: /auth/login");
                    exit();
                }

                unset($_SESSION['flash_success']);
                unset($_SESSION['flash_error']);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_code'] = $user['user_code'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role_name'];

                $_SESSION['flash_login_success'] = true;
            } else {
                $_SESSION['flash_error'] = "Tên đăng nhập hoặc mật khẩu không chính xác!";
                header("Location: /auth/login");
                exit();
            }
        }

        require_once 'views/auth/login.php';
    }

    public function logout()
    {
        session_destroy();
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash_success'] = "Bạn đã đăng xuất khỏi hệ thống!";
        header("Location: /auth/login");
        exit();
    }
}