<?php
// detail_pesanan.php - save in the same directory as your main orders page
session_start();
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    exit('Unauthorized access');
}

// Validate input
if (!isset($_POST['pesanan_id'])) {
    exit('ID pesanan tidak ditemukan');
}

$pesanan_id = $_POST['pesanan_id'];

// Get order details with customer information
$query = mysqli_query($conn, "
    SELECT p.*, u.nama as nama_pelanggan, u.email 
    FROM pesanan p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.id = $pesanan_id
");

if (!$query) {
    exit('Error query: ' . mysqli_error($conn));
}

$pesanan = mysqli_fetch_assoc($query);
if (!$pesanan) {
    exit('Data pesanan tidak ditemukan');
}

// Get order items
$items_query = mysqli_query($conn, "
    SELECT dp.*, pr.nama as nama_produk, pr.gambar
    FROM detail_pesanan dp
    JOIN produk pr ON dp.produk_id = pr.id
    WHERE dp.pesanan_id = $pesanan_id
");

if (!$items_query) {
    exit('Error query items: ' . mysqli_error($conn));
}
?>

<div class="p-4">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-xl font-bold mb-2">Detail Pesanan #<?php echo $pesanan['id']; ?></h2>
            <p class="text-gray-600">
                Tanggal: <?php echo date('d F Y H:i', strtotime($pesanan['created_at'])); ?>
            </p>
        </div>
        <div class="text-right">
            <span class="<?php
                            switch ($pesanan['status']) {
                                case 'Diproses':
                                    echo 'bg-yellow-100 text-yellow-800';
                                    break;
                                case 'Dikirim':
                                    echo 'bg-blue-100 text-blue-800';
                                    break;
                                case 'Selesai':
                                    echo 'bg-green-100 text-green-800';
                                    break;
                                default:
                                    echo 'bg-gray-100 text-gray-800';
                            }
                            ?> px-3 py-1 rounded-full text-sm font-medium">
                <?php echo $pesanan['status']; ?>
            </span>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="font-semibold mb-2">Informasi Pelanggan</h3>
            <p><strong>Nama:</strong> <?php echo htmlspecialchars($pesanan['nama_pelanggan']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($pesanan['email']); ?></p>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="font-semibold mb-2">Informasi Pesanan</h3>
            <p><strong>Status Pesanan:</strong> <?php echo $pesanan['status']; ?></p>
            <p><strong>Total Pesanan:</strong> Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?></p>
        </div>
    </div>

    <div class="mt-6">
        <h3 class="font-semibold mb-4">Detail Produk</h3>
        <div class="space-y-4">
            <?php while ($item = mysqli_fetch_assoc($items_query)): ?>
                <div class="flex items-center border-b pb-4">
                    <div class="flex-shrink-0 w-20 h-20">
                        <?php if ($item['gambar']): ?>
                            <img src="../assets/img/<?php echo htmlspecialchars($item['gambar']); ?>"
                                alt="<?php echo htmlspecialchars($item['nama_produk']); ?>"
                                class="w-full h-full object-cover rounded">
                        <?php else: ?>
                            <div class="w-full h-full bg-gray-200 rounded flex items-center justify-center">
                                <span class="text-gray-500">No Image</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="ml-4 flex-grow">
                        <h4 class="font-medium"><?php echo htmlspecialchars($item['nama_produk']); ?></h4>
                        <p class="text-gray-600">
                            <?php echo $item['jumlah']; ?> x Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?>
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="mt-6 border-t pt-4">
        <div class="text-right">
            <p class="text-gray-600">Subtotal: Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?></p>
            <p class="text-xl font-bold mt-2">
                Total: Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?>
            </p>
        </div>
    </div>
</div>