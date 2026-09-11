<?php
session_start();

require_once "../config/koneksi.php";
/** @var mysqli $conn */

/* ==========================
   TOTAL DATA MASTER
========================== */

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

/* ==========================
   STATISTIK BARANG
========================== */

$total_stok = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COALESCE(SUM(stok), 0) AS total
        FROM barang
    ")
)['total'];

$total_kamera = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM barang
        WHERE jenis_barang = 'kamera'
    ")
)['total'];

$total_tripod = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM barang
        WHERE jenis_barang = 'tripod'
    ")
)['total'];

/* ==========================
   DATA KONDISI BARANG
========================== */

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
    ORDER BY kondisi_barang.id_kondisi ASC
");
?>

<?php include "../layout/header.php"; ?>
<?php include "../layout/navbar.php"; ?>

<div class="main-content">

    <!-- HEADER DASHBOARD -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title mb-1">Dashboard</h2>
            <p class="text-muted mb-0">
                Sistem Peminjaman Kamera & Tripod
            </p>
        </div>

        <!-- JAM & TANGGAL -->
        <div class="card px-4 py-3 text-end">
            <div
                id="jam"
                style=" 
                    color:#8174A0;
                    font-size:28px;
                    font-weight:bold;
                ">
                00:00:00
            </div>

            <div
                id="tanggal"
                class="text-muted"
                style="font-size:14px;">
                Memuat tanggal...
            </div>
        </div>
    </div>

    <!-- TOTAL DATA MASTER -->
    <div class="row g-4">

        <!-- KATEGORI -->
        <div class="col-md-3">
            <div class="card p-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted">
                            Total Kategori
                        </small>
                        <h2 class="mt-2">
                            <?= $total_kategori; ?>
                        </h2>
                    </div>

                    <i
                        class="bi bi-tags-fill fs-1"
                        style="color:#8174A0;">
                    </i>
                </div>
            </div>
        </div>

        <!-- MERK -->
        <div class="col-md-3">
            <div class="card p-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted">
                            Total Merk
                        </small>
                        <h2 class="mt-2">
                            <?= $total_merk; ?>
                        </h2>
                    </div>

                    <i
                        class="bi bi-bookmark-fill fs-1"
                        style="color:#A888B5;">
                    </i>
                </div>
            </div>
        </div>

        <!-- BARANG -->
        <div class="col-md-3">
            <div class="card p-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted">
                            Total Barang
                        </small>
                        <h2 class="mt-2">
                            <?= $total_barang; ?>
                        </h2>
                    </div>

                    <i
                        class="bi bi-box-seam-fill fs-1"
                        style="color:#EFB6C8;">
                    </i>
                </div>
            </div>
        </div>

        <!-- PENGGUNA -->
        <div class="col-md-3">
            <div class="card p-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted">
                            Total Pengguna
                        </small>
                        <h2 class="mt-2">
                            <?= $total_pengguna; ?>
                        </h2>
                    </div>

                    <i
                        class="bi bi-people-fill fs-1"
                        style="color:#8174A0;">
                    </i>
                </div>
            </div>
        </div>

    </div>

    <!-- STATISTIK BARANG -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-bar-chart-fill"></i>
                Statistik Barang
            </h5>
        </div>

        <div class="card-body">
            <div class="row g-4">

                <!-- TOTAL STOK -->
                <div class="col-md-4">
                    <div class="dashboard-stat">
                        <div>
                            <small class="text-muted">
                                Total Stok
                            </small>
                            <h2>
                                <?= $total_stok; ?>
                            </h2>
                        </div>

                        <i
                            class="bi bi-stack"
                            style="color:#8174A0;">
                        </i>
                    </div>
                </div>

                <!-- KAMERA -->
                <div class="col-md-4">
                    <div class="dashboard-stat">
                        <div>
                            <small class="text-muted">
                                Total Kamera
                            </small>
                            <h2>
                                <?= $total_kamera; ?>
                            </h2>
                        </div>

                        <i
                            class="bi bi-camera-fill"
                            style="color:#A888B5;">
                        </i>
                    </div>
                </div>

                <!-- TRIPOD -->
                <div class="col-md-4">
                    <div class="dashboard-stat">
                        <div>
                            <small class="text-muted">
                                Total Tripod
                            </small>
                            <h2>
                                <?= $total_tripod; ?>
                            </h2>
                        </div>

                        <i
                            class="bi bi-camera-video-fill"
                            style="color:#EFB6C8;">
                        </i>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- KONDISI BARANG -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-tools"></i>
                Kondisi Barang
            </h5>
        </div>

        <div class="card-body">
            <?php
            if (mysqli_num_rows($query_kondisi) > 0) {
                while ($kondisi = mysqli_fetch_assoc($query_kondisi)) {

                    $jumlah = $kondisi['jumlah'];

                    $persentase = $total_barang > 0
                        ? ($jumlah / $total_barang) * 100
                        : 0;
            ?>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">
                                <?= htmlspecialchars($kondisi['nama_kondisi']); ?>
                            </span>

                            <span class="text-muted">
                                <?= $jumlah; ?> barang
                            </span>
                        </div>

                        <div
                            class="progress"
                            style="height:10px;">
                            <div class="progress" style="height:10px;">
                                <div class="progress-bar" style="width: <?= $persentase; ?>%; background-color: #8174A0;"></div>
                            </div>
                        </div>
                    </div>

                <?php
                }
            } else {
                ?>

                <div class="text-center text-muted">
                    Belum ada data kondisi barang.
                </div>

            <?php
            }
            ?>
        </div>
    </div>

    <!-- SELAMAT DATANG -->
    <div class="card mt-4 p-4">
        <h5 style="color:#8174A0;">
            Selamat Datang 👋
        </h5>
        <p class="text-muted mb-0">
            Selamat datang di Sistem Peminjaman
            Kamera & Tripod.
        </p>
    </div>
</div>
<!-- JAM & TANGGAL -->
<script>
    function updateWaktu() {
        const sekarang = new Date();
        const jam = sekarang.toLocaleTimeString(
            'id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            }
        );
        const tanggal = sekarang.toLocaleDateString(
            'id-ID', {
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