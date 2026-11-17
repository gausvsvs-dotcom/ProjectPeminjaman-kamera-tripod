
<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

// Ambil data merk
$query = mysqli_query(
    $conn,
    "SELECT * FROM merk ORDER BY id_merk DESC"
);

if (!$query) {
    die("Query error: " . mysqli_error($conn));
}

$data_merk = mysqli_fetch_all($query, MYSQLI_ASSOC);
$total_merk = count($data_merk);

// Notifikasi
$notifikasi = [
    'tambah' => [
        'class' => 'success',
        'icon' => 'check-circle-fill',
        'teks' => 'Data merk berhasil ditambahkan!'
    ],
    'ubah' => [
        'class' => 'warning',
        'icon' => 'check-circle-fill',
        'teks' => 'Data merk berhasil diubah!'
    ],
    'hapus' => [
        'class' => 'danger',
        'icon' => 'check-circle-fill',
        'teks' => 'Data merk berhasil dihapus!'
    ],
    'tidak_ditemukan' => [
        'class' => 'warning',
        'icon' => 'exclamation-circle-fill',
        'teks' => 'Data merk tidak ditemukan.'
    ],
    'digunakan' => [
    'class' => 'danger',
    'icon' => 'exclamation-circle-fill',
    'teks' => 'Merk tidak dapat dihapus karena masih digunakan oleh data barang!'
    ],
    'gagal' => [
        'class' => 'danger',
        'icon' => 'exclamation-circle-fill',
        'teks' => 'Data merk gagal dihapus!'
    ]
];

$pesan = $_GET['pesan'] ?? '';
?>

<?php include "../layout/header.php"; ?>
<link rel="stylesheet" href="../assets/css/data-master.css?v=2">
<?php include "../layout/navbar.php"; ?>

<div class="main-content data-master-page">
    <div class="container-fluid">

        <!-- Header Halaman -->
        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-tags"></i>
            </div>
            <div>
                <h2>Data Merk</h2>
                <p>Kelola daftar merk kamera dan tripod yang terdaftar.</p>
            </div>
        </div>

        <!-- Card Tabel -->
        <div class="data-master-card">

            <div class="data-card-header">
                <div>
                    <h5 class="data-card-title">Daftar Merk</h5>
                    <p class="mb-0 text-muted">
                        Informasi merk yang tersimpan.
                    </p>
                </div>

                <a href="tambah.php" class="btn-data-tambah">
                    <i class="bi bi-plus-lg"></i>
                    Tambah Merk
                </a>
            </div>

            <div class="data-card-body">

                <!-- Notifikasi -->
                <?php if (isset($notifikasi[$pesan])): ?>
                    <div class="data-notification">
                        <div class="alert alert-<?= $notifikasi[$pesan]['class']; ?> alert-dismissible fade show" role="alert">
                            <i class="bi bi-<?= $notifikasi[$pesan]['icon']; ?>"></i>
                            <?= $notifikasi[$pesan]['teks']; ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Tabel Merk -->
                <div class="data-table-wrapper">
                    <div class="table-responsive">
                        <table class="data-master-table">
                            <thead>
                                <tr>
                                    <th width="65">No.</th>
                                    <th>Nama Merk</th>
                                    <th>Keterangan</th>
                                    <th width="180">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if ($total_merk > 0): ?>
                                    <?php $no = 1; ?>

                                    <?php foreach ($data_merk as $data): ?>
                                        <?php $id_merk = (int) $data['id_merk']; ?>

                                        <tr>
                                            <td>
                                                <span class="data-number">
                                                    <?= $no++; ?>
                                                </span>
                                            </td>

                                            <td>
                                                <span class="data-name">
                                                    <?= htmlspecialchars(
                                                        $data['nama_merk'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ); ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?php if (!empty($data['keterangan'])): ?>
                                                    <?= nl2br(htmlspecialchars(
                                                        $data['keterangan'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    )); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">
                                                        Belum ada keterangan
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <div class="data-action">

                                                    <!-- Detail -->
                                                    <a
                                                        href="detail.php?id=<?= $id_merk; ?>"
                                                        class="data-detail"
                                                        title="Detail Merk"
                                                        aria-label="Detail Merk">
                                                        <i class="bi bi-eye"></i>
                                                    </a>

                                                    <!-- Edit -->
                                                    <a
                                                        href="edit.php?id=<?= $id_merk; ?>"
                                                        class="data-edit"
                                                        title="Edit Merk"
                                                        aria-label="Edit Merk">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>

                                                    <!-- Hapus -->
                                                    <a
                                                        href="hapus.php?id=<?= $id_merk; ?>"
                                                        class="data-delete"
                                                        title="Hapus Merk"
                                                        aria-label="Hapus Merk"
                                                        onclick="return confirm('Yakin ingin menghapus merk ini?')">
                                                        <i class="bi bi-trash3"></i>
                                                    </a>

                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                <?php else: ?>
                                    <tr>
                                        <td colspan="4">
                                            <div class="data-empty">
                                                <i class="bi bi-tags"></i>
                                                <h6>Belum Ada Data Merk</h6>
                                                <p>
                                                    Silakan tambahkan data merk terlebih dahulu.
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer Tabel -->
                <div class="data-table-footer">
                    Total merk:
                    <strong><?= $total_merk; ?></strong>
                    data
                </div>

            </div>
        </div>
    </div>
</div>

<?php include "../layout/footer.php"; ?>
