<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/UserController.php';
require_once 'controllers/CustomerController.php';
require_once 'controllers/CategoryController.php';
require_once 'controllers/ProductController.php';
require_once 'controllers/PosController.php';
require_once 'controllers/OrderController.php';
require_once 'controllers/SupplierController.php';
require_once 'controllers/DashboardController.php';

$database = new Database();
$db = $database->getConnection();

$request_uri = $_SERVER['REQUEST_URI'];
$url_path = parse_url($request_uri, PHP_URL_PATH);
$url_path = trim($url_path, '/');

if ($url_path == 'test_db.php') {
    require_once 'test_db.php';
    exit();
}

if ($url_path == 'create_admin.php') {
    require_once 'create_admin.php';
    exit();
}

$url_parts = explode('/', $url_path);
$controller_name = isset($url_parts[0]) && $url_parts[0] != '' ? $url_parts[0] : 'dashboard';
$action_name = isset($url_parts[1]) && $url_parts[1] != '' ? $url_parts[1] : 'index';

if ($controller_name == 'auth') {
    $controller = new AuthController();
    if ($action_name == 'login') {
        $controller->login();
    } else if ($action_name == 'logout') {
        $controller->logout();
    } else {
        echo "404 Not Found";
    }
    exit();
}

if (!isset($_SESSION['user_id'])) {
    $controller = new AuthController();
    $controller->login();
    exit();
}

if ($controller_name == 'dashboard') {
    $controller = new DashboardController();
    if ($action_name == 'index') {
        $controller->index();
    } else if ($action_name == 'fetchSaleOrderDetail') {
        $controller->fetchSaleOrderDetail();
    } else {
        echo "404 Not Found";
    }
    exit();
}

if ($controller_name == 'pos') {
    $controller = new PosController();
    if ($action_name == 'index') {
        $controller->index();
    } else if ($action_name == 'checkout') {
        $controller->checkout();
    } else {
        echo "404 Not Found";
    }
    exit();
}

if ($controller_name == 'user') {
    $controller = new UserController();
    if ($action_name == 'index') {
        $controller->index();
    } else if ($action_name == 'add') {
        $controller->add();
    } else if ($action_name == 'edit') {
        $controller->edit();
    } else if ($action_name == 'toggle') {
        $controller->toggle();
    } else if ($action_name == 'profile') {
        $controller->profile();
    } else if ($action_name == 'changePassword') {
        $controller->changePassword();
    } else if ($action_name == 'resetPassword') {
        $controller->resetPassword();
    } else {
        echo "404 Not Found";
    }
    exit();
}

if ($controller_name == 'customer') {
    $controller = new CustomerController();
    if ($action_name == 'index') {
        $controller->index();
    } else if ($action_name == 'add') {
        $controller->add();
    } else if ($action_name == 'edit') {
        $controller->edit();
    } else if ($action_name == 'payDebt') {
        $controller->payDebt();
    } else if ($action_name == 'toggle') {
        $controller->toggle();
    } else {
        echo "404 Not Found";
    }
    exit();
}

if ($controller_name == 'category') {
    $controller = new CategoryController();
    if ($action_name == 'index') {
        $controller->index();
    } else if ($action_name == 'add') {
        $controller->add();
    } else if ($action_name == 'edit') {
        $controller->edit();
    } else if ($action_name == 'toggle') {
        $controller->toggle();
    } else {
        echo "404 Not Found";
    }
    exit();
}

if ($controller_name == 'product') {
    $controller = new ProductController();
    if ($action_name == 'index') {
        $controller->index();
    } else if ($action_name == 'add') {
        $controller->add();
    } else if ($action_name == 'edit') {
        $controller->edit();
    } else if ($action_name == 'toggle') {
        $controller->toggle();
    } else if ($action_name == 'delete') {
        $controller->delete();
    } else {
        echo "404 Not Found";
    }
    exit();
}

if ($controller_name == 'inventory') {
    require_once 'controllers/InventoryController.php';
    $controller = new InventoryController();
    if ($action_name == 'index') {
        $controller->index();
    } else if ($action_name == 'create') {
        $controller->create();
    } else if ($action_name == 'add') {
        $controller->add();
    } else if ($action_name == 'detail') {
        $controller->detail();
    } else if ($action_name == 'approve') {
        $controller->approve();
    } else if ($action_name == 'logs') {
        $controller->logs();
    } else if ($action_name == 'delete') {
        $controller->delete();
    } else {
        echo "404 Not Found";
    }
    exit();
}

if ($controller_name == 'supplier') {
    $controller = new SupplierController();
    if ($action_name == 'index') {
        $controller->index();
    } else if ($action_name == 'addSupplier') {
        $controller->addSupplier();
    } else if ($action_name == 'editSupplier') {
        $controller->editSupplier();
    } else if ($action_name == 'toggleSupplier') {
        $controller->toggleSupplier();
    } else if ($action_name == 'orders') {
        $controller->orders();
    } else if ($action_name == 'createOrder') {
        $controller->createOrder();
    } else if ($action_name == 'orderDetail') {
        $controller->orderDetail();
    } else if ($action_name == 'addOrder') {
        $controller->addOrder();
    } else if ($action_name == 'approveOrder') {
        $controller->approveOrder();
    } else if ($action_name == 'deleteOrder') {
        $controller->deleteOrder();
    } else {
        echo "404 Not Found";
    }
    exit();
}

if ($controller_name == 'order') {
    $controller = new OrderController();
    if ($action_name == 'index') {
        $controller->index();
    } else if ($action_name == 'detail') {
        $controller->detail();
    } else {
        echo "404 Not Found";
    }
    exit();
}

if ($controller_name == 'home' || $controller_name == '') {
    header("Location: /dashboard/index");
    exit();
} else {
    echo "404 Not Found";
}
