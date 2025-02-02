<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $query = "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ssss', $nama, $email, $password, $role);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
                alert('Pengguna berhasil ditambahkan!');
                window.location='master_users.php';
              </script>";
    } else {
        echo "<script>alert('Gagal menambahkan pengguna!');</script>";
    }
}

include '../templates/header.php';
include '../templates/navbar.php';
include '../templates/sidebar.php';
?>

<div class="main-content">
    <section class="content-header">
        <h1>Tambah Pengguna</h1>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control">
                            <option value="admin">Admin</option>
                            <option value="kasir">Kasir</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                    <a href="users.php" class="btn btn-secondary">
                        <i class="fa fa-times"></i> Batal
                    </a>
                </form>
            </div>
        </div>
    </section>
</div>

<?php include '../templates/footer.php'; ?>