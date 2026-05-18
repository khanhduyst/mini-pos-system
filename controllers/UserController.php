<?php
require_once 'models/UserModel.php';

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
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: /auth/login");
            exit();
        }
    }

    public function index() {
        $users = $this->userModel->getAllUsers();
        require_once 'views/users/index.php';
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_code = $_POST['user_code'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $full_name = $_POST['full_name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $role = $_POST['role'];

            $this->userModel->createUser($user_code, $username, $password, $full_name, $email, $phone, $role);
            header("Location: /user/index");
            exit();
        }
    }

    public function toggle() {
        if (isset($_GET['id']) && isset($_GET['status'])) {
            $id = $_GET['id'];
            $status = $_GET['status'];
            $this->userModel->toggleStatus($id, $status);
        }
        header("Location: /user/index");
        exit();
    }

    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $full_name = $_POST['full_name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $role = $_POST['role'];

            $this->userModel->updateUser($id, $full_name, $email, $phone, $role);
            header("Location: /user/index");
            exit();
        }
    }
}