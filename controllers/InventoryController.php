<?php
require_once 'models/InventoryModel.php';

class InventoryController
{
    private $db;
    private $inventoryModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->inventoryModel = new InventoryModel($this->db);

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
        $sheets = $this->inventoryModel->getAllChecks();

        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'sheets' => $sheets
            ]);
            exit();
        }

        require_once 'views/inventory/index.php';
    }

    public function create()
    {
        $variants = $this->inventoryModel->getAllActiveVariantsWithProduct();
        require_once 'views/inventory/create.php';
    }

    public function detail()
    {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $sheet = $this->inventoryModel->getCheckById($id);
            $details = $this->inventoryModel->getCheckDetails($id);
            header('Content-Type: application/json');
            echo json_encode(['sheet' => $sheet, 'details' => $details]);
            exit();
        }
    }

    public function add()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $check_code = 'PK' . rand(10000, 99999);
                $user_id = $_SESSION['user_id'];

                $variant_ids = $_POST['variant_id'] ?? [];
                $system_qtys = $_POST['system_qty'] ?? [];
                $actual_qtys = $_POST['actual_qty'] ?? [];
                $note = $_POST['note'] ?? '';

                $items = [];
                for ($i = 0; $i < count($variant_ids); $i++) {
                    if ($actual_qtys[$i] === '') continue;
                    $items[] = [
                        'variant_id' => (int)$variant_ids[$i],
                        'system_qty' => (int)$system_qtys[$i],
                        'actual_qty' => (int)$actual_qtys[$i]
                    ];
                }

                if (empty($items)) {
                    echo json_encode(['success' => false, 'message' => 'Lỗi: Phiếu kiểm kho phải nhập ít nhất một mặt hàng!']);
                    exit();
                }

                if ($this->inventoryModel->createCheckSheet($check_code, $user_id, $items, $note)) {
                    echo json_encode(['success' => true, 'message' => 'Tạo phiếu kiểm kho thành công, đang chờ duyệt!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Lỗi: Không thể lưu phiếu kiểm kho!']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
            }
            exit();
        }
    }

    public function approve()
    {
        header('Content-Type: application/json');
        if (isset($_GET['id'])) {
            try {
                $id = (int)$_GET['id'];
                $user_id = $_SESSION['user_id'];

                if ($this->inventoryModel->approveCheckSheet($id, $user_id)) {
                    echo json_encode(['success' => true, 'message' => 'Đã duyệt phiếu kiểm kho, số lượng tồn kho và thẻ kho đã được cập nhật!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Lỗi: Không thể duyệt phiếu!']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
            }
            exit();
        }
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ!']);
        exit();
    }

    public function logs()
    {
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $action_type = isset($_GET['action_type']) ? trim($_GET['action_type']) : '';
        $start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
        $end_date = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';

        $logs = $this->inventoryModel->getStockLogs($search, $action_type, $start_date, $end_date);

        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'logs' => $logs
            ]);
            exit();
        }

        require_once 'views/inventory/logs.php';
    }

    public function delete()
    {
        header('Content-Type: application/json');
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            if ($this->inventoryModel->deleteCheckSheet($id)) {
                echo json_encode(['success' => true, 'message' => 'Đã xóa bỏ phiếu kiểm kho nháp thành công!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi: Không thể xóa phiếu hoặc phiếu đã được duyệt trước đó!']);
            }
            exit();
        }
        echo json_encode(['success' => false, 'message' => 'Mã phiếu không hợp lệ!']);
        exit();
    }
}