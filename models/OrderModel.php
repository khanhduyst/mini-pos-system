<?php
class OrderModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAllOrders()
    {
        $query = "SELECT o.*, c.full_name AS customer_name, c.phone AS customer_phone, u.full_name AS staff_name 
                  FROM orders o 
                  LEFT JOIN customers c ON o.customer_id = c.id 
                  LEFT JOIN users u ON o.user_id = u.id 
                  ORDER BY o.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderDetails($order_id)
    {
        $query = "SELECT od.*, p.product_name, v.variant_name, v.barcode 
                  FROM order_details od
                  JOIN product_variants v ON od.variant_id = v.id
                  JOIN products p ON v.product_id = p.id
                  WHERE od.order_id = :order_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}