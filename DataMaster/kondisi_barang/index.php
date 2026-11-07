<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

// Ambil seluruh data kondisi barang
$query = mysqli_query(
    $conn,
    "SELECT * FROM kondisi_barang ORDER BY id_kondisi DESC"
);

if (!$query) {
    die("Query error: " . mysqli_error($conn));
}

// Simpan data agar mudah ditampilkan
$data_kondisi = mysqli_fetch_all($query, MYSQLI_ASSOC);
$total_kondisi = count($data_kondisi);

// Daftar notifikasi
$notifikasi = [
    'tambah' => [
        'class' => 'success',
        'icon' => 'check-circle-fill',
        'teks' => 'Data kondisi barang berhasil ditambahkan!'
    ],
    'ubah' => [
        'class' => 'warning',
        'icon' => 'check-circle-fill',
        'teks' => 'Data kondisi barang berhasil diubah!'
    ],
    'hapus' => [
        'class' => 'danger',
        'icon' => 'check-circle-fill',
        'teks' => 'Data kondisi barang berhasil dihapus!'
    ]
];

$pesan = $_GET['pesan'] ?? '';
?>

<?php include "../layout/header.php"; ?>

<link rel="stylesheet" href="../assets/css/data-master.css?v=3">

<?php include "../layout/navbar.php"; ?>

<div class="main-content data-master-page">
    <div class="container-fluid">

        <!-- HEADER HALAMAN -->
        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-clipboard2-pulse"></i>
            </div>

            <div>
                <h2>Data Kondisi Barang</h2>
                <p>
                    Kelola informasi kondisi kamera dan tripod
                    yang tersedia dalam sistem peminjaman.
                </p>
            </div>
        </div>

        <!-- CARD DATA KONDISI -->
        <div class="data-master-card">

            <!-- HEADER CARD -->
            <div class="data-card-header">
                <div>
                    <h3 class="data-card-title">
                        <i class="bi bi-list-check me-2"></i>
                        Daftar Kondisi Barang
                    </h3>

                    <p class="mb-0">
                        Informasi kondisi barang yang tersimpan.
                    </p>
                </div>

                <!-- TOMBOL TAMBAH -->
                <a href="tambah.php" class="btn-data-tambah">
                    <i class="bi bi-plus-circle me-1"></i>
                    Tambah Kondisi
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

                <!-- TABEL DATA KONDISI -->
                <div class="data-table-wrapper">
                    <table class="data-master-table">

                        <thead>
                            <tr>
                                <th width="65">No.</th>
                                <th>Nama Kondisi</th>
                                <th>Keterangan</th>
                                <th width="190">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($total_kondisi > 0): ?>
                                <?php foreach ($data_kondisi as $index => $data): ?>
                                    <tr>

                                        <!-- NOMOR -->
                                        <td>
                                            <span class="data-number">
                                                <?= $index + 1; ?>
                                            </span>
                                        </td>

                                        <!-- NAMA KONDISI -->
                                        <td>
                                            <span class="data-name">
                                                <?= htmlspecialchars(
                                                    $data['nama_kondisi'] ?? '',
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
                                                    href="detail.php?id=<?= (int) $data['id_kondisi']; ?>"
                                                    class="data-detail"
                                                    title="Detail"
                                                    aria-label="Detail kondisi">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                <a
                                                    href="edit.php?id=<?= (int) $data['id_kondisi']; ?>"
                                                    class="data-edit"
                                                    title="Edit"
                                                    aria-label="Edit kondisi">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>

                                                <a
                                                    href="hapus.php?id=<?= (int) $data['id_kondisi']; ?>"
                                                    class="data-delete"
                                                    title="Hapus"
                                                    aria-label="Hapus kondisi"
                                                    onclick="return confirm('Yakin ingin menghapus kondisi barang ini?');">
                                                    <i class="bi bi-trash"></i>
                                                </a>

                                            </div>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>

                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="data-empty">
                                        <i class="bi bi-inbox"></i>
                                        <p>Belum ada data kondisi barang.</p>
                                        <span>
                                            Silakan tambahkan kondisi barang terlebih dahulu.
                                        </span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>

                    </table>
                </div>

                <!-- TOTAL DATA -->
                <div class="data-table-footer">
                    Total kondisi barang:
                    <strong><?= $total_kondisi; ?></strong> data
                </div>

            </div>
        </div>
    </div>
</div>

<?php include "../layout/footer.php"; ?>