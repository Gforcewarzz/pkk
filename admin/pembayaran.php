<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$user = $_SESSION['user']['nama'];
$user_id = $_SESSION['user']['id'];

// Get cart items
$cart_items = mysqli_query($conn, "SELECT k.*, p.nama, p.harga 
                                 FROM keranjang k 
                                 JOIN produk p ON k.produk_id = p.id 
                                 WHERE k.user = '$user'");

$total = 0;
$items = [];
while ($item = mysqli_fetch_assoc($cart_items)) {
    $subtotal = $item['jumlah'] * $item['harga'];
    $total += $subtotal;
    $items[] = $item;
}

// Process payment
if (isset($_POST['process_payment'])) {
    $total_bayar = $_POST['jumlah_bayar'];
    $kembalian = $total_bayar - $total;

    if ($total_bayar < $total) {
        $error = "Pembayaran kurang dari total belanja";
    } else {
        // Get current timestamp
        $current_time = date('Y-m-d H:i:s');

        // Create order in pesanan table with default status 'Diproses'
        $query = "INSERT INTO pesanan (user_id, total_harga, status, created_at, updated_at) 
                 VALUES ($user_id, $total, 'Diproses', '$current_time', '$current_time')";
        mysqli_query($conn, $query);

        $pesanan_id = mysqli_insert_id($conn);

        // Create order details
        foreach ($items as $item) {
            $subtotal = $item['jumlah'] * $item['harga'];
            mysqli_query($conn, "INSERT INTO detail_pesanan (pesanan_id, produk_id, jumlah, harga, subtotal) 
                               VALUES ($pesanan_id, {$item['produk_id']}, {$item['jumlah']}, {$item['harga']}, $subtotal)");

            // Update stock
            mysqli_query($conn, "UPDATE produk SET stok = stok - {$item['jumlah']} 
                               WHERE id = {$item['produk_id']}");
        }

        // Clear cart
        mysqli_query($conn, "DELETE FROM keranjang WHERE user = '$user'");

        // Set success message
        $success = "Pembayaran berhasil! Pesanan Anda sedang diproses.";
    }
}

include '../templates/header.php';
include '../templates/navbar.php';
include '../templates/sidebar.php';
?>

<div class="main-content">
    <div class="payment-header">
        <h1>Pembayaran</h1>
    </div>

    <div class="payment-content">
        <div class="payment-wrapper">
            <div class="payment-box">
                <?php if (isset($error)) : ?>
                <div class="payment-alert-error">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <?php if (isset($success)) : ?>
                <div class="payment-alert-success">
                    <?php echo $success; ?>
                </div>
                <?php endif; ?>

                <?php if (!isset($success)) : ?>
                <!-- Only show payment form if payment not yet successful -->
                <div class="order-summary">
                    <h2>Ringkasan Pesanan</h2>
                    <div class="customer-info">
                        <p><strong>Nama Pembeli:</strong> <?php echo $user; ?></p>
                        <p><strong>Tanggal:</strong> <?php echo date('d/m/Y H:i'); ?></p>
                    </div>
                    <table class="payment-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item) :
                                    $subtotal = $item['jumlah'] * $item['harga'];
                                ?>
                            <tr>
                                <td><?php echo $item['nama']; ?></td>
                                <td>Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                                <td><?php echo $item['jumlah']; ?></td>
                                <td>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="payment-total-label">Total:</td>
                                <td class="payment-total-amount">Rp <?php echo number_format($total, 0, ',', '.'); ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="payment-form">
                    <h2>Detail Pembayaran</h2>
                    <form method="POST" class="payment-form-wrapper">
                        <div class="form-group">
                            <label for="jumlah_bayar">Jumlah Bayar:</label>
                            <div class="input-group">
                                <span class="input-prefix">Rp</span>
                                <input type="number" name="jumlah_bayar" id="jumlah_bayar" min="<?php echo $total; ?>"
                                    class="payment-input" required
                                    onchange="calculateChange(this.value, <?php echo $total; ?>)">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Total Belanja:</label>
                            <span class="amount">Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
                        </div>
                        <div class="form-group">
                            <label>Kembalian:</label>
                            <span id="kembalian" class="amount">Rp 0</span>
                        </div>
                        <button type="submit" name="process_payment" class="payment-btn-submit">Proses
                            Pembayaran</button>
                    </form>
                </div>
                <?php else : ?>
                <div class="payment-success-actions">
                    <a href="menu.php" class="btn-back-to-menu">Kembali ke Menu</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function calculateChange(paymentAmount, total) {
    const change = paymentAmount - total;
    document.getElementById('kembalian').textContent =
        'Rp ' + new Intl.NumberFormat('id-ID').format(Math.max(0, change));
}
</script>

<?php include '../templates/footer.php'; ?>