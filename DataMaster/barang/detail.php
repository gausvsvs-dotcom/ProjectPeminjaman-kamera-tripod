
<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

// VALIDASI ID BARANG
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header("Location: index.php");
    exit;
}

// AMBIL DETAIL BARANG
$stmt = mysqli_prepare($conn, "
    SELECT
        b.*,
        k.nama_kategori,
        m.nama_merk
    FROM barang b
    INNER JOIN kategori k
        ON b.id_kategori = k.id_kategori
    INNER JOIN merk m
        ON b.id_merk = m.id_merk
    WHERE b.id_barang = ?
");

if (!$stmt) {
    die("Query detail barang gagal: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$query = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($query);

mysqli_stmt_close($stmt);

if (!$data) {
    die("Data barang tidak ditemukan.");
}

// REKAP JUMLAH UNIT BERDASARKAN KONDISI
$stmt_rekap = mysqli_prepare($conn, "
    SELECT
        kb.id_kondisi,
        kb.nama_kondisi,
        COUNT(bu.id_unit) AS jumlah
    FROM kondisi_barang kb
    LEFT JOIN barang_unit bu
        ON bu.id_kondisi = kb.id_kondisi
        AND bu.id_barang = ?
    GROUP BY kb.id_kondisi, kb.nama_kondisi
    ORDER BY kb.id_kondisi ASC
");

if (!$stmt_rekap) {
    die("Query rekap kondisi gagal: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt_rekap, "i", $id);
mysqli_stmt_execute($stmt_rekap);

$query_rekap = mysqli_stmt_get_result($stmt_rekap);

$rekap_kondisi = [];

while ($rekap = mysqli_fetch_assoc($query_rekap)) {
    $rekap["jumlah"] = (int) $rekap["jumlah"];
    $rekap_kondisi[] = $rekap;
}

mysqli_stmt_close($stmt_rekap);

// AMBIL DAFTAR UNIT BARANG
$stmt_unit = mysqli_prepare($conn, "
    SELECT
        bu.*,
        kb.nama_kondisi
    FROM barang_unit bu
    LEFT JOIN kondisi_barang kb
        ON bu.id_kondisi = kb.id_kondisi
    WHERE bu.id_barang = ?
    ORDER BY bu.id_unit ASC
");

if (!$stmt_unit) {
    die("Query unit barang gagal: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt_unit, "i", $id);
mysqli_stmt_execute($stmt_unit);

$query_unit = mysqli_stmt_get_result($stmt_unit);

// SIMPAN UNIT KE ARRAY
$daftar_unit = [];

$total_unit = 0;
$jumlah_tersedia = 0;
$jumlah_rusak = 0;

while ($unit = mysqli_fetch_assoc($query_unit)) {

    $nama_kondisi = strtolower($unit["nama_kondisi"] ?? "");
    $status_unit = $unit["status_unit"];

    $is_rusak = strpos($nama_kondisi, "rusak") !== false;

    // HITUNG UNIT RUSAK
    if ($is_rusak) {
        $jumlah_rusak++;
    }

    // UNIT SIAP DIPINJAM:
    // STATUS TERSEDIA DAN KONDISI BUKAN RUSAK
    if ($status_unit === "Tersedia" && !$is_rusak) {
        $jumlah_tersedia++;
    }

    $unit["is_rusak"] = $is_rusak;

    $daftar_unit[] = $unit;
    $total_unit++;
}

mysqli_stmt_close($stmt_unit);

?>

<?php include "../layout/header.php"; ?>

<link rel="stylesheet" href="../assets/css/data-master.css?v=3">

<?php include "../layout/navbar.php"; ?>

<div class="main-content data-master-page">
    <div class="container-fluid">

        <!-- PAGE HEADER -->
        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-camera"></i>
            </div>

            <div>
                <h2>Detail Barang</h2>
                <p>Informasi barang dan kondisi setiap unit.</p>
            </div>
        </div>

        <!-- DETAIL CARD -->
        <div class="data-detail-card">

            <div class="data-detail-header">
                <h3>
                    <i class="bi bi-info-circle me-2"></i>
                    Informasi Barang
                </h3>

                <p>Detail data barang yang dipilih.</p>
            </div>

            <div class="data-detail-body">

                <div class="detail-item">
                    <div class="detail-label">Kode Barang</div>
                    <div class="detail-value">
                        <?= htmlspecialchars($data["kode_barang"], ENT_QUOTES, "UTF-8"); ?>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Nama Barang</div>
                    <div class="detail-value">
                        <?= htmlspecialchars($data["nama_barang"], ENT_QUOTES, "UTF-8"); ?>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Jenis Barang</div>
                    <div class="detail-value">
                        <?= htmlspecialchars(ucfirst($data["jenis_barang"]), ENT_QUOTES, "UTF-8"); ?>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Kategori</div>
                    <div class="detail-value">
                        <?= htmlspecialchars($data["nama_kategori"], ENT_QUOTES, "UTF-8"); ?>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Merk</div>
                    <div class="detail-value">
                        <?= htmlspecialchars($data["nama_merk"], ENT_QUOTES, "UTF-8"); ?>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Total Unit</div>
                    <div class="detail-value">
                        <?= $total_unit; ?> unit
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Unit Siap Dipinjam</div>
                    <div class="detail-value">
                        <span class="text-success fw-bold">
                            <?= $jumlah_tersedia; ?> unit
                        </span>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Unit Rusak</div>
                    <div class="detail-value">
                        <span class="text-danger fw-bold">
                            <?= $jumlah_rusak; ?> unit
                        </span>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Foto Barang</div>
                    <div class="detail-value">

                        <?php if (!empty($data["foto"])): ?>

                            <img
                                src="../uploads/barang/<?= htmlspecialchars($data["foto"], ENT_QUOTES, "UTF-8"); ?>"
                                alt="Foto barang"
                                class="foto-detail">

                        <?php else: ?>

                            <span class="text-muted">
                                Tidak ada foto
                            </span>

                        <?php endif; ?>

                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Deskripsi</div>
                    <div class="detail-value">

                        <?php if (!empty($data["deskripsi"])): ?>

                            <?= nl2br(htmlspecialchars($data["deskripsi"], ENT_QUOTES, "UTF-8")); ?>

                        <?php else: ?>

                            <span class="text-muted">
                                Tidak ada deskripsi
                            </span>

                        <?php endif; ?>

                    </div>
                </div>

            </div>
        </div>

        <!-- REKAP KONDISI BARANG -->
        <div class="data-master-card mt-4">

            <div class="data-card-header">
                <div>
                    <h3 class="data-card-title">
                        <i class="bi bi-bar-chart-fill me-2"></i>
                        Rekap Kondisi Barang
                    </h3>

                    <p>
                        Ringkasan jumlah unit berdasarkan kondisi barang.
                    </p>
                </div>
            </div>

            <div class="p-3">
                <div class="row g-3">

                    <!-- TOTAL UNIT -->
                    <div class="col-md-4 col-sm-6">
                        <div class="p-3 rounded-3 h-100"
                             style="background: #f0ebf7; border: 1px solid #e4dced;">

                            <div class="mb-2" style="color: #8174A0;">
                                <i class="bi bi-boxes me-1"></i>
                                Total Unit
                            </div>

                            <h3 class="fw-bold mb-0" style="color: #8174A0;">
                                <?= $total_unit; ?>
                            </h3>

                            <small class="text-muted">Unit terdata</small>
                        </div>
                    </div>

                    <!-- REKAP SETIAP KONDISI -->
                    <?php foreach ($rekap_kondisi as $item):

                        $nama = strtolower($item["nama_kondisi"]);

                        if (strpos($nama, "baik") !== false) {
                            $warna = "#198754";
                            $ikon = "bi-check-circle";
                            $background = "#e8f5ed";

                        } elseif (strpos($nama, "lecet") !== false) {
                            $warna = "#d69b24";
                            $ikon = "bi-exclamation-circle";
                            $background = "#fff5df";

                        } elseif (strpos($nama, "rusak") !== false) {
                            $warna = "#dc3545";
                            $ikon = "bi-x-circle";
                            $background = "#fdebed";

                        } else {
                            $warna = "#8174A0";
                            $ikon = "bi-box";
                            $background = "#f0ebf7";
                        }
                    ?>

                        <div class="col-md-4 col-sm-6">
                            <div class="p-3 rounded-3 h-100"
                                 style="background: <?= $background; ?>;
                                        border: 1px solid <?= $warna; ?>33;">

                                <div class="mb-2" style="color: <?= $warna; ?>;">
                                    <i class="bi <?= $ikon; ?> me-1"></i>
                                    <?= htmlspecialchars($item["nama_kondisi"], ENT_QUOTES, "UTF-8"); ?>
                                </div>

                                <h3 class="fw-bold mb-0"
                                    style="color: <?= $warna; ?>;">
                                    <?= $item["jumlah"]; ?>
                                </h3>

                                <small class="text-muted">Unit</small>
                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>
            </div>
        </div>

        <!-- DAFTAR UNIT -->
        <div class="data-master-card mt-4">

            <div class="data-card-header">
                <div>
                    <h3 class="data-card-title">
                        <i class="bi bi-boxes me-2"></i>
                        Daftar Unit Barang
                    </h3>

                    <p>
                        Informasi kondisi dan ketersediaan setiap unit.
                    </p>
                </div>

                <a
                    href="unit.php?id=<?= (int) $data["id_barang"]; ?>"
                    class="btn-data-tambah">

                    <i class="bi bi-gear"></i>
                    Kelola Unit

                </a>
            </div>

            <div class="data-table-wrapper">
                <div class="table-responsive">

                    <table class="data-master-table">

                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Kode Unit</th>
                                <th>Kondisi</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if ($total_unit > 0): ?>

                                <?php foreach ($daftar_unit as $index => $unit): ?>

                                    <tr>

                                        <td>
                                            <?= $index + 1; ?>
                                        </td>

                                        <td>
                                            <span class="data-id">
                                                <?= htmlspecialchars($unit["kode_unit"], ENT_QUOTES, "UTF-8"); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($unit["nama_kondisi"] ?? "-", ENT_QUOTES, "UTF-8"); ?>
                                        </td>

                                        <td>

                                            <?php if ($unit["is_rusak"]): ?>

                                                <span class="badge bg-danger">
                                                    Tidak dapat dipinjam
                                                </span>

                                            <?php elseif ($unit["status_unit"] === "Tersedia"): ?>

                                                <span class="badge bg-success">
                                                    Siap dipinjam
                                                </span>

                                            <?php elseif ($unit["status_unit"] === "Dipinjam"): ?>

                                                <span class="badge bg-primary">
                                                    Dipinjam
                                                </span>

                                            <?php else: ?>

                                                <span class="badge bg-secondary">
                                                    <?= htmlspecialchars($unit["status_unit"], ENT_QUOTES, "UTF-8"); ?>
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <td>
                                            <?php if (!empty($unit["keterangan_kondisi"])): ?>

                                                <?= htmlspecialchars($unit["keterangan_kondisi"], ENT_QUOTES, "UTF-8"); ?>

                                            <?php else: ?>

                                                <span class="text-muted">-</span>

                                            <?php endif; ?>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="5" class="data-empty">
                                        <i class="bi bi-inbox"></i>

                                        <p>Belum ada unit barang.</p>

                                        <span>
                                            Silakan tambahkan unit melalui menu Kelola Unit.
                                        </span>
                                    </td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>
            </div>

            <div class="data-table-footer">
                Total unit:
                <strong><?= $total_unit; ?> unit</strong>
            </div>

        </div>

        <!-- BUTTON -->
        <div class="mt-4 mb-4">

            <a
                href="edit.php?id=<?= (int) $data["id_barang"]; ?>"
                class="btn-detail-edit">

                <i class="bi bi-pencil-square me-1"></i>
                Edit Barang

            </a>

            <a
                href="index.php"
                class="btn-detail-back ms-1">

                <i class="bi bi-arrow-left me-1"></i>
                Kembali

            </a>

        </div>

    </div>
</div>

<?php include "../layout/footer.php"; ?>
