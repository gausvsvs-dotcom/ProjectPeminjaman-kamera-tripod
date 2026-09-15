
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">

    <a href="home.php" class="navbar-brand d-flex align-items-center">
        <img src="assets/images/logo.jpg" alt="Logo" class="logo-navbar">

        <div>
            <h4 class="mb-0 fw-bold btn-primary"> Sistem Peminjaman Kamera dan Tripod</h4>
        </div>
    </a>

    <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarMenu">

        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMenu">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a href="home.php"
                class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : ''; ?>"> Home</a>
            </li>

            <li class="nav-item">
                <a href="kamera&tripod.php"
                class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'kamera&tripod.php' ? 'active' : ''; ?>"> Kamera dan Tripod</a>
            </li>

            <li class="nav-item">
                <a href="tentang.php"
                class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'tentang.php' ? 'active' : ''; ?>"> Tentang</a>
            </li>

            <li class="nav-item">
                <a href="kontak.php"
                class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'kontak.php' ? 'active' : ''; ?>"> Kontak</a>
            </li>
        </ul>

        <?php if (isset($_SESSION['admin'])): ?>
            <a href="dashboard_admin.php" class="btn btn-login me-2">
                Dashboard Admin</a>
                
            <a href="logout.php" class="btn btn-outline-danger">Logout</a>
        
        <?php else: ?>
            
            <a href="login.php" class="btn btn-login">Login</a>
            
        <?php endif; ?>

    </div>
    </div>
</nav>