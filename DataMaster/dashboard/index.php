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
<?php include "../layout/navbar.php"; ?>
<style>
    /*DASHBOARD MODERN*/
    .dashboard-wrapper {
        padding-bottom: 30px;
    }
    /* HEADER */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    .dashboard-title {
        font-size: 30px;
        font-weight: 700;
        color: #302b3f;
        margin-bottom: 5px;
    }
    .dashboard-subtitle {
        color: #77717f;
        font-size: 14px;
        margin: 0;
    }
    /* CLOCK */
    .clock-card {
        min-width: 190px;
        background: #ffffff;
        border: 1px solid rgba(129, 116, 160, 0.10);
        border-radius: 18px;
        padding: 14px 20px;
        text-align: right;
        box-shadow: 0 8px 25px rgba(129, 116, 160, 0.10);
    }
    .clock-time {
        color: #8174A0;
        font-size: 25px;
        font-weight: 700;
        letter-spacing: 1px;
    }
    .clock-date {
        color: #88818f;
        font-size: 13px;
        margin-top: 2px;
    }
    /* WELCOME */
    .welcome-card {
        position: relative;
        overflow: hidden;
        background: linear-gradient(
            135deg,
            #8174A0 0%,
            #A888B5 65%,
            #EFB6C8 100%
        );
        color: white;
        border: none;
        border-radius: 20px;
        padding: 25px 28px;
        margin-bottom: 25px;
        box-shadow: 0 10px 30px rgba(129, 116, 160, 0.20);
    }
    .welcome-card::after {
        content: "";
        position: absolute;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: rgba(255,255,255,0.10);
        right: -45px;
        top: -55px;
    }
    .welcome-card h5 {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 7px;
        position: relative;
        z-index: 2;
    }
    .welcome-card p {
        margin: 0;
        font-size: 14px;
        opacity: 0.90;
        position: relative;
        z-index: 2;
    }
    /* STAT CARDS */
    .stat-card {
        background: #ffffff;
        border: none;
        border-radius: 18px;
        padding: 21px;
        height: 100%;
        box-shadow: 0 8px 25px rgba(50, 40, 70, 0.07);
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 30px rgba(129, 116, 160, 0.15);
    }
    .stat-card::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        width: 4px;
        height: 100%;
        background: #8174A0;
    }
    .stat-label {
        font-size: 13px;
        color: #817b86;
        font-weight: 500;
    }
    .stat-number {
        font-size: 29px;
        font-weight: 700;
        color: #302b3f;
        margin-top: 5px;
        margin-bottom: 0;
    }
    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 23px;
    }
    .icon-purple {
        color: #8174A0;
        background: rgba(129, 116, 160, 0.13);
    }
    .icon-lilac {
        color: #A888B5;
        background: rgba(168, 136, 181, 0.14);
    }
    .icon-pink {
        color: #D98FA7;
        background: rgba(239, 182, 200, 0.22);
    }
    .icon-peach {
        color: #D89E69;
        background: rgba(255, 210, 160, 0.25);
    }
    /* SECTION CARD */
    .dashboard-card {
        background: #ffffff;
        border: none;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(50, 40, 70, 0.07);
        overflow: hidden;
    }
    .section-header {
        padding: 17px 22px;
        border-bottom: 1px solid #eeeaf2;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .section-title {
        color: #40384e;
        font-size: 16px;
        font-weight: 700;
        margin: 0;
    }
    .section-title i {
        color: #8174A0;
        margin-right: 7px;
    }
    .section-body {
        padding: 22px;
    }
    /* INVENTORY STAT */
    .inventory-item {
        background: #faf9fc;
        border: 1px solid #eeeaf3;
        border-radius: 16px;
        padding: 20px;
        height: 100%;
        transition: all 0.2s ease;
    }
    .inventory-item:hover {
        border-color: #d8cde1;
        transform: translateY(-2px);
    }
    .inventory-label {
        color: #817b86;
        font-size: 13px;
        margin-bottom: 8px;
    }

    .inventory-number {
        color: #302b3f;
        font-size: 27px;
        font-weight: 700;
        margin: 0;
    }

    .inventory-icon {
        font-size: 22px;
        color: #8174A0;
        margin-top: 13px;
    }

    /* CONDITION */
    .condition-item {
        margin-bottom: 20px;
    }

    .condition-item:last-child {
        margin-bottom: 0;
    }

    .condition-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .condition-name {
        font-size: 13px;
        font-weight: 600;
        color: #3d3745;
    }

    .condition-count {
        font-size: 12px;
        color: #8b8590;
    }

    .condition-progress {
        height: 9px;
        background: #eeeef2;
        border-radius: 20px;
        overflow: hidden;
    }

    .condition-bar {
        height: 100%;
        border-radius: 20px;
        background: linear-gradient(
            90deg,
            #8174A0,
            #A888B5
        );
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {

        .dashboard-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .clock-card {
            width: 100%;
            text-align: left;
        }

        .dashboard-title {
            font-size: 25px;
        }
    }
</style>


<div class="main-content dashboard-wrapper">
    <!-- ==========================
         HEADER
    =========================== -->
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
    <!-- ==========================
         WELCOME
    =========================== -->
    <div class="welcome-card">
        <h5>
            Selamat Datang 👋
        </h5>
        <p>
            Kelola data kamera, tripod, pengguna, dan peminjaman
            melalui dashboard admin.
        </p>
    </div>
    <!-- ==========================
         DATA MASTER
    =========================== -->
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
    <!-- ==========================
         STATISTIK BARANG
    =========================== -->
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
    <!-- ==========================
         KONDISI BARANG
    =========================== -->
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
<!-- ==========================
     JAM & TANGGAL
=========================== -->
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