<!-- user/dashboard.php -->
<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: login.php');
    exit();
}

$user_name = $_SESSION['user']['nama']; // Ambil nama user dari sesi

include '../templates/header.php';
include '../templates/navbar.php';
include '../templates/sidebar_user.php';
?>

<div class="main-content p-6">
    <div class="bg-white shadow-md rounded-lg p-6 text-center">
        <h2 class="text-3xl font-bold text-gray-800">Selamat Datang di Aromathica,
            <?php echo htmlspecialchars($user_name); ?>! 🌿</h2>
        <p class="text-gray-600 mt-2">Temukan pengalaman belanja terbaik untuk produk aroma terapi dan relaksasi. Kami
            menghadirkan berbagai produk berkualitas yang siap menemani hari-harimu.</p>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Produk Favorit -->
            <div class="bg-blue-500 text-white rounded-lg p-4 shadow-lg">
                <div class="flex items-center">
                    <i class="fas fa-heart text-4xl opacity-50"></i>
                    <div class="ml-4">
                        <h3 class="text-xl font-bold">Produk Favorit</h3>
                        <p class="text-blue-100">Lihat produk yang sedang tren dan banyak disukai.</p>
                    </div>
                </div>
            </div>

            <!-- Promo Spesial -->
            <div class="bg-green-500 text-white rounded-lg p-4 shadow-lg">
                <div class="flex items-center">
                    <i class="fas fa-tag text-4xl opacity-50"></i>
                    <div class="ml-4">
                        <h3 class="text-xl font-bold">Promo Spesial</h3>
                        <p class="text-green-100">Dapatkan diskon dan penawaran menarik.</p>
                    </div>
                </div>
            </div>

            <!-- Bantuan -->
            <div class="bg-yellow-500 text-white rounded-lg p-4 shadow-lg">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-4xl opacity-50"></i>
                    <div class="ml-4">
                        <h3 class="text-xl font-bold">Bantuan & Support</h3>
                        <p class="text-yellow-100">Hubungi kami jika ada pertanyaan atau butuh bantuan.</p>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-gray-500 mt-8">Nikmati pengalaman belanja yang nyaman hanya di <b>Aromathica</b>. Kami siap
            memberikan yang terbaik untuk Anda! 💖</p>
    </div>
</div>

<?php include '../templates/footer.php'; ?>