<?php
// admin/produk.php

// Include koneksi database dan template yang diperlukan
require_once '../config/database.php';
include '../templates/header.php';
include '../templates/navbar.php';
include '../templates/sidebar.php';

// Ambil data produk dari database
$query  = "SELECT * FROM produk";
$result = mysqli_query($conn, $query);
?>

<!-- Content Wrapper. Contains page content -->
<div class="main-content">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Manajemen Produk Parfum</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <a href="master_produk_add.php" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Tambah Produk
                </a>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Produk</th>
                            <th>Deskripsi</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Gambar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['nama'] ?></td>
                            <td><?= $row['deskripsi'] ?></td>
                            <td><?= number_format($row['harga'], 2, ',', '.') ?></td>
                            <td><?= $row['stok'] ?></td>
                            <td>
                                <?php if (!empty($row['gambar'])): ?>
                                <img src="../assets/img/<?= $row['gambar'] ?>" alt="<?= $row['nama'] ?>" width="50">
                                <?php else: ?>
                                Tidak ada gambar
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_produk.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <a href="../process/delete_produk.php?id=<?= $row['id'] ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                    <i class="fa fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Belum ada produk</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
include '../templates/footer.php';
?>