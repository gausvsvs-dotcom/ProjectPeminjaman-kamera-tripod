<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

$error = "";

// Ambil ID kategori
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}

// Ambil data kategori
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM kategori WHERE id_kategori = ?"
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

// Nilai awal form
$nama_kategori = $data['nama_kategori'] ?? '';
$keterangan = $data['keterangan'] ?? '';

// Proses update
if (isset($_POST['update'])) {

    $nama_kategori = trim($_POST['nama_kategori'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    if ($nama_kategori === '') {
        $error = "Nama kategori wajib diisi!";
    } else {

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE kategori
             SET nama_kategori = ?, keterangan = ?
             WHERE id_kategori = ?"
        );

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "ssi",
                $nama_kategori,
                $keterangan,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);

                header("Location: index.php?pesan=ubah");
                exit;
            } else {
                $error = "Data kategori gagal diperbarui: "
                    . mysqli_error($conn);

                mysqli_stmt_close($stmt);
            }
        } else {
            $error = "Query gagal disiapkan: "
                . mysqli_error($conn);
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
                <h2>Edit Kategori</h2>
                <p>Perbarui informasi kategori barang yang tersimpan.</p>
            </div>
        </div>

        <!-- CARD FORM EDIT -->
        <div class="data-form-card">

            <div class="data-form-header">
                <h3>
                    <i class="bi bi-pencil-square me-2"></i>
                    Form Edit Kategori
                </h3>

                <p>
                    Ubah informasi kategori sesuai kebutuhan.
                </p>
            </div>

            <div class="data-form-body">

                <!-- PESAN ERROR -->
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

                <form method="POST" action="">

                    <!-- ID KATEGORI -->
                    <div class="mb-4">
                        <label class="data-form-label">
                            ID Kategori
                        </label>

                        <input
                            type="text"
                            class="data-form-control"
                            value="#<?= (int) $id; ?>"
                            readonly
                        >
                    </div>

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
                            placeholder="Masukkan nama kategori"
                            value="<?= htmlspecialchars(
                                $nama_kategori,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            maxlength="100"
                            required>
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
                            placeholder="Masukkan keterangan kategori">
                            <?= htmlspecialchars(
                            $keterangan,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?></textarea>
                        <small class="text-muted">
                            Keterangan boleh dikosongkan.
                        </small>
                    </div>
                    <!-- TOMBOL -->
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button
                            type="submit"
                            name="update"
                            class="btn-form-save">
                            <i class="bi bi-save"></i>
                            Simpan Perubahan
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