<?php
class CategoryModel
{
    private $conn;
    private $table_name = "categories";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function countCategoriesWithFilter($search, $status)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE 1=1";
        if (!empty($search)) {
            $query .= " AND (category_name LIKE :search1 OR category_code LIKE :search2)";
        }
        if ($status !== null) {
            $query .= " AND status = :status";
        }
        $stmt = $this->conn->prepare($query);
        if (!empty($search)) {
            $searchTerm = "%{$search}%";
            $stmt->bindParam(':search1', $searchTerm);
            $stmt->bindParam(':search2', $searchTerm);
        }
        if ($status !== null) {
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function getCategoriesWithFilter($search, $status, $limit, $offset)
    {
        $query = "SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) as total_products 
                  FROM " . $this->table_name . " c WHERE 1=1";
        if (!empty($search)) {
            $query .= " AND (c.category_name LIKE :search1 OR c.category_code LIKE :search2)";
        }
        if ($status !== null) {
            $query .= " AND c.status = :status";
        }
        $query .= " ORDER BY c.id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        if (!empty($search)) {
            $searchTerm = "%{$search}%";
            $stmt->bindParam(':search1', $searchTerm);
            $stmt->bindParam(':search2', $searchTerm);
        }
        if ($status !== null) {
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createCategory($category_code, $category_name, $description)
    {
        $query = "INSERT INTO " . $this->table_name . " (category_code, category_name, description, status) 
                  VALUES (:category_code, :category_name, :description, 1)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_code', $category_code);
        $stmt->bindParam(':category_name', $category_name);
        $stmt->bindParam(':description', $description);
        return $stmt->execute();
    }

    public function updateCategory($id, $category_name, $description)
    {
        $query = "UPDATE " . $this->table_name . " SET category_name = :category_name, description = :description WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_name', $category_name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function toggleStatus($id, $status)
    {
        $new_status = ($status == 1) ? 0 : 1;
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $new_status, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function isCategoryNameExists($name, $exclude_id = null)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE category_name = :name";
        if ($exclude_id !== null) {
            $query .= " AND id != :exclude_id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        if ($exclude_id !== null) {
            $stmt->bindParam(':exclude_id', $exclude_id, PDO::PARAM_INT);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] > 0;
    }
}