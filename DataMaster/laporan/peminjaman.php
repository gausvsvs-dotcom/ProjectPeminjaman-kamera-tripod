<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

// FILTER TANGGAL DAN STATUS
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : "";
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : "";
$status = isset($_GET['status']) ? $_GET['status'] : "";

// QUERY LAPORAN
$sql = "SELECT * FROM peminjaman WHERE 1=1";

// Filter tanggal peminjaman
if (!empty($tanggal_awal)) {
    $tanggal_awal = mysqli_real_escape_string($conn, $tanggal_awal);
    $sql .= " AND tgl_peminjaman >= '$tanggal_awal'";
}

if (!empty($tanggal_akhir)) {
    $tanggal_akhir = mysqli_real_escape_string($conn, $tanggal_akhir);
    $sql .= " AND tgl_peminjaman <= '$tanggal_akhir'";
}

// Filter status
if (!empty($status)) {
    $status = mysqli_real_escape_string($conn, $status);
    $sql .= " AND status = '$status'";
}

$sql .= " ORDER BY tgl_peminjaman DESC";

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

        <!-- PAGE HEADER -->
        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-file-earmark-text"></i>
            </div>
            <div>
                <h2>Laporan Peminjaman</h2>
                <p>
                    Lihat dan filter data peminjaman kamera dan tripod.
                </p>
            </div>
        </div>

        <!-- FILTER LAPORAN -->
        <div class="laporan-filter-card">
            <h3 class="laporan-card-title">
                <i class="bi bi-funnel me-2"></i>
                Filter Laporan
            </h3>

            <form method="GET" action="">
                <div class="row g-3 align-items-end">

                    <div class="col-md-3">
                        <label for="tanggal_awal">Tanggal Awal</label>
                        <input
                            type="date"
                            name="tanggal_awal"
                            id="tanggal_awal"
                            class="form-control"
                            value="<?= htmlspecialchars($tanggal_awal); ?>">
                    </div>

                    <div class="col-md-3">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <input
                            type="date"
                            name="tanggal_akhir"
                            id="tanggal_akhir"
                            class="form-control"
                            value="<?= htmlspecialchars($tanggal_akhir); ?>">
                    </div>

                    <div class="col-md-3">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="menunggu" <?= $status == "menunggu" ? "selected" : ""; ?>>
                                Menunggu
                            </option>
                            <option value="disetujui" <?= $status == "disetujui" ? "selected" : ""; ?>>
                                Disetujui
                            </option>
                            <option value="ditolak" <?= $status == "ditolak" ? "selected" : ""; ?>>
                                Ditolak
                            </option>
                            <option value="dipinjam" <?= $status == "dipinjam" ? "selected" : ""; ?>>
                                Dipinjam
                            </option>
                            <option value="dikembalikan" <?= $status == "dikembalikan" ? "selected" : ""; ?>>
                                Dikembalikan
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn-laporan-filter">
                            <i class="bi bi-search me-1"></i>
                            Cari
                        </button>

                        <a href="peminjaman.php" class="btn-laporan-reset">
                            Reset
                        </a>
                    </div>

                </div>
            </form>
        </div>

        <!-- DATA LAPORAN -->
        <div class="data-master-card">
            <div class="data-card-header">
                <div>
                    <h3 class="data-card-title">
                        <i class="bi bi-table me-2"></i>
                        Daftar Laporan Peminjaman
                    </h3>
                    <p>
                        Data peminjaman berdasarkan tanggal dan status.
                    </p>
                </div>
            </div>

            <div class="data-table-wrapper">
                <div class="table-responsive">
                    <table class="data-master-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Peminjaman</th>
                                <th>Nama Peminjam</th>
                                <th>Barang Dipinjam</th>
                                <th>Tanggal Peminjaman</th>
                                <th>Rencana Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if (mysqli_num_rows($query) > 0) {
                                while ($data = mysqli_fetch_assoc($query)) {
                            ?>
                                    <tr>
                                        <td>
                                            <span class="data-number">
                                                <?= $no++; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="data-id">
                                                <?= htmlspecialchars($data['kode_peminjaman']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="data-name">
                                                <?= htmlspecialchars($data['nama_peminjam']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            // Mengambil ID barang dari kolom keterangan
                                            $barang_dipinjam = "-";
                                            if (preg_match('/ID Barang:\s*(\d+)/', $data['keterangan'], $hasil)) {
                                                $id_barang = $hasil[1];

                                                $query_barang = mysqli_query(
                                                    $conn,
                                                    "SELECT nama_barang FROM barang WHERE id_barang = '$id_barang'"
                                                );
                                                if ($query_barang && mysqli_num_rows($query_barang) > 0) {
                                                    $barang = mysqli_fetch_assoc($query_barang);
                                                    $barang_dipinjam = $barang['nama_barang'];
                                                }
                                            }
                                            // Mengambil jumlah barang
                                            if (preg_match('/Jumlah:\s*(\d+)/', $data['keterangan'], $hasil_jumlah)) {
                                                $barang_dipinjam .= " (" . $hasil_jumlah[1] . ")";
                                            }
                                            echo htmlspecialchars($barang_dipinjam);
                                            ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($data['tgl_peminjaman']); ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($data['tanggal_rencana_kembali']); ?>
                                        </td>
                                        <td>
                                            <span class="laporan-status">
                                                <?= htmlspecialchars(ucfirst($data['status'])); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="7" class="text-center">
                                        Belum ada data peminjaman.
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php"; ?>