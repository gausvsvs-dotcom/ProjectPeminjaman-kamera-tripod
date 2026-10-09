<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

$menu = isset($_GET['menu']) ? $_GET['menu'] : 'peminjaman';
$id_struk = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : '';
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
// QUERY LAPORAN GABUNGAN
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
                    ' (', d.jumlah, ' x Rp', FORMAT(d.harga_sewa, 0), ')'
                )
                SEPARATOR '<br>'
            )
            FROM detail_peminjaman d
            INNER JOIN barang b ON d.id_barang = b.id_barang
            WHERE d.id_peminjaman = p.id_peminjaman
        ) AS daftar_barang,
        (
            SELECT SUM(pb.total)
            FROM pembayaran pb
            WHERE pb.id_peminjaman = p.id_peminjaman
        ) AS total_bayar,
        (
            SELECT MAX(pb.tgl_bayar)
            FROM pembayaran pb
            WHERE pb.id_peminjaman = p.id_peminjaman
        ) AS tgl_bayar,
        (
            SELECT GROUP_CONCAT(DISTINCT pb.metode_pembayaran SEPARATOR ', ')
            FROM pembayaran pb
            WHERE pb.id_peminjaman = p.id_peminjaman
        ) AS metode_pembayaran,
        (
            SELECT GROUP_CONCAT(DISTINCT pb.status_pembayaran SEPARATOR ', ')
            FROM pembayaran pb
            WHERE pb.id_peminjaman = p.id_peminjaman
        ) AS status_pembayaran,
        (
            SELECT MAX(pg.tgl_pengembalian)
            FROM pengembalian pg
            WHERE pg.id_peminjaman = p.id_peminjaman
        ) AS tgl_pengembalian,
        (
            SELECT GROUP_CONCAT(DISTINCT pg.kondisi_pengembalian SEPARATOR ', ')
            FROM pengembalian pg
            WHERE pg.id_peminjaman = p.id_peminjaman
        ) AS kondisi_pengembalian,
        (
            SELECT SUM(pg.denda)
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
// FILTER TANGGAL
if (!empty($tanggal_awal)) {
    $tanggal_awal = mysqli_real_escape_string($conn, $tanggal_awal);
    $sql .= " AND p.tgl_peminjaman >= '$tanggal_awal'";
}
if (!empty($tanggal_akhir)) {
    $tanggal_akhir = mysqli_real_escape_string($conn, $tanggal_akhir);
    $sql .= " AND p.tgl_peminjaman <= '$tanggal_akhir'";
}
// FILTER ID UNTUK MELIHAT SATU STRUK
if ($menu == 'struk' && $id_struk > 0) {
    $sql .= " AND p.id_peminjaman = $id_struk";
}

// FILTER STATUS
if (!empty($status_filter)) {
    $status_filter = mysqli_real_escape_string($conn, $status_filter);
    $sql .= " AND p.status = '$status_filter'";
}
// MENU PENGEMBALIAN HANYA MENAMPILKAN TRANSAKSI YANG SUDAH ADA DATA PENGEMBALIAN
if ($menu == 'pengembalian') {
    $sql .= "
        AND EXISTS (
            SELECT 1 FROM pengembalian pg
            WHERE pg.id_peminjaman = p.id_peminjaman
        )";
}
$sql .= " ORDER BY p.tgl_peminjaman DESC";
$query = mysqli_query($conn, $sql);
if (!$query) {
    die("Query error: " . mysqli_error($conn));
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
                    Laporan peminjaman, pengembalian, status transaksi, dan struk.
                </p>
            </div>
        </div>
        <!-- BUBBLE MENU -->
        <div class="laporan-menu">
            <a href="index.php?menu=peminjaman"
               class="laporan-bubble <?= $menu == 'peminjaman' ? 'active' : ''; ?>">
                <i class="bi bi-box-arrow-up-right"></i>
                Peminjaman
            </a>
            <a href="index.php?menu=pengembalian"
               class="laporan-bubble <?= $menu == 'pengembalian' ? 'active' : ''; ?>">
                <i class="bi bi-box-arrow-in-left"></i>
                Pengembalian
            </a>
            <a href="index.php?menu=status"
               class="laporan-bubble <?= $menu == 'status' ? 'active' : ''; ?>">
                <i class="bi bi-clipboard-check"></i>
                Status
            </a>
            <a href="index.php?menu=struk"
                class="laporan-bubble <?= $menu == 'struk' ? 'active' : ''; ?>">
                <i class="bi bi-receipt"></i>
                Struk
            </a>
        </div>
        <!-- FILTER -->
        <?php if (!($menu == "struk" && $id_struk > 0)) { ?>
        <div class="laporan-filter-card">
            <h3 class="laporan-card-title">
                <i class="bi bi-funnel me-2"></i>
                Filter Laporan
            </h3>
            <form method="GET" action="index.php">
                <input type="hidden" name="menu"
                       value="<?= htmlspecialchars($menu); ?>">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="tanggal_awal">Tanggal Awal</label>
                        <input type="date" name="tanggal_awal" id="tanggal_awal"
                               class="form-control"
                               value="<?= htmlspecialchars($tanggal_awal); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" id="tanggal_akhir"
                               class="form-control"
                               value="<?= htmlspecialchars($tanggal_akhir); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="status">Status Peminjaman</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="menunggu" <?= $status_filter == 'menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                            <option value="disetujui" <?= $status_filter == 'disetujui' ? 'selected' : ''; ?>>Disetujui</option>
                            <option value="ditolak" <?= $status_filter == 'ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                            <option value="dipinjam" <?= $status_filter == 'dipinjam' ? 'selected' : ''; ?>>Dipinjam</option>
                            <option value="dikembalikan" <?= $status_filter == 'dikembalikan' ? 'selected' : ''; ?>>Dikembalikan</option>
                        </select>
                    </div>
                    <div class="col-md-3 laporan-filter-action">
                        <button type="submit" class="btn-laporan-filter">
                            <i class="bi bi-search me-1"></i> Cari
                        </button>
                        <a href="index.php?menu=<?= htmlspecialchars($menu); ?>"
                           class="btn-laporan-reset">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <?php } ?>

        <!-- DAFTAR STRUK -->
        <div class="laporan-list-header">
            <div>
                <h3>
                    <?php
                    if ($menu == 'pengembalian') {
                        echo "Laporan Pengembalian";
                    } elseif ($menu == 'status') {
                        echo "Laporan Status Transaksi";
                    } elseif ($menu == 'struk') {
                        echo $id_struk > 0 ? "Detail Struk Transaksi" : "Daftar Struk Transaksi";
                    } else {
                        echo "Laporan Peminjaman";
                    }
                    ?>
                </h3>
                <p>Ringkasan transaksi berdasarkan data peminjaman, pembayaran, dan pengembalian.</p>
            </div>
        </div>

        <?php if ($menu == 'struk' && $id_struk > 0) { ?>
            <div class="mb-3">
                <a href="index.php?menu=struk" class="btn-laporan-reset">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Struk
                </a>
            </div>
        <?php } ?>

        <div class="laporan-struk-list">
            <?php
            if (mysqli_num_rows($query) > 0) {
                while ($data = mysqli_fetch_assoc($query)) {
            ?>

                <div class="laporan-struk">

                    <?php if ($menu == 'struk' && $id_struk == 0) { ?>

                        <div class="struk-header">
                            <div>
                                <span class="struk-label">KODE PEMINJAMAN</span>
                                <h4>#<?= htmlspecialchars($data['id_peminjaman']); ?></h4>
                            </div>
                            <span class="struk-status">
                                <?= htmlspecialchars(ucfirst($data['status'])); ?>
                            </span>
                        </div>

                        <div class="struk-section">
                            <div class="struk-row">
                                <span>Nama Peminjam</span>
                                <strong><?= htmlspecialchars($data['nama_peminjam']); ?></strong>
                            </div>
                            <div class="struk-row">
                                <span>Tanggal Peminjaman</span>
                                <strong><?= htmlspecialchars($data['tgl_peminjaman']); ?></strong>
                            </div>
                        </div>

                        <div class="struk-footer">
                            <span>Ringkasan transaksi</span>
                            <a href="index.php?menu=struk&id=<?= (int)$data['id_peminjaman']; ?>"
                               class="btn-struk-print">
                                <i class="bi bi-receipt me-1"></i> Lihat Struk
                            </a>
                        </div>

                    <?php } else { ?>

                    <div class="struk-header">
                        <div>
                            <span class="struk-label">KODE PEMINJAMAN</span>
                            <h4>#<?= htmlspecialchars($data['id_peminjaman']); ?></h4>
                        </div>

                        <span class="struk-status">
                            <?= htmlspecialchars(ucfirst($data['status'])); ?>
                        </span>
                    </div>
                    <div class="struk-section">
                        <h5>Data Peminjam</h5>
                        <div class="struk-row">
                            <span>Nama Peminjam</span>
                            <strong><?= htmlspecialchars($data['nama_peminjam']); ?></strong>
                        </div>
                        <div class="struk-row">
                            <span>No. HP</span>
                            <strong><?= htmlspecialchars($data['no_hp']); ?></strong>
                        </div>
                        <div class="struk-row">
                            <span>Alamat</span>
                            <strong><?= htmlspecialchars($data['Alamat']); ?></strong>
                        </div>
                    </div>
                    <div class="struk-section">
                        <h5>Data Peminjaman</h5>
                        <div class="struk-row">
                            <span>Tanggal Peminjaman</span>
                            <strong><?= htmlspecialchars($data['tgl_peminjaman']); ?></strong>
                        </div>
                        <div class="struk-row">
                            <span>Jatuh Tempo</span>
                            <strong><?= htmlspecialchars($data['tgl_jatuh_tempo']); ?></strong>
                        </div>
                        <div class="struk-row">
                            <span>Jenis Peminjaman</span>
                            <strong><?= htmlspecialchars(ucfirst($data['jenis_peminjaman'])); ?></strong>
                        </div>
                        <div class="struk-barang">
                            <span>Barang Dipinjam</span>
                            <div>
                                <?= $data['daftar_barang'] ? $data['daftar_barang'] : '-'; ?>
                            </div>
                        </div>
                    </div>
                    <div class="struk-section">
                        <h5>Data Pembayaran</h5>
                        <div class="struk-row">
                            <span>Tanggal Bayar</span>
                            <strong><?= $data['tgl_bayar'] ? htmlspecialchars($data['tgl_bayar']) : '-'; ?></strong>
                        </div>
                        <div class="struk-row">
                            <span>Metode Pembayaran</span>
                            <strong><?= $data['metode_pembayaran'] ? htmlspecialchars($data['metode_pembayaran']) : '-'; ?></strong>
                        </div>
                        <div class="struk-row">
                            <span>Status Pembayaran</span>
                            <strong><?= $data['status_pembayaran'] ? htmlspecialchars($data['status_pembayaran']) : 'Belum ada pembayaran'; ?></strong>
                        </div>
                        <div class="struk-row struk-total">
                            <span>Total Dibayar</span>
                            <strong>Rp<?= number_format((float)$data['total_bayar'], 0, ',', '.'); ?></strong>
                        </div>
                    </div>
                    <div class="struk-section">
                        <h5>Data Pengembalian</h5>
                        <div class="struk-row">
                            <span>Tanggal Pengembalian</span>
                            <strong><?= $data['tgl_pengembalian'] ? htmlspecialchars($data['tgl_pengembalian']) : 'Belum dikembalikan'; ?></strong>
                        </div>
                        <div class="struk-row">
                            <span>Kondisi Pengembalian</span>
                            <strong><?= $data['kondisi_pengembalian'] ? htmlspecialchars($data['kondisi_pengembalian']) : '-'; ?></strong>
                        </div>
                        <div class="struk-row struk-total">
                            <span>Denda</span>
                            <strong>Rp<?= number_format((float)$data['total_denda'], 0, ',', '.'); ?></strong>
                        </div>
                    </div>
                    <div class="struk-footer">
                        <span>Ringkasan laporan transaksi</span>
                        <button type="button" onclick="window.print()" class="btn-struk-print">
                            <i class="bi bi-printer me-1"></i> Cetak
                        </button>
                    </div>
                    <?php } ?>
                </div>
            <?php
                }
            } else {
            ?>
                <div class="laporan-kosong">
                    <i class="bi bi-inbox"></i>
                    <p>Belum ada data laporan.</p>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>
<?php include "../layout/footer.php"; ?>