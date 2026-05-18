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
        $query = "SELECT u.id, u.user_code, u.username, u.password, u.full_name, u.email, r.role_name, u.status 
                  FROM " . $this->table_name . " u 
                  LEFT JOIN roles r ON u.role_id = r.id 
                  WHERE u.username = :username LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function getAllUsers()
    {
        $query = "SELECT u.*, r.role_name 
                  FROM " . $this->table_name . " u 
                  LEFT JOIN roles r ON u.role_id = r.id 
                  ORDER BY u.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createUser($user_code, $username, $password, $full_name, $email, $phone, $gender, $date_of_birth, $address, $role_id, $note)
    {
        $query = "INSERT INTO " . $this->table_name . " (user_code, username, password, full_name, email, phone, gender, date_of_birth, address, role_id, status, note) 
                  VALUES (:user_code, :username, :password, :full_name, :email, :phone, :gender, :date_of_birth, :address, :role_id, 1, :note)";
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

    public function toggleStatus($id, $current_status)
    {
        $new_status = ($current_status == 1) ? 0 : 1;
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $new_status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
