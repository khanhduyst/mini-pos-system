<?php
require_once 'models/CategoryModel.php';

class CategoryController
{
    private $db;
    private $categoryModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
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

        $total_categories = $this->categoryModel->countCategoriesWithFilter($search, $status);
        $total_pages = ceil($total_categories / $limit);

        $categories = $this->categoryModel->getCategoriesWithFilter($search, $status, $limit, $offset);

        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'categories' => $categories,
                'total_pages' => $total_pages,
                'current_page' => $current_page
            ]);
            exit();
        }

        require_once 'views/categories/index.php';
    }

    public function add()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $category_code = $_POST['category_code'] ?? '';
            $category_name = trim($_POST['category_name'] ?? '');
            $description   = !empty($_POST['description']) ? $_POST['description'] : null;

            if ($this->categoryModel->isCategoryNameExists($category_name)) {
                echo json_encode([
                    'success' => false,
                    'error_type' => 'category_name',
                    'message' => 'Tên danh mục sản phẩm này đã tồn tại trên hệ thống!'
                ]);
                exit();
            }

            if ($this->categoryModel->createCategory($category_code, $category_name, $description)) {
                echo json_encode(['success' => true, 'message' => 'Thêm mới danh mục thành công!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi: Không thể thêm danh mục mới!']);
            }
            exit();
        }
    }

    public function edit()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id            = $_POST['id'] ?? 0;
            $category_name = trim($_POST['category_name'] ?? '');
            $description   = !empty($_POST['description']) ? $_POST['description'] : null;

            if ($this->categoryModel->isCategoryNameExists($category_name, $id)) {
                echo json_encode([
                    'success' => false,
                    'error_type' => 'category_name',
                    'message' => 'Tên danh mục cập nhật đã tồn tại!'
                ]);
                exit();
            }

            if ($this->categoryModel->updateCategory($id, $category_name, $description)) {
                echo json_encode(['success' => true, 'message' => 'Cập nhật danh mục thành công!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi: Thao tác cập nhật thất bại!']);
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
            if ($this->categoryModel->toggleStatus($id, $status)) {
                $action_text = ($status == 1) ? "Ẩn" : "Hiển thị lại";
                echo json_encode([
                    'success' => true,
                    'message' => $action_text . " danh mục thành công!"
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi: Thao tác đổi trạng thái thất bại!']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ!']);
        }
        exit();
    }
}