<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

// FILTER TANGGAL
$tanggal_awal  = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';
$status_filter = $_GET['status'] ?? '';

// QUERY DATA PEMINJAMAN
$sql = "
    SELECT
        p.id_peminjaman,
        p.nama_peminjam,
        p.tgl_peminjaman,
        p.tgl_jatuh_tempo,

        (
            SELECT GROUP_CONCAT(
                CONCAT(
                    b.nama_barang,
                    ' (', d.jumlah, ' x Rp',
                    FORMAT(b.harga_sewa, 0), ')'
                )
                SEPARATOR '<br>'
            )
            FROM detail_peminjaman d
            INNER JOIN barang b
                ON d.id_barang = b.id_barang
            WHERE d.id_peminjaman = p.id_peminjaman
        ) AS daftar_barang,

        (
            SELECT SUM(d.jumlah * b.harga_sewa)
            FROM detail_peminjaman d
            INNER JOIN barang b
                ON d.id_barang = b.id_barang
            WHERE d.id_peminjaman = p.id_peminjaman
        ) AS total_tagihan,

        (
            SELECT COALESCE(SUM(pb.total), 0)
            FROM pembayaran pb
            WHERE pb.id_peminjaman = p.id_peminjaman
        ) AS total_bayar

    FROM peminjaman p
    WHERE 1=1
";

// FILTER TANGGAL AWAL
if (!empty($tanggal_awal)) {
    $tanggal_awal = mysqli_real_escape_string($conn, $tanggal_awal);
    $sql .= " AND DATE(p.tgl_peminjaman) >= '$tanggal_awal'";
}

// FILTER TANGGAL AKHIR
if (!empty($tanggal_akhir)) {
    $tanggal_akhir = mysqli_real_escape_string($conn, $tanggal_akhir);
    $sql .= " AND DATE(p.tgl_peminjaman) <= '$tanggal_akhir'";
}

// FILTER STATUS PEMBAYARAN
if ($status_filter === 'lunas') {
    $sql .= "
        AND (
            SELECT COALESCE(SUM(pb.total), 0)
            FROM pembayaran pb
            WHERE pb.id_peminjaman = p.id_peminjaman
        ) >= (
            SELECT COALESCE(SUM(d.jumlah * b.harga_sewa), 0)
            FROM detail_peminjaman d
            INNER JOIN barang b
                ON d.id_barang = b.id_barang
            WHERE d.id_peminjaman = p.id_peminjaman
        )
    ";
} elseif ($status_filter === 'belum_lunas') {
    $sql .= "
        AND (
            SELECT COALESCE(SUM(pb.total), 0)
            FROM pembayaran pb
            WHERE pb.id_peminjaman = p.id_peminjaman
        ) < (
            SELECT COALESCE(SUM(d.jumlah * b.harga_sewa), 0)
            FROM detail_peminjaman d
            INNER JOIN barang b
                ON d.id_barang = b.id_barang
            WHERE d.id_peminjaman = p.id_peminjaman
        )
    ";
}

$sql .= " ORDER BY p.tgl_peminjaman DESC";

$query = mysqli_query($conn, $sql);

if (!$query) {
    die("Query laporan peminjaman gagal: " . mysqli_error($conn));
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
                <i class="bi bi-file-earmark-text"></i>
            </div>

            <div>
                <h2>Laporan Peminjaman</h2>
                <p>Menampilkan laporan data peminjaman barang.</p>
            </div>
        </div>

        <!-- MENU LAPORAN -->
        <div class="laporan-menu">

            <a href="peminjaman.php"
               class="laporan-bubble active">
                <i class="bi bi-box-arrow-up-right"></i>
                Peminjaman
            </a>

            <a href="pengembalian.php"
               class="laporan-bubble">
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

            <form method="GET" action="peminjaman.php">
                <div class="row g-3 align-items-end">

                    <div class="col-md-4">
                        <label for="tanggal_awal">Tanggal Awal</label>
                        <input
                            type="date"
                            name="tanggal_awal"
                            id="tanggal_awal"
                            class="form-control"
                            value="<?= htmlspecialchars($tanggal_awal); ?>"
                        >
                    </div>

                    <div class="col-md-4">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <input
                            type="date"
                            name="tanggal_akhir"
                            id="tanggal_akhir"
                            class="form-control"
                            value="<?= htmlspecialchars($tanggal_akhir); ?>"
                        >
                    </div>

                    <div class="col-md-4">
                        <label for="status">Status Pembayaran</label>
                        <select name="status" id="status" class="form-select">
            <option value="">Semua Status</option>
            <option value="lunas" <?= $status_filter === 'lunas' ? 'selected' : ''; ?>>
                Lunas
            </option>
            <option value="belum_lunas" <?= $status_filter === 'belum_lunas' ? 'selected' : ''; ?>>
                Belum Lunas
            </option>
                        </select>
                    </div>

                    <div class="col-12 laporan-filter-action">
                        <button type="submit" class="btn-laporan-filter">
                            <i class="bi bi-search me-1"></i>
                            Cari
                        </button>

                        <a href="peminjaman.php" class="btn-laporan-reset">
                            Reset
                        </a>

                        <a href="index.php" class="btn-laporan-reset">
                            Kembali
                        </a>
                    </div>

                </div>
            </form>
        </div>

      <!-- TABEL LAPORAN -->
<div class="laporan-list-header">
    <div>
        <h3>Data Peminjaman</h3>
        <p>Daftar peminjaman berdasarkan tanggal dan status pembayaran.</p>
    </div>
</div>

<div class="data-master-card laporan-table-card">

    <?php if (mysqli_num_rows($query) > 0) { ?>

        <div class="data-table-wrapper">
            <table class="data-master-table laporan-table">

                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Kode Peminjaman</th>
                        <th>Nama Peminjam</th>
                        <th>Tanggal Peminjaman</th>
                        <th>Jatuh Tempo</th>
                        <th>Status Pembayaran</th>
                        <th class="text-center">Detail</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $no = 1;

                    while ($data = mysqli_fetch_assoc($query)) {
                        $id = (int) $data['id_peminjaman'];

                        $total_tagihan = (float) ($data['total_tagihan'] ?? 0);
                        $total_bayar = (float) ($data['total_bayar'] ?? 0);

                        $status_bayar = (
                            $total_tagihan > 0 &&
                            $total_bayar >= $total_tagihan
                        ) ? 'Lunas' : 'Belum Lunas';
                    ?>

                    <tr>
                        <td>
                            <span class="data-number"><?= $no++; ?></span>
                        </td>

                        <td>
                            <span class="data-id">#<?= $id; ?></span>
                        </td>

                        <td>
                            <span class="data-name">
                                <?= htmlspecialchars($data['nama_peminjam'] ?? '-'); ?>
                            </span>
                        </td>

                        <td>
                            <?= htmlspecialchars($data['tgl_peminjaman'] ?? '-'); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($data['tgl_jatuh_tempo'] ?? '-'); ?>
                        </td>

                        <td>
                            <span class="laporan-status <?= $status_bayar === 'Lunas' ? 'status-lunas' : 'status-belum-lunas'; ?>">
                                <i class="bi <?= $status_bayar === 'Lunas' ? 'bi-check-circle-fill' : 'bi-clock-fill'; ?>"></i>
                                <?= $status_bayar; ?>
                            </span>
                        </td>

                        <td class="text-center">
                            <button
                                type="button"
                                class="data-detail"
                                data-bs-toggle="collapse"
                                data-bs-target="#detail<?= $id; ?>"
                                aria-expanded="false"
                                aria-controls="detail<?= $id; ?>"
                            >
                                <i class="bi bi-eye"></i>
                                Detail
                            </button>
                        </td>
                    </tr>

                    <tr class="laporan-detail-row">
                        <td colspan="7" class="p-0">
                            <div class="collapse" id="detail<?= $id; ?>">
                                <div class="laporan-detail-content">

                                    <div class="laporan-detail-heading">
                                        <i class="bi bi-box-seam"></i>
                                        <h6>Detail Barang yang Dipinjam</h6>
                                    </div>

                                    <div class="laporan-detail-barang">
                                        <?= $data['daftar_barang'] ?: 'Tidak ada detail barang.'; ?>
                                    </div>

                                    <div class="laporan-detail-total">

                                        <div class="laporan-total-item">
                                            <span>Total Tagihan</span>
                                            <strong>
                                                Rp<?= number_format($total_tagihan, 0, ',', '.'); ?>
                                            </strong>
                                        </div>

                                        <div class="laporan-total-item">
                                            <span>Total Dibayar</span>
                                            <strong>
                                                Rp<?= number_format($total_bayar, 0, ',', '.'); ?>
                                            </strong>
                                        </div>

                                        <div class="laporan-total-item">
                                            <span>Sisa Pembayaran</span>
                                            <strong class="laporan-sisa">
                                                Rp<?= number_format(
                                                    max(0, $total_tagihan - $total_bayar),
                                                    0,
                                                    ',',
                                                    '.'
                                                ); ?>
                                            </strong>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </td>
                    </tr>

                    <?php } ?>
                </tbody>

            </table>
        </div>

        <div class="data-table-footer">
            <span>
                <i class="bi bi-info-circle me-1"></i>
                Total <?= $no - 1; ?> data peminjaman
            </span>
        </div>

    <?php } else { ?>

        <div class="laporan-kosong">
            <i class="bi bi-inbox"></i>
            <p>Belum ada data laporan peminjaman.</p>
        </div>

    <?php } ?>

</div>

<?php include "../layout/footer.php"; ?>