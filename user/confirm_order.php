<?php
// confirm_order.php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['confirm_delivery']) && isset($_POST['pesanan_id'])) {
    $pesanan_id = $_POST['pesanan_id'];
    $user_id = $_SESSION['user']['id'];

    // Verify the order belongs to the user
    $verify_query = mysqli_query($conn, "
        SELECT id FROM pesanan 
        WHERE id = $pesanan_id 
        AND user_id = $user_id 
        AND status = 'Dikirim'
    ");

    if (mysqli_num_rows($verify_query) > 0) {
        // Update order status
        $update = mysqli_query($conn, "
            UPDATE pesanan 
            SET status = 'Selesai',
                updated_at = CURRENT_TIMESTAMP 
            WHERE id = $pesanan_id
        ");

        if ($update) {
            $_SESSION['success'] = "Pesanan telah dikonfirmasi sebagai diterima";
        } else {
            $_SESSION['error'] = "Gagal mengonfirmasi pesanan";
        }
    } else {
        $_SESSION['error'] = "Pesanan tidak valid";
    }
}

header('Location: history_pesanan.php');
exit();
