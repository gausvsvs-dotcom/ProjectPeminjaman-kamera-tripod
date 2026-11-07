
<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

// QUERY DATA BARANG DAN RINGKASAN UNIT
$query = mysqli_query($conn, "
    SELECT
        b.*,
        k.nama_kategori,
        m.nama_merk,

        COUNT(bu.id_unit) AS total_unit,

        SUM(
            CASE
                WHEN bu.status_unit = 'Tersedia'
                THEN 1 ELSE 0
            END
        ) AS unit_tersedia,

        SUM(
            CASE
                WHEN LOWER(kb.nama_kondisi) LIKE '%rusak%'
                THEN 1 ELSE 0
            END
        ) AS unit_rusak

    FROM barang b

    INNER JOIN kategori k
        ON b.id_kategori = k.id_kategori

    INNER JOIN merk m
        ON b.id_merk = m.id_merk

    LEFT JOIN barang_unit bu
        ON b.id_barang = bu.id_barang

    LEFT JOIN kondisi_barang kb
        ON bu.id_kondisi = kb.id_kondisi

    GROUP BY b.id_barang

    ORDER BY b.id_barang DESC
");

if (!$query) {
    die("Query error: " . mysqli_error($conn));
}
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
                <h2>Data Barang</h2>
                <p>
                    Kelola data barang kamera dan tripod pada sistem.
                </p>
            </div>
        </div>

        <!-- DATA CARD -->
        <div class="data-master-card">

            <div class="data-card-header">
                <div>
                    <h3 class="data-card-title">
                        <i class="bi bi-box-seam me-2"></i>
                        Daftar Barang
                    </h3>

                    <p>
                        Daftar barang dan kondisi unit yang tersedia dalam sistem.
                    </p>
                </div>

                <a href="tambah.php" class="btn-data-tambah">
                    <i class="bi bi-plus-lg"></i>
                    Tambah Barang
                </a>
            </div>

            <!-- NOTIFIKASI -->
            <?php if (isset($_GET['pesan'])) { ?>
                <div class="data-notification">

                    <?php if ($_GET['pesan'] == 'tambah') { ?>
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle me-2"></i>
                            Data barang berhasil ditambahkan!
                        </div>

                    <?php } elseif ($_GET['pesan'] == 'ubah') { ?>
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-check-circle me-2"></i>
                            Data barang berhasil diubah!
                        </div>

                    <?php } elseif ($_GET['pesan'] == 'hapus') { ?>
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-check-circle me-2"></i>
                            Data barang berhasil dihapus!
                        </div>
                    <?php } ?>

                </div>
            <?php } ?>

            <!-- TABEL BARANG -->
            <div class="data-table-wrapper">
                <div class="table-responsive">

                    <table class="data-master-table">
                        <thead>
                            <tr>
                                <th width="65">No.</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Jenis Barang</th>
                                <th>Stok</th>
                                <th>Kondisi Unit</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (mysqli_num_rows($query) > 0) { ?>

                                <?php
                                $no = 1;

                                while ($data = mysqli_fetch_assoc($query)) {

                                    $total_unit = (int) $data['total_unit'];
                                    $unit_tersedia = (int) $data['unit_tersedia'];
                                    $unit_rusak = (int) $data['unit_rusak'];
                                ?>

                                    <tr>

                                        <!-- NOMOR -->
                                        <td>
                                            <span class="data-number">
                                                <?= $no++; ?>
                                            </span>
                                        </td>

                                        <!-- KODE BARANG -->
                                        <td>
                                            <span class="data-id">
                                                <?= htmlspecialchars($data['kode_barang'] ?? ''); ?>
                                            </span>
                                        </td>

                                        <!-- NAMA BARANG -->
                                        <td>
                                            <span class="data-name">
                                                <?= htmlspecialchars($data['nama_barang'] ?? ''); ?>
                                            </span>
                                        </td>

                                        <!-- JENIS BARANG -->
                                        <td>
                                            <?= htmlspecialchars(ucfirst($data['jenis_barang'] ?? '')); ?>
                                        </td>

                                        <!-- STOK -->
                                        <td>
                                            <strong>
                                                <?= $unit_tersedia; ?>
                                            </strong>
                                            <span class="text-muted">
                                                / <?= $total_unit; ?>
                                            </span>
                                        </td>

                                        <!-- KONDISI -->
                                        <td>
                                            <?php if ($total_unit == 0) { ?>

                                                <span class="badge bg-secondary">
                                                    Belum ada unit
                                                </span>

                                            <?php } elseif ($unit_rusak > 0) { ?>

                                                <span class="badge bg-danger">
                                                    <?= $unit_rusak; ?> Rusak
                                                </span>

                                            <?php } else { ?>

                                                <span class="badge bg-success">
                                                    Kondisi baik
                                                </span>

                                            <?php } ?>
                                        </td>

                                        <!-- AKSI -->
                                        <td>
                                            <div class="data-action">

                                                <a
                                                    href="detail.php?id=<?= (int) $data['id_barang']; ?>"
                                                    class="data-detail"
                                                    title="Detail"
                                                    aria-label="Detail barang">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                <a
                                                    href="edit.php?id=<?= (int) $data['id_barang']; ?>"
                                                    class="data-edit"
                                                    title="Edit"
                                                    aria-label="Edit barang">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>

                                                <a
                                                    href="hapus.php?id=<?= (int) $data['id_barang']; ?>"
                                                    class="data-delete"
                                                    title="Hapus"
                                                    aria-label="Hapus barang"
                                                    onclick="return confirm('Yakin ingin menghapus barang ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>

                                            </div>
                                        </td>

                                    </tr>

                                <?php } ?>

                            <?php } else { ?>

                                <tr>
                                    <td colspan="7" class="data-empty">
                                        <i class="bi bi-inbox"></i>
                                        <p>Belum ada data barang.</p>
                                        <span>
                                            Silakan tambahkan data barang terlebih dahulu.
                                        </span>
                                    </td>
                                </tr>

                            <?php } ?>

                        </tbody>
                    </table>

                </div>
            </div>

            <!-- TOTAL DATA -->
            <div class="data-table-footer">
                Total data barang:
                <strong><?= mysqli_num_rows($query); ?> barang</strong>
            </div>

        </div>
    </div>
</div>

<?php include "../layout/footer.php"; ?>
