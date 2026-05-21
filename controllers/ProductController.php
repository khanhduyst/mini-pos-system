<?php
require_once 'models/ProductModel.php';

class ProductController
{
    private $db;
    private $productModel;
    private $cloud_name   = 'dnjbvgejr';
    private $upload_preset = 'miniPosSystem_upload';

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->productModel = new ProductModel($this->db);

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
        $search      = isset($_GET['search']) ? trim($_GET['search']) : '';
        $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';

        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($current_page < 1) $current_page = 1;
        $limit = 10;
        $offset = ($current_page - 1) * $limit;

        $total_products = $this->productModel->countProductsWithFilter($search, $category_id);
        $total_pages = ceil($total_products / $limit);

        $products = $this->productModel->getProductsWithFilter($search, $category_id, $limit, $offset);
        $categories = $this->productModel->getAllActiveCategories();

        require_once 'views/products/index.php';
    }

    private function uploadToCloudinary($file_tmp, $product_code)
    {
        if (!file_exists($file_tmp)) {
            return null;
        }

        $url = "https://api.cloudinary.com/v1_1/" . $this->cloud_name . "/image/upload";

        // TỐI ƯU 1: Lấy Mime-Type chuẩn bằng cách kiểm tra phần mở rộng hoặc ép cứng image/jpeg phòng khi hàm lỗi
        $mime = mime_content_type($file_tmp);
        if ($mime == 'application/octet-stream') {
            $mime = 'image/jpeg'; // Ép kiểu an toàn để Cloudinary không chặn file tạm trên Windows
        }

        // Đặt tên file hiển thị trên Cloudinary
        $post_filename = $product_code . "_" . time();
        $cfile = new CURLFile($file_tmp, $mime, $post_filename);

        $data = [
            'file'          => $cfile,
            'upload_preset' => $this->upload_preset,
            'folder'        => 'mini_pos_products'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // Truyền mảng chứa CURLFile chuẩn
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        // Nếu cURL lỗi đường truyền kết nối mạng
        if ($err) {
            return null;
        }

        $result = json_decode($response, true);

        // TỐI ƯU 2: BẮT BỆNH ẨN TỪ CLOUDINARY
        // Nếu API Cloudinary trả về lỗi (Ví dụ: Sai upload_preset, sai cloud_name), nó sẽ trả về mảng có key ['error']
        if (isset($result['error'])) {
            // Dừng hệ thống hiển thị thẳng thông báo lỗi của Cloudinary để bác đọc bệnh (Ví dụ: "Invalid Upload Preset")
            die("Lỗi từ server Cloudinary: " . $result['error']['message']);
        }

        return isset($result['secure_url']) ? $result['secure_url'] : null;
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $product_code      = trim($_POST['product_code']);
            $product_name      = trim($_POST['product_name']);
            $short_description = !empty($_POST['short_description']) ? trim($_POST['short_description']) : null;
            $category_id       = $_POST['category_id'];

            $v_names     = $_POST['v_name'] ?? [];
            $barcodes    = $_POST['v_barcode'] ?? [];
            $cost_prices = $_POST['v_cost'] ?? [];
            $sale_prices = $_POST['v_sale'] ?? [];
            $stock_qtys  = $_POST['v_stock'] ?? [];
            $limits      = $_POST['v_limit'] ?? [];

            $variants = [];
            for ($i = 0; $i < count($v_names); $i++) {
                if (empty(trim($v_names[$i]))) continue;
                $variants[] = [
                    'variant_name'        => trim($v_names[$i]),
                    'barcode'             => !empty($barcodes[$i]) ? trim($barcodes[$i]) : null,
                    'cost_price'          => (float)$cost_prices[$i],
                    'sale_price'          => (float)$sale_prices[$i],
                    'stock_qty'           => (int)$stock_qtys[$i],
                    'low_stock_threshold' => !empty($limits[$i]) ? (int)$limits[$i] : 10
                ];
            }

            if ($this->productModel->isCodeOrBarcodeExists($product_code, $variants)) {
                $_SESSION['error_add_prod'] = "Mã hàng gốc hoặc mã vạch barcode nhập vào đã tồn tại trên kệ!";
                $_SESSION['old_add_prod_data'] = $_POST;
                header("Location: /product/index");
                exit();
            }

            $image_url = 'https://res.cloudinary.com/dnjbvgejr/image/upload/v1779205656/09b31927-1b26-4980-9463-77b005a9cd38_e5l0iy.png';

            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
                $uploaded_url = $this->uploadToCloudinary($_FILES['product_image']['tmp_name'], $product_code);
                if ($uploaded_url !== null) {
                    $image_url = $uploaded_url;
                }
            }

            if ($this->productModel->createProductWithVariants($product_code, $product_name, $image_url, $short_description, $category_id, $variants)) {
                $_SESSION['flash_success'] = "Thêm hàng hóa đa quy cách mới thành công!";
            } else {
                $_SESSION['flash_error'] = "Lỗi hệ thống không lưu được dữ liệu!";
            }
            header("Location: /product/index");
            exit();
        }
    }

    public function edit()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id                = $_POST['id'];
            $product_code      = trim($_POST['product_code']);
            $product_name      = trim($_POST['product_name']);
            $short_description = !empty($_POST['short_description']) ? trim($_POST['short_description']) : null;
            $category_id       = $_POST['category_id'];

            $v_names     = $_POST['v_name'] ?? [];
            $barcodes    = $_POST['v_barcode'] ?? [];
            $cost_prices = $_POST['v_cost'] ?? [];
            $sale_prices = $_POST['v_sale'] ?? [];
            $stock_qtys  = $_POST['v_stock'] ?? [];
            $limits      = $_POST['v_limit'] ?? [];

            $variants = [];
            for ($i = 0; $i < count($v_names); $i++) {
                if (empty(trim($v_names[$i]))) continue;
                $variants[] = [
                    'variant_name'        => trim($v_names[$i]),
                    'barcode'             => !empty($barcodes[$i]) ? trim($barcodes[$i]) : null,
                    'cost_price'          => (float)$cost_prices[$i],
                    'sale_price'          => (float)$sale_prices[$i],
                    'stock_qty'           => (int)$stock_qtys[$i],
                    'low_stock_threshold' => !empty($limits[$i]) ? (int)$limits[$i] : 10
                ];
            }

            if ($this->productModel->isCodeOrBarcodeExists($product_code, $variants, $id)) {
                $_SESSION['error_edit_prod_id'] = $id;
                $_SESSION['error_edit_prod_msg'] = "Mã hàng hoặc mã vạch cập nhật bị trùng lặp ở sản phẩm khác!";
                header("Location: /product/index");
                exit();
            }

            $image_url = !empty($_POST['current_image']) ? $_POST['current_image'] : 'https://res.cloudinary.com/dnjbvgejr/image/upload/v1779205656/09b31927-1b26-4980-9463-77b005a9cd38_e5l0iy.png';
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
                $new_image_url = $this->uploadToCloudinary($_FILES['product_image']['tmp_name'], $product_code);
                if ($new_image_url !== null) {
                    $image_url = $new_image_url;
                }
            }

            if ($this->productModel->updateProductWithVariants($id, $product_name, $image_url, $short_description, $category_id, $variants)) {
                $_SESSION['flash_success'] = "Cập nhật thông tin quy cách sản phẩm thành công!";
            } else {
                $_SESSION['flash_error'] = "Lỗi: Cập nhật thất bại!";
            }
            header("Location: /product/index");
            exit();
        }
    }

    public function toggle()
    {
        if (isset($_GET['id']) && isset($_GET['status'])) {
            $id = (int)$_GET['id'];
            $status = (int)$_GET['status'];
            if ($this->productModel->toggleStatus($id, $status)) {
                $_SESSION['flash_success'] = "Thay đổi trạng thái kinh doanh thành công!";
            } else {
                $_SESSION['flash_error'] = "Lỗi: Thao tác thất bại!";
            }
        }
        header("Location: /product/index");
        exit();
    }

    public function delete()
    {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            if ($this->productModel->deleteProduct($id)) {
                $_SESSION['flash_success'] = "Đã xóa sản phẩm và ngừng nhập vĩnh viễn!";
            } else {
                $_SESSION['flash_error'] = "Lỗi: Không thể xóa sản phẩm!";
            }
        }
        header("Location: /product/index");
        exit();
    }
}