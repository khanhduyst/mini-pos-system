<?php
require_once 'config/database.php';

$database = new Database();
$status = $database->testConnection();

if ($status === true) {
    echo "<div style='color: green; font-weight: bold; font-size: 20px; padding: 20px;'>Kết nối tới database mini_pos_db trên Aiven Cloud THÀNH CÔNG!</div>";
} else {
    echo "<div style='color: red; font-weight: bold; font-size: 20px; padding: 20px;'>Kết nối THẤT BẠI!<br>Lỗi chi tiết: " . $status . "</div>";
}