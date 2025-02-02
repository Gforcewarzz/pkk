<?php
require_once '../config/database.php';
include '../templates/header.php';
include '../templates/navbar.php';
include '../templates/sidebar.php';

// Ambil data users dari database
$query  = "SELECT * FROM users";
$result = mysqli_query($conn, $query);
?>

<div class="main-content">
    <section class="content-header">
        <h1>Manajemen Pengguna</h1>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <a href="master_users_add.php" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Tambah Pengguna
                </a>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['nama'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= ucfirst($row['role']) ?></td>
                            <td>
                                <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <a href="master_users_delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus pengguna ini?')">
                                    <i class="fa fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Belum ada pengguna</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<?php include '../templates/footer.php'; ?>