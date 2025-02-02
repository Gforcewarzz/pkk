<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$user = $_SESSION['user']['nama'];

// Update quantity
if (isset($_POST['update_quantity'])) {
    $cart_id = $_POST['cart_id'];
    $jumlah = $_POST['jumlah'];

    if ($jumlah > 0) {
        mysqli_query($conn, "UPDATE keranjang SET jumlah = $jumlah WHERE id = $cart_id AND user = '$user'");
    } else {
        mysqli_query($conn, "DELETE FROM keranjang WHERE id = $cart_id AND user = '$user'");
    }

    header("Location: cart.php");
    exit();
}

// Remove item
if (isset($_POST['remove_item'])) {
    $cart_id = $_POST['cart_id'];
    mysqli_query($conn, "DELETE FROM keranjang WHERE id = $cart_id AND user = '$user'");

    header("Location: cart.php");
    exit();
}

// Checkout process
if (isset($_POST['checkout'])) {
    mysqli_query($conn, "INSERT INTO pesanan (user_id, total_harga) VALUES ({$_SESSION['user']['id']}, {$_POST['total_harga']})");
    $pesanan_id = mysqli_insert_id($conn);

    $cart_items = mysqli_query($conn, "SELECT k.*, p.harga 
                                     FROM keranjang k 
                                     JOIN produk p ON k.produk_id = p.id 
                                     WHERE k.user = '$user'");

    while ($item = mysqli_fetch_assoc($cart_items)) {
        $subtotal = $item['jumlah'] * $item['harga'];
        mysqli_query($conn, "INSERT INTO detail_pesanan (pesanan_id, produk_id, jumlah, harga, subtotal) 
                            VALUES ($pesanan_id, {$item['produk_id']}, {$item['jumlah']}, {$item['harga']}, $subtotal)");
        mysqli_query($conn, "UPDATE produk SET stok = stok - {$item['jumlah']} WHERE id = {$item['produk_id']}");
    }

    mysqli_query($conn, "DELETE FROM keranjang WHERE user = '$user'");
    header("Location: menu.php?checkout_success=1");
    exit();
}

include '../templates/header.php';
include '../templates/navbar.php';
include '../templates/sidebar_user.php';

?>

<div class="main-content">
    <div class="cart-header">
        <div class="cart-title">
            <h1>Shopping Cart</h1>
        </div>
    </div>

    <div class="cart-content">
        <div class="cart-wrapper">
            <div class="cart-box">
                <?php if (isset($_GET['checkout_success'])) : ?>
                    <div class="cart-alert-success">
                        Checkout successful! Your order has been placed.
                    </div>
                <?php endif; ?>

                <div class="cart-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                            $cart_query = mysqli_query($conn, "SELECT k.*, p.nama, p.harga, p.stok 
                                                             FROM keranjang k 
                                                             JOIN produk p ON k.produk_id = p.id 
                                                             WHERE k.user = '$user'");

                            while ($cart = mysqli_fetch_assoc($cart_query)) {
                                $subtotal = $cart['jumlah'] * $cart['harga'];
                                $total += $subtotal;
                            ?>
                                <tr>
                                    <td><?php echo $cart['nama']; ?></td>
                                    <td>Rp <?php echo number_format($cart['harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <form method="POST" class="cart-quantity">
                                            <input type="hidden" name="cart_id" value="<?php echo $cart['id']; ?>">
                                            <button type="button" class="cart-btn-minus"
                                                onclick="if(this.form.jumlah.value > 1) this.form.jumlah.value--; this.form.submit();">-</button>
                                            <input type="number" name="jumlah" value="<?php echo $cart['jumlah']; ?>"
                                                class="cart-input" min="1" max="<?php echo $cart['stok']; ?>"
                                                onchange="this.form.submit();">
                                            <button type="button" class="cart-btn-plus"
                                                onclick="if(this.form.jumlah.value < <?php echo $cart['stok']; ?>) this.form.jumlah.value++; this.form.submit();">+</button>
                                            <input type="hidden" name="update_quantity" value="1">
                                        </form>
                                    </td>
                                    <td>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                                    <td>
                                        <form method="POST" class="cart-remove">
                                            <input type="hidden" name="cart_id" value="<?php echo $cart['id']; ?>">
                                            <button type="submit" name="remove_item" class="cart-btn-remove">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <?php if (mysqli_num_rows($cart_query) > 0) : ?>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="cart-total-label">Total:</td>
                                    <td colspan="2" class="cart-total-amount">Rp
                                        <?php echo number_format($total, 0, ',', '.'); ?></td>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>

                <?php if (mysqli_num_rows($cart_query) > 0) : ?>
                    <!-- In cart.php, modify the checkout form to: -->
                    <form method="POST" action="pembayaran.php" class="cart-checkout">
                        <button type="submit" name="checkout" class="cart-btn-checkout">Checkout</button>
                    </form>
                <?php else : ?>
                    <div class="cart-empty">
                        <p>Your cart is empty</p>
                        <a href="menu.php" class="cart-btn-shopping">Continue Shopping</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>