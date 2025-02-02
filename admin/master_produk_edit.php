<?php
// edit_produk.php

require_once '../config/database.php';

// Ambil ID produk dari parameter URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID produk tidak ditemukan!";
    exit;
}

$id = $_GET['id'];

// Ambil data produk berdasarkan ID
$query  = "SELECT * FROM produk WHERE id = ?";
$stmt   = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$produk = mysqli_fetch_assoc($result);

if (!$produk) {
    echo "Produk tidak ditemukan!";
    exit;
}

// Proses update data produk
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama      = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $harga     = $_POST['harga'];
    $stok      = $_POST['stok'];
    $gambar    = $produk['gambar']; // Gunakan gambar lama secara default

    // Jika ada gambar baru yang diupload
    if (!empty($_FILES['gambar']['name'])) {
        $gambar = time() . "_" . $_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'], "../assets/img/" . $gambar);
    }

    // Update data produk di database
    $updateQuery = "UPDATE produk SET nama = ?, deskripsi = ?, harga = ?, stok = ?, gambar = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "ssdisi", $nama, $deskripsi, $harga, $stok, $gambar, $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Produk berhasil diperbarui!'); window.location='master_produk.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui produk!');</script>";
    }
}

include '../templates/header.php';
include '../templates/navbar.php';
include '../templates/sidebar.php';
?>

<!-- Content Wrapper -->
<div class="main-content">
    <section class="content-header">
        <h1>Edit Produk</h1>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Form Edit Produk</h3>
            </div>
            <div class="box-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Nama Produk</label>
                        <input type="text" name="nama" class="form-control" value="<?= $produk['nama']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" required><?= $produk['deskripsi']; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" name="harga" class="form-control" value="<?= $produk['harga']; ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Stok</label>
                        <input type="number" name="stok" class="form-control" value="<?= $produk['stok']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Gambar Produk</label><br>
                        <img src="../assets/img/<?= $produk['gambar']; ?>" width="100" alt="Gambar Produk">
                        <input type="file" name="gambar" class="form-control">
                        <small>Kosongkan jika tidak ingin mengubah gambar.</small>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Simpan Perubahan
                    </button>
                    <a href="produk.php" class="btn btn-danger">
                        <i class="fa fa-arrow-left"></i> Batal
                    </a>
                </form>
            </div>
        </div>
    </section>
</div>

<?php include '../templates/footer.php'; ?>