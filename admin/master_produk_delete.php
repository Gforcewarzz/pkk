<?php
require_once '../config/database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('ID produk tidak ditemukan!'); window.location='master_produk.php';</script>";
    exit;
}

$id = $_GET['id'];

// Hapus gambar produk jika ada
$query_gambar = "SELECT gambar FROM produk WHERE id = ?";
$stmt_gambar = mysqli_prepare($conn, $query_gambar);
mysqli_stmt_bind_param($stmt_gambar, 'i', $id);
mysqli_stmt_execute($stmt_gambar);
$result_gambar = mysqli_stmt_get_result($stmt_gambar);
$row = mysqli_fetch_assoc($result_gambar);

if (!empty($row['gambar']) && file_exists("../assets/img/" . $row['gambar'])) {
    unlink("../assets/img/" . $row['gambar']);
}

// Hapus produk dari database
$query = "DELETE FROM produk WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>
            alert('Produk berhasil dihapus!');
            window.location='produk.php';
          </script>";
} else {
    echo "<script>alert('Gagal menghapus produk!'); window.location='master_produk.php';</script>";
}
