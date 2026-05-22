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
                         COUNT(pv.id) as total_variants,
                         SUM(CASE WHEN pv.stock_qty <= pv.low_stock_threshold THEN 1 ELSE 0 END) as alert_count
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

            $query_v = "INSERT INTO product_variants (product_id, variant_name, barcode, cost_price, sale_price, stock_qty, low_stock_threshold) 
                        VALUES (:product_id, :v_name, :barcode, :cost, :sale, :stock, :limit)";
            $stmt_v = $this->conn->prepare($query_v);

            foreach ($variants as $v) {
                $stmt_v->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                $stmt_v->bindParam(':v_name', $v['variant_name']);
                $stmt_v->bindParam(':barcode', $v['barcode']);
                $stmt_v->bindParam(':cost', $v['cost_price']);
                $stmt_v->bindParam(':sale', $v['sale_price']);
                $stmt_v->bindParam(':stock', $v['stock_qty'], PDO::PARAM_INT);
                $stmt_v->bindParam(':limit', $v['low_stock_threshold'], PDO::PARAM_INT);
                $stmt_v->execute();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
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

            $current_variants = $this->getVariantsByProductId($id);
            $processed_ids = [];

            foreach ($variants as $v) {
                $matched_id = null;
                
                foreach ($current_variants as $curr) {
                    if ($curr['variant_name'] === $v['variant_name'] || (!empty($v['barcode']) && $curr['barcode'] === $v['barcode'])) {
                        $matched_id = $curr['id'];
                        break;
                    }
                }

                if ($matched_id !== null) {
                    $query_u = "UPDATE product_variants 
                                SET barcode = :barcode, cost_price = :cost_price, sale_price = :sale_price, 
                                    stock_qty = :stock_qty, low_stock_threshold = :limit 
                                WHERE id = :v_id";
                    $stmt_u = $this->conn->prepare($query_u);
                    $stmt_u->bindParam(':barcode', $v['barcode']);
                    $stmt_u->bindParam(':cost_price', $v['cost_price']);
                    $stmt_u->bindParam(':sale_price', $v['sale_price']);
                    $stmt_u->bindParam(':stock_qty', $v['stock_qty'], PDO::PARAM_INT);
                    $stmt_u->bindParam(':limit', $v['low_stock_threshold'], PDO::PARAM_INT);
                    $stmt_u->bindParam(':v_id', $matched_id, PDO::PARAM_INT);
                    $stmt_u->execute();
                    
                    $processed_ids[] = $matched_id;
                } else {
                    $query_i = "INSERT INTO product_variants (product_id, variant_name, barcode, cost_price, sale_price, stock_qty, low_stock_threshold) 
                                VALUES (:product_id, :variant_name, :barcode, :cost_price, :sale_price, :stock_qty, :limit)";
                    $stmt_i = $this->conn->prepare($query_i);
                    $stmt_i->bindParam(':product_id', $id, PDO::PARAM_INT);
                    $stmt_i->bindParam(':variant_name', $v['variant_name']);
                    $stmt_i->bindParam(':barcode', $v['barcode']);
                    $stmt_i->bindParam(':cost_price', $v['cost_price']);
                    $stmt_i->bindParam(':sale_price', $v['sale_price']);
                    $stmt_i->bindParam(':stock_qty', $v['stock_qty'], PDO::PARAM_INT);
                    $stmt_i->bindParam(':limit', $v['low_stock_threshold'], PDO::PARAM_INT);
                    $stmt_i->execute();
                }
            }

            foreach ($current_variants as $curr) {
                if (!in_array($curr['id'], $processed_ids)) {
                    $query_check = "SELECT COUNT(*) as log_total FROM stock_logs WHERE product_variant_id = :v_id";
                    $stmt_check = $this->conn->prepare($query_check);
                    $stmt_check->bindParam(':v_id', $curr['id'], PDO::PARAM_INT);
                    $stmt_check->execute();
                    $has_log = ($stmt_check->fetch(PDO::FETCH_ASSOC)['log_total'] ?? 0) > 0;

                    if (!$has_log) {
                        $query_d = "DELETE FROM product_variants WHERE id = :v_id";
                        $stmt_d = $this->conn->prepare($query_d);
                        $stmt_d->bindParam(':v_id', $curr['id'], PDO::PARAM_INT);
                        $stmt_d->execute();
                    }
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function toggleStatus($id, $status)
    {
        $new_status = ($status == 1) ? 0 : 1;
        $query = "UPDATE products SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $new_status, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteProduct($id)
    {
        try {
            $this->conn->beginTransaction();
            $query_v = "DELETE FROM product_variants WHERE product_id = :id";
            $stmt_v = $this->conn->prepare($query_v);
            $stmt_v->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_v->execute();

            $query_p = "DELETE FROM products WHERE id = :id";
            $stmt_p = $this->conn->prepare($query_p);
            $stmt_p->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_p->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
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

    public function getAllVariantsForPos()
    {
        $query = "SELECT pv.*, p.product_name, p.image 
                  FROM product_variants pv 
                  JOIN products p ON pv.product_id = p.id 
                  WHERE p.status = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVariantStockForUpdate($id)
    {
        $query = "SELECT stock_qty FROM product_variants WHERE id = :id FOR UPDATE";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateVariantStock($id, $new_stock)
    {
        $query = "UPDATE product_variants SET stock_qty = :new_stock WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':new_stock', $new_stock, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function logStockChange($variant_id, $user_id, $ref_code, $old_qty, $change_qty, $new_qty)
    {
        $query = "INSERT INTO stock_logs (product_variant_id, user_id, action_type, reference_code, old_qty, change_qty, new_qty, created_at) 
                  VALUES (:pv_id, :u_id, 'export', :ref, :old, :change, :new, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pv_id', $variant_id, PDO::PARAM_INT);
        $stmt->bindParam(':u_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':ref', $ref_code);
        $stmt->bindParam(':old', $old_qty, PDO::PARAM_INT);
        $stmt->bindParam(':change', $change_qty, PDO::PARAM_INT);
        $stmt->bindParam(':new', $new_qty, PDO::PARAM_INT);
        return $stmt->execute();
    }
}