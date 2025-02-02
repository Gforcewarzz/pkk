<!-- templates/navbar.php -->
<nav class="navbar">
    <div class="navbar-left">
        <a href="#" class="nav-toggle" id="sidebar-toggle">
            <i class="fas fa-bars"></i> <!-- Menggunakan ikon bars yang standard -->
        </a>
        <div class="navbar-brand">
            Parfum Shop
        </div>
    </div>

    <div class="navbar-nav">
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="far fa-bell"></i> <!-- Menggunakan far untuk outline style -->
                <span class="badge">3</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="far fa-envelope"></i> <!-- Menggunakan far untuk outline style -->
                <span class="badge">4</span>
            </a>
        </div>
        <div class="user-dropdown">
            <a href="#" class="nav-link">
                <i class="far fa-user-circle"></i> <!-- Menggunakan far untuk outline style -->
                <span>Administrator</span>
            </a>
            <div class="user-menu">
                <a href="#">
                    <i class="far fa-user"></i>
                    <span>My Profile</span>
                </a>
                <a href="#">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                <a href="#">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>
</nav>