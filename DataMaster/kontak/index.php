<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../LandingPage/login.php");
    exit;
}
require_once "../config/koneksi.php";
/** @var mysqli $conn */
// Ambil semua pesan kontak
$query = mysqli_query($conn, "
    SELECT id, nama, email, pesan, balasan,
           status_balasan, created_at
    FROM kontak
    ORDER BY created_at DESC
");
if (!$query) {
    die("Gagal mengambil data kontak: " . mysqli_error($conn));
}
function aman($teks) {
    return htmlspecialchars((string)($teks ?? ''), ENT_QUOTES, 'UTF-8');
}
?>
<?php include "../layout/header.php"; ?>
<link rel="stylesheet" href="../assets/css/data-master.css">
<?php include "../layout/navbar.php"; ?>
<div class="main-content dashboard-wrapper">
    <div class="dashboard-header">
        <div>
            <h1 class="dashboard-title">Pesan Kontak</h1>
            <p class="dashboard-subtitle">
                Kelola pesan dan balasan untuk konsumen.
            </p>
        </div>
    </div>
    <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
        <div class="alert alert-success">
            Balasan berhasil disimpan.
        </div>
    <?php endif; ?>
    <div class="dashboard-card">
        <div class="section-header">
            <h5 class="section-title">
                <i class="bi bi-chat-left-text-fill"></i>
                Daftar Pesan Konsumen
            </h5>
        </div>
        <div class="section-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Pesan</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($query) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($query)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= aman($row['nama']); ?></td>
                                <td><?= aman($row['email']); ?></td>
                                <td style="min-width:220px; max-width:350px;">
                                    <?= nl2br(aman($row['pesan'])); ?>
                                </td>
                                <td>
                                    <?= aman(date(
                                        'd-m-Y H:i',
                                        strtotime($row['created_at'])
                                    )); ?>
                                </td>
                                <td>
                                    <?php if ($row['status_balasan'] === 'sudah_dijawab'): ?>
                                        <span class="badge text-bg-success">
                                            Sudah dijawab
                                        </span>
                                    <?php else: ?>
                                        <span class="badge text-bg-warning">
                                            Belum dijawab
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a
                                        href="jawab.php?id=<?= (int)$row['id']; ?>"
                                        class="btn btn-sm btn-primary"
                                        style="background:#8174A0; border-color:#8174A0;">
                                        <i class="bi bi-reply-fill"></i>
                                        Jawab
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Belum ada pesan dari konsumen.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php"; ?>
