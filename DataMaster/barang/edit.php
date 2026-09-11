<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM barang
    WHERE id_barang = '$id'");

if (!$query) {
    die("Query error: " . mysqli_error($conn));
}

$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data barang tidak ditemukan.");
}


/* Ambil data master */
$kategori = mysqli_query($conn, "SELECT * FROM kategori");
$merk = mysqli_query($conn, "SELECT * FROM merk");
$kondisi = mysqli_query($conn, "SELECT * FROM kondisi_barang");


/* Proses update */
if (isset($_POST['update'])) {

    $kode_barang = $_POST['kode_barang'];
    $nama_barang = $_POST['nama_barang'];
    $jenis_barang = $_POST['jenis_barang'];
    $id_kategori = $_POST['id_kategori'];
    $id_merk = $_POST['id_merk'];
    $id_kondisi = $_POST['id_kondisi'];
    $stok = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];

    $foto_lama = $data['foto'];
    $foto = $foto_lama;


    /* Jika user memilih foto baru */
    if (!empty($_FILES['foto']['name'])) {

        $nama_foto = $_FILES['foto']['name'];
        $tmp_foto = $_FILES['foto']['tmp_name'];

        $folder = "../uploads/barang/";

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $foto = time() . "_" . $nama_foto;

        move_uploaded_file(
            $tmp_foto,
            $folder . $foto
        );


        /* Hapus foto lama */
        if (!empty($foto_lama) && file_exists($folder . $foto_lama)) {
            unlink($folder . $foto_lama);
        }
    }


    $query_update = mysqli_query($conn, "UPDATE barang SET

        kode_barang = '$kode_barang',
        nama_barang = '$nama_barang',
        jenis_barang = '$jenis_barang',
        id_kategori = '$id_kategori',
        id_merk = '$id_merk',
        id_kondisi = '$id_kondisi',
        stok = '$stok',
        foto = '$foto',
        deskripsi = '$deskripsi'

        WHERE id_barang = '$id'

    ");


    if ($query_update) {

        header("Location: index.php?pesan=ubah");
        exit;

    } else {

        $error = "Data barang gagal diubah: " . mysqli_error($conn);
    }
}
?>
<?php include "../layout/header.php" ?>
<?php include "../layout/navbar.php" ?>

<div class="main-content">
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">Edit Barang</h3>
        </div>
        <div class="card-body">
            <?php if (isset($error)) { ?>
                <div class="alert alert-danger">
                    <?= $error; ?>
                </div>
            <?php } ?>
            <form method="POST"
                  enctype="multipart/form-data">
                <!-- Kode Barang -->
                <div class="mb-3">
                    <label class="form-label">
                        Kode Barang
                    </label>
                    <input
                        type="text"
                        name="kode_barang"
                        class="form-control"
                        value="<?= htmlspecialchars($data['kode_barang']); ?>"
                        required
                    >
                </div>
                <!-- Nama Barang -->
                <div class="mb-3">
                    <label class="form-label">
                        Nama Barang
                    </label>
                    <input
                        type="text"
                        name="nama_barang"
                        class="form-control"
                        value="<?= htmlspecialchars($data['nama_barang']); ?>"
                        required
                    >
                </div>
                <!-- Jenis -->
                <div class="mb-3">

                    <label class="form-label">
                        Jenis Barang
                    </label>
                    <select
                        name="jenis_barang"
                        class="form-select"
                        required
                    >
                        <option value="">
                            -- Pilih Jenis Barang --
                        </option>

                        <option
                            value="kamera"
                            <?= $data['jenis_barang'] == 'kamera' ? 'selected' : ''; ?>
                        >
                            Kamera
                        </option>

                        <option
                            value="tripod"
                            <?= $data['jenis_barang'] == 'tripod' ? 'selected' : ''; ?>
                        >
                            Tripod
                        </option>
                    </select>
                </div>
                <!-- Kategori -->
                <div class="mb-3">
                    <label class="form-label">
                        Kategori
                    </label>
                    <select
                        name="id_kategori"
                        class="form-select"
                        required
                    >
                        <option value="">
                            -- Pilih Kategori --
                        </option>
                        <?php while ($item = mysqli_fetch_assoc($kategori)) { ?>
                            <option
                                value="<?= $item['id_kategori']; ?>"
                                <?= $data['id_kategori'] == $item['id_kategori'] ? 'selected' : ''; ?>
                            >
                                <?= htmlspecialchars($item['nama_kategori']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <!-- Merk -->
                <div class="mb-3">
                    <label class="form-label">
                        Merk
                    </label>
                    <select
                        name="id_merk"
                        class="form-select"
                        required
                    >
                        <option value="">
                            -- Pilih Merk --
                        </option>
                        <?php while ($item = mysqli_fetch_assoc($merk)) { ?>
                            <option
                                value="<?= $item['id_merk']; ?>"
                                <?= $data['id_merk'] == $item['id_merk'] ? 'selected' : ''; ?>
                            >
                                <?= htmlspecialchars($item['nama_merk']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <!-- Kondisi -->
                <div class="mb-3">
                    <label class="form-label">
                        Kondisi
                    </label>
                    <select
                        name="id_kondisi"
                        class="form-select"
                        required
                    >
                        <option value="">
                            -- Pilih Kondisi --
                        </option>
                        <?php while ($item = mysqli_fetch_assoc($kondisi)) { ?>
                            <option
                                value="<?= $item['id_kondisi']; ?>"
                                <?= $data['id_kondisi'] == $item['id_kondisi'] ? 'selected' : ''; ?>
                            >
                                <?= htmlspecialchars($item['nama_kondisi']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <!-- Stok -->
                <div class="mb-3">
                    <label class="form-label">
                        Stok
                    </label>
                    <input
                        type="number"
                        name="stok"
                        class="form-control"
                        min="0"
                        value="<?= $data['stok']; ?>"
                        required
                    >
                </div>
                <!-- Foto -->
                <div class="mb-3">
                    <label class="form-label">
                        Foto
                    </label>
                    <input
                        type="file"
                        name="foto"
                        class="form-control"
                        accept="image/*"
                    >
                    <?php if (!empty($data['foto'])) { ?>
                        <div class="mt-2">
                            <small class="text-muted">
                                Foto saat ini:
                            </small>
                            <br>
                            <img
                                src="../uploads/barang/<?= htmlspecialchars($data['foto']); ?>"
                                class="foto-lama"
                            >
                        </div>
                    <?php } ?>
                </div>
                <!-- Deskripsi -->
                <div class="mb-3">
                    <label class="form-label">
                        Deskripsi
                    </label>
                    <textarea
                        name="deskripsi"
                        class="form-control"
                        rows="4"
                    ><?= htmlspecialchars($data['deskripsi']); ?></textarea>
                </div>
                <button
                    type="submit"
                    name="update"
                    class="btn btn-update">
                    Update
                </button>
                <a
                    href="index.php"
                    class="btn btn-secondary">
                    Kembali
                </a>
            </form>
        </div>
    </div>
</div>
</div>
<?php include "../layout/footer.php" ?>