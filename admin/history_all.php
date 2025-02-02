<?php
session_start();
require_once '../config/database.php';
// Fetch successful orders statistics
$stats_query = mysqli_query($conn, "
    SELECT 
        COUNT(*) as total_orders,
        SUM(total_harga) as total_revenue,
        DATE_FORMAT(created_at, '%Y-%m') as month
    FROM pesanan 
    WHERE status = 'Selesai'
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month DESC
");

$monthly_stats = [];
while ($row = mysqli_fetch_assoc($stats_query)) {
    $monthly_stats[] = $row;
}

// Get latest successful orders
$recent_orders = mysqli_query($conn, "
    SELECT p.*, u.nama as nama_pelanggan 
    FROM pesanan p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.status = 'Selesai'
    ORDER BY p.created_at DESC 
    LIMIT 10
");

include '../templates/header.php';
include '../templates/navbar.php';
include '../templates/sidebar.php';
?>

<!-- Main Content -->
<div class="main-content">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Laporan Penjualan</h2>
        <button onclick="printReport()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
            Cetak Laporan
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <?php
        $total_all_time = 0;
        $total_orders = 0;
        foreach ($monthly_stats as $stat) {
            $total_all_time += $stat['total_revenue'];
            $total_orders += $stat['total_orders'];
        }
        ?>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm">Total Pendapatan</h3>
            <p class="text-2xl font-bold">Rp <?php echo number_format($total_all_time, 0, ',', '.'); ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm">Total Pesanan Selesai</h3>
            <p class="text-2xl font-bold"><?php echo $total_orders; ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm">Rata-rata Per Pesanan</h3>
            <p class="text-2xl font-bold">Rp
                <?php echo $total_orders > 0 ? number_format($total_all_time / $total_orders, 0, ',', '.') : 0; ?></p>
        </div>
    </div>

    <!-- Monthly Report Table -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4">
            <h3 class="text-lg font-semibold mb-4">Laporan Bulanan</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bulan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Pesanan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Pendapatan
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($monthly_stats as $stat): ?>
                            <tr>
                                <td class="px-6 py-4"><?php echo date('F Y', strtotime($stat['month'] . '-01')); ?></td>
                                <td class="px-6 py-4"><?php echo $stat['total_orders']; ?></td>
                                <td class="px-6 py-4">Rp <?php echo number_format($stat['total_revenue'], 0, ',', '.'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-4">
            <h3 class="text-lg font-semibold mb-4">Pesanan Selesai Terbaru</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
                            <tr>
                                <td class="px-6 py-4">#<?php echo $order['id']; ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($order['nama_pelanggan']); ?></td>
                                <td class="px-6 py-4">Rp <?php echo number_format($order['total_harga'], 0, ',', '.'); ?>
                                </td>
                                <td class="px-6 py-4"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Print Modal -->
<div id="printModal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div id="printContent">
            <h2 class="text-2xl font-bold mb-4 text-center">Laporan Penjualan</h2>
            <div class="mb-4">
                <p class="text-center">Periode: <?php echo date('F Y'); ?></p>
            </div>

            <!-- Print Statistics -->
            <div class="mb-6">
                <table class="min-w-full border">
                    <tr>
                        <td class="border px-4 py-2 font-bold">Total Pendapatan:</td>
                        <td class="border px-4 py-2">Rp <?php echo number_format($total_all_time, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td class="border px-4 py-2 font-bold">Total Pesanan:</td>
                        <td class="border px-4 py-2"><?php echo $total_orders; ?></td>
                    </tr>
                    <tr>
                        <td class="border px-4 py-2 font-bold">Rata-rata Per Pesanan:</td>
                        <td class="border px-4 py-2">Rp
                            <?php echo $total_orders > 0 ? number_format($total_all_time / $total_orders, 0, ',', '.') : 0; ?>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Monthly Data for Print -->
            <h3 class="font-bold mb-2">Detail Bulanan</h3>
            <table class="min-w-full border mb-6">
                <thead>
                    <tr>
                        <th class="border px-4 py-2">Bulan</th>
                        <th class="border px-4 py-2">Jumlah Pesanan</th>
                        <th class="border px-4 py-2">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($monthly_stats as $stat): ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo date('F Y', strtotime($stat['month'] . '-01')); ?></td>
                            <td class="border px-4 py-2 text-center"><?php echo $stat['total_orders']; ?></td>
                            <td class="border px-4 py-2">Rp
                                <?php echo number_format($stat['total_revenue'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="flex justify-end space-x-4 mt-4">
            <button onclick="doPrint()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Cetak
            </button>
            <button onclick="closePrintModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function printReport() {
        document.getElementById('printModal').classList.remove('hidden');
    }

    function closePrintModal() {
        document.getElementById('printModal').classList.add('hidden');
    }

    function doPrint() {
        const printContent = document.getElementById('printContent').innerHTML;
        const originalContent = document.body.innerHTML;

        document.body.innerHTML = `
        <div style="padding: 20px;">
            ${printContent}
        </div>
    `;

        window.print();
        document.body.innerHTML = originalContent;

        // Reattach event listeners
        document.querySelector('[onclick="printReport()"]').addEventListener('click', printReport);
        document.querySelector('[onclick="closePrintModal()"]').addEventListener('click', closePrintModal);
        document.querySelector('[onclick="doPrint()"]').addEventListener('click', doPrint);
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('printModal');
        if (event.target == modal) {
            closePrintModal();
        }
    }
</script>