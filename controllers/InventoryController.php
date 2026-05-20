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
            echo json_encode(['sheet' => $sheet, 'details' => $details]);
            exit();
        }
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
                $_SESSION['flash_error'] = "Lỗi: Phiếu kiểm kho phải nhập ít nhất một mặt hàng!";
                header("Location: /inventory/index");
                exit();
            }

            if ($this->inventoryModel->createCheckSheet($check_code, $user_id, $items, $note)) {
                $_SESSION['flash_success'] = "Tạo phiếu kiểm kho thành công, đang chờ duyệt!";
            } else {
                $_SESSION['flash_error'] = "Lỗi: Không thể lưu phiếu kiểm kho!";
            }
            header("Location: /inventory/index");
            exit();
        }
    }

    public function approve()
    {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $user_id = $_SESSION['user_id'];

            if ($this->inventoryModel->approveCheckSheet($id, $user_id)) {
                $_SESSION['flash_success'] = "Đã duyệt phiếu kiểm kho, số lượng tồn kho và thẻ kho đã được cập nhật!";
            } else {
                $_SESSION['flash_error'] = "Lỗi: Không thể duyệt phiếu!";
            }
        }
        header("Location: /inventory/index");
        exit();
    }

    public function logs()
    {
        $logs = $this->inventoryModel->getStockLogs();
        require_once 'views/inventory/logs.php';
    }

    public function delete()
    {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            if ($this->inventoryModel->deleteCheckSheet($id)) {
                $_SESSION['flash_success'] = "Đã xóa bỏ phiếu kiểm kho nháp thành công!";
            } else {
                $_SESSION['flash_error'] = "Lỗi: Không thể xóa phiếu hoặc phiếu đã được duyệt trước đó!";
            }
        }
        header("Location: /inventory/index");
        exit();
    }
}
