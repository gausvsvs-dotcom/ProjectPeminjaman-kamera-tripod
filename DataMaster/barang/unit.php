
<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

$error = "";
$success = "";

// VALIDASI ID BARANG
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header("Location: index.php");
    exit;
}

// AMBIL DATA BARANG
$stmt = mysqli_prepare($conn, "
    SELECT id_barang, kode_barang, nama_barang, jenis_barang
    FROM barang
    WHERE id_barang = ?
");

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$data_barang = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$data_barang) {
    die("Data barang tidak ditemukan.");
}

// AMBIL DAFTAR KONDISI
$query_kondisi = mysqli_query($conn, "
    SELECT id_kondisi, nama_kondisi
    FROM kondisi_barang
    ORDER BY id_kondisi ASC
");

$daftar_kondisi = [];

while ($kondisi = mysqli_fetch_assoc($query_kondisi)) {
    $daftar_kondisi[] = $kondisi;
}

// PROSES UPDATE UNIT
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_unit"])) {

    $id_unit = filter_input(INPUT_POST, "id_unit", FILTER_VALIDATE_INT);
    $id_kondisi = filter_input(INPUT_POST, "id_kondisi", FILTER_VALIDATE_INT);
    $status_unit = $_POST["status_unit"] ?? "";
    $keterangan = trim($_POST["keterangan_kondisi"] ?? "");

    $status_valid = [
        "Tersedia",
        "Dipinjam",
        "Tidak tersedia"
    ];

    if (!$id_unit || !$id_kondisi || !in_array($status_unit, $status_valid, true)) {
        $error = "Data unit tidak valid.";
    } else {

        // PASTIKAN UNIT MILIK BARANG YANG SEDANG DIBUKA
        $stmt_cek = mysqli_prepare($conn, "
            SELECT id_unit, status_unit
            FROM barang_unit
            WHERE id_unit = ? AND id_barang = ?
        ");

        mysqli_stmt_bind_param($stmt_cek, "ii", $id_unit, $id);
        mysqli_stmt_execute($stmt_cek);

        $unit_lama = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_cek));
        mysqli_stmt_close($stmt_cek);

        // PASTIKAN KONDISI VALID
        $stmt_cek_kondisi = mysqli_prepare($conn, "
            SELECT id_kondisi, nama_kondisi
            FROM kondisi_barang
            WHERE id_kondisi = ?
        ");

        mysqli_stmt_bind_param($stmt_cek_kondisi, "i", $id_kondisi);
        mysqli_stmt_execute($stmt_cek_kondisi);

        $kondisi_dipilih = mysqli_fetch_assoc(
            mysqli_stmt_get_result($stmt_cek_kondisi)
        );

        mysqli_stmt_close($stmt_cek_kondisi);

        if (!$unit_lama || !$kondisi_dipilih) {
            $error = "Unit atau kondisi barang tidak ditemukan.";

        } elseif (
            $unit_lama["status_unit"] === "Dipinjam" &&
            (
                $status_unit !== "Dipinjam" ||
                $id_kondisi != $id_kondisi
            )
        ) {
            $error = "Unit yang sedang dipinjam tidak boleh diubah sembarangan.";

        } else {

            // UNIT RUSAK TIDAK BOLEH BERSTATUS TERSEDIA
            if (
                stripos($kondisi_dipilih["nama_kondisi"], "rusak") !== false &&
                $status_unit === "Tersedia"
            ) {
                $error = "Unit dengan kondisi rusak tidak dapat berstatus tersedia.";
            } else {

                $stmt_update = mysqli_prepare($conn, "
                    UPDATE barang_unit
                    SET
                        id_kondisi = ?,
                        status_unit = ?,
                        keterangan_kondisi = ?
                    WHERE id_unit = ?
                    AND id_barang = ?
                ");

                mysqli_stmt_bind_param(
                    $stmt_update,
                    "issii",
                    $id_kondisi,
                    $status_unit,
                    $keterangan,
                    $id_unit,
                    $id
                );

                if (mysqli_stmt_execute($stmt_update)) {
                    mysqli_stmt_close($stmt_update);

                    header("Location: unit.php?id=" . $id . "&success=1");
                    exit;
                } else {
                    $error = "Gagal memperbarui unit: " . mysqli_stmt_error($stmt_update);
                    mysqli_stmt_close($stmt_update);
                }
            }
        }
    }
}

// PESAN BERHASIL
if (isset($_GET["success"])) {
    $success = "Data unit berhasil diperbarui.";
}

// AMBIL SEMUA UNIT
$stmt_unit = mysqli_prepare($conn, "
    SELECT
        bu.*,
        kb.nama_kondisi
    FROM barang_unit bu
    LEFT JOIN kondisi_barang kb
        ON bu.id_kondisi = kb.id_kondisi
    WHERE bu.id_barang = ?
    ORDER BY bu.id_unit ASC
");

mysqli_stmt_bind_param($stmt_unit, "i", $id);
mysqli_stmt_execute($stmt_unit);

$query_unit = mysqli_stmt_get_result($stmt_unit);

?>

<?php include "../layout/header.php"; ?>

<link rel="stylesheet" href="../assets/css/data-master.css?v=3">

<?php include "../layout/navbar.php"; ?>

<div class="main-content data-master-page">
    <div class="container-fluid">

        <!-- HEADER -->
        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-boxes"></i>
            </div>

            <div>
                <h2>Kelola Unit Barang</h2>
                <p>Atur kondisi dan status setiap unit barang.</p>
            </div>
        </div>

        <!-- INFORMASI BARANG -->
        <div class="data-detail-card mb-4">

            <div class="data-detail-header">
                <h3>
                    <i class="bi bi-info-circle me-2"></i>
                    Informasi Barang
                </h3>

                <p>Barang yang sedang dikelola.</p>
            </div>

            <div class="data-detail-body">

                <div class="detail-item">
                    <div class="detail-label">Kode Barang</div>
                    <div class="detail-value">
                        <?= htmlspecialchars($data_barang["kode_barang"]); ?>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Nama Barang</div>
                    <div class="detail-value">
                        <?= htmlspecialchars($data_barang["nama_barang"]); ?>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Jenis Barang</div>
                    <div class="detail-value">
                        <?= htmlspecialchars(ucfirst($data_barang["jenis_barang"])); ?>
                    </div>
                </div>

            </div>
        </div>

        <!-- PESAN -->
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle me-2"></i>
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i>
                <?= htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <!-- DAFTAR UNIT -->
        <div class="data-master-card">

            <div class="data-card-header">
                <div>
                    <h3 class="data-card-title">
                        <i class="bi bi-boxes me-2"></i>
                        Daftar Unit Barang
                    </h3>

                    <p>
                        Perbarui kondisi dan status masing-masing unit.
                    </p>
                </div>
            </div>

            <div class="data-table-wrapper">
                <div class="table-responsive">

                    <table class="data-master-table">

                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Kode Unit</th>
                                <th>Kondisi</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (mysqli_num_rows($query_unit) > 0): ?>

                                <?php $no = 1; ?>

                                <?php while ($unit = mysqli_fetch_assoc($query_unit)): ?>

                                    <tr>
                                        <td><?= $no++; ?></td>

                                        <td>
                                            <span class="data-id">
                                                <?= htmlspecialchars($unit["kode_unit"]); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($unit["nama_kondisi"] ?? "-"); ?>
                                        </td>

                                        <td>
                                            <?php
                                            $nama_kondisi = strtolower($unit["nama_kondisi"] ?? "");
                                            $is_rusak = strpos($nama_kondisi, "rusak") !== false;
                                            ?>

                                            <?php if ($is_rusak): ?>

                                                <span class="badge bg-danger">
                                                    Tidak dapat dipinjam
                                                </span>

                                            <?php elseif ($unit["status_unit"] === "Tersedia"): ?>

                                                <span class="badge bg-success">
                                                    Siap dipinjam
                                                </span>

                                            <?php elseif ($unit["status_unit"] === "Dipinjam"): ?>

                                                <span class="badge bg-primary">
                                                    Dipinjam
                                                </span>

                                            <?php else: ?>

                                                <span class="badge bg-secondary">
                                                    <?= htmlspecialchars($unit["status_unit"]); ?>
                                                </span>

                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?= !empty($unit["keterangan_kondisi"])
                                                ? htmlspecialchars($unit["keterangan_kondisi"])
                                                : '<span class="text-muted">-</span>'; ?>
                                        </td>

                                        <td>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editUnit<?= (int) $unit["id_unit"]; ?>">

                                                <i class="bi bi-pencil-square"></i>
                                                Edit
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- MODAL EDIT UNIT -->
                                    <div
                                        class="modal fade"
                                        id="editUnit<?= (int) $unit["id_unit"]; ?>"
                                        tabindex="-1"
                                        aria-hidden="true">

                                        <div class="modal-dialog modal-dialog-centered">

                                            <div class="modal-content">

                                                <form method="POST">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            Edit Unit <?= htmlspecialchars($unit["kode_unit"]); ?>
                                                        </h5>

                                                        <button
                                                            type="button"
                                                            class="btn-close"
                                                            data-bs-dismiss="modal">
                                                        </button>
                                                    </div>

                                                    <div class="modal-body">

                                                        <input
                                                            type="hidden"
                                                            name="id_unit"
                                                            value="<?= (int) $unit["id_unit"]; ?>">

                                                        <div class="mb-3">
                                                            <label class="form-label">
                                                                Kondisi Unit
                                                            </label>

                                                            <select
                                                                name="id_kondisi"
                                                                class="form-select"
                                                                required>

                                                                <?php foreach ($daftar_kondisi as $kondisi): ?>

                                                                    <option
                                                                        value="<?= (int) $kondisi["id_kondisi"]; ?>"
                                                                        <?= $unit["id_kondisi"] == $kondisi["id_kondisi"] ? "selected" : ""; ?>>

                                                                        <?= htmlspecialchars($kondisi["nama_kondisi"]); ?>

                                                                    </option>

                                                                <?php endforeach; ?>

                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">
                                                                Status Unit
                                                            </label>

                                                            <select
                                                                name="status_unit"
                                                                class="form-select"
                                                                required>

                                                                <?php
                                                                $status_options = [
                                                                    "Tersedia",
                                                                    "Dipinjam",
                                                                    "Tidak tersedia"
                                                                ];
                                                                ?>

                                                                <?php foreach ($status_options as $status): ?>

                                                                    <option
                                                                        value="<?= $status; ?>"
                                                                        <?= $unit["status_unit"] === $status ? "selected" : ""; ?>>

                                                                        <?= $status; ?>

                                                                    </option>

                                                                <?php endforeach; ?>

                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">
                                                                Keterangan Kondisi
                                                            </label>

                                                            <textarea
                                                                name="keterangan_kondisi"
                                                                class="form-control"
                                                                rows="3"
                                                                placeholder="Contoh: Terdapat goresan kecil pada bagian samping"><?= htmlspecialchars($unit["keterangan_kondisi"] ?? ""); ?></textarea>
                                                        </div>

                                                        <?php if ($unit["status_unit"] === "Dipinjam"): ?>
                                                            <div class="alert alert-warning mb-0">
                                                                Unit ini sedang dipinjam. Perubahan kondisi dan status dibatasi agar tidak mengganggu transaksi.
                                                            </div>
                                                        <?php endif; ?>

                                                    </div>

                                                    <div class="modal-footer">

                                                        <button
                                                            type="button"
                                                            class="btn btn-secondary"
                                                            data-bs-dismiss="modal">
                                                            Batal
                                                        </button>

                                                        <button
                                                            type="submit"
                                                            name="update_unit"
                                                            class="btn btn-primary">
                                                            Simpan Perubahan
                                                        </button>

                                                    </div>

                                                </form>

                                            </div>
                                        </div>
                                    </div>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="6" class="data-empty">
                                        <i class="bi bi-inbox"></i>
                                        <p>Belum ada unit barang.</p>
                                        <span>Tambahkan unit melalui proses tambah barang.</span>
                                    </td>
                                </tr>

                            <?php endif; ?>

                        </tbody>
                    </table>

                </div>
            </div>

            <div class="data-table-footer">
                Total unit:
                <strong><?= mysqli_num_rows($query_unit); ?> unit</strong>
            </div>

        </div>

        <!-- TOMBOL KEMBALI -->
        <div class="mt-4 mb-4">

            <a
                href="detail.php?id=<?= (int) $id; ?>"
                class="btn-detail-back">

                <i class="bi bi-arrow-left me-1"></i>
                Kembali ke Detail Barang

            </a>

        </div>

    </div>
</div>

<?php include "../layout/footer.php"; ?>
