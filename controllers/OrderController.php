<?php
require_once 'models/OrderModel.php';

class OrderController
{
    private $db;
    private $orderModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->orderModel = new OrderModel($this->db);

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
        $orders = $this->orderModel->getAllOrders();
        require_once 'views/order/index.php';
    }

    public function detail()
    {
        header('Content-Type: application/json');
        $order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($order_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Mã đơn hàng không hợp lệ!']);
            exit();
        }

        $details = $this->orderModel->getOrderDetails($order_id);
        echo json_encode(['success' => true, 'details' => $details]);
        exit();
    }
}