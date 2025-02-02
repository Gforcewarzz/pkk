<!-- admin/dashboard.php -->
<?php
session_start();
require_once '../config/database.php';

// Check admin authentication
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch total products
$products_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM produk");
$total_products = mysqli_fetch_assoc($products_query)['total'];

// Fetch total orders and calculate total revenue
$orders_query = mysqli_query($conn, "SELECT 
    COUNT(*) as total_orders,
    SUM(total_harga) as total_revenue,
    COUNT(CASE WHEN status = 'Diproses' THEN 1 END) as pending_orders,
    COUNT(CASE WHEN status = 'Selesai' THEN 1 END) as completed_orders
    FROM pesanan");
$orders_data = mysqli_fetch_assoc($orders_query);

// Fetch total users
$users_query = mysqli_query($conn, "SELECT 
    COUNT(*) as total_users,
    COUNT(CASE WHEN role = 'user' THEN 1 END) as customer_count
    FROM users");
$users_data = mysqli_fetch_assoc($users_query);

// Fetch cart items
$cart_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM keranjang");
$total_cart_items = mysqli_fetch_assoc($cart_query)['total'];

include '../templates/header.php';
include '../templates/navbar.php';
include '../templates/sidebar.php';
?>

<div class="main-content p-4">
    <h2 class="text-2xl font-bold mb-6">Dashboard</h2>

    <!-- Info boxes -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Orders -->
        <div class="bg-blue-500 text-white rounded-lg p-4 shadow-lg">
            <div class="flex justify-between">
                <div>
                    <h3 class="text-3xl font-bold mb-2"><?php echo number_format($orders_data['total_orders']); ?></h3>
                    <p class="text-blue-100">Total Pesanan</p>
                </div>
                <div class="text-4xl opacity-50">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-green-500 text-white rounded-lg p-4 shadow-lg">
            <div class="flex justify-between">
                <div>
                    <h3 class="text-3xl font-bold mb-2">Rp
                        <?php echo number_format($orders_data['total_revenue'], 0, ',', '.'); ?></h3>
                    <p class="text-green-100">Total Pendapatan</p>
                </div>
                <div class="text-4xl opacity-50">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="bg-purple-500 text-white rounded-lg p-4 shadow-lg">
            <div class="flex justify-between">
                <div>
                    <h3 class="text-3xl font-bold mb-2"><?php echo number_format($total_products); ?></h3>
                    <p class="text-purple-100">Total Produk</p>
                </div>
                <div class="text-4xl opacity-50">
                    <i class="fas fa-box"></i>
                </div>
            </div>
        </div>

        <!-- Total Customers -->
        <div class="bg-yellow-500 text-white rounded-lg p-4 shadow-lg">
            <div class="flex justify-between">
                <div>
                    <h3 class="text-3xl font-bold mb-2"><?php echo number_format($users_data['customer_count']); ?></h3>
                    <p class="text-yellow-100">Total Customers</p>
                </div>
                <div class="text-4xl opacity-50">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Pending Orders -->
        <div class="bg-red-500 text-white rounded-lg p-4 shadow-lg">
            <div class="flex justify-between">
                <div>
                    <h3 class="text-3xl font-bold mb-2"><?php echo number_format($orders_data['pending_orders']); ?>
                    </h3>
                    <p class="text-red-100">Pesanan Diproses</p>
                </div>
                <div class="text-4xl opacity-50">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>

        <!-- Completed Orders -->
        <div class="bg-indigo-500 text-white rounded-lg p-4 shadow-lg">
            <div class="flex justify-between">
                <div>
                    <h3 class="text-3xl font-bold mb-2"><?php echo number_format($orders_data['completed_orders']); ?>
                    </h3>
                    <p class="text-indigo-100">Pesanan Selesai</p>
                </div>
                <div class="text-4xl opacity-50">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <!-- Cart Items -->
        <div class="bg-teal-500 text-white rounded-lg p-4 shadow-lg">
            <div class="flex justify-between">
                <div>
                    <h3 class="text-3xl font-bold mb-2"><?php echo number_format($total_cart_items); ?></h3>
                    <p class="text-teal-100">Items dalam Cart</p>
                </div>
                <div class="text-4xl opacity-50">
                    <i class="fas fa-shopping-basket"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>