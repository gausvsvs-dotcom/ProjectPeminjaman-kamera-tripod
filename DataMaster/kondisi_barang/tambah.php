<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

$error = "";
$nama_kondisi = "";
$keterangan = "";

// Proses tambah kondisi
if (isset($_POST['simpan'])) {

    $nama_kondisi = trim($_POST['nama_kondisi'] ?? "");
    $keterangan = trim($_POST['keterangan'] ?? "");

    // Validasi nama kondisi
    if ($nama_kondisi === "") {
        $error = "Nama kondisi wajib diisi.";
    }

    // Validasi panjang karakter
    elseif (strlen($nama_kondisi) > 50) {
        $error = "Nama kondisi maksimal 50 karakter.";
    }

    // Validasi keterangan
    elseif ($keterangan === "") {
        $error = "Keterangan wajib diisi.";
    }

    // Validasi duplikat
    else {
        $stmt = mysqli_prepare(
            $conn,
            "SELECT id_kondisi
             FROM kondisi_barang
             WHERE LOWER(nama_kondisi) = LOWER(?)"
        );

        mysqli_stmt_bind_param($stmt, "s", $nama_kondisi);
        mysqli_stmt_execute($stmt);

        $hasil = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($hasil) > 0) {
            $error = "Nama kondisi tersebut sudah terdaftar.";
        }

        mysqli_stmt_close($stmt);
    }

    // Simpan jika tidak ada error
    if ($error === "") {

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO kondisi_barang (nama_kondisi, keterangan)
             VALUES (?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ss",
            $nama_kondisi,
            $keterangan
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);

            header("Location: index.php?pesan=tambah");
            exit;

        } else {
            $error = "Data kondisi gagal ditambahkan: " . mysqli_error($conn);
            mysqli_stmt_close($stmt);
        }
    }
}
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
                <h2>Tambah Kondisi Barang</h2>
                <p>
                    Tambahkan kondisi untuk kamera dan tripod
                    pada sistem peminjaman.
                </p>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="data-form-card">

            <!-- HEADER FORM -->
            <div class="data-form-header">
                <h3>
                    <i class="bi bi-plus-circle me-2"></i>
                    Form Tambah Kondisi
                </h3>

                <p>
                    Lengkapi informasi kondisi barang yang ingin ditambahkan.
                </p>
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

                <form method="POST">

                    <!-- NAMA KONDISI -->
                    <div class="mb-4">
                        <label
                            for="nama_kondisi"
                            class="data-form-label">
                            Nama Kondisi
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="nama_kondisi"
                            name="nama_kondisi"
                            class="data-form-control"
                            placeholder="Contoh: Baik"
                            maxlength="50"
                            value="<?= htmlspecialchars(
                                $nama_kondisi,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            required>

                        <small class="text-muted">
                            Contoh kondisi: Baik, Perawatan, atau Rusak.
                        </small>
                    </div>

                    <!-- KETERANGAN -->
                    <div class="mb-4">
                        <label
                            for="keterangan"
                            class="data-form-label">
                            Keterangan
                            <span class="text-danger">*</span>
                        </label>

                        <textarea
                            id="keterangan"
                            name="keterangan"
                            class="data-form-control"
                            placeholder="Masukkan keterangan kondisi barang"
                            rows="3"
                            required><?= htmlspecialchars(
                                $keterangan,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?></textarea>
                    </div>

                    <!-- BUTTON -->
                    <div class="d-flex gap-2 flex-wrap">

                        <button
                            type="submit"
                            name="simpan"
                            class="btn-form-save">
                            <i class="bi bi-check-circle me-1"></i>
                            Simpan
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