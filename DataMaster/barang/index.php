
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

// SIMPAN DATA BARANG
$data_barang = mysqli_fetch_all($query, MYSQLI_ASSOC);
$total_barang = count($data_barang);

// DAFTAR NOTIFIKASI
$notifikasi = [
    'tambah' => [
        'class' => 'success',
        'icon' => 'check-circle-fill',
        'teks' => 'Data barang berhasil ditambahkan!'
    ],
    'ubah' => [
        'class' => 'warning',
        'icon' => 'check-circle-fill',
        'teks' => 'Data barang berhasil diubah!'
    ],
    'hapus' => [
        'class' => 'danger',
        'icon' => 'check-circle-fill',
        'teks' => 'Data barang berhasil dihapus!'
    ]
];

$pesan = $_GET['pesan'] ?? '';
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

            <!-- HEADER CARD -->
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

            <!-- ISI CARD -->
            <div class="data-card-body">

                <!-- NOTIFIKASI -->
                <?php if (isset($notifikasi[$pesan])): ?>
                    <?php $notif = $notifikasi[$pesan]; ?>

                    <div class="data-notification">
                        <div
                            class="alert alert-<?= htmlspecialchars($notif['class'], ENT_QUOTES, 'UTF-8'); ?> alert-dismissible fade show"
                            role="alert"
                        >
                            <i class="bi bi-<?= htmlspecialchars($notif['icon'], ENT_QUOTES, 'UTF-8'); ?> me-2"></i>

                            <?= htmlspecialchars(
                                $notif['teks'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"
                                aria-label="Tutup"
                            ></button>
                        </div>
                    </div>
                <?php endif; ?>

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

                                <?php if ($total_barang > 0): ?>

                                    <?php foreach ($data_barang as $index => $data): ?>

                                        <?php
                                        $total_unit = (int) $data['total_unit'];
                                        $unit_tersedia = (int) $data['unit_tersedia'];
                                        $unit_rusak = (int) $data['unit_rusak'];
                                        ?>

                                        <tr>

                                            <!-- NOMOR -->
                                            <td>
                                                <span class="data-number">
                                                    <?= $index + 1; ?>
                                                </span>
                                            </td>

                                            <!-- KODE BARANG -->
                                            <td>
                                                <span class="data-id">
                                                    <?= htmlspecialchars(
                                                        $data['kode_barang'] ?? '',
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ); ?>
                                                </span>
                                            </td>

                                            <!-- NAMA BARANG -->
                                            <td>
                                                <span class="data-name">
                                                    <?= htmlspecialchars(
                                                        $data['nama_barang'] ?? '',
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ); ?>
                                                </span>
                                            </td>

                                            <!-- JENIS BARANG -->
                                            <td>
                                                <?= htmlspecialchars(
                                                    ucfirst($data['jenis_barang'] ?? ''),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>
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

                                            <!-- KONDISI UNIT -->
                                            <td>
                                                <?php if ($total_unit == 0): ?>

                                                    <span class="badge bg-secondary">
                                                        Belum ada unit
                                                    </span>

                                                <?php elseif ($unit_rusak > 0): ?>

                                                    <span class="badge bg-danger">
                                                        <?= $unit_rusak; ?> Rusak
                                                    </span>

                                                <?php else: ?>

                                                    <span class="badge bg-success">
                                                        Kondisi baik
                                                    </span>

                                                <?php endif; ?>
                                            </td>

                                            <!-- AKSI -->
                                            <td>
                                                <div class="data-action">

                                                    <a
                                                        href="detail.php?id=<?= (int) $data['id_barang']; ?>"
                                                        class="data-detail"
                                                        title="Detail"
                                                        aria-label="Detail barang"
                                                    >
                                                        <i class="bi bi-eye"></i>
                                                    </a>

                                                    <a
                                                        href="edit.php?id=<?= (int) $data['id_barang']; ?>"
                                                        class="data-edit"
                                                        title="Edit"
                                                        aria-label="Edit barang"
                                                    >
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>

                                                    <a
                                                        href="hapus.php?id=<?= (int) $data['id_barang']; ?>"
                                                        class="data-delete"
                                                        title="Hapus"
                                                        aria-label="Hapus barang"
                                                        onclick="return confirm('Yakin ingin menghapus barang ini?')"
                                                    >
                                                        <i class="bi bi-trash"></i>
                                                    </a>

                                                </div>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <tr>
                                        <td colspan="7" class="data-empty">
                                            <i class="bi bi-inbox"></i>
                                            <p>Belum ada data barang.</p>
                                            <span>
                                                Silakan tambahkan data barang terlebih dahulu.
                                            </span>
                                        </td>
                                    </tr>

                                <?php endif; ?>

                            </tbody>
                        </table>

                    </div>
                </div>

                <!-- TOTAL DATA -->
                <div class="data-table-footer">
                    Total data barang:
                    <strong><?= $total_barang; ?> barang</strong>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include "../layout/footer.php"; ?>
