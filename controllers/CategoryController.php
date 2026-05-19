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

        require_once 'views/categories/index.php';
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $category_code = $_POST['category_code'];
            $category_name = trim($_POST['category_name']);
            $description   = !empty($_POST['description']) ? $_POST['description'] : null;

            if ($this->categoryModel->isCategoryNameExists($category_name)) {
                $_SESSION['error_add_cat_name'] = "Tên danh mục sản phẩm này đã tồn tại trên hệ thống!";
                $_SESSION['old_add_cat_data'] = $_POST;
                header("Location: /category/index");
                exit();
            }

            if ($this->categoryModel->createCategory($category_code, $category_name, $description)) {
                $_SESSION['flash_success'] = "Thêm mới danh mục thành công!";
            } else {
                $_SESSION['flash_error'] = "Lỗi: Không thể thêm danh mục!";
            }
            header("Location: /category/index");
            exit();
        }
    }

    public function edit()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id            = $_POST['id'];
            $category_name = trim($_POST['category_name']);
            $description   = !empty($_POST['description']) ? $_POST['description'] : null;

            if ($this->categoryModel->isCategoryNameExists($category_name, $id)) {
                $_SESSION['error_edit_cat_id'] = $id;
                $_SESSION['error_edit_cat_msg'] = "Tên danh mục cập nhật đã tồn tại!";
                header("Location: /category/index");
                exit();
            }

            if ($this->categoryModel->updateCategory($id, $category_name, $description)) {
                $_SESSION['flash_success'] = "Cập nhật danh mục thành công!";
            } else {
                $_SESSION['flash_error'] = "Lỗi: Thao tác thất bại!";
            }
            header("Location: /category/index");
            exit();
        }
    }

    public function toggle()
    {
        if (isset($_GET['id']) && isset($_GET['status'])) {
            $id     = $_GET['id'];
            $status = $_GET['status'];
            if ($this->categoryModel->toggleStatus($id, $status)) {
                $action_text = ($status == 1) ? "Ẩn" : "Hiển thị lại";
                $_SESSION['flash_success'] = $action_text . " danh mục thành công!";
            } else {
                $_SESSION['flash_error'] = "Lỗi: Thao tác thất bại!";
            }
        }
        header("Location: /category/index");
        exit();
    }
}