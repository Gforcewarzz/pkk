<?php
require_once '../config/database.php';
include '../templates/header.php';
include '../templates/navbar.php';
include '../templates/sidebar.php';

// Inisialisasi variabel error dan success
$error = '';
$success = '';

// Proses ketika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan amankan inputan form
    $nama      = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga     = mysqli_real_escape_string($conn, $_POST['harga']);
    $stok      = mysqli_real_escape_string($conn, $_POST['stok']);

    // Proses upload gambar jika ada file yang diupload
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $file_name = $_FILES['gambar']['name'];
        $file_tmp  = $_FILES['gambar']['tmp_name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Format yang diperbolehkan
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed_ext)) {
            // Rename gambar agar unik
            $file_name_new = time() . "_" . uniqid() . "." . $file_ext;
            $target_dir  = "../assets/img/";
            $target_file = $target_dir . $file_name_new;

            // Pindahkan file ke folder tujuan
            if (move_uploaded_file($file_tmp, $target_file)) {
                $gambar = $file_name_new;
            } else {
                $error = "Gagal mengupload gambar.";
            }
        } else {
            $error = "Format file tidak didukung. Gunakan JPG, JPEG, PNG, atau GIF.";
        }
    }

    // Jika tidak ada error, insert data ke database
    if (empty($error)) {
        $query = "INSERT INTO produk (nama, deskripsi, harga, stok, gambar) 
                  VALUES ('$nama', '$deskripsi', '$harga', '$stok', '$gambar')";
        if (mysqli_query($conn, $query)) {
            $success = "Produk berhasil ditambahkan!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<div class="main-content">
    <!-- Content Header -->
    <section class="content-header">
        <h1>Tambah Produk Parfum</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Gagal!</strong> <?= $error; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Berhasil!</strong> <?= $success; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="box">
            <div class="box-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nama">Nama Produk</label>
                        <input type="text" name="nama" id="nama" class="form-control" placeholder="Masukkan nama produk"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Produk</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4"
                            placeholder="Masukkan deskripsi produk" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="harga">Harga</label>
                        <input type="number" name="harga" id="harga" class="form-control"
                            placeholder="Masukkan harga produk" required>
                    </div>

                    <div class="form-group">
                        <label for="stok">Stok</label>
                        <input type="number" name="stok" id="stok" class="form-control"
                            placeholder="Masukkan jumlah stok" required>
                    </div>

                    <div class="form-group">
                        <label for="gambar">Gambar Produk</label>
                        <input type="file" name="gambar" id="gambar" class="form-control">
                        <small class="form-text text-muted">Format yang diperbolehkan: JPG, JPEG, PNG, GIF.</small>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Simpan Produk
                        </button>
                        <a href="master_produk.php" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?php include '../templates/footer.php'; ?>