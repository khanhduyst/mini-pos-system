<?php
class ProductModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function countProductsWithFilter($search, $category_id)
    {
        $query = "SELECT COUNT(DISTINCT p.id) as total FROM products p 
                  LEFT JOIN product_variants pv ON p.id = pv.product_id WHERE 1=1";
        if (!empty($search)) {
            $query .= " AND (p.product_name LIKE :search1 OR p.product_code LIKE :search2 OR pv.barcode LIKE :search3)";
        }
        if (!empty($category_id)) {
            $query .= " AND p.category_id = :category_id";
        }
        $stmt = $this->conn->prepare($query);
        if (!empty($search)) {
            $searchTerm = "%{$search}%";
            $stmt->bindParam(':search1', $searchTerm);
            $stmt->bindParam(':search2', $searchTerm);
            $stmt->bindParam(':search3', $searchTerm);
        }
        if (!empty($category_id)) {
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    public function getProductsWithFilter($search, $category_id, $limit, $offset)
    {
        $query = "SELECT p.*, c.category_name, SUM(pv.stock_qty) as total_stock,
                         MIN(pv.sale_price) as min_price, MAX(pv.sale_price) as max_price,
                         COUNT(pv.id) as total_variants
                  FROM products p 
                  JOIN categories c ON p.category_id = c.id
                  LEFT JOIN product_variants pv ON p.id = pv.product_id
                  WHERE 1=1";
        if (!empty($search)) {
            $query .= " AND (p.product_name LIKE :search1 OR p.product_code LIKE :search2 OR pv.barcode LIKE :search3)";
        }
        if (!empty($category_id)) {
            $query .= " AND p.category_id = :category_id";
        }
        $query .= " GROUP BY p.id ORDER BY p.id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        if (!empty($search)) {
            $searchTerm = "%{$search}%";
            $stmt->bindParam(':search1', $searchTerm);
            $stmt->bindParam(':search2', $searchTerm);
            $stmt->bindParam(':search3', $searchTerm);
        }
        if (!empty($category_id)) {
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($products as &$prod) {
            $prod['variants'] = $this->getVariantsByProductId($prod['id']);
        }
        return $products;
    }

    public function getVariantsByProductId($product_id)
    {
        $query = "SELECT * FROM product_variants WHERE product_id = :product_id ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllActiveCategories()
    {
        $query = "SELECT id, category_name FROM categories WHERE status = 1 ORDER BY category_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createProductWithVariants($product_code, $product_name, $image_url, $short_description, $category_id, $variants)
    {
        try {
            $this->conn->beginTransaction();

            $query = "INSERT INTO products (product_code, product_name, image, short_description, category_id, status) 
                      VALUES (:product_code, :product_name, :image, :short_description, :category_id, 1)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':product_code', $product_code);
            $stmt->bindParam(':product_name', $product_name);
            $stmt->bindParam(':image', $image_url);
            $stmt->bindParam(':short_description', $short_description);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->execute();

            $product_id = $this->conn->lastInsertId();

            $query_v = "INSERT INTO product_variants (product_id, variant_name, barcode, cost_price, sale_price, stock_qty) 
                        VALUES (:product_id, :v_name, :barcode, :cost, :sale, :stock)";
            $stmt_v = $this->conn->prepare($query_v);

            foreach ($variants as $v) {
                $stmt_v->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                $stmt_v->bindParam(':v_name', $v['variant_name']);
                $stmt_v->bindParam(':barcode', $v['barcode']);
                $stmt_v->bindParam(':cost', $v['cost_price']);
                $stmt_v->bindParam(':sale', $v['sale_price']);
                $stmt_v->bindParam(':stock', $v['stock_qty'], PDO::PARAM_INT);
                $stmt_v->execute();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            die($e->getMessage());
        }
    }

    public function updateProductWithVariants($id, $product_name, $image_url, $short_description, $category_id, $variants)
    {
        try {
            $this->conn->beginTransaction();

            $query = "UPDATE products 
                      SET product_name = :product_name, 
                          image = :image, 
                          short_description = :short_description, 
                          category_id = :category_id
                      WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':product_name', $product_name);
            $stmt->bindParam(':image', $image_url);
            $stmt->bindParam(':short_description', $short_description);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $query_delete = "DELETE FROM product_variants WHERE product_id = :product_id";
            $stmt_delete = $this->conn->prepare($query_delete);
            $stmt_delete->bindParam(':product_id', $id, PDO::PARAM_INT);
            $stmt_delete->execute();

            $query_variant = "INSERT INTO product_variants (product_id, variant_name, barcode, cost_price, sale_price, stock_qty) 
                              VALUES (:product_id, :variant_name, :barcode, :cost_price, :sale_price, :stock_qty)";

            $stmt_variant = $this->conn->prepare($query_variant);

            foreach ($variants as $v) {
                $stmt_variant->bindParam(':product_id', $id, PDO::PARAM_INT);
                $stmt_variant->bindParam(':variant_name', $v['variant_name']);
                $stmt_variant->bindParam(':barcode', $v['barcode']);
                $stmt_variant->bindParam(':cost_price', $v['cost_price']);
                $stmt_variant->bindParam(':sale_price', $v['sale_price']);
                $stmt_variant->bindParam(':stock_qty', $v['stock_qty'], PDO::PARAM_INT);
                $stmt_variant->execute();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            die($e->getMessage());
        }
    }

    public function isCodeOrBarcodeExists($code, $variants, $exclude_id = null)
    {
        $query = "SELECT COUNT(*) as total FROM products WHERE product_code = :code";
        if ($exclude_id !== null) {
            $query .= " AND id != :exclude_id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        if ($exclude_id !== null) {
            $stmt->bindParam(':exclude_id', $exclude_id, PDO::PARAM_INT);
        }
        $stmt->execute();
        if (($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0) > 0) return true;

        foreach ($variants as $v) {
            if (empty($v['barcode'])) continue;

            $query_b = "SELECT COUNT(*) as total FROM product_variants WHERE barcode = :barcode";
            if ($exclude_id !== null) {
                $query_b .= " AND product_id != :exclude_id";
            }

            $stmt_b = $this->conn->prepare($query_b);
            $stmt_b->bindParam(':barcode', $v['barcode']);

            if ($exclude_id !== null) {
                $stmt_b->bindParam(':exclude_id', $exclude_id, PDO::PARAM_INT);
            }

            $stmt_b->execute();
            if (($stmt_b->fetch(PDO::FETCH_ASSOC)['total'] ?? 0) > 0) return true;
        }
        return false;
    }
}