<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */
$nama_merk = "";
$error = "";
if (isset($_POST['simpan'])) {
    $nama_merk = trim($_POST['nama_merk'] ?? '');
    // Validasi nama merk
    if ($nama_merk === "") {
        $error = "Nama merk wajib diisi.";
    } elseif (strlen($nama_merk) > 100) {
        $error = "Nama merk maksimal 100 karakter.";
    } else {
        // Cek apakah nama merk sudah terdaftar
        $stmt_cek = mysqli_prepare(
            $conn,
            "SELECT id_merk FROM merk
             WHERE LOWER(nama_merk) = LOWER(?)"
        );
        mysqli_stmt_bind_param($stmt_cek, "s", $nama_merk);
        mysqli_stmt_execute($stmt_cek);
        $hasil_cek = mysqli_stmt_get_result($stmt_cek);
        $duplikat = mysqli_num_rows($hasil_cek) > 0;
        mysqli_stmt_close($stmt_cek);
        if ($duplikat) {
            $error = "Nama merk tersebut sudah terdaftar.";
        } else {
            // Simpan data merk
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO merk (nama_merk) VALUES (?)"
            );
            mysqli_stmt_bind_param($stmt, "s", $nama_merk);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: index.php?pesan=tambah");
                exit;
            } else {
                $error = "Data merk gagal ditambahkan.";
                mysqli_stmt_close($stmt);
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
                <h2>Tambah Merk</h2>
                <p>Tambahkan merk kamera atau tripod ke dalam sistem.</p>
            </div>
        </div>
        <!-- Form Tambah -->
        <div class="data-form-card">
            <div class="data-form-header">
                <div>
                    <h5 class="mb-1">Form Tambah Merk</h5>
                    <p class="mb-0">
                        Lengkapi informasi merk yang ingin ditambahkan.
                    </p>
                </div>
            </div>
            <div class="data-form-body">
                <!-- Pesan Error -->
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
                <form method="POST">
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
                            Contoh: Canon, Nikon, Sony, Fujifilm, atau Weifeng.
                        </small>
                    </div>
                    <!-- Tombol -->
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button
                            type="submit"
                            name="simpan"
                            class="btn-form-save">
                            <i class="bi bi-check-circle me-1"></i>
                            Simpan Merk
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