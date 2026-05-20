<?php
class InventoryModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllChecks()
    {
        $query = "SELECT ic.*, u.username as fullname FROM inventory_checks ic 
                  JOIN users u ON ic.user_id = u.id 
                  ORDER BY ic.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCheckById($id)
    {
        $query = "SELECT ic.*, u.username as fullname FROM inventory_checks ic 
                  JOIN users u ON ic.user_id = u.id 
                  WHERE ic.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCheckDetails($check_id)
    {
        $query = "SELECT icd.*, pv.variant_name, pv.barcode, p.product_name, p.product_code 
                  FROM inventory_check_details icd
                  JOIN product_variants pv ON icd.product_variant_id = pv.id
                  JOIN products p ON pv.product_id = p.id
                  WHERE icd.inventory_check_id = :check_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':check_id', $check_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllActiveVariantsWithProduct()
    {
        $query = "SELECT pv.id as variant_id, pv.variant_name, pv.barcode, pv.stock_qty, p.product_name, p.product_code 
                  FROM product_variants pv
                  JOIN products p ON pv.product_id = p.id
                  WHERE p.status = 1
                  ORDER BY p.product_name ASC, pv.id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createCheckSheet($check_code, $user_id, $items, $note)
    {
        try {
            $this->conn->beginTransaction();

            $query = "INSERT INTO inventory_checks (check_code, user_id, status, note) VALUES (:code, :user_id, 0, :note)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':code', $check_code);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':note', $note);
            $stmt->execute();

            $check_id = $this->conn->lastInsertId();

            $query_d = "INSERT INTO inventory_check_details (inventory_check_id, product_variant_id, system_qty, actual_qty, variance) 
                        VALUES (:check_id, :v_id, :sys_qty, :act_qty, :variance)";
            $stmt_d = $this->conn->prepare($query_d);

            foreach ($items as $item) {
                $variance = $item['actual_qty'] - $item['system_qty'];
                $stmt_d->bindParam(':check_id', $check_id, PDO::PARAM_INT);
                $stmt_d->bindParam(':v_id', $item['variant_id'], PDO::PARAM_INT);
                $stmt_d->bindParam(':sys_qty', $item['system_qty'], PDO::PARAM_INT);
                $stmt_d->bindParam(':act_qty', $item['actual_qty'], PDO::PARAM_INT);
                $stmt_d->bindParam(':variance', $variance, PDO::PARAM_INT);
                $stmt_d->execute();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            die($e->getMessage());
        }
    }

    public function approveCheckSheet($id, $user_id)
    {
        try {
            $this->conn->beginTransaction();

            $details = $this->getCheckDetails($id);
            $check = $this->getCheckById($id);

            foreach ($details as $d) {
                $q_curr = "SELECT stock_qty FROM product_variants WHERE id = :v_id";
                $s_curr = $this->conn->prepare($q_curr);
                $s_curr->bindParam(':v_id', $d['product_variant_id'], PDO::PARAM_INT);
                $s_curr->execute();
                $old_qty = $s_curr->fetch(PDO::FETCH_ASSOC)['stock_qty'] ?? 0;

                $q_up = "UPDATE product_variants SET stock_qty = :act_qty WHERE id = :v_id";
                $s_up = $this->conn->prepare($q_up);
                $s_up->bindParam(':act_qty', $d['actual_qty'], PDO::PARAM_INT);
                $s_up->bindParam(':v_id', $d['product_variant_id'], PDO::PARAM_INT);
                $s_up->execute();

                $q_log = "INSERT INTO stock_logs (product_variant_id, user_id, action_type, reference_code, old_qty, change_qty, new_qty) 
                          VALUES (:v_id, :user_id, 'ADJUST', :ref, :old, :change, :new)";
                $s_log = $this->conn->prepare($q_log);
                $s_log->bindParam(':v_id', $d['product_variant_id'], PDO::PARAM_INT);
                $s_log->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $s_log->bindParam(':ref', $check['check_code']);
                $s_log->bindParam(':old', $old_qty, PDO::PARAM_INT);
                $s_log->bindParam(':change', $d['variance'], PDO::PARAM_INT);
                $s_log->bindParam(':new', $d['actual_qty'], PDO::PARAM_INT);
                $s_log->execute();
            }

            $q_status = "UPDATE inventory_checks SET status = 1 WHERE id = :id";
            $s_status = $this->conn->prepare($q_status);
            $s_status->bindParam(':id', $id, PDO::PARAM_INT);
            $s_status->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            die($e->getMessage());
        }
    }

    public function getStockLogs()
    {
        $query = "SELECT sl.*, pv.variant_name, p.product_name, p.product_code, u.username as fullname 
                  FROM stock_logs sl
                  JOIN product_variants pv ON sl.product_variant_id = pv.id
                  JOIN products p ON pv.product_id = p.id
                  JOIN users u ON sl.user_id = u.id
                  ORDER BY sl.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteCheckSheet($id)
    {
        try {
            $this->conn->beginTransaction();

            $query_d = "DELETE FROM inventory_check_details WHERE inventory_check_id = :id";
            $stmt_d = $this->conn->prepare($query_d);
            $stmt_d->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_d->execute();


            $query_m = "DELETE FROM inventory_checks WHERE id = :id AND status = 0"; // Chỉ cho xóa phiếu chưa duyệt
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
