<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Handle status update
if (isset($_POST['update_status'])) {
    $pesanan_id = $_POST['pesanan_id'];
    $new_status = $_POST['status'];

    $update_query = mysqli_query($conn, "
        UPDATE pesanan 
        SET status = '$new_status', 
            updated_at = CURRENT_TIMESTAMP 
        WHERE id = $pesanan_id
    ");

    if ($update_query) {
        $_SESSION['success'] = "Status pesanan berhasil diperbarui";
    } else {
        $_SESSION['error'] = "Gagal memperbarui status pesanan";
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Handle delete
if (isset($_POST['delete'])) {
    $pesanan_id = $_POST['pesanan_id'];

    // Start transaction
    mysqli_begin_transaction($conn);
    try {
        // Delete detail pesanan first
        mysqli_query($conn, "DELETE FROM detail_pesanan WHERE pesanan_id = $pesanan_id");
        // Then delete pesanan
        mysqli_query($conn, "DELETE FROM pesanan WHERE id = $pesanan_id");

        mysqli_commit($conn);
        $_SESSION['success'] = "Pesanan berhasil dihapus";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error'] = "Gagal menghapus pesanan";
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch all orders with user information
$query = mysqli_query($conn, "
    SELECT p.*, u.nama as nama_pelanggan 
    FROM pesanan p 
    JOIN users u ON p.user_id = u.id 
    ORDER BY p.created_at DESC
");

include '../templates/header.php';
include '../templates/navbar.php';
include '../templates/sidebar.php';
?>

<!-- Main Content -->
<div class="main-content">
    <h2 class="text-2xl font-bold mb-4">Daftar Pesanan</h2>

    <?php if (isset($_SESSION['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        <?php
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
    </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <?php
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
    </div>
    <?php endif; ?>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 border-b text-left">ID</th>
                    <th class="px-6 py-3 border-b text-left">Pelanggan</th>
                    <th class="px-6 py-3 border-b text-left">Total</th>
                    <th class="px-6 py-3 border-b text-left">Status</th>
                    <th class="px-6 py-3 border-b text-left">Tanggal</th>
                    <th class="px-6 py-3 border-b text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($pesanan = mysqli_fetch_assoc($query)): ?>
                <tr>
                    <td class="px-6 py-4 border-b">#<?php echo $pesanan['id']; ?></td>
                    <td class="px-6 py-4 border-b"><?php echo htmlspecialchars($pesanan['nama_pelanggan']); ?></td>
                    <td class="px-6 py-4 border-b">Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?>
                    </td>
                    <td class="px-6 py-4 border-b">
                        <form method="POST" class="inline">
                            <input type="hidden" name="pesanan_id" value="<?php echo $pesanan['id']; ?>">
                            <select name="status" onchange="this.form.submit()" class="border rounded px-2 py-1">
                                <option value="Diproses"
                                    <?php echo $pesanan['status'] == 'Diproses' ? 'selected' : ''; ?>>Diproses</option>
                                <option value="Dikirim"
                                    <?php echo $pesanan['status'] == 'Dikirim' ? 'selected' : ''; ?>>Dikirim</option>
                                <option value="Selesai"
                                    <?php echo $pesanan['status'] == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                            </select>
                            <input type="hidden" name="update_status" value="1">
                        </form>
                    </td>
                    <td class="px-6 py-4 border-b">
                        <?php echo date('d/m/Y H:i', strtotime($pesanan['created_at'])); ?>
                    </td>
                    <td class="px-6 py-4 border-b">
                        <div class="flex space-x-2">
                            <button onclick="showDetail(<?php echo $pesanan['id']; ?>)"
                                class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                Detail
                            </button>
                            <button onclick="editPesanan(<?php echo $pesanan['id']; ?>)"
                                class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                                Edit
                            </button>
                            <form method="POST" class="inline"
                                onsubmit="return confirm('Yakin ingin menghapus pesanan ini?');">
                                <input type="hidden" name="pesanan_id" value="<?php echo $pesanan['id']; ?>">
                                <input type="hidden" name="delete" value="1">
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for order details -->
<div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div id="modalContent"></div>
        <div class="text-right mt-4">
            <button onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
function showDetail(pesananId) {
    const modal = document.getElementById('detailModal');
    const modalContent = document.getElementById('modalContent');

    // Show loading state
    modal.classList.remove('hidden');
    modalContent.innerHTML = 'Loading...';

    // Fetch order details
    fetch('detail_pesanan.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'pesanan_id=' + pesananId
        })
        .then(response => response.text())
        .then(html => {
            modalContent.innerHTML = html;
        })
        .catch(error => {
            modalContent.innerHTML = 'Error loading details: ' + error;
        });
}

function closeModal() {
    document.getElementById('detailModal').classList.add('hidden');
}

function editPesanan(pesananId) {
    // Redirect to edit page
    window.location.href = 'edit_pesanan.php?id=' + pesananId;
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('detailModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>