<?php
class CustomerModel {
    private $conn;
    private $table_name = "customers";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function countCustomersWithFilter($search, $status) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE 1=1";
        if (!empty($search)) {
            $query .= " AND (full_name LIKE :search1 OR customer_code LIKE :search2 OR phone LIKE :search3)";
        }
        if ($status !== null) {
            $query .= " AND status = :status";
        }
        $stmt = $this->conn->prepare($query);
        if (!empty($search)) {
            $searchTerm = "%{$search}%";
            $stmt->bindParam(':search1', $searchTerm);
            $stmt->bindParam(':search2', $searchTerm);
            $stmt->bindParam(':search3', $searchTerm);
        }
        if ($status !== null) {
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function getCustomersWithFilter($search, $status, $limit, $offset) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        if (!empty($search)) {
            $query .= " AND (full_name LIKE :search1 OR customer_code LIKE :search2 OR phone LIKE :search3)";
        }
        if ($status !== null) {
            $query .= " AND status = :status";
        }
        $query .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        if (!empty($search)) {
            $searchTerm = "%{$search}%";
            $stmt->bindParam(':search1', $searchTerm);
            $stmt->bindParam(':search2', $searchTerm);
            $stmt->bindParam(':search3', $searchTerm);
        }
        if ($status !== null) {
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createCustomer($customer_code, $full_name, $phone, $email, $gender, $date_of_birth, $address, $note) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (customer_code, full_name, phone, email, gender, date_of_birth, address, note, status, points, total_spent, debt, created_at) 
                  VALUES (:customer_code, :full_name, :phone, :email, :gender, :date_of_birth, :address, :note, 1, 0, 0.00, 0.00, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':customer_code', $customer_code);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':date_of_birth', $date_of_birth);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':note', $note);
        return $stmt->execute();
    }

    public function updateCustomer($id, $full_name, $phone, $email, $gender, $date_of_birth, $address, $note) {
        $query = "UPDATE " . $this->table_name . " 
                  SET full_name = :full_name, phone = :phone, email = :email, gender = :gender, 
                      date_of_birth = :date_of_birth, address = :address, note = :note 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':date_of_birth', $date_of_birth);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':note', $note);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function payDebt($customer_id, $user_id, $amount, $note) {
        try {
            $this->conn->beginTransaction();

            $query = "SELECT debt FROM " . $this->table_name . " WHERE id = :id FOR UPDATE";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $customer_id, PDO::PARAM_INT);
            $stmt->execute();
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$customer) {
                $this->conn->rollBack();
                return false;
            }

            $current_debt = (float)$customer['debt'];
            $new_debt = $current_debt - (float)$amount;

            $updateQuery = "UPDATE " . $this->table_name . " SET debt = :new_debt WHERE id = :id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':new_debt', $new_debt);
            $updateStmt->bindParam(':id', $customer_id, PDO::PARAM_INT);
            $updateStmt->execute();

            $logQuery = "INSERT INTO customer_debts (customer_id, user_id, type, amount, balance_after, note, created_at) 
                         VALUES (:customer_id, :user_id, 'decrease', :amount, :balance_after, :note, NOW())";
            $logStmt = $this->conn->prepare($logQuery);
            $logStmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
            $logStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $logStmt->bindParam(':amount', $amount);
            $logStmt->bindParam(':balance_after', $new_debt);
            $logStmt->bindParam(':note', $note);
            $logStmt->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function getDebtHistory($customer_id) {
        $query = "SELECT d.*, u.full_name as staff_name FROM customer_debts d
                  JOIN users u ON d.user_id = u.id
                  WHERE d.customer_id = :customer_id ORDER BY d.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function toggleStatus($id, $status) {
        $new_status = ($status == 1) ? 0 : 1;
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $new_status, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}