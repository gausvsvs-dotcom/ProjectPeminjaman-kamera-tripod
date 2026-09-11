<div class="sidebar">
    <!-- BRAND -->
    <div class="sidebar-brand">
        <i class="bi bi-camera-fill"></i>
        <span>ProjectPeminjaman</span>
    </div>
    <!-- PROFILE -->
    <div class="profile-box">
        <img
            src="../assets/img/<?= htmlspecialchars($_SESSION['foto'] ?? 'archenPP.jpg'); ?>"
            alt="Foto Profil"
            class="profile-photo">
        <div class="profile-name">
            <?= htmlspecialchars($_SESSION['nama'] ?? 'Administrator'); ?>
        </div>
        <div class="profile-username">
            @<?= htmlspecialchars($_SESSION['username'] ?? 'admin'); ?>
        </div>
        <span class="profile-role">
            <?= htmlspecialchars($_SESSION['role'] ?? 'admin'); ?>
        </span>
    </div>
    <!-- MENU -->
    <div class="sidebar-menu">
        <a href="../dashboard/index.php" class="menu-item">
            <i class="bi bi-house-door-fill"></i>
            <span>Dashboard</span>
        </a>
        <!-- DATA MASTER -->
        <div class="menu-title">
            DATA MASTER
        </div>
        <a href="../kategori/index.php" class="menu-item">
            <i class="bi bi-tags-fill"></i>
            <span>Kategori</span>
        </a>
        <a href="../merk/index.php" class="menu-item">
            <i class="bi bi-bookmark-fill"></i>
            <span>Merk</span>
        </a>
        <a href="../kondisi_barang/index.php" class="menu-item">
            <i class="bi bi-tools"></i>
            <span>Kondisi Barang</span>
        </a>
        <a href="../barang/index.php" class="menu-item">
            <i class="bi bi-box-seam-fill"></i>
            <span>Barang</span>
        </a>
        <a href="../pengguna/index.php" class="menu-item">
            <i class="bi bi-people-fill"></i>
            <span>Pengguna</span>
        </a>
        <!-- TRANSAKSI -->
        <div class="menu-title">
            TRANSAKSI
        </div>
        <a href="#" class="menu-item">
            <i class="bi bi-clipboard-check-fill"></i>
            <span>Peminjaman</span>
        </a>
        <a href="#" class="menu-item">
            <i class="bi bi-arrow-return-left"></i>
            <span>Pengembalian</span>
        </a>
        <!-- LAPORAN -->
        <div class="menu-title">
            LAPORAN
        </div>
        <a href="#" class="menu-item">
            <i class="bi bi-file-earmark-bar-graph-fill"></i>
            <span>Laporan</span>
        </a>

        <!-- LOGOUT -->
        <div class="sidebar-bottom">
            <a
                href="../login/logout.php"
                class="menu-item logout"
                onclick="return confirm('Yakin ingin logout?')">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</div>