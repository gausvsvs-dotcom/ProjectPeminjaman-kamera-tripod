
<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

// MENU AKTIF
$menu = $_GET['menu'] ?? 'peminjaman';
$menu_valid = ['peminjaman', 'pengembalian', 'statistik', 'struk'];

if (!in_array($menu, $menu_valid, true)) {
    $menu = 'peminjaman';
}

// ID STRUK
$id_struk = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// FILTER TANGGAL
$tanggal_awal  = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';

// QUERY LAPORAN STRUK
$sql = "
    SELECT
        p.id_peminjaman,
        p.nama_peminjam,
        p.no_hp,
        p.Alamat,
        p.tgl_peminjaman,
        p.tgl_jatuh_tempo,
        p.jenis_peminjaman,
        p.status,

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
            SELECT COALESCE(SUM(pb.total), 0)
            FROM pembayaran pb
            WHERE pb.id_peminjaman = p.id_peminjaman
        ) AS total_bayar,

        (
            SELECT MAX(pb.tgl_bayar)
            FROM pembayaran pb
            WHERE pb.id_peminjaman = p.id_peminjaman
        ) AS tgl_bayar,

        (
            SELECT GROUP_CONCAT(
                DISTINCT pb.metode_pembayaran
                SEPARATOR ', '
            )
            FROM pembayaran pb
            WHERE pb.id_peminjaman = p.id_peminjaman
        ) AS metode_pembayaran,

        (
            SELECT GROUP_CONCAT(
                DISTINCT pb.status_pembayaran
                SEPARATOR ', '
            )
            FROM pembayaran pb
            WHERE pb.id_peminjaman = p.id_peminjaman
        ) AS status_pembayaran,

        (
            SELECT MAX(pg.tgl_pengembalian)
            FROM pengembalian pg
            WHERE pg.id_peminjaman = p.id_peminjaman
        ) AS tgl_pengembalian,

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
    WHERE 1=1
";

// FILTER TANGGAL PEMINJAMAN
if (!empty($tanggal_awal)) {
    $tanggal_awal = mysqli_real_escape_string(
        $conn,
        $tanggal_awal
    );

    $sql .= " AND DATE(p.tgl_peminjaman) >= '$tanggal_awal'";
}

if (!empty($tanggal_akhir)) {
    $tanggal_akhir = mysqli_real_escape_string(
        $conn,
        $tanggal_akhir
    );

    $sql .= " AND DATE(p.tgl_peminjaman) <= '$tanggal_akhir'";
}

// FILTER SATU STRUK
if ($menu === 'struk' && $id_struk > 0) {
    $sql .= " AND p.id_peminjaman = $id_struk";
}

// FILTER PENGEMBALIAN
if ($menu === 'pengembalian') {
    $sql .= "
        AND EXISTS (
            SELECT 1
            FROM pengembalian pg
            WHERE pg.id_peminjaman = p.id_peminjaman
        )
    ";
}

$sql .= " ORDER BY p.tgl_peminjaman DESC";

$query = mysqli_query($conn, $sql);

if (!$query) {
    die("Query laporan gagal: " . mysqli_error($conn));
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
                <h2>Laporan</h2>
                <p>
                    Laporan peminjaman, pengembalian, statistik, dan struk transaksi.
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
               class="laporan-bubble">
                <i class="bi bi-bar-chart-line"></i>
                Statistik
            </a>

            <a href="index.php?menu=struk"
               class="laporan-bubble <?= $menu === 'struk' ? 'active' : ''; ?>">
                <i class="bi bi-receipt"></i>
                Struk
            </a>

        </div>

        <!-- JUDUL DAFTAR -->
        <div class="laporan-list-header">
            <div>
                <h3>
                    <?= $menu === 'struk'
                        ? ($id_struk > 0
                            ? 'Detail Struk Transaksi'
                            : 'Daftar Struk Transaksi')
                        : 'Daftar Struk Transaksi'; ?>
                </h3>

                <p>
                    Ringkasan transaksi peminjaman, pembayaran, dan pengembalian.
                </p>
            </div>
        </div>

        <!-- TOMBOL KEMBALI -->
        <?php if ($menu === 'struk' && $id_struk > 0) { ?>

            <div class="mb-3">
                <a href="index.php?menu=struk"
                   class="btn-laporan-reset">
                    <i class="bi bi-arrow-left me-1"></i>
                    Kembali ke Daftar Struk
                </a>
            </div>

        <?php } ?>

        <!-- DAFTAR STRUK -->
        <div class="laporan-struk-list">

        <?php if (mysqli_num_rows($query) > 0) { ?>

            <?php while ($data = mysqli_fetch_assoc($query)) { ?>

                <div class="laporan-struk">

                    <!-- MODE DAFTAR STRUK -->
                    <?php if ($menu === 'struk' && $id_struk === 0) { ?>

                        <div class="struk-header">
                            <div>
                                <span class="struk-label">
                                    KODE PEMINJAMAN
                                </span>

                                <h4>
                                    #<?= (int) $data['id_peminjaman']; ?>
                                </h4>
                            </div>

                            <span class="struk-status">
                                <?= htmlspecialchars(
                                    ucfirst($data['status'] ?? '-')
                                ); ?>
                            </span>
                        </div>

                        <div class="struk-section">

                            <div class="struk-row">
                                <span>Nama Peminjam</span>

                                <strong>
                                    <?= htmlspecialchars(
                                        $data['nama_peminjam'] ?? '-'
                                    ); ?>
                                </strong>
                            </div>

                            <div class="struk-row">
                                <span>Tanggal Peminjaman</span>

                                <strong>
                                    <?= htmlspecialchars(
                                        $data['tgl_peminjaman'] ?? '-'
                                    ); ?>
                                </strong>
                            </div>

                        </div>

                        <div class="struk-footer">
                            <span>Ringkasan transaksi</span>

                            <a href="index.php?menu=struk&id=<?= (int) $data['id_peminjaman']; ?>"
                               class="btn-struk-print">
                                <i class="bi bi-receipt me-1"></i>
                                Lihat Struk
                            </a>
                        </div>

                    <!-- MODE DETAIL STRUK -->
                    <?php } else { ?>

                        <!-- HEADER STRUK -->
                        <div class="struk-header">
                            <div>
                                <span class="struk-label">
                                    KODE PEMINJAMAN
                                </span>

                                <h4>
                                    #<?= (int) $data['id_peminjaman']; ?>
                                </h4>
                            </div>

                            <span class="struk-status">
                                <?= htmlspecialchars(
                                    ucfirst($data['status'] ?? '-')
                                ); ?>
                            </span>
                        </div>

                        <!-- DATA PEMINJAM -->
                        <div class="struk-section">

                            <h5>Data Peminjam</h5>

                            <div class="struk-row">
                                <span>Nama Peminjam</span>

                                <strong>
                                    <?= htmlspecialchars(
                                        $data['nama_peminjam'] ?? '-'
                                    ); ?>
                                </strong>
                            </div>

                            <div class="struk-row">
                                <span>No. HP</span>

                                <strong>
                                    <?= htmlspecialchars(
                                        $data['no_hp'] ?? '-'
                                    ); ?>
                                </strong>
                            </div>

                            <div class="struk-row">
                                <span>Alamat</span>

                                <strong>
                                    <?= htmlspecialchars(
                                        $data['Alamat'] ?? '-'
                                    ); ?>
                                </strong>
                            </div>

                        </div>

                        <!-- DATA PEMINJAMAN -->
                        <div class="struk-section">

                            <h5>Data Peminjaman</h5>

                            <div class="struk-row">
                                <span>Tanggal Peminjaman</span>

                                <strong>
                                    <?= htmlspecialchars(
                                        $data['tgl_peminjaman'] ?? '-'
                                    ); ?>
                                </strong>
                            </div>

                            <div class="struk-row">
                                <span>Jatuh Tempo</span>

                                <strong>
                                    <?= htmlspecialchars(
                                        $data['tgl_jatuh_tempo'] ?? '-'
                                    ); ?>
                                </strong>
                            </div>

                            <div class="struk-row">
                                <span>Jenis Peminjaman</span>

                                <strong>
                                    <?= htmlspecialchars(
                                        ucfirst(
                                            $data['jenis_peminjaman'] ?? '-'
                                        )
                                    ); ?>
                                </strong>
                            </div>

                            <div class="struk-barang">
                                <span>Barang Dipinjam</span>

                                <div>
                                    <?= $data['daftar_barang']
                                        ?: '-'; ?>
                                </div>
                            </div>

                        </div>

                        <!-- DATA PEMBAYARAN -->
                        <div class="struk-section">

                            <h5>Data Pembayaran</h5>

                            <div class="struk-row">
                                <span>Tanggal Bayar</span>

                                <strong>
                                    <?= !empty($data['tgl_bayar'])
                                        ? htmlspecialchars(
                                            $data['tgl_bayar']
                                        )
                                        : 'Belum ada pembayaran'; ?>
                                </strong>
                            </div>

                            <div class="struk-row">
                                <span>Metode Pembayaran</span>

                                <strong>
                                    <?= !empty($data['metode_pembayaran'])
                                        ? htmlspecialchars(
                                            $data['metode_pembayaran']
                                        )
                                        : '-'; ?>
                                </strong>
                            </div>

                            <div class="struk-row">
                                <span>Status Pembayaran</span>

                                <strong>
                                    <?= !empty($data['status_pembayaran'])
                                        ? htmlspecialchars(
                                            $data['status_pembayaran']
                                        )
                                        : 'Belum ada pembayaran'; ?>
                                </strong>
                            </div>

                            <div class="struk-row struk-total">
                                <span>Total Dibayar</span>

                                <strong>
                                    Rp<?= number_format(
                                        (float) ($data['total_bayar'] ?? 0),
                                        0,
                                        ',',
                                        '.'
                                    ); ?>
                                </strong>
                            </div>

                        </div>

                        <!-- DATA PENGEMBALIAN -->
                        <div class="struk-section">

                            <h5>Data Pengembalian</h5>

                            <div class="struk-row">
                                <span>Tanggal Pengembalian</span>

                                <strong>
                                    <?= !empty($data['tgl_pengembalian'])
                                        ? htmlspecialchars(
                                            $data['tgl_pengembalian']
                                        )
                                        : 'Belum dikembalikan'; ?>
                                </strong>
                            </div>

                            <div class="struk-row">
                                <span>Kondisi Pengembalian</span>

                                <strong>
                                    <?= !empty($data['kondisi_pengembalian'])
                                        ? htmlspecialchars(
                                            $data['kondisi_pengembalian']
                                        )
                                        : '-'; ?>
                                </strong>
                            </div>

                            <div class="struk-row struk-total">
                                <span>Denda</span>

                                <strong>
                                    Rp<?= number_format(
                                        (float) ($data['total_denda'] ?? 0),
                                        0,
                                        ',',
                                        '.'
                                    ); ?>
                                </strong>
                            </div>

                        </div>

                        <!-- FOOTER STRUK -->
                        <div class="struk-footer">
                            <span>Ringkasan laporan transaksi</span>

                            <button
                                type="button"
                                onclick="window.print()"
                                class="btn-struk-print">
                                <i class="bi bi-printer me-1"></i>
                                Cetak Struk
                            </button>
                        </div>

                    <?php } ?>

                </div>

            <?php } ?>

        <?php } else { ?>

            <div class="laporan-kosong">
                <i class="bi bi-inbox"></i>
                <p>Belum ada data laporan.</p>
            </div>

        <?php } ?>

        </div>

    </div>
</div>

<?php include "../layout/footer.php"; ?>
