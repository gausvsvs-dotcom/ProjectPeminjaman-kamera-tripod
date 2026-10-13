<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../config/koneksi.php";
/** @var mysqli $conn */

$error = "";
$nama_kategori = "";
$keterangan = "";
if (isset($_POST['simpan'])) {
    $nama_kategori = trim($_POST['nama_kategori'] ?? "");
    $keterangan = trim($_POST['keterangan'] ?? "");
    // Validasi nama kategori
    if ($nama_kategori === "") {
        $error = "Nama kategori wajib diisi!";
    } else {

        // Simpan data menggunakan prepared statement
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO kategori (nama_kategori, keterangan)
             VALUES (?, ?)"
        );
        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "ss",
                $nama_kategori,
                $keterangan
            );
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: index.php?pesan=tambah");
                exit;
            } else {
                $error = "Data kategori gagal disimpan: "
                    . mysqli_error($conn);
                mysqli_stmt_close($stmt);
            }
        } else {
            $error = "Query gagal disiapkan: " . mysqli_error($conn);
        }
    }
}
?>
<?php include "../layout/header.php"; ?>
<link
    rel="stylesheet"
    href="../assets/css/data-master.css?v=3"
>
<?php include "../layout/navbar.php"; ?>
<div class="main-content data-master-page">
    <div class="container-fluid">
        <!-- HEADER HALAMAN -->
        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-tags"></i>
            </div>
            <div>
                <h2>Tambah Kategori</h2>
                <p>Tambahkan kategori barang baru ke dalam sistem.</p>
            </div>
        </div>
        <!-- FORM TAMBAH KATEGORI -->
        <div class="data-form-card">
            <!-- HEADER FORM -->
            <div class="data-form-header">
                <h3>
                    <i class="bi bi-plus-circle me-2"></i>
                    Form Tambah Kategori
                </h3>
                <p>Lengkapi informasi kategori di bawah ini.</p>
            </div>
            <!-- ISI FORM -->
            <div class="data-form-body">
                <!-- PESAN ERROR -->
                <?php if ($error !== ""): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <?= htmlspecialchars(
                            $error,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="">
                    <!-- NAMA KATEGORI -->
                    <div class="mb-4">
                        <label
                            for="nama_kategori"
                            class="data-form-label">
                            Nama Kategori
                            <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            id="nama_kategori"
                            name="nama_kategori"
                            class="data-form-control"
                            placeholder="Contoh: Kamera Aksi"
                            value="<?= htmlspecialchars(
                                $nama_kategori,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            maxlength="100"
                            required>
                        <small class="text-muted">
                            Masukkan nama kategori barang.
                        </small>
                    </div>
                    <!-- KETERANGAN -->
                    <div class="mb-4">
                        <label
                            for="keterangan"
                            class="data-form-label">
                            Keterangan
                        </label>
                        <textarea
                            id="keterangan"
                            name="keterangan"
                            class="data-form-control"
                            rows="4"
                            placeholder="Masukkan keterangan kategori (opsional)"
                        ><?= htmlspecialchars(
                            $keterangan,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?></textarea>
                        <small class="text-muted">
                            Berikan penjelasan singkat mengenai kategori.
                        </small>
                    </div>
                    <!-- TOMBOL -->
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button
                            type="submit"
                            name="simpan"
                            class="btn-form-save">
                            <i class="bi bi-save"></i>
                            Simpan
                        </button>
                        <a
                            href="index.php"
                            class="btn-form-cancel">
                            <i class="bi bi-arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php"; ?>