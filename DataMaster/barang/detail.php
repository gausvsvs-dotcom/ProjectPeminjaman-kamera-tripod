
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

<style>
    .detail-info-layout {
        display: grid;
        grid-template-columns: minmax(220px, 0.8fr) minmax(0, 1.5fr);
        gap: 24px;
        padding: 24px;
    }

    .detail-photo-box {
        background: #f5f1f8;
        border-radius: 14px;
        min-height: 280px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .detail-photo-box img {
        width: 100%;
        max-height: 300px;
        object-fit: contain;
        border-radius: 10px;
    }

    .detail-photo-empty {
        color: #8174A0;
        text-align: center;
    }

    .detail-info-content {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
        align-content: start;
    }

    .detail-info-item {
        padding: 13px 15px;
        background: #faf8fc;
        border: 1px solid #eee8f3;
        border-radius: 10px;
    }

    .detail-info-item.full {
        grid-column: 1 / -1;
    }

    .detail-info-label {
        color: #8174A0;
        font-size: 13px;
        margin-bottom: 6px;
    }

    .detail-info-value {
        font-weight: 600;
        color: #34303a;
        overflow-wrap: anywhere;
    }

    .detail-price {
        color: #8174A0;
        font-size: 20px;
        font-weight: 700;
    }

    .detail-description {
        padding: 0 24px 24px;
    }

    .detail-description-content {
        background: #faf8fc;
        border: 1px solid #eee8f3;
        border-radius: 10px;
        padding: 15px;
        line-height: 1.7;
    }

    @media (max-width: 768px) {
        .detail-info-layout {
            grid-template-columns: 1fr;
            padding: 16px;
        }

        .detail-info-content {
            grid-template-columns: 1fr;
        }

        .detail-info-item.full {
            grid-column: auto;
        }

        .detail-description {
            padding: 0 16px 16px;
        }
    }

    @media print {
        .data-page-header,
        .data-card-header a,
        .detail-action-buttons,
        .data-table-footer {
            display: none !important;
        }

        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }

        .data-master-card,
        .data-detail-card {
            break-inside: avoid;
        }
    }
</style>

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

        <!-- INFORMASI BARANG -->
        <div class="data-detail-card">

            <div class="data-detail-header">
                <h3>
                    <i class="bi bi-info-circle me-2"></i>
                    Informasi Barang
                </h3>
                <p>Detail data barang yang dipilih.</p>
            </div>

            <div class="detail-info-layout">

                <!-- FOTO BARANG -->
                <div class="detail-photo-box">

                    <?php if (!empty($data["foto"])): ?>

                        <img
                            src="../../uploads/barang/<?= htmlspecialchars($data["foto"], ENT_QUOTES, "UTF-8"); ?>"
                            alt="Foto <?= htmlspecialchars($data["nama_barang"], ENT_QUOTES, "UTF-8"); ?>">

                    <?php else: ?>

                        <div class="detail-photo-empty">
                            <i class="bi bi-image" style="font-size: 50px;"></i>
                            <p class="mt-2 mb-0">Tidak ada foto barang</p>
                        </div>

                    <?php endif; ?>

                </div>

                <!-- INFORMASI -->
                <div class="detail-info-content">

                    <div class="detail-info-item">
                        <div class="detail-info-label">Kode Barang</div>
                        <div class="detail-info-value">
                            <?= htmlspecialchars($data["kode_barang"], ENT_QUOTES, "UTF-8"); ?>
                        </div>
                    </div>

                    <div class="detail-info-item">
                        <div class="detail-info-label">Nama Barang</div>
                        <div class="detail-info-value">
                            <?= htmlspecialchars($data["nama_barang"], ENT_QUOTES, "UTF-8"); ?>
                        </div>
                    </div>

                    <div class="detail-info-item">
                        <div class="detail-info-label">Jenis Barang</div>
                        <div class="detail-info-value">
                            <?= htmlspecialchars(ucfirst($data["jenis_barang"]), ENT_QUOTES, "UTF-8"); ?>
                        </div>
                    </div>

                    <div class="detail-info-item">
                        <div class="detail-info-label">Kategori</div>
                        <div class="detail-info-value">
                            <?= htmlspecialchars($data["nama_kategori"], ENT_QUOTES, "UTF-8"); ?>
                        </div>
                    </div>

                    <div class="detail-info-item">
                        <div class="detail-info-label">Merk</div>
                        <div class="detail-info-value">
                            <?= htmlspecialchars($data["nama_merk"], ENT_QUOTES, "UTF-8"); ?>
                        </div>
                    </div>

                    <div class="detail-info-item">
                        <div class="detail-info-label">Harga Sewa</div>
                        <div class="detail-price">
                            Rp <?= number_format((float) $data["harga_sewa"], 0, ",", "."); ?>
                        </div>
                        <small class="text-muted">Per paket</small>
                    </div>

                    <div class="detail-info-item">
                        <div class="detail-info-label">Total Unit</div>
                        <div class="detail-info-value">
                            <?= $total_unit; ?> unit
                        </div>
                    </div>

                    <div class="detail-info-item">
                        <div class="detail-info-label">Unit Siap Dipinjam</div>
                        <div class="detail-info-value text-success">
                            <?= $jumlah_tersedia; ?> unit
                        </div>
                    </div>

                    <div class="detail-info-item">
                        <div class="detail-info-label">Unit Rusak</div>
                        <div class="detail-info-value text-danger">
                            <?= $jumlah_rusak; ?> unit
                        </div>
                    </div>

                </div>
            </div>

            <!-- DESKRIPSI -->
            <div class="detail-description">
                <div class="detail-info-label">Deskripsi Barang</div>

                <div class="detail-description-content">

                    <?php if (!empty($data["deskripsi"])): ?>

                        <?= nl2br(htmlspecialchars($data["deskripsi"], ENT_QUOTES, "UTF-8")); ?>

                    <?php else: ?>

                        <span class="text-muted">Tidak ada deskripsi.</span>

                    <?php endif; ?>

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

                    <p>Ringkasan jumlah unit berdasarkan kondisi barang.</p>
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

                    <!-- REKAP KONDISI -->
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

                    <p>Informasi kondisi dan ketersediaan setiap unit.</p>
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
                                        <td><?= $index + 1; ?></td>

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

        <!-- TOMBOL AKSI -->
        <div class="mt-4 mb-4 detail-action-buttons">

            <button
                type="button"
                onclick="window.print()"
                class="btn-detail-edit">

                <i class="bi bi-printer me-1"></i>
                Cetak Data Barang

            </button>

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
