<?php
class DashboardController
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();

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
        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            header('Content-Type: application/json');
            
            $filter_type = $_GET['filter_type'] ?? '7days';
            $where_time = " WHERE 1=1 ";
            $where_chart = " WHERE 1=1 ";
            $where_top = " WHERE 1=1 ";

            if ($filter_type === 'today') {
                $where_time  .= " AND DATE(created_at) = CURDATE() ";
                $where_chart .= " AND DATE(created_at) = CURDATE() ";
                $where_top   .= " AND DATE(o.created_at) = CURDATE() ";
                $group_chart = "DATE_FORMAT(created_at, '%H:00')";
                $order_chart = "MIN(created_at) ASC";
            } elseif ($filter_type === 'this_month') {
                $current_year_month = date('Y-m');
                $where_time  .= " AND DATE_FORMAT(created_at, '%Y-%m') = '$current_year_month' ";
                $where_chart .= " AND DATE_FORMAT(created_at, '%Y-%m') = '$current_year_month' ";
                $where_top   .= " AND DATE_FORMAT(o.created_at, '%Y-%m') = '$current_year_month' ";
                $group_chart = "DATE_FORMAT(created_at, '%d/%m')";
                $order_chart = "MIN(created_at) ASC";
            } elseif ($filter_type === 'last_month') {
                $last_year_month = date('Y-m', strtotime('-1 month'));
                $where_time  .= " AND DATE_FORMAT(created_at, '%Y-%m') = '$last_year_month' ";
                $where_chart .= " AND DATE_FORMAT(created_at, '%Y-%m') = '$last_year_month' ";
                $where_top   .= " AND DATE_FORMAT(o.created_at, '%Y-%m') = '$last_year_month' ";
                $group_chart = "DATE_FORMAT(created_at, '%d/%m')";
                $order_chart = "MIN(created_at) ASC";
            } else {
                $where_time  .= " AND created_at >= SUBDATE(CURDATE(), INTERVAL 6 DAY) ";
                $where_chart .= " AND created_at >= SUBDATE(CURDATE(), INTERVAL 6 DAY) ";
                $where_top   .= " AND o.created_at >= SUBDATE(CURDATE(), INTERVAL 6 DAY) ";
                $group_chart = "DATE_FORMAT(created_at, '%d/%m')";
                $order_chart = "MIN(created_at) ASC";
            }

            $q_cards = "SELECT 
                            (SELECT COUNT(*) FROM orders $where_time) as today_orders,
                            (SELECT IFNULL(SUM(total_amount), 0) FROM orders $where_time) as today_revenue,
                            (SELECT COUNT(*) FROM product_variants WHERE stock_qty <= low_stock_threshold) as alert_products,
                            (SELECT COUNT(*) FROM customers WHERE debt > 0) as debt_customers";
            $s_cards = $this->db->prepare($q_cards);
            $s_cards->execute();
            $cards = $s_cards->fetch(PDO::FETCH_ASSOC);

            $q_chart = "SELECT $group_chart as date_label, SUM(total_amount) as daily_revenue 
                        FROM orders 
                        $where_chart
                        GROUP BY $group_chart
                        ORDER BY $order_chart";
            $s_chart = $this->db->prepare($q_chart);
            $s_chart->execute();
            $chart_data = $s_chart->fetchAll(PDO::FETCH_ASSOC);

            $q_top = "SELECT pv.variant_name, p.product_name, SUM(od.quantity) as total_sold, SUM(od.price) as total_revenue
                      FROM order_details od
                      JOIN product_variants pv ON od.variant_id = pv.id
                      JOIN products p ON pv.product_id = p.id
                      JOIN orders o ON od.order_id = o.id
                      $where_top
                      GROUP BY od.variant_id, pv.variant_name, p.product_name
                      ORDER BY total_sold DESC
                      LIMIT 5";
            $s_top = $this->db->prepare($q_top);
            $s_top->execute();
            $top_products = $s_top->fetchAll(PDO::FETCH_ASSOC);

            $q_stock = "SELECT pv.variant_name, pv.stock_qty, pv.low_stock_threshold, p.product_name, p.product_code
                        FROM product_variants pv
                        JOIN products p ON pv.product_id = p.id
                        WHERE pv.stock_qty <= pv.low_stock_threshold
                        ORDER BY pv.stock_qty ASC
                        LIMIT 5";
            $s_stock = $this->db->prepare($q_stock);
            $s_stock->execute();
            $stock_alerts = $s_stock->fetchAll(PDO::FETCH_ASSOC);

            $q_recent_orders = "SELECT o.id, o.order_code, o.total_amount, o.created_at, 
                                       IFNULL(c.full_name, 'Khách vãng lai') as customer_name, u.username
                                FROM orders o
                                LEFT JOIN customers c ON o.customer_id = c.id
                                JOIN users u ON o.user_id = u.id
                                ORDER BY o.id DESC
                                LIMIT 5";
            $s_recent = $this->db->prepare($q_recent_orders);
            $s_recent->execute();
            $recent_orders = $s_recent->fetchAll(PDO::FETCH_ASSOC);

            $q_top_debts = "SELECT id, customer_code, full_name, phone, debt 
                            FROM customers 
                            WHERE debt > 0 
                            ORDER BY debt DESC 
                            LIMIT 5";
            $s_debts = $this->db->prepare($q_top_debts);
            $s_debts->execute();
            $top_debts = $s_debts->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'cards' => $cards,
                'chart' => $chart_data,
                'top_products' => $top_products,
                'stock_alerts' => $stock_alerts,
                'recent_orders' => $recent_orders,
                'top_debts' => $top_debts
            ]);
            exit();
        }

        require_once 'views/dashboard/index.php';
    }

    public function fetchSaleOrderDetail()
    {
        if (isset($_GET['id'])) {
            header('Content-Type: application/json');
            $id = (int)$_GET['id'];

            try {
                $q_order = "SELECT o.order_code, o.total_amount, o.customer_paid, o.debt, o.pay_method, o.created_at,
                                   IFNULL(c.full_name, 'Khách vãng lai') as customer_name, IFNULL(c.phone, '---') as customer_phone, u.username
                            FROM orders o
                            LEFT JOIN customers c ON o.customer_id = c.id
                            JOIN users u ON o.user_id = u.id
                            WHERE o.id = :id";
                $s_order = $this->db->prepare($q_order);
                $s_order->bindParam(':id', $id, PDO::PARAM_INT);
                $s_order->execute();
                $order = $s_order->fetch(PDO::FETCH_ASSOC);

                $q_details = "SELECT od.quantity, od.price as total_line_price, pv.variant_name, p.product_name 
                              FROM order_details od
                              JOIN product_variants pv ON od.variant_id = pv.id
                              JOIN products p ON pv.product_id = p.id
                              WHERE od.order_id = :id";
                $s_details = $this->db->prepare($q_details);
                $s_details->bindParam(':id', $id, PDO::PARAM_INT);
                $s_details->execute();
                $details = $s_details->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode(['success' => true, 'order' => $order, 'details' => $details]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit();
        }
    }
}