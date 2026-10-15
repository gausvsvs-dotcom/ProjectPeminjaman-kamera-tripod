<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}
// Ambil data merk
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM merk WHERE id_merk = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$query = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($query);
mysqli_stmt_close($stmt);

if (!$data) {
    header("Location: index.php?pesan=tidak_ditemukan");
    exit;
}
$nama_merk = $data['nama_merk'];
$error = "";
// Proses update
if (isset($_POST['update'])) {
    $nama_merk = trim($_POST['nama_merk'] ?? '');
    if ($nama_merk === '') {
        $error = "Nama merk wajib diisi.";
    } elseif (strlen($nama_merk) > 100) {
        $error = "Nama merk maksimal 100 karakter.";
    } else {
        // Periksa nama merk duplikat, kecuali data yang sedang diedit
        $stmt_cek = mysqli_prepare(
            $conn,
            "SELECT id_merk FROM merk
             WHERE LOWER(nama_merk) = LOWER(?)
             AND id_merk != ?");
        mysqli_stmt_bind_param(
            $stmt_cek,
            "si",
            $nama_merk,
            $id
        );
        mysqli_stmt_execute($stmt_cek);
        $hasil_cek = mysqli_stmt_get_result($stmt_cek);
        $duplikat = mysqli_num_rows($hasil_cek) > 0;
        mysqli_stmt_close($stmt_cek);
        if ($duplikat) {
            $error = "Nama merk tersebut sudah terdaftar.";
        } else {
            // Update data merk
            $stmt_update = mysqli_prepare(
                $conn,
                "UPDATE merk SET nama_merk = ? WHERE id_merk = ?"
            );
            mysqli_stmt_bind_param(
                $stmt_update,
                "si",
                $nama_merk,
                $id
            );
            if (mysqli_stmt_execute($stmt_update)) {
                mysqli_stmt_close($stmt_update);
                header("Location: index.php?pesan=ubah");
                exit;
            } else {
                $error = "Data merk gagal diubah.";
                mysqli_stmt_close($stmt_update);
            }
        }
    }
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
                <i class="bi bi-tags"></i>
            </div>
            <div>
                <h2>Edit Merk</h2>
                <p>Perbarui informasi merk barang yang terdaftar.</p>
            </div>
        </div>
        <!-- Form Edit -->
        <div class="data-form-card">
            <div class="data-form-header">
                <div>
                    <h5 class="mb-1">Form Edit Merk</h5>
                    <p class="mb-0">
                        Ubah nama merk sesuai data yang diperlukan.
                    </p>
                </div>
            </div>
            <div class="data-form-body">
                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <?= htmlspecialchars(
                            $error,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </div>
                <?php endif; ?>
                <form method="POST">
                    <!-- ID Merk -->
                    <div class="mb-4">
                        <label class="data-form-label">
                            ID Merk
                        </label>
                        <input
                            type="text"
                            class="data-form-control"
                            value="<?= (int) $data['id_merk']; ?>"
                            disabled>
                    </div>
                    <!-- Nama Merk -->
                    <div class="mb-4">
                        <label
                            for="nama_merk"
                            class="data-form-label">
                            Nama Merk
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="nama_merk"
                            name="nama_merk"
                            class="data-form-control"
                            placeholder="Masukkan nama merk"
                            value="<?= htmlspecialchars(
                                $nama_merk,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            maxlength="100"
                            required
                            autofocus>
                        <small class="text-muted">
                            Contoh: Canon, Nikon, Sony, atau Fujifilm.
                        </small>
                    </div>
                    <!-- Tombol -->
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button
                            type="submit"
                            name="update"
                            class="btn-form-save">
                            <i class="bi bi-check-circle me-1"></i>
                            Simpan Perubahan
                        </button>
                        <a
                            href="index.php"
                            class="btn-form-cancel">
                            <i class="bi bi-arrow-left me-1"></i>
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php"; ?>