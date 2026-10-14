<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */
function aman($teks)
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}
// Validasi ID pesan
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}
// Proses simpan balasan
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $balasan = trim($_POST['balasan'] ?? '');
    if ($balasan === '') {
        $error = 'Balasan tidak boleh kosong.';
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE kontak
             SET balasan = ?, status_balasan = 'sudah_dijawab'
             WHERE id = ?"
        );
        mysqli_stmt_bind_param($stmt, "si", $balasan, $id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);

            header("Location: index.php?pesan=jawab");
            exit;
        }
        $error = 'Balasan gagal disimpan: ' . mysqli_error($conn);
        mysqli_stmt_close($stmt);
    }
}
// Ambil data pesan
$stmt = mysqli_prepare(
    $conn,
    "SELECT id, nama, email, pesan, balasan, status_balasan, created_at
     FROM kontak
     WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
if (!$data) {
    header("Location: index.php");
    exit;
}
?>
<?php include "../layout/header.php"; ?>
<link rel="stylesheet" href="../assets/css/data-master.css?v=2">
<?php include "../layout/navbar.php"; ?>
<div class="main-content data-master-page">
    <div class="container-fluid">
        <!-- HEADER HALAMAN -->
        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-reply-all"></i>
            </div>
            <div>
                <h2>Balas Pesan Kontak</h2>
                <p>Baca pesan pengunjung dan kirimkan tanggapan.</p>
            </div>
        </div>
        <div class="row g-4">
            <!-- DETAIL PESAN -->
            <div class="col-lg-5">
                <div class="data-master-card h-100">
                    <div class="data-card-header">
                        <div>
                            <h3 class="data-card-title">
                                <i class="bi bi-envelope-open me-2"></i>
                                Detail Pesan
                            </h3>
                            <p class="mb-0">
                                Informasi dari pengunjung website.
                            </p>
                        </div>
                    </div>
                    <div class="data-card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">
                                Nama Pengirim
                            </small>
                            <strong><?= aman($data['nama']); ?></strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">
                                Email
                            </small>
                            <strong><?= aman($data['email']); ?></strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">
                                Tanggal Pesan
                            </small>
                            <strong><?= aman($data['created_at']); ?></strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-2">
                                Status
                            </small>
                            <?php if (
                                $data['status_balasan'] === 'sudah_dijawab'
                            ): ?>
                                <span class="badge bg-success">
                                    Sudah Dijawab
                                </span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">
                                    Belum Dijawab
                                </span>
                            <?php endif; ?>
                        </div>
                        <hr>
                        <div>
                            <small class="text-muted d-block mb-2">
                                Isi Pesan
                            </small>
                            <div class="p-3 rounded"
                                 style="background:#FFFBEB; line-height:1.8; overflow-wrap:anywhere;">
                                <?= nl2br(aman($data['pesan'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- FORM BALASAN -->
            <div class="col-lg-7">
                <div class="data-master-card">
                    <div class="data-card-header">
                        <div>
                            <h3 class="data-card-title">
                                <i class="bi bi-chat-square-text me-2"></i>
                                Form Balasan
                            </h3>
                            <p class="mb-0">
                                Tulis tanggapan untuk pesan pengunjung.
                            </p>
                        </div>
                    </div>
                    <div class="data-card-body">
                        <?php if ($error !== ''): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                <?= aman($error); ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Email Pengunjung
                                </label>
                                <input
                                    type="email"
                                    class="form-control"
                                    value="<?= aman($data['email']); ?>"
                                    readonly>
                            </div>
                            <div class="mb-4">
                                <label for="balasan" class="form-label fw-semibold">
                                    Isi Balasan
                                </label>
                                <textarea
                                    name="balasan"
                                    id="balasan"
                                    class="form-control"
                                    rows="7"
                                    placeholder="Tuliskan balasan untuk pengunjung..."
                                    required><?= aman(
                                        $_POST['balasan'] ?? $data['balasan'] ?? ''
                                    ); ?></textarea>
                                <small class="text-muted">
                                    Balasan akan disimpan ke database.
                                </small>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send-fill me-1"></i>
                                    Simpan Balasan
                                </button>
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                        </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php"; ?>
