<?php
require_once 'config/database.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/UserController.php';
require_once 'controllers/CustomerController.php';
require_once 'controllers/CategoryController.php';
require_once 'controllers/ProductController.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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
$controller_name = isset($url_parts[0]) && $url_parts[0] != '' ? $url_parts[0] : 'home';
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

if ($controller_name == 'home' || $controller_name == '') {
    header("Location: /user/index");
    exit();
} else {
    echo "404 Not Found";
}
