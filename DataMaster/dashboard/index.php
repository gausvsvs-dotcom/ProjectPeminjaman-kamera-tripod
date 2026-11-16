<?php
session_start();

require_once "../config/koneksi.php";
/** @var mysqli $conn */

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

$total_stok = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(bu.id_unit) AS total
        FROM barang_unit bu
        LEFT JOIN kondisi_barang k
            ON bu.id_kondisi = k.id_kondisi
        WHERE bu.status_unit = 'Tersedia'
        AND LOWER(COALESCE(k.nama_kondisi, '')) NOT LIKE '%rusak%'")
)['total'];

$total_kamera = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(bu.id_unit) AS total
        FROM barang_unit bu
        INNER JOIN barang b
            ON bu.id_barang = b.id_barang
        LEFT JOIN kondisi_barang k
            ON bu.id_kondisi = k.id_kondisi
        WHERE LOWER(b.jenis_barang) = 'kamera'
        AND bu.status_unit = 'Tersedia'
        AND LOWER(COALESCE(k.nama_kondisi, '')) NOT LIKE '%rusak%'")
)['total'];

$total_tripod = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(bu.id_unit) AS total
        FROM barang_unit bu
        INNER JOIN barang b
            ON bu.id_barang = b.id_barang
        LEFT JOIN kondisi_barang k
            ON bu.id_kondisi = k.id_kondisi
        WHERE LOWER(b.jenis_barang) = 'tripod'
        AND bu.status_unit = 'Tersedia'
        AND LOWER(COALESCE(k.nama_kondisi, '')) NOT LIKE '%rusak%'")
)['total'];

$total_dipinjam = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM barang_unit
        WHERE status_unit = 'Dipinjam'")
)['total'];

$query_kondisi = mysqli_query($conn, "
    SELECT
        k.nama_kondisi,
        COUNT(bu.id_unit) AS jumlah
    FROM kondisi_barang k
    LEFT JOIN barang_unit bu
        ON bu.id_kondisi = k.id_kondisi
    GROUP BY
        k.id_kondisi,
        k.nama_kondisi
    ORDER BY k.id_kondisi ASC");

$total_unit = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM barang_unit")
)['total'];

$tahun_sekarang = date('Y');
$bulan_sekarang = date('m');

$stmt_pendapatan = mysqli_prepare($conn, "
    SELECT COALESCE(SUM(total), 0) AS pendapatan
    FROM pembayaran
    WHERE LOWER(status_pembayaran) = 'lunas'
    AND YEAR(tgl_bayar) = ?
    AND MONTH(tgl_bayar) = ?");

mysqli_stmt_bind_param(
    $stmt_pendapatan,
    "ss",
    $tahun_sekarang,
    $bulan_sekarang);

mysqli_stmt_execute($stmt_pendapatan);

$pendapatan_bulan_ini = mysqli_fetch_assoc(
    mysqli_stmt_get_result($stmt_pendapatan)
)['pendapatan'];


$stmt_transaksi = mysqli_prepare($conn, "
    SELECT COUNT(DISTINCT id_peminjaman) AS total
    FROM pembayaran
    WHERE LOWER(status_pembayaran) = 'lunas'
    AND YEAR(tgl_bayar) = ?
    AND MONTH(tgl_bayar) = ?");

mysqli_stmt_bind_param(
    $stmt_transaksi,
    "ss",
    $tahun_sekarang,
    $bulan_sekarang
);

mysqli_stmt_execute($stmt_transaksi);

$total_transaksi_bulan_ini = mysqli_fetch_assoc(
    mysqli_stmt_get_result($stmt_transaksi)
)['total'];


// Pendapatan setiap bulan dalam tahun berjalan
$nama_bulan = [
    'Januari', 'Februari', 'Maret', 'April',
    'Mei', 'Juni', 'Juli', 'Agustus',
    'September', 'Oktober', 'November', 'Desember'
];

$data_pendapatan = array_fill(0, 12, 0);

$stmt_grafik = mysqli_prepare($conn, "
    SELECT
        MONTH(tgl_bayar) AS bulan,
        COALESCE(SUM(total), 0) AS pendapatan
    FROM pembayaran
    WHERE LOWER(status_pembayaran) = 'lunas'
    AND YEAR(tgl_bayar) = ?
    GROUP BY MONTH(tgl_bayar)
    ORDER BY MONTH(tgl_bayar) ASC
");

mysqli_stmt_bind_param(
    $stmt_grafik,
    "s",
    $tahun_sekarang
);

mysqli_stmt_execute($stmt_grafik);

$result_grafik = mysqli_stmt_get_result($stmt_grafik);

while ($grafik = mysqli_fetch_assoc($result_grafik)) {
    $index = (int) $grafik['bulan'] - 1;
    $data_pendapatan[$index] = (float) $grafik['pendapatan'];
}

$pendapatan_format = 'Rp ' . number_format(
    (float) $pendapatan_bulan_ini,
    0,
    ',',
    '.'
);
?>

<?php include "../layout/header.php"; ?>
<link rel="stylesheet" href="../assets/css/dashboard.css?v=3">
<?php include "../layout/navbar.php"; ?>

<div class="main-content dashboard-wrapper">

    <!-- HEADER -->
    <div class="dashboard-header">
        <div>
            <h1 class="dashboard-title">Dashboard</h1>
            <p class="dashboard-subtitle">
                Sistem Peminjaman Kamera & Tripod
            </p>
        </div>

        <div class="clock-card">
            <div id="jam" class="clock-time">00:00:00</div>
            <div id="tanggal" class="clock-date">
                Memuat tanggal...
            </div>
        </div>
    </div>

    <!-- WELCOME -->
    <div class="welcome-card">
        <h5>Selamat Datang</h5>
        <p>
            Kelola data kamera, tripod, pengguna, dan peminjaman
            melalui dashboard admin.
        </p>
    </div>


    <!-- DATA MASTER -->
    <div class="row g-4 mb-4">

        <!-- KATEGORI -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Total Kategori</div>
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
                        <div class="stat-label">Total Merk</div>
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
                        <div class="stat-label">Total Barang</div>
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
                        <div class="stat-label">Total Pengguna</div>
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


    <!-- STATISTIK INVENTARIS -->
    <div class="dashboard-card mb-4">

        <div class="section-header">
            <h5 class="section-title">
                <i class="bi bi-bar-chart-fill"></i>
                Statistik Inventaris
            </h5>
        </div>

        <div class="section-body">
            <div class="row g-3">

                <!-- STOK TERSEDIA -->
                <div class="col-xl-3 col-md-6">
                    <div class="inventory-item">
                        <div class="inventory-label">
                            Total Stok Tersedia
                        </div>
                        <h3 class="inventory-number">
                            <?= $total_stok; ?>
                        </h3>
                        <i class="bi bi-box-seam inventory-icon"></i>
                    </div>
                </div>

                <!-- KAMERA -->
                <div class="col-xl-3 col-md-6">
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
                <div class="col-xl-3 col-md-6">
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

                <!-- DIPINJAM -->
                <div class="col-xl-3 col-md-6">
                    <div class="inventory-item">
                        <div class="inventory-label">
                            Sedang Dipinjam
                        </div>
                        <h3 class="inventory-number">
                            <?= $total_dipinjam; ?>
                        </h3>
                        <i class="bi bi-clock-history inventory-icon"></i>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <!-- KONDISI BARANG -->
    <div class="dashboard-card mb-4">

        <div class="section-header">
            <h5 class="section-title">
                <i class="bi bi-tools"></i>
                Kondisi Barang
            </h5>
        </div>

        <div class="section-body">

            <?php if ($query_kondisi && mysqli_num_rows($query_kondisi) > 0): ?>

                <?php while ($kondisi = mysqli_fetch_assoc($query_kondisi)): ?>

                    <?php
                    $jumlah = (int) $kondisi['jumlah'];

                    $persentase = $total_unit > 0
                        ? ($jumlah / $total_unit) * 100
                        : 0;
                    ?>

                    <div class="condition-item">

                        <div class="condition-top">
                            <span class="condition-name">
                                <?= htmlspecialchars($kondisi['nama_kondisi']); ?>
                            </span>

                            <span class="condition-count">
                                <?= $jumlah; ?> unit
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


    <!-- STATISTIK PEMINJAMAN -->
    <div class="dashboard-card mb-4">

        <div class="section-header">
            <h5 class="section-title">
                <i class="bi bi-cash-stack"></i>
                Statistik Peminjaman
            </h5>
        </div>

        <div class="section-body">

            <div class="row g-4 align-items-center">

                <!-- RINGKASAN PENDAPATAN -->
                <div class="col-lg-4">

                    <div class="inventory-item mb-3">
                        <div class="inventory-label">
                            Total Pendapatan Bulan Ini
                        </div>

                        <h3 class="inventory-number">
                            <?= $pendapatan_format; ?>
                        </h3>

                        <i class="bi bi-wallet2 inventory-icon"></i>
                    </div>

                    <div class="inventory-item">
                        <div class="inventory-label">
                            Total Transaksi Bulan Ini
                        </div>

                        <h3 class="inventory-number">
                            <?= $total_transaksi_bulan_ini; ?>
                        </h3>

                        <i class="bi bi-receipt inventory-icon"></i>
                    </div>

                </div>

                <!-- GRAFIK PENDAPATAN -->
                <div class="col-lg-8">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-1 fw-semibold">
                                Grafik Pendapatan
                            </h6>

                            <small class="text-muted">
                                Tahun <?= $tahun_sekarang; ?>
                            </small>
                        </div>
                    </div>

                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="grafikPendapatan"></canvas>
                    </div>

                </div>

            </div>

        </div>
    </div>

</div>


<!-- CHART JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // JAM DAN TANGGAL
    function updateWaktu() {
        const sekarang = new Date();

        const jam = sekarang.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });

        const tanggal = sekarang.toLocaleDateString('id-ID', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });

        document.getElementById('jam').innerHTML = jam;
        document.getElementById('tanggal').innerHTML = tanggal;
    }

    updateWaktu();
    setInterval(updateWaktu, 1000);


    // GRAFIK PENDAPATAN
    const canvasPendapatan = document.getElementById('grafikPendapatan');

    if (canvasPendapatan) {
        const ctx = canvasPendapatan.getContext('2d');

        const dataBulan = <?= json_encode($nama_bulan); ?>;
        const dataPendapatan = <?= json_encode($data_pendapatan); ?>;

        new Chart(ctx, {
            type: 'bar',

            data: {
                labels: dataBulan,

                datasets: [{
                    label: 'Pendapatan',
                    data: dataPendapatan,
                    backgroundColor: '#a58abd',
                    hoverBackgroundColor: '#8174A0',
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.65
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        display: false
                    },

                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(
                                    context.raw
                                );
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,

                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID', {
                                    notation: 'compact'
                                }).format(value);
                            }
                        },

                        grid: {
                            color: '#eee8f2'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
</script>
<?php include "../layout/footer.php"; ?>