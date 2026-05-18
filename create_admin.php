<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$username = 'admin2';
$password = '123456';
$hashed_password = password_hash($password, PASSWORD_BCRYPT);
$full_name = 'Quản Trị Viên';
$user_code = 'NV002';
$email = 'admina@gmail.com';
$phone = '09012234567';

try {
    $query = "DELETE FROM users WHERE username = :username";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $query = "INSERT INTO users (user_code, username, password, full_name, email, phone, role, status) 
              VALUES (:user_code, :username, :password, :full_name, :email, :phone, 'admin', 1)";
              
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_code', $user_code);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    
    if ($stmt->execute()) {
        echo "Tạo tài khoản Admin thành công! Chuỗi mã hóa thực tế là: " . $hashed_password;
    }
} catch (PDOException $e) {
    echo "Lỗi: " . $e->getMessage();
}