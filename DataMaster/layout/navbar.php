<!-- TOP NAVBAR -->
<nav class="top-navbar">
    <!-- BRAND -->
    <a href="../dashboard/index.php" class="top-navbar-brand">
        <div class="brand-icon">
            <i class="bi bi-camera-fill"></i>
        </div>
        <span>ProjectPeminjaman</span>
    </a>
    <!-- NAVBAR CONTENT -->
    <div class="top-navbar-content">
        <div class="d-flex align-items-center">
            <!-- TOGGLE SIDEBAR -->
            <button
                type="button"
                class="navbar-toggle"
                id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <!-- SEARCH -->
            <div class="navbar-search">
                <i class="bi bi-search"></i>
                <input
                    type="text"
                    id="menuSearch"
                    placeholder="Cari menu, data, atau fitur..."
                    autocomplete="off">
            </div>
        </div>
        <!-- RIGHT -->
        <div class="top-navbar-right">
            <div class="dropdown">
                <button
                    type="button"
                    class="navbar-notification"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="bi bi-bell-fill"></i>
                    <span class="notification-dot"></span>
                </button>
                <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                    <div class="notification-header">
                        <strong>Notifikasi</strong>
                        <span>0</span>
                    </div>
                    <div class="notification-empty">
                        <i class="bi bi-bell-slash"></i>
                        <p>Tidak ada notifikasi</p>
                        <small>
                            Notifikasi peminjaman akan muncul di sini.
                        </small>
                    </div>
                </div>
            </div>
            <!-- PROFILE -->
            <div class="dropdown">
                <button
                    class="profile-trigger dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <img
                        src="../assets/img/<?= htmlspecialchars($_SESSION['foto'] ?? 'archenPP.jpg'); ?>"
                        alt="Foto Profil"
                        class="top-profile-photo">
                    <div class="top-profile-info">
                        <div class="top-profile-name">
                            <?= htmlspecialchars($_SESSION['nama'] ?? 'Administrator'); ?>
                        </div>
                        <div class="top-profile-role">
                            <?= htmlspecialchars($_SESSION['role'] ?? 'admin'); ?>
                        </div>
                    </div>
                    <i class="bi bi-chevron-down profile-arrow"></i>
                </button>
                <!-- PROFILE DROPDOWN -->
                <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                    <div class="profile-dropdown-header">
                        <img
                            src="../assets/img/<?= htmlspecialchars($_SESSION['foto'] ?? 'archenPP.jpg'); ?>"
                            alt="Foto Profil"
                            class="profile-dropdown-photo">
                        <div>
                            <div class="profile-dropdown-name">
                                <?= htmlspecialchars($_SESSION['nama'] ?? 'Administrator'); ?>
                            </div>
                            <div class="profile-dropdown-username">
                                @<?= htmlspecialchars($_SESSION['username'] ?? 'admin'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-dropdown-divider"></div>
                    <!-- NANTI UNTUK PROFIL -->
                    <a
                        href="../pengguna/profile.php"
                        class="profile-dropdown-link">
                        <i class="bi bi-person-fill"></i>
                        <span>Profil Saya</span>
                    </a>
                    <a
                        href=".."
                        class="profile-dropdown-link">
                        <i class="bi bi-gear-fill"></i>
                        <span>Pengaturan</span>
                    </a>
                    <div class="profile-dropdown-divider"></div>
                    <!-- LOGOUT -->
                    <a
                        href="../login/logout.php"
                        class="profile-dropdown-link profile-logout"
                        onclick="return confirm('Yakin ingin logout?')">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <!-- PROFILE -->
    <div class="profile-box">
        <div class="profile-box-top">
            <img
                src="../assets/img/<?= htmlspecialchars($_SESSION['foto'] ?? 'archenPP.jpg'); ?>"
                alt="Foto Profil"
                class="profile-photo">
            <div>
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
        </div>
    </div>
    <!-- MENU -->
    <div class="sidebar-menu">
        <!-- DASHBOARD -->
        <a
            href="../dashboard/index.php"
            class="menu-item">
            <i class="bi bi-house-door-fill"></i>
            <span>Dashboard</span>
        </a>
        <!-- DATA MASTER -->
        <div class="menu-title">
            DATA MASTER
        </div>
        <a
            href="../kategori/index.php"
            class="menu-item">
            <i class="bi bi-tags-fill"></i>
            <span>Kategori</span>
        </a>
        <a
            href="../merk/index.php"
            class="menu-item">
            <i class="bi bi-bookmark-fill"></i>
            <span>Merk</span>
        </a>
        <a
            href="../kondisi_barang/index.php"
            class="menu-item">
            <i class="bi bi-tools"></i>
            <span>Kondisi Barang</span>
        </a>
        <a
            href="../barang/index.php"
            class="menu-item">
            <i class="bi bi-box-seam-fill"></i>
            <span>Barang</span>
        </a>
        <a
            href="../pengguna/index.php"
            class="menu-item">
            <i class="bi bi-people-fill"></i>
            <span>Pengguna</span>
        </a>
        <!-- TRANSAKSI -->
        <div class="menu-title">
            TRANSAKSI
        </div>
        <a
            href="#"
            class="menu-item">
            <i class="bi bi-clipboard-check-fill"></i>
            <span>Peminjaman</span>
        </a>
        <a
            href="#"
            class="menu-item">
            <i class="bi bi-arrow-return-left"></i>
            <span>Pengembalian</span>
        </a>
        <!-- LAPORAN -->
        <div class="menu-title">
            LAPORAN
        </div>
        <a
            href="#"
            class="menu-item">
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
</aside>
<script>
document.addEventListener("DOMContentLoaded", function () {
    /*SIDEBAR TOGGLE*/
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener("click", function () {
            sidebar.classList.toggle("closed");
            document.body.classList.toggle("sidebar-closed");
        });
    }
    /* SEARCH MENU */
    const searchInput = document.getElementById("menuSearch");
    const menuItems = document.querySelectorAll(".sidebar .menu-item");
    if (searchInput) {
        searchInput.addEventListener("input", function () {
            const keyword = this.value.toLowerCase().trim();
            menuItems.forEach(function (item) {
                const text = item.innerText.toLowerCase();
                if (text.includes(keyword)) {
                    item.style.display = "flex";
                } else {
                    item.style.display = "none";
                }
            });
        });
    }
});
</script>