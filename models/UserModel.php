<?php
class UserModel
{
    private $conn;
    private $table_name = "users";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function login($username)
    {
        $query = "SELECT u.*, r.role_name FROM " . $this->table_name . " u 
                  JOIN roles r ON u.role_id = r.id 
                  WHERE u.username = :username LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function countUsersWithFilter($search, $role_id, $status)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " u WHERE 1=1";

        if (!empty($search)) {
            $query .= " AND (u.full_name LIKE :search1 OR u.user_code LIKE :search2 OR u.username LIKE :search3)";
        }
        if ($role_id !== null) {
            $query .= " AND u.role_id = :role_id";
        }
        if ($status !== null) {
            $query .= " AND u.status = :status";
        }

        $stmt = $this->conn->prepare($query);

        if (!empty($search)) {
            $searchTerm = "%{$search}%";
            $stmt->bindParam(':search1', $searchTerm);
            $stmt->bindParam(':search2', $searchTerm);
            $stmt->bindParam(':search3', $searchTerm);
        }
        if ($role_id !== null) {
            $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
        }
        if ($status !== null) {
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function getUsersWithFilter($search, $role_id, $status, $limit, $offset)
    {
        try {
            $query = "SELECT u.*, r.role_name FROM " . $this->table_name . " u 
                      JOIN roles r ON u.role_id = r.id 
                      WHERE 1=1";

            if (!empty($search)) {
                $query .= " AND (u.full_name LIKE :search1 OR u.user_code LIKE :search2 OR u.username LIKE :search3)";
            }
            if ($role_id !== null) {
                $query .= " AND u.role_id = :role_id";
            }
            if ($status !== null) {
                $query .= " AND u.status = :status";
            }

            $query .= " ORDER BY u.id DESC LIMIT :limit OFFSET :offset";

            $stmt = $this->conn->prepare($query);

            if (!empty($search)) {
                $searchTerm = "%{$search}%";
                $stmt->bindParam(':search1', $searchTerm);
                $stmt->bindParam(':search2', $searchTerm);
                $stmt->bindParam(':search3', $searchTerm);
            }
            if ($role_id !== null) {
                $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
            }
            if ($status !== null) {
                $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            }

            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Lệnh này sẽ in thẳng lỗi cấu trúc bảng SQL ra màn hình để bạn biết đang sai ở đâu
            echo "Lỗi truy vấn SQL: " . $e->getMessage();
            exit();
        }
    }

    public function createUser($user_code, $username, $password, $full_name, $email, $phone, $gender, $date_of_birth, $address, $role_id, $note)
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_code, username, password, full_name, email, phone, gender, date_of_birth, address, role_id, note, status, created_at) 
                  VALUES (:user_code, :username, :password, :full_name, :email, :phone, :gender, :date_of_birth, :address, :role_id, :note, 1, NOW())";

        $stmt = $this->conn->prepare($query);
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt->bindParam(':user_code', $user_code);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':date_of_birth', $date_of_birth);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':role_id', $role_id);
        $stmt->bindParam(':note', $note);

        return $stmt->execute();
    }

    public function updateUser($id, $full_name, $email, $phone, $gender, $date_of_birth, $address, $role_id, $note)
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET full_name = :full_name, email = :email, phone = :phone, gender = :gender, 
                      date_of_birth = :date_of_birth, address = :address, role_id = :role_id, note = :note 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':date_of_birth', $date_of_birth);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':role_id', $role_id);
        $stmt->bindParam(':note', $note);
        $stmt->bindParam(':id', $id);

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
}
