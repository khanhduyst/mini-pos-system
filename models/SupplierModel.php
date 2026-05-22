<?php
class SupplierModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllSuppliers($search = '')
    {
        $query = "SELECT * FROM suppliers WHERE 1=1";
        if (!empty($search)) {
            $query .= " AND (supplier_name LIKE :search1 OR supplier_code LIKE :search2 OR phone LIKE :search3)";
        }
        $query .= " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        if (!empty($search)) {
            $searchTerm = "%{$search}%";
            $stmt->bindParam(':search1', $searchTerm);
            $stmt->bindParam(':search2', $searchTerm);
            $stmt->bindParam(':search3', $searchTerm);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSupplierById($id)
    {
        $query = "SELECT * FROM suppliers WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isSupplierCodeOrPhoneExists($code, $phone, $exclude_id = null)
    {
        $query = "SELECT COUNT(*) as total FROM suppliers WHERE (supplier_code = :code OR phone = :phone)";
        if ($exclude_id !== null) {
            $query .= " AND id != :exclude_id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':phone', $phone);
        if ($exclude_id !== null) {
            $stmt->bindParam(':exclude_id', $exclude_id, PDO::PARAM_INT);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row['total'] ?? 0) > 0;
    }

    public function createSupplier($code, $name, $phone, $email, $address)
    {
        $query = "INSERT INTO suppliers (supplier_code, supplier_name, phone, email, address, status) 
                  VALUES (:code, :name, :phone, :email, :address, 1)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':address', $address);
        return $stmt->execute();
    }

    public function updateSupplier($id, $name, $phone, $email, $address)
    {
        $query = "UPDATE suppliers SET supplier_name = :name, phone = :phone, email = :email, address = :address WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function toggleStatus($id, $status)
    {
        $new_status = ($status == 1) ? 0 : 1;
        $query = "UPDATE suppliers SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $new_status, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getAllPurchaseOrders($search = '', $start_date = '', $end_date = '')
    {
        $query = "SELECT po.*, s.supplier_name, u.username FROM purchase_orders po
                  JOIN suppliers s ON po.supplier_id = s.id
                  JOIN users u ON po.user_id = u.id
                  WHERE 1=1";

        if (!empty($search)) {
            $query .= " AND (po.purchase_code LIKE :search1 OR s.supplier_name LIKE :search2)";
        }
        if (!empty($start_date)) {
            $query .= " AND DATE(po.created_at) >= :start_date";
        }
        if (!empty($end_date)) {
            $query .= " AND DATE(po.created_at) <= :end_date";
        }

        $query .= " ORDER BY po.id DESC";
        $stmt = $this->conn->prepare($query);

        if (!empty($search)) {
            $searchTerm = "%{$search}%";
            $stmt->bindParam(':search1', $searchTerm);
            $stmt->bindParam(':search2', $searchTerm);
        }
        if (!empty($start_date)) {
            $stmt->bindParam(':start_date', $start_date);
        }
        if (!empty($end_date)) {
            $stmt->bindParam(':end_date', $end_date);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPurchaseOrderById($id)
    {
        $query = "SELECT po.*, s.supplier_name, s.phone, s.address, u.username FROM purchase_orders po
                  JOIN suppliers s ON po.supplier_id = s.id
                  JOIN users u ON po.user_id = u.id
                  WHERE po.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPurchaseOrderDetails($order_id)
    {
        $query = "SELECT pod.*, pv.variant_name, pv.barcode, p.product_name, p.product_code 
                  FROM purchase_order_details pod
                  JOIN product_variants pv ON pod.product_variant_id = pv.id
                  JOIN products p ON pv.product_id = p.id
                  WHERE pod.purchase_order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createPurchaseOrder($code, $supplier_id, $user_id, $items, $note)
    {
        try {
            $this->conn->beginTransaction();
            $total_amount = 0;
            foreach ($items as $item) {
                $total_amount += $item['quantity'] * $item['import_price'];
            }

            $query = "INSERT INTO purchase_orders (purchase_code, supplier_id, user_id, total_amount, status, note) 
                      VALUES (:code, :supplier_id, :user_id, :total_amount, 0, :note)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':code', $code);
            $stmt->bindParam(':supplier_id', $supplier_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':total_amount', $total_amount);
            $stmt->bindParam(':note', $note);
            $stmt->execute();

            $order_id = $this->conn->lastInsertId();
            $query_d = "INSERT INTO purchase_order_details (purchase_order_id, product_variant_id, quantity, import_price, amount) 
                        VALUES (:order_id, :v_id, :qty, :price, :amount)";
            $stmt_d = $this->conn->prepare($query_d);

            foreach ($items as $item) {
                $amount = $item['quantity'] * $item['import_price'];
                $stmt_d->bindParam(':order_id', $order_id, PDO::PARAM_INT);
                $stmt_d->bindParam(':v_id', $item['variant_id'], PDO::PARAM_INT);
                $stmt_d->bindParam(':qty', $item['quantity'], PDO::PARAM_INT);
                $stmt_d->bindParam(':price', $item['import_price']);
                $stmt_d->bindParam(':amount', $amount);
                $stmt_d->execute();
            }
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function approvePurchaseOrder($id, $user_id)
    {
        try {
            $this->conn->beginTransaction();
            $order = $this->getPurchaseOrderById($id);
            if (!$order || $order['status'] == 1) {
                $this->conn->rollBack(); return false;
            }
            $details = $this->getPurchaseOrderDetails($id);

            foreach ($details as $d) {
                $q_curr = "SELECT stock_qty FROM product_variants WHERE id = :v_id";
                $s_curr = $this->conn->prepare($q_curr);
                $s_curr->bindParam(':v_id', $d['product_variant_id'], PDO::PARAM_INT);
                $s_curr->execute();
                $old_qty = $s_curr->fetch(PDO::FETCH_ASSOC)['stock_qty'] ?? 0;

                $new_qty = $old_qty + $d['quantity'];
                $q_up = "UPDATE product_variants SET stock_qty = :new_qty, cost_price = :import_price WHERE id = :v_id";
                $s_up = $this->conn->prepare($q_up);
                $s_up->bindParam(':new_qty', $new_qty, PDO::PARAM_INT);
                $s_up->bindParam(':import_price', $d['import_price']);
                $s_up->bindParam(':v_id', $d['product_variant_id'], PDO::PARAM_INT);
                $s_up->execute();

                $q_log = "INSERT INTO stock_logs (product_variant_id, user_id, action_type, reference_code, old_qty, change_qty, new_qty) 
                          VALUES (:v_id, :user_id, 'IMPORT', :ref, :old, :change, :new)";
                $s_log = $this->conn->prepare($q_log);
                $s_log->bindParam(':v_id', $d['product_variant_id'], PDO::PARAM_INT);
                $s_log->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $s_log->bindParam(':ref', $order['purchase_code']);
                $s_log->bindParam(':old', $old_qty, PDO::PARAM_INT);
                $s_log->bindParam(':change', $d['quantity'], PDO::PARAM_INT);
                $s_log->bindParam(':new', $new_qty, PDO::PARAM_INT);
                $s_log->execute();
            }

            $q_status = "UPDATE purchase_orders SET status = 1 WHERE id = :id";
            $s_status = $this->conn->prepare($q_status);
            $s_status->bindParam(':id', $id, PDO::PARAM_INT);
            $s_status->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function deletePurchaseOrder($id)
    {
        try {
            $this->conn->beginTransaction();
            $query_d = "DELETE FROM purchase_order_details WHERE purchase_order_id = :id";
            $stmt_d = $this->conn->prepare($query_d);
            $stmt_d->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_d->execute();

            $query_m = "DELETE FROM purchase_orders WHERE id = :id AND status = 0";
            $stmt_m = $this->conn->prepare($query_m);
            $stmt_m->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_m->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}