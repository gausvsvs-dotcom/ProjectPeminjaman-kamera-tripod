
<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

// FILTER TANGGAL
$tanggal_awal = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';

// FILTER BERDASARKAN TANGGAL PEMINJAMAN
$whereTanggal = "";

if (!empty($tanggal_awal)) {
    $tanggal_awal = mysqli_real_escape_string($conn, $tanggal_awal);
    $whereTanggal .= " AND DATE(p.tgl_peminjaman) >= '$tanggal_awal'";
}

if (!empty($tanggal_akhir)) {
    $tanggal_akhir = mysqli_real_escape_string($conn, $tanggal_akhir);
    $whereTanggal .= " AND DATE(p.tgl_peminjaman) <= '$tanggal_akhir'";
}

// TOTAL PEMINJAMAN
$sqlPeminjaman = "
    SELECT COUNT(DISTINCT p.id_peminjaman) AS total
    FROM peminjaman p
    WHERE 1=1 $whereTanggal
";

$queryPeminjaman = mysqli_query($conn, $sqlPeminjaman);

if (!$queryPeminjaman) {
    die("Query total peminjaman gagal: " . mysqli_error($conn));
}

$dataPeminjaman = mysqli_fetch_assoc($queryPeminjaman);
$totalPeminjaman = (int) ($dataPeminjaman['total'] ?? 0);


// TOTAL PENGEMBALIAN
$sqlPengembalian = "
    SELECT COUNT(DISTINCT pg.id_peminjaman) AS total
    FROM pengembalian pg
    INNER JOIN peminjaman p
        ON pg.id_peminjaman = p.id_peminjaman
    WHERE 1=1 $whereTanggal
";

$queryPengembalian = mysqli_query($conn, $sqlPengembalian);

if (!$queryPengembalian) {
    die("Query total pengembalian gagal: " . mysqli_error($conn));
}

$dataPengembalian = mysqli_fetch_assoc($queryPengembalian);
$totalPengembalian = (int) ($dataPengembalian['total'] ?? 0);


// TOTAL PEMINJAMAN DENGAN BARANG RUSAK
$sqlRusak = "
    SELECT COUNT(DISTINCT pg.id_peminjaman) AS total
    FROM pengembalian pg
    INNER JOIN peminjaman p
        ON pg.id_peminjaman = p.id_peminjaman
    WHERE LOWER(pg.kondisi_pengembalian) LIKE '%rusak%'
    $whereTanggal
";

$queryRusak = mysqli_query($conn, $sqlRusak);

if (!$queryRusak) {
    die("Query total barang rusak gagal: " . mysqli_error($conn));
}

$dataRusak = mysqli_fetch_assoc($queryRusak);
$totalRusak = (int) ($dataRusak['total'] ?? 0);


// STATISTIK SETIAP BARANG
$sqlStatistik = "
    SELECT
        b.id_barang,
        b.nama_barang,

        COALESCE((
            SELECT SUM(d.jumlah)
            FROM detail_peminjaman d
            INNER JOIN peminjaman p
                ON d.id_peminjaman = p.id_peminjaman
            WHERE d.id_barang = b.id_barang
            $whereTanggal
        ), 0) AS total_dipinjam,

        COALESCE((
            SELECT SUM(d.jumlah)
            FROM detail_peminjaman d
            INNER JOIN peminjaman p
                ON d.id_peminjaman = p.id_peminjaman
            WHERE d.id_barang = b.id_barang
            AND EXISTS (
                SELECT 1
                FROM pengembalian pg
                WHERE pg.id_peminjaman = p.id_peminjaman
            )
            $whereTanggal
        ), 0) AS total_dikembalikan,

        COALESCE((
            SELECT SUM(d.jumlah)
            FROM detail_peminjaman d
            INNER JOIN peminjaman p
                ON d.id_peminjaman = p.id_peminjaman
            WHERE d.id_barang = b.id_barang
            AND EXISTS (
                SELECT 1
                FROM pengembalian pg
                WHERE pg.id_peminjaman = p.id_peminjaman
                AND LOWER(pg.kondisi_pengembalian) LIKE '%rusak%'
            )
            $whereTanggal
        ), 0) AS total_rusak

    FROM barang b
    ORDER BY b.nama_barang ASC
";

$queryStatistik = mysqli_query($conn, $sqlStatistik);

if (!$queryStatistik) {
    die("Query statistik barang gagal: " . mysqli_error($conn));
}
?>

<?php include "../layout/header.php"; ?>

<link rel="stylesheet" href="../assets/css/data-master.css">
<link rel="stylesheet" href="../assets/css/laporan.css">

<?php include "../layout/navbar.php"; ?>

<div class="main-content data-master-page">
    <div class="container-fluid">

        <!-- HEADER -->
        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-bar-chart-line"></i>
            </div>

            <div>
                <h2>Laporan Statistik</h2>
                <p>
                    Ringkasan statistik peminjaman dan pengembalian barang.
                </p>
            </div>
        </div>

        <!-- MENU LAPORAN -->
        <div class="laporan-menu">

            <a href="peminjaman.php"
               class="laporan-bubble">
                <i class="bi bi-box-arrow-up-right"></i>
                Peminjaman
            </a>

            <a href="pengembalian.php"
               class="laporan-bubble">
                <i class="bi bi-box-arrow-in-left"></i>
                Pengembalian
            </a>

            <a href="statistik.php"
               class="laporan-bubble active">
                <i class="bi bi-bar-chart-line"></i>
                Statistik
            </a>

            <a href="index.php?menu=struk"
               class="laporan-bubble">
                <i class="bi bi-receipt"></i>
                Struk
            </a>

        </div>

        <!-- FILTER TANGGAL -->
        <div class="laporan-filter-card">

            <h3 class="laporan-card-title">
                <i class="bi bi-funnel me-2"></i>
                Filter Statistik
            </h3>

            <form method="GET" action="statistik.php">

                <div class="row g-3 align-items-end">

                    <div class="col-md-5">
                        <label for="tanggal_awal">Tanggal Awal</label>

                        <input
                            type="date"
                            name="tanggal_awal"
                            id="tanggal_awal"
                            class="form-control"
                            value="<?= htmlspecialchars($tanggal_awal); ?>"
                        >
                    </div>

                    <div class="col-md-5">
                        <label for="tanggal_akhir">Tanggal Akhir</label>

                        <input
                            type="date"
                            name="tanggal_akhir"
                            id="tanggal_akhir"
                            class="form-control"
                            value="<?= htmlspecialchars($tanggal_akhir); ?>"
                        >
                    </div>

                    <div class="col-12 laporan-filter-action">

                        <button type="submit" class="btn-laporan-filter">
                            <i class="bi bi-search me-1"></i>
                            Cari
                        </button>

                        <a href="statistik.php" class="btn-laporan-reset">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Reset
                        </a>

                    </div>

                </div>

            </form>
        </div>

        <!-- RINGKASAN STATISTIK -->
        <div class="row g-3 mt-3">

            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">

                        <div class="rounded p-3 bg-primary bg-opacity-10">
                            <i class="bi bi-box-arrow-up-right fs-3 text-primary"></i>
                        </div>

                        <div>
                            <p class="text-muted mb-1">Total Peminjaman</p>
                            <h3 class="mb-0">
                                <?= $totalPeminjaman; ?>
                            </h3>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">

                        <div class="rounded p-3 bg-success bg-opacity-10">
                            <i class="bi bi-box-arrow-in-down fs-3 text-success"></i>
                        </div>

                        <div>
                            <p class="text-muted mb-1">Total Pengembalian</p>
                            <h3 class="mb-0">
                                <?= $totalPengembalian; ?>
                            </h3>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">

                        <div class="rounded p-3 bg-danger bg-opacity-10">
                            <i class="bi bi-exclamation-triangle fs-3 text-danger"></i>
                        </div>

                        <div>
                            <p class="text-muted mb-1">
                                Peminjaman dengan Barang Rusak
                            </p>

                            <h3 class="mb-0">
                                <?= $totalRusak; ?>
                            </h3>
                        </div>

                    </div>
                </div>
            </div>

        </div>

<!-- TABEL STATISTIK BARANG -->
<div class="laporan-list-header mt-4">
    <div>
        <h3>Statistik Barang</h3>
        <p>
            Jumlah barang yang dipinjam, dikembalikan,
            dan mengalami kerusakan.
        </p>
    </div>
</div>

<div class="data-master-card laporan-table-card">

    <?php if (mysqli_num_rows($queryStatistik) > 0) { ?>

        <div class="data-table-wrapper">
            <table class="data-master-table laporan-table statistik-table">

                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Barang</th>
                        <th>Total Dipinjam</th>
                        <th>Total Dikembalikan</th>
                        <th>Total Rusak</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $no = 1;

                    while ($data = mysqli_fetch_assoc($queryStatistik)) {
                    ?>

                    <tr>
                        <td>
                            <span class="data-number">
                                <?= $no++; ?>
                            </span>
                        </td>

                        <td>
                            <span class="data-name">
                                <?= htmlspecialchars($data['nama_barang']); ?>
                            </span>
                        </td>

                        <td>
                            <span class="statistik-angka statistik-dipinjam">
                                <i class="bi bi-box-arrow-up-right"></i>
                                <?= (int) $data['total_dipinjam']; ?>
                                <small>unit</small>
                            </span>
                        </td>

                        <td>
                            <span class="statistik-angka statistik-dikembalikan">
                                <i class="bi bi-check-circle"></i>
                                <?= (int) $data['total_dikembalikan']; ?>
                                <small>unit</small>
                            </span>
                        </td>

                        <td>
                            <span class="statistik-angka statistik-rusak">
                                <i class="bi bi-exclamation-circle"></i>
                                <?= (int) $data['total_rusak']; ?>
                                <small>unit</small>
                            </span>
                        </td>
                    </tr>

                    <?php } ?>
                </tbody>

            </table>
        </div>

        <div class="data-table-footer">
            <span>
                <i class="bi bi-info-circle me-1"></i>
                Total <?= $no - 1; ?> jenis barang
            </span>
        </div>

    <?php } else { ?>

        <div class="laporan-kosong">
            <i class="bi bi-inbox"></i>
            <p>Belum ada data statistik barang.</p>
        </div>

    <?php } ?>

</div>

</div>
</div>

<?php include "../layout/footer.php"; ?>