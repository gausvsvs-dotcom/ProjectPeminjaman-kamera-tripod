<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../config/koneksi.php";
/** @var mysqli $conn */

// Fungsi untuk mengamankan output
function aman($teks)
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $balasan = trim($_POST['balasan'] ?? '');
    if ($balasan === '') {
        $error = 'Balasan tidak boleh kosong.';
    } else {
        $sql_update = "UPDATE kontak
                       SET balasan = ?,
                           status_balasan = 'sudah_dijawab'
                       WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql_update);
        // Periksa query UPDATE
        if (!$stmt) {
            die("Query UPDATE gagal: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt, "si", $balasan, $id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: index.php?pesan=jawab");
            exit;
        } else {
            $error = "Balasan gagal disimpan: "
                     . mysqli_stmt_error($stmt);

            mysqli_stmt_close($stmt);
        }
    }
}
$sql_select = "SELECT id, nama, email, pesan, balasan,
                      status_balasan, created_at
               FROM kontak
               WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql_select);
// Periksa query SELECT
if (!$stmt) {
    die("Query SELECT gagal: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $id);

if (!mysqli_stmt_execute($stmt)) {

    die("Eksekusi SELECT gagal: "
        . mysqli_stmt_error($stmt));

}
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
// Jika data tidak ditemukan
if (!$data) {
    header("Location: index.php");
    exit;
}
?>
<?php include "../layout/header.php"; ?>
<link rel="stylesheet" href="../assets/css/data-master.css?v=3">
<?php include "../layout/navbar.php"; ?>
<div class="main-content data-master-page">
    <div class="container-fluid reply-container">
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
        <div class="row g-4 reply-layout">
            <!-- ================================== -->
            <!-- DETAIL PESAN -->
            <!-- ================================== -->
            <div class="col-lg-5">
                <div class="data-master-card reply-card">
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
                    <div class="data-card-body reply-detail">
                        <!-- NAMA PENGIRIM -->
                        <div class="reply-info-item">
                            <span>Nama Pengirim</span>
                            <strong>
                                <?= aman($data['nama']); ?>
                            </strong>
                        </div>
                        <!-- EMAIL -->
                        <div class="reply-info-item">
                            <span>Email</span>
                            <strong>
                                <?= aman($data['email']); ?>
                            </strong>
                        </div>
                        <!-- TANGGAL -->
                        <div class="reply-info-item">
                            <span>Tanggal Pesan</span>
                            <strong>
                                <?= aman($data['created_at']); ?>
                            </strong>
                        </div>
                        <!-- STATUS BALASAN -->
                        <div class="reply-info-item">
                            <span>Status</span>
                            <?php if (
                                ($data['status_balasan'] ?? '') === 'sudah_dijawab'
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

                        <!-- ISI PESAN -->
                        <div class="reply-message-section">

                            <span>Isi Pesan</span>

                            <div class="reply-message">
                                <?= nl2br(aman($data['pesan'])); ?>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!-- ================================== -->
            <!-- FORM BALASAN -->
            <!-- ================================== -->

            <div class="col-lg-7">

                <div class="data-master-card reply-card">

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

                    <div class="data-card-body reply-form-body">

                        <!-- NOTIFIKASI ERROR -->
                        <?php if ($error !== ''): ?>

                            <div class="alert alert-danger" role="alert">

                                <i class="bi bi-exclamation-circle me-2"></i>

                                <?= aman($error); ?>

                            </div>

                        <?php endif; ?>

                        <!-- FORM -->
                        <form method="POST">

                            <!-- EMAIL PENGUNJUNG -->
                            <div class="mb-4">

                                <label
                                    for="email_pengunjung"
                                    class="form-label">

                                    Email Pengunjung

                                </label>

                                <input
                                    type="email"
                                    id="email_pengunjung"
                                    class="form-control"
                                    value="<?= aman($data['email']); ?>"
                                    readonly>

                            </div>
                            <!-- ISI BALASAN -->
                            <div class="mb-4">
                                <label
                                    for="balasan"
                                    class="form-label">
                                    Isi Balasan
                                </label>
                                <textarea
                                    name="balasan"
                                    id="balasan"
                                    class="form-control reply-textarea"
                                    rows="7"
                                    placeholder="Tuliskan balasan untuk pengunjung..."
                                    required><?= aman(
                                        $_POST['balasan']
                                        ?? $data['balasan']
                                        ?? ''
                                    ); ?></textarea>
                                <small class="text-muted reply-help">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Balasan akan disimpan ke database.
                                </small>
                            </div>
                            <!-- TOMBOL AKSI -->
                            <div class="reply-actions">
                                <button
                                    type="submit"
                                    class="btn btn-primary">
                                    <i class="bi bi-send-fill me-1"></i>
                                    Simpan Balasan
                                </button>
                                <a
                                    href="index.php"
                                    class="btn btn-outline-secondary">
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