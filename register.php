<?php
session_start();
require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validasi input
    if (empty($nama) || empty($password) || empty($confirm_password)) {
        $_SESSION['register_error'] = "Semua kolom harus diisi!";
        header("Location: register.php");
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION['register_error'] = "Password tidak cocok!";
        header("Location: register.php");
        exit;
    }

    // Cek apakah nama sudah digunakan
    $query = "SELECT * FROM users WHERE nama = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $nama);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_fetch_assoc($result)) {
        $_SESSION['register_error'] = "Nama sudah digunakan!";
        header("Location: register.php");
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Simpan data ke database dengan role 'user'
    $insert_query = "INSERT INTO users (nama, password, role) VALUES (?, ?, 'user')";
    $insert_stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($insert_stmt, "ss", $nama, $hashed_password);
    $insert_success = mysqli_stmt_execute($insert_stmt);

    if ($insert_success) {
        $_SESSION['register_success'] = "Registrasi berhasil! Silakan login.";
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['register_error'] = "Terjadi kesalahan, coba lagi!";
        header("Location: register.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f4f4f4;
        }

        .register-container {
            background: white;
            padding: 30px;
            width: 350px;
            border-radius: 12px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            transition: transform 0.3s;
        }

        .register-container:hover {
            transform: scale(1.02);
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
            font-weight: 600;
        }

        .register-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            outline: none;
            transition: 0.3s;
        }

        .register-container input:focus {
            border-color: #5a67d8;
            box-shadow: 0px 0px 5px rgba(90, 103, 216, 0.5);
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            background-color: #5a67d8;
            border: none;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #434190;
        }

        .alert {
            background-color: #e53e3e;
            color: white;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        p {
            margin-top: 10px;
            font-size: 14px;
        }

        a {
            color: #5a67d8;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <?php if (isset($_SESSION['register_error'])): ?>
            <div class="alert"><?= $_SESSION['register_error'] ?></div>
            <?php unset($_SESSION['register_error']); ?>
        <?php endif; ?>

        <h2>Register</h2>
        <form action="" method="POST">
            <input type="text" name="nama" placeholder="Nama" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
            <button type="submit">Daftar</button>
        </form>
        <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</body>

</html>