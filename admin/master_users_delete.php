<?php
require_once '../config/database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('ID pengguna tidak ditemukan!'); window.location='users.php';</script>";
    exit;
}

$id = $_GET['id'];
$query = "DELETE FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>
            alert('Pengguna berhasil dihapus!');
            window.location='master_users.php';
          </script>";
} else {
    echo "<script>alert('Gagal menghapus pengguna!'); window.location='users.php';</script>";
}
