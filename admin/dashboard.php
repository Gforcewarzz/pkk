<!-- admin/dashboard.php -->
<?php
include '../templates/header.php';
include '../templates/navbar.php';
include '../templates/sidebar.php';
?>

<div class="main-content">
    <!-- Info boxes -->
    <div class="card-container">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>150</h3>
                <p>New Orders</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
        <div class="small-box bg-success">
            <div class="inner">
                <h3>53%</h3>
                <p>Bounce Rate</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>

    <div class="card-container">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>150</h3>
                <p>New Orders</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
        <div class="small-box bg-success">
            <div class="inner">
                <h3>53%</h3>
                <p>Bounce Rate</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        <!-- Tambahkan card lainnya sesuai kebutuhan -->
    </div>
</div>


<?php include '../templates/footer.php'; ?>