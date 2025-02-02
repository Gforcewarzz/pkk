<?php
// edit_pesanan.php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Get pesanan data
if (isset($_GET['id'])) {
    $pesanan_id = $_GET['id'];

    $query = mysqli_query($conn, "
        SELECT p.*, u.nama as nama_pelanggan 
        FROM pesanan p 
        JOIN users u ON p.user_id = u.id 
        WHERE p.id = $pesanan_id
    ");

    $pesanan = mysqli_fetch_assoc($query);

    // Get detail pesanan
    $detail_query = mysqli_query($conn, "
        SELECT dp.*, pr.nama as nama_produk, pr.harga as harga_produk
        FROM detail_pesanan dp
        JOIN produk pr ON dp.produk_id = pr.id
        WHERE dp.pesanan_id = $pesanan_id
    ");

    // Get all products for dropdown
    $produk_query = mysqli_query($conn, "SELECT * FROM produk WHERE stok > 0");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_pesanan'])) {
    $pesanan_id = $_POST['pesanan_id'];
    $status = $_POST['status'];
    $produk_ids = $_POST['produk_id'];
    $quantities = $_POST['jumlah'];

    mysqli_begin_transaction($conn);

    try {
        // Update pesanan status
        mysqli_query($conn, "
            UPDATE pesanan 
            SET status = '$status',
                updated_at = CURRENT_TIMESTAMP
            WHERE id = $pesanan_id
        ");

        // Delete existing detail_pesanan
        mysqli_query($conn, "DELETE FROM detail_pesanan WHERE pesanan_id = $pesanan_id");

        // Insert new detail_pesanan
        $total_harga = 0;
        for ($i = 0; $i < count($produk_ids); $i++) {
            if (empty($produk_ids[$i]) || empty($quantities[$i])) continue;

            // Get product price
            $produk_query = mysqli_query($conn, "SELECT harga FROM produk WHERE id = " . $produk_ids[$i]);
            $produk = mysqli_fetch_assoc($produk_query);
            $harga = $produk['harga'];
            $subtotal = $harga * $quantities[$i];
            $total_harga += $subtotal;

            mysqli_query($conn, "
                INSERT INTO detail_pesanan (pesanan_id, produk_id, jumlah, harga, subtotal)
                VALUES ($pesanan_id, " . $produk_ids[$i] . ", " . $quantities[$i] . ", $harga, $subtotal)
            ");
        }

        // Update total harga in pesanan
        mysqli_query($conn, "
            UPDATE pesanan 
            SET total_harga = $total_harga
            WHERE id = $pesanan_id
        ");

        mysqli_commit($conn);
        $_SESSION['success'] = "Pesanan berhasil diperbarui";
        header('Location: index.php'); // Redirect to order list
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error'] = "Gagal memperbarui pesanan: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pesanan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-6">Edit Pesanan #<?php echo $pesanan_id; ?></h2>

            <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="pesanan_id" value="<?php echo $pesanan_id; ?>">

                <div class="mb-6">
                    <label class="block mb-2">Pelanggan:</label>
                    <div class="font-medium"><?php echo htmlspecialchars($pesanan['nama_pelanggan']); ?></div>
                </div>

                <div class="mb-6">
                    <label class="block mb-2">Status:</label>
                    <select name="status" class="border rounded px-3 py-2 w-full">
                        <option value="Diproses" <?php echo $pesanan['status'] == 'Diproses' ? 'selected' : ''; ?>>
                            Diproses</option>
                        <option value="Dikirim" <?php echo $pesanan['status'] == 'Dikirim' ? 'selected' : ''; ?>>Dikirim
                        </option>
                        <option value="Selesai" <?php echo $pesanan['status'] == 'Selesai' ? 'selected' : ''; ?>>Selesai
                        </option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block mb-2">Produk:</label>
                    <div id="produk-container">
                        <?php while ($detail = mysqli_fetch_assoc($detail_query)): ?>
                        <div class="produk-item flex gap-4 mb-4">
                            <select name="produk_id[]" class="border rounded px-3 py-2 flex-1">
                                <?php
                                    mysqli_data_seek($produk_query, 0);
                                    while ($produk = mysqli_fetch_assoc($produk_query)):
                                    ?>
                                <option value="<?php echo $produk['id']; ?>"
                                    <?php echo $produk['id'] == $detail['produk_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($produk['nama']); ?> -
                                    Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                            <input type="number" name="jumlah[]" value="<?php echo $detail['jumlah']; ?>"
                                class="border rounded px-3 py-2 w-32" min="1">
                            <button type="button" onclick="removeProduk(this)"
                                class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                Hapus
                            </button>
                        </div>
                        <?php endwhile; ?>
                    </div>

                    <button type="button" onclick="addProduk()"
                        class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mt-2">
                        Tambah Produk
                    </button>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="index.php" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                        Batal
                    </a>
                    <button type="submit" name="update_pesanan"
                        class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function addProduk() {
        const container = document.getElementById('produk-container');
        const produkItem = document.createElement('div');
        produkItem.className = 'produk-item flex gap-4 mb-4';

        // Clone the first product dropdown
        const firstSelect = document.querySelector('select[name="produk_id[]"]');
        const newSelect = firstSelect.cloneNode(true);
        newSelect.selectedIndex = 0;

        produkItem.innerHTML = `
            ${newSelect.outerHTML}
            <input type="number" name="jumlah[]" value="1" class="border rounded px-3 py-2 w-32" min="1">
            <button type="button" onclick="removeProduk(this)" 
                    class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                Hapus
            </button>
        `;

        container.appendChild(produkItem);
    }

    function removeProduk(button) {
        const container = document.getElementById('produk-container');
        if (container.children.length > 1) {
            button.parentElement.remove();
        } else {
            alert('Pesanan harus memiliki minimal satu produk');
        }
    }
    </script>
</body>

</html>