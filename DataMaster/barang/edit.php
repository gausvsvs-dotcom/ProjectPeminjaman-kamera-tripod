
<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

$error = "";

// Lokasi folder upload di direktori utama project
$folder_upload = dirname(__DIR__, 2) . "/uploads/barang/";

// VALIDASI ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header("Location: index.php");
    exit;
}

// AMBIL DATA BARANG
$stmt = mysqli_prepare($conn, "
    SELECT * FROM barang
    WHERE id_barang = ?
");

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data) {
    die("Data barang tidak ditemukan.");
}
$data_awal = $data;
// AMBIL DATA MASTER
$kategori = mysqli_query($conn, "SELECT * FROM kategori");
$merk = mysqli_query($conn, "SELECT * FROM merk");

$hasil_kondisi = mysqli_query($conn, "
    SELECT * FROM kondisi_barang
    ORDER BY id_kondisi ASC
");

if (!$kategori || !$merk || !$hasil_kondisi) {
    die("Gagal mengambil data master: " . mysqli_error($conn));
}

// SIMPAN KONDISI KE ARRAY
$daftar_kondisi = [];

while ($item = mysqli_fetch_assoc($hasil_kondisi)) {
    $daftar_kondisi[] = $item;
}

// KONDISI DEFAULT UNIT BARU
$id_kondisi_default = (int) ($data['id_kondisi'] ?? 0);

if (!empty($daftar_kondisi)) {
    $id_valid = array_column($daftar_kondisi, 'id_kondisi');

    if (!in_array($id_kondisi_default, array_map('intval', $id_valid), true)) {
        $id_kondisi_default = (int) $daftar_kondisi[0]['id_kondisi'];
    }
}

// PROSES UPDATE
if (isset($_POST['update'])) {

    $foto_lama = $data['foto'];
    $stok_sebelumnya = (int) $data['stok'];

    // AMBIL INPUT
    $nama_barang = trim($_POST['nama_barang'] ?? "");
    $jenis_barang = $_POST['jenis_barang'] ?? "";
    $kode_barang = $data['kode_barang'];

    $id_kategori = filter_input(INPUT_POST, 'id_kategori', FILTER_VALIDATE_INT);
    $id_merk = filter_input(INPUT_POST, 'id_merk', FILTER_VALIDATE_INT);

    $id_kondisi_unit_baru = filter_input(
        INPUT_POST,
        'id_kondisi_unit_baru',
        FILTER_VALIDATE_INT
    );

    $stok = filter_input(INPUT_POST, 'stok', FILTER_VALIDATE_INT);
    $harga_sewa = filter_input(INPUT_POST, 'harga_sewa', FILTER_VALIDATE_FLOAT);
    $deskripsi = trim($_POST['deskripsi'] ?? "");

    $foto = $foto_lama;
    $foto_baru = "";

    // PERTAHANKAN INPUT JIKA VALIDASI GAGAL
    $data['kode_barang'] = $kode_barang;
    $data['nama_barang'] = $nama_barang;
    $data['jenis_barang'] = $jenis_barang;
    $data['id_kategori'] = $id_kategori;
    $data['id_merk'] = $id_merk;
    $data['stok'] = $stok;
    $data['harga_sewa'] = $harga_sewa;
    $data['deskripsi'] = $deskripsi;

    // VALIDASI
    if (
        $nama_barang === "" ||
        !in_array($jenis_barang, ['kamera', 'tripod'], true) ||
        !$id_kategori ||
        !$id_merk ||
        $stok === false ||
        $stok === null ||
        $stok < 0 ||
        $harga_sewa === false ||
        $harga_sewa === null ||
        $harga_sewa <= 0
    ) {
        $error = "Periksa kembali data yang diisi. Pastikan semua kolom wajib diisi dengan benar.";
    }
    // CEK APAKAH ADA PERUBAHAN DATA
    if ($error === "") {

        $ada_perubahan =
            $nama_barang !== $data_awal['nama_barang'] ||
            $jenis_barang !== $data_awal['jenis_barang'] ||
            (int) $id_kategori !== (int) $data_awal['id_kategori'] ||
            (int) $id_merk !== (int) $data_awal['id_merk'] ||
            (int) $stok !== (int) $data_awal['stok'] ||
            (float) $harga_sewa !== (float) $data_awal['harga_sewa'] ||
            $deskripsi !== (string) ($data_awal['deskripsi'] ?? '') ||
            !empty($_FILES['foto']['name']);

        if (!$ada_perubahan) {
            $error = "Tidak ada perubahan data. Silakan ubah minimal satu informasi sebelum menyimpan.";
        }
    }

    // VALIDASI KONDISI JIKA STOK BERTAMBAH
    if ($error === "" && $stok > $stok_sebelumnya) {

        $kondisi_ditemukan = false;

        foreach ($daftar_kondisi as $item) {
            if ((int) $item['id_kondisi'] === (int) $id_kondisi_unit_baru) {
                $kondisi_ditemukan = true;
                break;
            }
        }

        if (!$kondisi_ditemukan) {
            $error = "Pilih kondisi untuk unit baru yang akan ditambahkan.";
        }
    }

    // UPLOAD FOTO BARU
    if ($error === "" && !empty($_FILES['foto']['name'])) {

        if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            $error = "Foto gagal diunggah. Silakan coba kembali.";

        } else {

            $tmp_foto = $_FILES['foto']['tmp_name'];
            $ukuran_foto = $_FILES['foto']['size'];

            $ekstensi = strtolower(
                pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION)
            );

            $ekstensi_diizinkan = ['jpg', 'jpeg', 'png', 'webp'];
            $info_foto = getimagesize($tmp_foto);

            if (
                $info_foto === false ||
                !in_array($ekstensi, $ekstensi_diizinkan, true)
            ) {
                $error = "Format foto harus JPG, JPEG, PNG, atau WEBP.";

            } elseif ($ukuran_foto > 5 * 1024 * 1024) {
                $error = "Ukuran foto maksimal 5 MB.";

            } else {

                // BUAT FOLDER JIKA BELUM ADA
                if (
                    !is_dir($folder_upload) &&
                    !mkdir($folder_upload, 0755, true)
                ) {
                    $error = "Folder upload foto gagal dibuat.";

                } elseif (!is_writable($folder_upload)) {
                    $error = "Folder upload tidak memiliki izin penulisan.";

                } else {

                    $nama_file = bin2hex(random_bytes(16)) . "." . $ekstensi;
                    $lokasi_foto = $folder_upload . $nama_file;

                    if (move_uploaded_file($tmp_foto, $lokasi_foto)) {
                        $foto = $nama_file;
                        $foto_baru = $lokasi_foto;
                    } else {
                        $error = "Foto gagal disimpan.";
                    }
                }
            }
        }
    }

    // SIMPAN BARANG DAN SESUAIKAN UNIT
    if ($error === "") {

        mysqli_begin_transaction($conn);

        try {

            // KUNCI DATA BARANG
            $stmt_lama = mysqli_prepare($conn, "
                SELECT stok, kode_barang
                FROM barang
                WHERE id_barang = ?
                FOR UPDATE
            ");

            if (!$stmt_lama) {
                throw new Exception(mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt_lama, "i", $id);
            mysqli_stmt_execute($stmt_lama);

            $hasil_lama = mysqli_stmt_get_result($stmt_lama);
            $barang_lama = mysqli_fetch_assoc($hasil_lama);

            mysqli_stmt_close($stmt_lama);

            if (!$barang_lama) {
                throw new Exception("Data barang tidak ditemukan.");
            }

            $stok_lama = (int) $barang_lama['stok'];
            $kode_lama = $barang_lama['kode_barang'];
            // GENERATE KODE BARANG BERDASARKAN JENIS
            if ($jenis_barang === 'kamera') {
                $prefix = 'CAM';
            } else {
                $prefix = 'TRI';
            }

            if (substr($kode_lama, 0, 3) !== $prefix) {

                // AMBIL NOMOR URUT DARI KODE LAMA
                preg_match('/(\d+)$/', $kode_lama, $match);

                $nomor_kode = isset($match[1])
                    ? (int) $match[1]
                    : (int) $id;

                $kode_barang = $prefix . str_pad(
                    $nomor_kode,
                    3,
                    '0',
                    STR_PAD_LEFT
                );

            }

            // HITUNG PERUBAHAN STOK
            $selisih = (int) $stok - $stok_lama;

            // UPDATE DATA BARANG
            $stmt_update = mysqli_prepare($conn, "
                UPDATE barang SET
                    kode_barang = ?,
                    nama_barang = ?,
                    jenis_barang = ?,
                    id_kategori = ?,
                    id_merk = ?,
                    stok = ?,
                    harga_sewa = ?,
                    foto = ?,
                    deskripsi = ?
                WHERE id_barang = ?
            ");

            if (!$stmt_update) {
                throw new Exception(mysqli_error($conn));
            }

            mysqli_stmt_bind_param(
                $stmt_update,
                "sssiiidssi",
                $kode_barang,
                $nama_barang,
                $jenis_barang,
                $id_kategori,
                $id_merk,
                $stok,
                $harga_sewa,
                $foto,
                $deskripsi,
                $id
            );

            if (!mysqli_stmt_execute($stmt_update)) {
                throw new Exception(mysqli_stmt_error($stmt_update));
            }

            mysqli_stmt_close($stmt_update);

            // JIKA KODE BARANG BERUBAH, SESUAIKAN KODE UNIT
            if ($kode_lama !== $kode_barang) {

                $stmt_daftar_unit = mysqli_prepare($conn, "
                    SELECT id_unit, kode_unit
                    FROM barang_unit
                    WHERE id_barang = ?
                ");

                if (!$stmt_daftar_unit) {
                    throw new Exception(mysqli_error($conn));
                }

                mysqli_stmt_bind_param($stmt_daftar_unit, "i", $id);
                mysqli_stmt_execute($stmt_daftar_unit);

                $hasil_daftar_unit = mysqli_stmt_get_result($stmt_daftar_unit);
                $daftar_unit = [];

                while ($unit = mysqli_fetch_assoc($hasil_daftar_unit)) {
                    $daftar_unit[] = $unit;
                }

                mysqli_stmt_close($stmt_daftar_unit);

                $stmt_update_kode = mysqli_prepare($conn, "
                    UPDATE barang_unit
                    SET kode_unit = ?
                    WHERE id_unit = ? AND id_barang = ?
                ");

                if (!$stmt_update_kode) {
                    throw new Exception(mysqli_error($conn));
                }

                foreach ($daftar_unit as $unit) {

                    if (preg_match('/-(\d+)$/', $unit['kode_unit'], $match)) {
                        $nomor_unit = (int) $match[1];
                    } else {
                        $nomor_unit = (int) $unit['id_unit'];
                    }

                    $kode_unit_baru = $kode_barang . "-" .
                        str_pad($nomor_unit, 3, "0", STR_PAD_LEFT);

                    mysqli_stmt_bind_param(
                        $stmt_update_kode,
                        "sii",
                        $kode_unit_baru,
                        $unit['id_unit'],
                        $id
                    );

                    if (!mysqli_stmt_execute($stmt_update_kode)) {
                        throw new Exception(mysqli_stmt_error($stmt_update_kode));
                    }
                }

                mysqli_stmt_close($stmt_update_kode);
            }

            // JIKA STOK BERTAMBAH, BUAT UNIT BARU
            if ($selisih > 0) {

                $stmt_max = mysqli_prepare($conn, "
                    SELECT COALESCE(
                        MAX(CAST(SUBSTRING_INDEX(kode_unit, '-', -1) AS UNSIGNED)),
                        0
                    ) AS nomor_terakhir
                    FROM barang_unit
                    WHERE id_barang = ?
                ");

                if (!$stmt_max) {
                    throw new Exception(mysqli_error($conn));
                }

                mysqli_stmt_bind_param($stmt_max, "i", $id);
                mysqli_stmt_execute($stmt_max);

                $hasil_max = mysqli_stmt_get_result($stmt_max);
                $nomor = (int) mysqli_fetch_assoc($hasil_max)['nomor_terakhir'];

                mysqli_stmt_close($stmt_max);

                $stmt_unit = mysqli_prepare($conn, "
                    INSERT INTO barang_unit
                    (id_barang, kode_unit, id_kondisi, status_unit)
                    VALUES (?, ?, ?, 'Tersedia')
                ");

                if (!$stmt_unit) {
                    throw new Exception(mysqli_error($conn));
                }

                for ($i = 1; $i <= $selisih; $i++) {

                    $nomor++;

                    $kode_unit = $kode_barang . "-" .
                        str_pad($nomor, 3, "0", STR_PAD_LEFT);

                    mysqli_stmt_bind_param(
                        $stmt_unit,
                        "isi",
                        $id,
                        $kode_unit,
                        $id_kondisi_unit_baru
                    );

                    if (!mysqli_stmt_execute($stmt_unit)) {
                        throw new Exception(mysqli_stmt_error($stmt_unit));
                    }
                }

                mysqli_stmt_close($stmt_unit);
            }

            // JIKA STOK BERKURANG, HAPUS UNIT TERSEDIA
            elseif ($selisih < 0) {

                $jumlah_hapus = abs($selisih);

                $stmt_cek = mysqli_prepare($conn, "
                    SELECT id_unit
                    FROM barang_unit
                    WHERE id_barang = ?
                    AND status_unit = 'Tersedia'
                    ORDER BY id_unit DESC
                    LIMIT ?
                ");

                if (!$stmt_cek) {
                    throw new Exception(mysqli_error($conn));
                }

                mysqli_stmt_bind_param(
                    $stmt_cek,
                    "ii",
                    $id,
                    $jumlah_hapus
                );

                mysqli_stmt_execute($stmt_cek);
                $hasil_unit = mysqli_stmt_get_result($stmt_cek);

                $unit_dihapus = [];

                while ($unit = mysqli_fetch_assoc($hasil_unit)) {
                    $unit_dihapus[] = (int) $unit['id_unit'];
                }

                mysqli_stmt_close($stmt_cek);

                if (count($unit_dihapus) < $jumlah_hapus) {
                    throw new Exception(
                        "Stok tidak dapat dikurangi karena jumlah unit tersedia tidak mencukupi. Unit yang sedang dipinjam tidak dapat dihapus."
                    );
                }

                $stmt_delete = mysqli_prepare($conn, "
                    DELETE FROM barang_unit
                    WHERE id_unit = ?
                    AND id_barang = ?
                    AND status_unit = 'Tersedia'
                ");

                if (!$stmt_delete) {
                    throw new Exception(mysqli_error($conn));
                }

                foreach ($unit_dihapus as $id_unit) {

                    mysqli_stmt_bind_param(
                        $stmt_delete,
                        "ii",
                        $id_unit,
                        $id
                    );

                    if (!mysqli_stmt_execute($stmt_delete)) {
                        throw new Exception(mysqli_stmt_error($stmt_delete));
                    }

                    if (mysqli_stmt_affected_rows($stmt_delete) !== 1) {
                        throw new Exception(
                            "Unit gagal dihapus. Silakan periksa kembali data unit."
                        );
                    }
                }

                mysqli_stmt_close($stmt_delete);
            }

            // SEMUA PROSES BERHASIL
            mysqli_commit($conn);

            // HAPUS FOTO LAMA SETELAH UPDATE BERHASIL
            if ($foto_baru !== "" && !empty($foto_lama)) {

                $lokasi_foto_lama = $folder_upload . basename($foto_lama);

                if (is_file($lokasi_foto_lama)) {
                    unlink($lokasi_foto_lama);
                }
            }

            header("Location: index.php?pesan=ubah");
            exit;

        } catch (Throwable $e) {

            mysqli_rollback($conn);

            // HAPUS FOTO BARU JIKA PROSES DATABASE GAGAL
            if ($foto_baru !== "" && is_file($foto_baru)) {
                unlink($foto_baru);
            }

            $error = $e->getMessage();
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
                <i class="bi bi-camera"></i>
            </div>

            <div>
                <h2>Edit Barang</h2>
                <p>Perbarui informasi barang kamera dan tripod.</p>
            </div>
        </div>

        <!-- FORM EDIT -->
        <div class="data-form-card">

            <div class="data-form-header">
                <h3>
                    <i class="bi bi-pencil-square"></i>
                    Form Edit Barang
                </h3>
                <p>Ubah informasi barang sesuai data terbaru.</p>
            </div>

            <div class="data-form-body">

                <?php if ($error !== "") { ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php } ?>

                <form method="POST" enctype="multipart/form-data">

                    <!-- KODE BARANG -->
                    <div class="mb-3">
                        <label class="data-form-label">Kode Barang</label>

                        <input
                            type="text"
                            class="data-form-control"
                            value="<?= htmlspecialchars($data['kode_barang'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            readonly>

                        <small class="text-muted">
                            Kode barang akan menyesuaikan jenis barang secara otomatis.
                        </small>
                    </div>

                    <!-- NAMA BARANG -->
                    <div class="mb-3">
                        <label class="data-form-label">Nama Barang</label>
                        <input
                            type="text"
                            name="nama_barang"
                            class="data-form-control"
                            value="<?= htmlspecialchars($data['nama_barang'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            required>
                    </div>

                    <!-- JENIS BARANG -->
                    <div class="mb-3">
                        <label class="data-form-label">Jenis Barang</label>
                        <select name="jenis_barang" class="data-form-control" required>
                            <option value="">-- Pilih Jenis Barang --</option>

                            <option value="kamera"
                                <?= ($data['jenis_barang'] ?? '') === 'kamera' ? 'selected' : ''; ?>>
                                Kamera
                            </option>

                            <option value="tripod"
                                <?= ($data['jenis_barang'] ?? '') === 'tripod' ? 'selected' : ''; ?>>
                                Tripod
                            </option>
                        </select>
                    </div>

                    <!-- KATEGORI -->
                    <div class="mb-3">
                        <label class="data-form-label">Kategori</label>

                        <select name="id_kategori" class="data-form-control" required>
                            <option value="">-- Pilih Kategori --</option>

                            <?php while ($item = mysqli_fetch_assoc($kategori)) { ?>
                                <option
                                    value="<?= (int) $item['id_kategori']; ?>"
                                    <?= (int) $data['id_kategori'] === (int) $item['id_kategori'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($item['nama_kategori'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- MERK -->
                    <div class="mb-3">
                        <label class="data-form-label">Merk</label>

                        <select name="id_merk" class="data-form-control" required>
                            <option value="">-- Pilih Merk --</option>

                            <?php while ($item = mysqli_fetch_assoc($merk)) { ?>
                                <option
                                    value="<?= (int) $item['id_merk']; ?>"
                                    <?= (int) $data['id_merk'] === (int) $item['id_merk'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($item['nama_merk'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- KONDISI UNIT BARU -->
                    <div class="mb-3">
                        <label class="data-form-label">
                            Kondisi Unit Baru
                        </label>

                        <select
                            name="id_kondisi_unit_baru"
                            class="data-form-control">

                            <option value="">
                                -- Pilih Kondisi Unit Baru --
                            </option>

                            <?php foreach ($daftar_kondisi as $item) { ?>
                                <option
                                    value="<?= (int) $item['id_kondisi']; ?>"
                                    <?= $id_kondisi_default === (int) $item['id_kondisi'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($item['nama_kondisi'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php } ?>

                        </select>

                        <small class="text-muted">
                            Digunakan hanya jika stok ditambah. Kondisi unit lama
                            dapat diubah melalui halaman Detail Unit.
                        </small>
                    </div>

                    <!-- STOK -->
                    <div class="mb-3">
                        <label class="data-form-label">Stok</label>

                        <input
                            type="number"
                            name="stok"
                            class="data-form-control"
                            min="0"
                            value="<?= htmlspecialchars((string) $data['stok'], ENT_QUOTES, 'UTF-8'); ?>"
                            required>

                        <small class="text-muted">
                            Jika stok dikurangi, sistem hanya menghapus unit
                            yang berstatus tersedia.
                        </small>
                    </div>

                    <!-- HARGA SEWA -->
                    <div class="mb-3">
                        <label class="data-form-label">Harga Sewa</label>

                        <input
                            type="number"
                            name="harga_sewa"
                            class="data-form-control"
                            min="1"
                            step="0.01"
                            value="<?= htmlspecialchars((string) ($data['harga_sewa'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                            required>

                        <small class="text-muted">
                            Masukkan harga sewa per paket.
                        </small>
                    </div>

                    <!-- FOTO -->
                    <div class="mb-3">
                        <label class="data-form-label">Foto Barang</label>

                        <input
                            type="file"
                            name="foto"
                            class="data-form-control"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

                        <small class="text-muted d-block mt-2">
                            Format JPG, JPEG, PNG, atau WEBP. Maksimal 5 MB.
                            Kosongkan jika tidak ingin mengganti foto.
                        </small>

                        <?php if (!empty($data['foto'])) { ?>
                            <div class="mt-3">
                                <label class="data-form-label">Foto Saat Ini</label>

                                <div>
                                    <img
                                        src="/../uploads/barang/<?= htmlspecialchars($data['foto'], ENT_QUOTES, 'UTF-8'); ?>"
                                        alt="Foto <?= htmlspecialchars($data['nama_barang'], ENT_QUOTES, 'UTF-8'); ?>"
                                        class="foto-detail"
                                        style="max-width: 220px; max-height: 220px; object-fit: contain;">
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- DESKRIPSI -->
                    <div class="mb-4">
                        <label class="data-form-label">Deskripsi</label>

                        <textarea
                            name="deskripsi"
                            class="data-form-control"
                            rows="4"><?= htmlspecialchars($data['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <!-- TOMBOL -->
                    <div class="d-flex flex-wrap gap-2">

                        <button
                            type="submit"
                            name="update"
                            class="btn-form-save">
                            <i class="bi bi-save"></i>
                            Simpan Perubahan
                        </button>

                        <a href="index.php" class="btn-form-cancel">
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
