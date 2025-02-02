<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

// Add to Cart functionality
if (isset($_POST['add_to_cart'])) {
    $produk_id = $_POST['produk_id'];
    $user = $_SESSION['user']['nama']; // Mengambil nama user dari session array

    // Check if product already in cart
    $check_cart = mysqli_query($conn, "SELECT * FROM keranjang WHERE user = '$user' AND produk_id = $produk_id");

    if (mysqli_num_rows($check_cart) > 0) {
        // Update quantity if product exists
        mysqli_query($conn, "UPDATE keranjang SET jumlah = jumlah + 1 WHERE user = '$user' AND produk_id = $produk_id");
    } else {
        // Insert new product to cart
        $insert_query = "INSERT INTO keranjang (user, produk_id, jumlah) VALUES ('$user', $produk_id, 1)";
        if (!mysqli_query($conn, $insert_query)) {
            die("Error: " . mysqli_error($conn));
        }
    }

    header("Location: menu.php");
    exit();
}

include '../templates/header.php';
include '../templates/navbar.php';
include '../templates/sidebar.php';
?>

<div class="main-content">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Products Menu</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <?php
                $query = mysqli_query($conn, "SELECT * FROM produk WHERE stok > 0");
                while ($produk = mysqli_fetch_assoc($query)) {
                ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <?php if ($produk['gambar']) : ?>
                                <img src="../assets/img/<?php echo $produk['gambar']; ?>" class="card-img-top"
                                    alt="<?php echo $produk['nama']; ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $produk['nama']; ?></h5>
                                <p class="card-text"><?php echo $produk['deskripsi']; ?></p>
                                <p class="card-text"><strong>Price: Rp
                                        <?php echo number_format($produk['harga'], 0, ',', '.'); ?></strong></p>
                                <p class="card-text">Stock: <?php echo $produk['stok']; ?></p>
                                <form method="POST">
                                    <input type="hidden" name="produk_id" value="<?php echo $produk['id']; ?>">
                                    <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>