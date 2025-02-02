<?php
require_once '../config/database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('ID pengguna tidak ditemukan!'); window.location='users.php';</script>";
    exit;
}

$id = $_GET['id'];
$query  = "SELECT * FROM users WHERE id = ?";
$stmt   = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $updateQuery = "UPDATE users SET nama = ?, email = ?, role = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, 'sssi', $nama, $email, $role, $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
                alert('Pengguna berhasil diperbarui!');
                window.location='users.php';
              </script>";
    } else {
        echo "<script>alert('Gagal memperbarui pengguna!');</script>";
    }
}

include '../templates/header.php';
include '../templates/navbar.php';
include '../templates/sidebar.php';
?>

<div class="main-content">
    <section class="content-header">
        <h1>Edit Pengguna</h1>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="nama" class="form-control" value="<?= $user['nama']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= $user['email']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control">
                            <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="kasir" <?= $user['role'] == 'kasir' ? 'selected' : '' ?>>Kasir</option>
                            <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Simpan Perubahan
                    </button>
                    <a href="users.php" class="btn btn-danger">
                        <i class="fa fa-times"></i> Batal
                    </a>
                </form>
            </div>
        </div>
    </section>
</div>

<?php include '../templates/footer.php'; ?>