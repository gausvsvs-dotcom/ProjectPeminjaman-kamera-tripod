<?php
session_start();
require_once "../config/koneksi.php";
/** @var mysqli $conn */
/* TOTAL DATA MASTER */

$total_kategori = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM kategori")
)['total'];
$total_merk = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM merk")
)['total'];
$total_barang = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM barang")
)['total'];
$total_pengguna = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM pengguna")
)['total'];

/* STATISTIK BARANG*/
$total_stok = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COALESCE(SUM(stok), 0) AS total
        FROM barang")
)['total'];
$total_kamera = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM barang
        WHERE jenis_barang = 'kamera'")
)['total'];
$total_tripod = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM barang
        WHERE jenis_barang = 'tripod'")
)['total'];

/* DATA KONDISI BARANG */
$query_kondisi = mysqli_query($conn, "
    SELECT
        kondisi_barang.nama_kondisi,
        COUNT(barang.id_barang) AS jumlah
    FROM kondisi_barang
    LEFT JOIN barang
        ON barang.id_kondisi = kondisi_barang.id_kondisi
    GROUP BY
        kondisi_barang.id_kondisi,
        kondisi_barang.nama_kondisi
    ORDER BY kondisi_barang.id_kondisi ASC");
?>

<?php include "../layout/header.php"; ?>
<link rel="stylesheet" href="../assets/css/dashboard.css?v=3">
<?php include "../layout/navbar.php"; ?>

<div class="main-content dashboard-wrapper">
    <!--EADER-->
    <div class="dashboard-header">
        <div>
            <h1 class="dashboard-title">
                Dashboard
            </h1>
            <p class="dashboard-subtitle">
                Sistem Peminjaman Kamera & Tripod
            </p>
        </div>
        <!-- JAM -->
        <div class="clock-card">
            <div id="jam" class="clock-time">
                00:00:00
            </div>
            <div id="tanggal" class="clock-date">
                Memuat tanggal...
            </div>
        </div>
    </div>
    <!-- WELCOME-->
    <div class="welcome-card">
        <h5>
            Selamat Datang 👋
        </h5>
        <p>
            Kelola data kamera, tripod, pengguna, dan peminjaman
            melalui dashboard admin.
        </p>
    </div>
    <!--DATA MASTER -->
    <div class="row g-4 mb-4">
        <!-- KATEGORI -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">
                            Total Kategori
                        </div>
                        <div class="stat-number">
                            <?= $total_kategori; ?>
                        </div>
                    </div>
                    <div class="stat-icon icon-purple">
                        <i class="bi bi-tags-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- MERK -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">
                            Total Merk
                        </div>
                        <div class="stat-number">
                            <?= $total_merk; ?>
                        </div>
                    </div>
                    <div class="stat-icon icon-lilac">
                        <i class="bi bi-bookmark-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- BARANG -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">
                            Total Barang
                        </div>
                        <div class="stat-number">
                            <?= $total_barang; ?>
                        </div>
                    </div>
                    <div class="stat-icon icon-pink">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- PENGGUNA -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">
                            Total Pengguna
                        </div>
                        <div class="stat-number">
                            <?= $total_pengguna; ?>
                        </div>
                    </div>
                    <div class="stat-icon icon-peach">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- STATISTIK BARANG-->
    <div class="dashboard-card mb-4">
        <div class="section-header">
            <h5 class="section-title">
                <i class="bi bi-bar-chart-fill"></i>
                Statistik Barang
            </h5>
        </div>
        <div class="section-body">
            <div class="row g-3">
                <!-- TOTAL STOK -->
                <div class="col-md-4">
                    <div class="inventory-item">
                        <div class="inventory-label">
                            Total Stok
                        </div>
                        <h3 class="inventory-number">
                            <?= $total_stok; ?>
                        </h3>
                        <i class="bi bi-stack inventory-icon"></i>
                    </div>
                </div>
                <!-- KAMERA -->
                <div class="col-md-4">
                    <div class="inventory-item">
                        <div class="inventory-label">
                            Total Kamera
                        </div>
                        <h3 class="inventory-number">
                            <?= $total_kamera; ?>
                        </h3>
                        <i class="bi bi-camera-fill inventory-icon"></i>
                    </div>
                </div>
                <!-- TRIPOD -->
                <div class="col-md-4">
                    <div class="inventory-item">
                        <div class="inventory-label">
                            Total Tripod
                        </div>
                        <h3 class="inventory-number">
                            <?= $total_tripod; ?>
                        </h3>
                        <i class="bi bi-camera-video-fill inventory-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--KONDISI BARANG-->
    <div class="dashboard-card">
        <div class="section-header">
            <h5 class="section-title">
                <i class="bi bi-tools"></i>
                Kondisi Barang
            </h5>
        </div>
        <div class="section-body">
            <?php if (mysqli_num_rows($query_kondisi) > 0): ?>
                <?php while ($kondisi = mysqli_fetch_assoc($query_kondisi)): ?>
                    <?php
                    $jumlah = $kondisi['jumlah'];
                    $persentase = $total_barang > 0
                        ? ($jumlah / $total_barang) * 100
                        : 0;
                    ?>
                    <div class="condition-item">
                        <div class="condition-top">
                            <span class="condition-name">
                                <?= htmlspecialchars($kondisi['nama_kondisi']); ?>
                            </span>
                            <span class="condition-count">
                                <?= $jumlah; ?> barang
                            </span>
                        </div>
                        <div class="condition-progress">
                            <div
                                class="condition-bar"
                                style="width: <?= $persentase; ?>%;">
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center text-muted py-3">
                    Belum ada data kondisi barang.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- JAM & TANGGAL-->
<script>
    function updateWaktu() {
        const sekarang = new Date();
        const jam = sekarang.toLocaleTimeString(
            'id-ID',
            {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            }
        );
        const tanggal = sekarang.toLocaleDateString(
            'id-ID',
            {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            }
        );
        document.getElementById('jam').innerHTML = jam;
        document.getElementById('tanggal').innerHTML = tanggal;
    }
    updateWaktu();
    setInterval(updateWaktu, 1000);
</script>
<?php include "../layout/footer.php"; ?>