<?php
// history_pesanan.php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];

// Fetch user's orders with pagination
$items_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Get total orders for pagination
$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM pesanan WHERE user_id = $user_id");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $items_per_page);

// Fetch orders for current page
$query = mysqli_query($conn, "
    SELECT p.*, 
           COUNT(dp.id) as total_items
    FROM pesanan p 
    LEFT JOIN detail_pesanan dp ON p.id = dp.pesanan_id
    WHERE p.user_id = $user_id
    GROUP BY p.id
    ORDER BY p.created_at DESC
    LIMIT $offset, $items_per_page
");

include '../templates/header.php';
include '../templates/navbar.php';
include '../templates/sidebar.php';
?>
<div class="main-content">
    <h1 class="text-2xl font-bold mb-6">Riwayat Pesanan Saya</h1>

    <?php if (mysqli_num_rows($query) > 0): ?>
        <div class="grid gap-6">
            <?php while ($pesanan = mysqli_fetch_assoc($query)): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold">Order #<?php echo $pesanan['id']; ?></h3>
                            <p class="text-gray-600">
                                <?php echo date('d F Y H:i', strtotime($pesanan['created_at'])); ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <?php
                            $statusClass = '';
                            switch ($pesanan['status']) {
                                case 'Diproses':
                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                    break;
                                case 'Dikirim':
                                    $statusClass = 'bg-blue-100 text-blue-800';
                                    break;
                                case 'Selesai':
                                    $statusClass = 'bg-green-100 text-green-800';
                                    break;
                                default:
                                    $statusClass = 'bg-gray-100 text-gray-800';
                            }
                            ?>
                            <div class="<?php echo $statusClass; ?> px-3 py-1 rounded-full text-sm font-medium">
                                <?php echo $pesanan['status']; ?>
                            </div>
                            <p class="mt-2 font-semibold">
                                Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?>
                            </p>
                        </div>
                    </div>

                    <?php
                    // Fetch order details
                    $detail_query = mysqli_query(
                        $conn,
                        "
                            SELECT dp.*, p.nama as nama_produk
                            FROM detail_pesanan dp
                            JOIN produk p ON dp.produk_id = p.id
                            WHERE dp.pesanan_id = " . $pesanan['id']
                    );
                    ?>

                    <div class="border-t pt-4">
                        <div class="space-y-3">
                            <?php while ($detail = mysqli_fetch_assoc($detail_query)): ?>
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="font-medium"><?php echo htmlspecialchars($detail['nama_produk']); ?></h4>
                                        <p class="text-sm text-gray-600">
                                            <?php echo $detail['jumlah']; ?> x
                                            Rp <?php echo number_format($detail['harga'], 0, ',', '.'); ?>
                                        </p>
                                    </div>
                                    <div class="font-medium">
                                        Rp <?php echo number_format($detail['subtotal'], 0, ',', '.'); ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <?php if ($pesanan['status'] == 'Dikirim'): ?>
                        <div class="mt-4 border-t pt-4">
                            <form method="POST" action="confirm_order.php" class="text-right">
                                <input type="hidden" name="pesanan_id" value="<?php echo $pesanan['id']; ?>">
                                <button type="submit" name="confirm_delivery"
                                    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                                    Konfirmasi Pesanan Diterima
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="mt-6 flex justify-center gap-2">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="<?php echo $page === $i ? 'bg-blue-500 text-white' : 'bg-white text-blue-500'; ?> 
                                  px-4 py-2 rounded border hover:bg-blue-100">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <p class="text-gray-600">Anda belum memiliki pesanan</p>
            <a href="products.php" class="inline-block mt-4 bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                Mulai Berbelanja
            </a>
        </div>
    <?php endif; ?>
</div>