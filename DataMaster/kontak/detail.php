<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */
// Validasi ID pesan
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    echo "ID pesan tidak valid.";
    exit;
}
// Ambil data kontak
$stmt = mysqli_prepare($conn, "
    SELECT *
    FROM kontak
    WHERE id = ?
");
if (!$stmt) {
    die("Query gagal disiapkan: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
// Validasi data
if (!$data) {
    echo "Data pesan kontak tidak ditemukan.";
    exit;
}
?>
<?php include "../layout/header.php"; ?>
<link rel="stylesheet" href="../assets/css/data-master.css?v=3">
<?php include "../layout/navbar.php"; ?>
<div class="main-content data-master-page">
    <div class="container-fluid">
        <!-- Header Halaman -->
        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-envelope"></i>
            </div>
            <div>
                <h2>Detail Pesan Kontak</h2>
                <p>Informasi lengkap pesan yang dikirim oleh pelanggan.</p>
            </div>
        </div>
        <!-- Card Detail -->
        <div class="data-detail-card">
            <div class="data-detail-header">
                <h3>
                    <i class="bi bi-info-circle"></i>
                    Informasi Pesan
                </h3>
                <p>Detail pesan kontak pelanggan.</p>
            </div>
            <div class="data-detail-body">
                <div class="detail-item">
                    <div class="detail-label">Nama Pengirim</div>
                    <div class="detail-value">
                        <?= htmlspecialchars($data['nama'] ?? '-'); ?>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Email</div>
                    <div class="detail-value">
                        <?= htmlspecialchars($data['email'] ?? '-'); ?>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Isi Pesan</div>
                    <div class="detail-value">
                        <?= nl2br(htmlspecialchars($data['pesan'] ?? '-')); ?>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Tanggal Pesan</div>
                    <div class="detail-value">
                        <?= htmlspecialchars($data['created_at'] ?? '-'); ?>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Status Balasan</div>
                    <div class="detail-value">
                        <?php if (($data['status_balasan'] ?? '') === 'sudah_dijawab'): ?>
                            <span class="badge bg-success">Sudah Dijawab</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Belum Dijawab</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Balasan Admin</div>
                    <div class="detail-value">
                        <?= !empty($data['balasan'])
                            ? nl2br(htmlspecialchars($data['balasan']))
                            : 'Belum ada balasan dari admin.'; ?>
                    </div>
                </div>
                <!-- Tombol -->
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a href="jawab.php?id=<?= (int) $data['id']; ?>"
                       class="btn-detail-edit">
                        <i class="bi bi-reply"></i>
                        <?= !empty($data['balasan']) ? 'Edit Balasan' : 'Beri Balasan'; ?>
                    </a>
                    <a href="index.php" class="btn-detail-back">
                        <i class="bi bi-arrow-left"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php"; ?>