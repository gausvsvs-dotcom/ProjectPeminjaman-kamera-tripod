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
            <div class="navbar-search-wrapper">
                <div class="navbar-search">
                    <i class="bi bi-search"></i>
                    <input
                        type="text"
                        id="menuSearch"
                        placeholder="Cari menu dan data..."
                        autocomplete="off"> 
                </div>

                <div id="searchResults" class="search-results"></div>
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
                            Notifikasi pembayaran akan muncul di sini.
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
                        src="/ProjectPeminjaman-kamera-tripod/DataMaster/assets/img/<?= htmlspecialchars($_SESSION['foto'] ?? 'diana.jpg'); ?>"
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
                            src="/ProjectPeminjaman-kamera-tripod/DataMaster/assets/img/<?= htmlspecialchars($_SESSION['foto'] ?? 'diana.jpg'); ?>"
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
                    <!-- BUKA MODAL PROFIL -->
                    <button
                        type="button"
                        class="profile-dropdown-link border-0 w-100 text-start"
                        data-bs-toggle="modal"
                        data-bs-target="#profileModal">
                        <i class="bi bi-person-fill"></i>
                        <span>Profil Saya</span>
                    </button>
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
                src="/ProjectPeminjaman-kamera-tripod/DataMaster/assets/img/<?= htmlspecialchars($_SESSION['foto'] ?? 'diana.jpg'); ?>"
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

        <div class="menu-title">
            TRANSAKSI
        </div>

        <a
            href="../peminjaman/index.php"
            class="menu-item">
            <i class="bi bi-clipboard-check-fill"></i>
            <span>Peminjaman</span>
        </a>
        <a
            href="../../Transaksi/Pengembalian/_detail_pengembalian.php "
            class="menu-item">
            <i class="bi bi-arrow-return-left"></i>
            <span>Pengembalian</span>
        </a>
        <a
            href="../../Transaksi/Pembayaran/detail_pembayaran.php"
            class="menu-item">
            <i class="bi bi-wallet-fill"></i>
            <span>Pembayaran</span>
        </a>
        <div class="menu-title">
            LAYANAN KONSUMEN
        </div>
        <a
            href="../kontak/index.php"
            class="menu-item">
            <i class="bi bi-chat-left-text-fill"></i>
            <span>Pesan Kontak</span>
        </a>
        <!-- LAPORAN -->
        <div class="menu-title">
            LAPORAN
        </div> 
        <a
            href="../laporan/peminjaman.php"
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

    // SIDEBAR TOGGLE
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener("click", function () {
            sidebar.classList.toggle("closed");
            document.body.classList.toggle("sidebar-closed");
        });
    }

    // SEARCH MENU DAN DATA
    const searchInput = document.getElementById("menuSearch");
    const searchResults = document.getElementById("searchResults");
    const menuItems = document.querySelectorAll(".sidebar .menu-item");

    if (!searchInput) return;

    searchInput.addEventListener("input", function () {
        const keyword = this.value.toLowerCase().trim();

        // FILTER MENU SIDEBAR
        let menuDitemukan = false;

        menuItems.forEach(function (item) {
            const cocok = item.innerText.toLowerCase().includes(keyword);

            item.style.display = cocok ? "flex" : "none";

            if (cocok && keyword.length >= 2) {
                menuDitemukan = true;
            }
        });

        if (!searchResults) return;

        searchResults.innerHTML = "";

        // SEMBUNYIKAN HASIL JIKA KURANG DARI 2 KARAKTER
        if (keyword.length < 2) {
            searchResults.classList.remove("show");
            return;
        }

        // CARI DATA DARI DATABASE
        fetch("/ProjectPeminjaman-kamera-tripod/DataMaster/search.php?q=" + encodeURIComponent(keyword))
            .then(function (response) {
                if (!response.ok) {
                    throw new Error("Gagal mengambil data pencarian");
                }

                return response.json();
            })
            .then(function (data) {

                searchResults.innerHTML = "";

                // KELOMPOKKAN DATA BERDASARKAN KATEGORI
                const kelompok = {};

                data.forEach(function (item) {
                    const kategori = item.kategori || "Data";

                    if (!kelompok[kategori]) {
                        kelompok[kategori] = [];
                    }

                    kelompok[kategori].push(item);
                });

                // TAMPILKAN HASIL DATA
                Object.keys(kelompok).forEach(function (kategori) {

                    const title = document.createElement("div");
                    title.className = "search-result-title";
                    title.textContent = kategori.toUpperCase();

                    searchResults.appendChild(title);

                    kelompok[kategori].forEach(function (item) {

                        const link = document.createElement("a");
                        link.href = "/ProjectPeminjaman-kamera-tripod/DataMaster/" + item.url;
                        link.className = "search-result-item";

                        const nama = document.createElement("strong");
                        nama.textContent = item.nama;

                        const detail = document.createElement("small");
                        detail.textContent = item.kode + " • " + item.jenis;

                        link.appendChild(nama);
                        link.appendChild(detail);

                        searchResults.appendChild(link);
                    });
                });

                // PESAN JIKA MENU DAN DATA TIDAK DITEMUKAN
                if (!menuDitemukan && data.length === 0) {
                    const empty = document.createElement("div");
                    empty.className = "search-result-empty";
                    empty.textContent = "Tidak ada hasil pencarian.";

                    searchResults.appendChild(empty);
                }

                // TAMPILKAN DROPDOWN JIKA ADA HASIL
                if (menuDitemukan || data.length > 0) {
                    searchResults.classList.add("show");
                } else {
                    searchResults.classList.add("show");
                }

            })
            .catch(function (error) {
                console.error("Pencarian gagal:", error);
            });
    });

});
</script>

<?php include __DIR__ . '/profile_modal.php'; ?>