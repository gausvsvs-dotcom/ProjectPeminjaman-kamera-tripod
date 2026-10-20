
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$halamanAktif = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-modern">
    <div class="container">

        <!-- Logo dan nama website -->
        <a href="home.php" class="navbar-brand brand-modern">
            <img src="assets/images/logo.jpg"
                 alt="Logo Peminjaman Kamera dan Tripod"
                 class="logo-navbar">

            <span class="brand-text">
                <strong>PEMINJAMAN KAMERA & TRIPOD</strong>
                <small>TEMUKAN KAMERA & TRIPPOD YG ANDA INGINKAN.</small>
            </span>
        </a>

        <!-- Tombol menu untuk HP -->
        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarMenu"
                aria-controls="navbarMenu"
                aria-expanded="false"
                aria-label="Buka menu navigasi">

            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu navigasi -->
        <div class="collapse navbar-collapse" id="navbarMenu">

            <ul class="navbar-nav navbar-menu-modern mx-auto">

                <li class="nav-item">
                    <a href="home.php"
                       class="nav-link <?php echo $halamanAktif == 'home.php' ? 'active' : ''; ?>">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a href="kamera&tripod.php"
                       class="nav-link <?php echo $halamanAktif == 'kamera&tripod.php' ? 'active' : ''; ?>">
                        Kamera & Tripod
                    </a>
                </li>

                <li class="nav-item">
                    <a href="tentang.php"
                       class="nav-link <?php echo $halamanAktif == 'tentang.php' ? 'active' : ''; ?>">
                        Tentang
                    </a>
                </li>

                <li class="nav-item">
                    <a href="kontak.php"
                       class="nav-link <?php echo $halamanAktif == 'kontak.php' ? 'active' : ''; ?>">
                        Kontak
                    </a>
                </li>

            </ul>

            <!-- Tombol login admin -->
            <div class="navbar-action">
                <?php if (isset($_SESSION['admin'])): ?>

                    <a href="dashboard_admin.php"
                       class="btn-login-modern">
                        Dashboard Admin
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                    <a href="logout.php"
                       class="btn-logout-modern">
                        Logout
                    </a>

                <?php else: ?>

                    <a href="login.php" class="btn-login-modern">
                        Login
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                <?php endif; ?>
            </div>

        </div>
    </div>
</nav>