<?php
session_start();
require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['username']); // Ganti dari username ke nama
    $password = trim($_POST['password']);

    // Cek user di database berdasarkan nama
    $query = "SELECT * FROM users WHERE nama = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $nama);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;

        // Arahkan berdasarkan role
        switch ($user['role']) {
            case 'admin':
                header("Location: admin/dashboard.php");
                break;
            case 'kasir':
                header("Location: kasir/dashboard.php");
                break;
            case 'users':
                header("Location: users/dashboard.php");
                break;
            default:
                $_SESSION['login_error'] = "Role tidak dikenali!";
                header("Location: login.php");
                break;
        }
        exit;
    } else {
        $_SESSION['login_error'] = "Nama atau password salah!";
        header("Location: login.php");
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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

        .login-container {
            background: white;
            padding: 30px;
            width: 350px;
            border-radius: 12px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            transition: transform 0.3s;
        }

        .login-container:hover {
            transform: scale(1.02);
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
            font-weight: 600;
        }

        .login-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            outline: none;
            transition: 0.3s;
        }

        .login-container input:focus {
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
    <div class="login-container">
        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="alert"><?= $_SESSION['login_error'] ?></div>
            <?php unset($_SESSION['login_error']); ?>
        <?php endif; ?>

        <h2>Login</h2>
        <div class="alert" style="display: none;">Username atau password salah!</div>
        <form action="" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>
</body>

</html>