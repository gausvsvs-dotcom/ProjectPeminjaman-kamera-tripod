
<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

// Ambil seluruh data kategori
$query = mysqli_query(
    $conn,
    "SELECT * FROM kategori ORDER BY id_kategori DESC"
);

if (!$query) {
    die("Query error: " . mysqli_error($conn));
}

// Simpan semua data agar bisa dihitung dan ditampilkan dengan benar
$data_kategori = mysqli_fetch_all($query, MYSQLI_ASSOC);
$total_kategori = count($data_kategori);

// Daftar notifikasi
$notifikasi = [
    'tambah' => [
        'class' => 'success',
        'icon' => 'check-circle-fill',
        'teks' => 'Data kategori berhasil ditambahkan!'
    ],
    'ubah' => [
        'class' => 'warning',
        'icon' => 'check-circle-fill',
        'teks' => 'Data kategori berhasil diubah!'
    ],
    'hapus' => [
        'class' => 'danger',
        'icon' => 'check-circle-fill',
        'teks' => 'Data kategori berhasil dihapus!'
    ]
];

$pesan = $_GET['pesan'] ?? '';
?>

<?php include "../layout/header.php"; ?>

<link rel="stylesheet" href="../assets/css/data-master.css?v=2">

<?php include "../layout/navbar.php"; ?>

<div class="main-content data-master-page">
    <div class="container-fluid">

        <!-- HEADER HALAMAN -->
        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-tags"></i>
            </div>

            <div>
                <h2>Data Kategori</h2>
                <p>Kelola kategori barang yang tersedia pada sistem.</p>
            </div>
        </div>

        <!-- CARD DATA KATEGORI -->
        <div class="data-master-card">

            <!-- HEADER CARD -->
            <div class="data-card-header">
                <div>
                    <h3 class="data-card-title">
                        <i class="bi bi-list-ul me-2"></i>
                        Daftar Kategori
                    </h3>

                    <p class="mb-0">
                        Informasi kategori barang yang tersimpan.
                    </p>
                </div>

                <!-- TOMBOL TAMBAH -->
                <a href="tambah.php" class="btn-data-tambah">
                    <i class="bi bi-plus-circle me-1"></i>
                    Tambah Kategori
                </a>
            </div>

            <!-- ISI CARD -->
            <div class="data-card-body">

                <!-- NOTIFIKASI -->
                <?php if (isset($notifikasi[$pesan])): ?>
                    <?php $notif = $notifikasi[$pesan]; ?>

                    <div class="data-notification">
                        <div
                            class="alert alert-<?= htmlspecialchars($notif['class']); ?> alert-dismissible fade show"
                            role="alert"
                        >
                            <i class="bi bi-<?= htmlspecialchars($notif['icon']); ?> me-2"></i>

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

                <!-- TABEL DATA KATEGORI -->
                <div class="data-table-wrapper">
                    <table class="data-master-table">

                        <thead>
                            <tr>
                                <th width="65">No.</th>
                                <th>Nama Kategori</th>
                                <th>Keterangan</th>
                                <th width="190">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($total_kategori > 0): ?>
                                <?php foreach ($data_kategori as $index => $data): ?>
                                    <tr>
                                        <!-- NOMOR -->
                                        <td>
                                            <span class="data-number">
                                                <?= $index + 1; ?>
                                            </span>
                                        </td>
                                        <!-- NAMA KATEGORI -->
                                        <td>
                                            <span class="data-name">
                                                <?= htmlspecialchars(
                                                    $data['nama_kategori'] ?? '',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>
                                            </span>
                                        </td>

                                        <!-- KETERANGAN -->
                                        <td>
                                            <?php if (!empty($data['keterangan'])): ?>
                                                <?= nl2br(
                                                    htmlspecialchars(
                                                        $data['keterangan'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    )
                                                ); ?>
                                            <?php else: ?>
                                                <span class="text-muted">
                                                    Belum ada keterangan
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- TOMBOL AKSI -->
                                        <td>
                                            <div class="data-action">

                                                <a
                                                    href="detail.php?id=<?= (int) $data['id_kategori']; ?>"
                                                    class="data-detail"
                                                    title="Detail"
                                                    aria-label="Detail kategori" >
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a
                                                    href="edit.php?id=<?= (int) $data['id_kategori']; ?>"
                                                    class="data-edit"
                                                    title="Edit"
                                                    aria-label="Edit kategori">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a
                                                    href="hapus.php?id=<?= (int) $data['id_kategori']; ?>"
                                                    class="data-delete"
                                                    title="Hapus"
                                                    aria-label="Hapus kategori"
                                                    onclick="return confirm('Yakin ingin menghapus kategori ini?');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="data-empty">
                                        <i class="bi bi-inbox"></i>
                                        <p>Belum ada data kategori.</p>
                                        <span>
                                            Silakan tambahkan kategori baru.
                                        </span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>

                    </table>
                </div>
                <!-- TOTAL DATA -->
                <div class="data-table-footer">
                    Total kategori:
                    <strong><?= $total_kategori; ?></strong> data
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php"; ?>
