
<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

// FILTER TANGGAL
$tanggal_awal  = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';

// QUERY LAPORAN PENGEMBALIAN
$sql = "
    SELECT
        p.id_peminjaman,
        p.nama_peminjam,
        p.tgl_peminjaman,
        p.tgl_jatuh_tempo,

        (
            SELECT MAX(pg.tgl_pengembalian)
            FROM pengembalian pg
            WHERE pg.id_peminjaman = p.id_peminjaman
        ) AS tgl_pengembalian,

        (
            SELECT GROUP_CONCAT(
                CONCAT(
                    b.nama_barang,
                    ' (', d.jumlah, ' unit)'
                )
                SEPARATOR '<br>'
            )
            FROM detail_peminjaman d
            INNER JOIN barang b
                ON d.id_barang = b.id_barang
            WHERE d.id_peminjaman = p.id_peminjaman
        ) AS daftar_barang,

        (
            SELECT GROUP_CONCAT(
                DISTINCT pg.kondisi_pengembalian
                SEPARATOR ', '
            )
            FROM pengembalian pg
            WHERE pg.id_peminjaman = p.id_peminjaman
        ) AS kondisi_pengembalian,

        (
            SELECT COALESCE(SUM(pg.denda), 0)
            FROM pengembalian pg
            WHERE pg.id_peminjaman = p.id_peminjaman
        ) AS total_denda,

        (
            SELECT COUNT(*)
            FROM pengembalian pg
            WHERE pg.id_peminjaman = p.id_peminjaman
        ) AS jumlah_pengembalian

    FROM peminjaman p
    WHERE EXISTS (
        SELECT 1
        FROM pengembalian pg
        WHERE pg.id_peminjaman = p.id_peminjaman
    )
";

// FILTER TANGGAL PENGEMBALIAN
if (!empty($tanggal_awal) || !empty($tanggal_akhir)) {

    $tanggal_awal = mysqli_real_escape_string($conn, $tanggal_awal);
    $tanggal_akhir = mysqli_real_escape_string($conn, $tanggal_akhir);

    $sql .= "
        AND EXISTS (
            SELECT 1
            FROM pengembalian pg
            WHERE pg.id_peminjaman = p.id_peminjaman
    ";

    if (!empty($tanggal_awal)) {
        $sql .= " AND DATE(pg.tgl_pengembalian) >= '$tanggal_awal'";
    }

    if (!empty($tanggal_akhir)) {
        $sql .= " AND DATE(pg.tgl_pengembalian) <= '$tanggal_akhir'";
    }

    $sql .= ")";
}

$sql .= " ORDER BY tgl_pengembalian DESC";

$query = mysqli_query($conn, $sql);

if (!$query) {
    die("Query laporan pengembalian gagal: " . mysqli_error($conn));
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
                <i class="bi bi-file-earmark-check"></i>
            </div>

            <div>
                <h2>Laporan Pengembalian</h2>
                <p>
                    Menampilkan data pengembalian barang yang dipinjam.
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
               class="laporan-bubble active">
                <i class="bi bi-box-arrow-in-left"></i>
                Pengembalian
            </a>

            <a href="statistik.php"
               class="laporan-bubble">
                <i class="bi bi-bar-chart-line"></i>
                Statistik
            </a>

            <a href="index.php?menu=struk"
               class="laporan-bubble">
                <i class="bi bi-receipt"></i>
                Struk
            </a>

        </div>

        <!-- FILTER -->
        <div class="laporan-filter-card">

            <h3 class="laporan-card-title">
                <i class="bi bi-funnel me-2"></i>
                Filter Laporan
            </h3>

            <form method="GET" action="pengembalian.php">

                <div class="row g-3 align-items-end">

                    <div class="col-md-5">
                        <label for="tanggal_awal">
                            Tanggal Awal Pengembalian
                        </label>

                        <input
                            type="date"
                            name="tanggal_awal"
                            id="tanggal_awal"
                            class="form-control"
                            value="<?= htmlspecialchars($tanggal_awal); ?>"
                        >
                    </div>

                    <div class="col-md-5">
                        <label for="tanggal_akhir">
                            Tanggal Akhir Pengembalian
                        </label>

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

                        <a href="pengembalian.php" class="btn-laporan-reset">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Reset
                        </a>

                    </div>

                </div>

            </form>
        </div>

        <!-- TABEL LAPORAN -->
        <div class="laporan-list-header">

            <div>
                <h3>Data Pengembalian</h3>
                <p>
                    Daftar transaksi barang yang telah dikembalikan.
                </p>
            </div>

        </div>

        <div class="laporan-struk-list">

            <?php if (mysqli_num_rows($query) > 0) { ?>

                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Kode Peminjaman</th>
                                <th>Nama Peminjam</th>
                                <th>Tanggal Peminjaman</th>
                                <th>Tanggal Pengembalian</th>
                                <th>Detail</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php
                            $no = 1;

                            while ($data = mysqli_fetch_assoc($query)) {
                                $id = (int) $data['id_peminjaman'];
                            ?>

                                <tr>

                                    <td><?= $no++; ?></td>

                                    <td>#<?= $id; ?></td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $data['nama_peminjam'] ?? '-'
                                        ); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $data['tgl_peminjaman'] ?? '-'
                                        ); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $data['tgl_pengembalian'] ?? '-'
                                        ); ?>
                                    </td>

                                    <td>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-primary"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#detail<?= $id; ?>"
                                        >
                                            <i class="bi bi-eye"></i>
                                            Detail
                                        </button>
                                    </td>

                                </tr>

                                <!-- DETAIL PENGEMBALIAN -->
                                <tr>

                                    <td colspan="6" class="p-0 border-0">

                                        <div
                                            class="collapse"
                                            id="detail<?= $id; ?>"
                                        >

                                            <div class="p-3">

                                                <h6>
                                                    Detail Barang yang Dikembalikan
                                                </h6>

                                                <div>
                                                    <?= $data['daftar_barang']
                                                        ?: 'Tidak ada detail barang.'; ?>
                                                </div>

                                                <hr>

                                                <div class="mb-2">
                                                    <strong>Kondisi Barang:</strong>

                                                    <?= htmlspecialchars(
                                                        $data['kondisi_pengembalian'] ?? '-'
                                                    ); ?>
                                                </div>

                                                <div class="mb-2">
                                                    <strong>Total Denda:</strong>

                                                    Rp<?= number_format(
                                                        (float) ($data['total_denda'] ?? 0),
                                                        0,
                                                        ',',
                                                        '.'
                                                    ); ?>
                                                </div>

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

            <?php } else { ?>

                <div class="laporan-kosong">
                    <i class="bi bi-inbox"></i>
                    <p>Belum ada data laporan pengembalian.</p>
                </div>

            <?php } ?>

        </div>

    </div>
</div>

<?php include "../layout/footer.php"; ?>
