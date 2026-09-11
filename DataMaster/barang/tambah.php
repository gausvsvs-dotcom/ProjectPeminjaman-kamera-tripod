<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

$kategori = mysqli_query($conn, "SELECT * FROM kategori");
$merk = mysqli_query($conn, "SELECT * FROM merk");
$kondisi = mysqli_query($conn, "SELECT * FROM kondisi_barang");

if (isset($_POST['simpan'])) {

    $kode_barang = $_POST['kode_barang'];
    $nama_barang = $_POST['nama_barang'];
    $jenis_barang = $_POST['jenis_barang'];
    $id_kategori = $_POST['id_kategori'];
    $id_merk = $_POST['id_merk'];
    $id_kondisi = $_POST['id_kondisi'];
    $stok = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];

    $foto = null;

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
    }

    $query = mysqli_query($conn, "INSERT INTO barang
        (
            kode_barang,
            nama_barang,
            jenis_barang,
            id_kategori,
            id_merk,
            id_kondisi,
            stok,
            foto,
            deskripsi
        )
        VALUES
        (
            '$kode_barang',
            '$nama_barang',
            '$jenis_barang',
            '$id_kategori',
            '$id_merk',
            '$id_kondisi',
            '$stok',
            '$foto',
            '$deskripsi'
        )
    ");

    if ($query) {

        header("Location: index.php?pesan=tambah");
        exit;

    } else {

        $error = "Data barang gagal ditambahkan: " . mysqli_error($conn);
    }
}
?>
<?php include "../layout/header.php" ?>
<?php include "../layout/navbar.php" ?>

<div class="main-content">
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">Tambah Barang</h3>
        </div>
        <div class="card-body">
            <?php if (isset($error)) { ?>
                <div class="alert alert-danger">
                    <?= $error; ?>
                </div>
            <?php } ?>
            <form method="POST"
                  enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">
                        Kode Barang
                    </label>
                    <input
                        type="text"
                        name="kode_barang"
                        class="form-control"
                        required
                    >
                </div>
                <div class="mb-3">
                    <label class="form-label">
                        Nama Barang
                    </label>
                    <input
                        type="text"
                        name="nama_barang"
                        class="form-control"
                        required
                    >
                </div>
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
                        <option value="kamera">
                            Kamera
                        </option>
                        <option value="tripod">
                            Tripod
                        </option>
                    </select>
                </div>
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
                        <?php while ($data = mysqli_fetch_assoc($kategori)) { ?>

                            <option value="<?= $data['id_kategori']; ?>">
                                <?= htmlspecialchars($data['nama_kategori']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
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
                        <?php while ($data = mysqli_fetch_assoc($merk)) { ?>

                            <option value="<?= $data['id_merk']; ?>">
                                <?= htmlspecialchars($data['nama_merk']); ?>
                            </option>

                        <?php } ?>
                    </select>
                </div>
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
                        <?php while ($data = mysqli_fetch_assoc($kondisi)) { ?>

                            <option value="<?= $data['id_kondisi']; ?>">
                                <?= htmlspecialchars($data['nama_kondisi']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">
                        Stok
                    </label>
                    <input
                        type="number"
                        name="stok"
                        class="form-control"
                        min="0"
                        value="0"
                        required
                    >
                </div>
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
                </div>
                <div class="mb-3">
                    <label class="form-label">
                        Deskripsi
                    </label>
                    <textarea
                        name="deskripsi"
                        class="form-control"
                        rows="4"
                    ></textarea>
                </div>
                <button
                    type="submit"
                    name="simpan"
                    class="btn btn-simpan">
                    Simpan
                </button>
                <a href="index.php"
                   class="btn btn-secondary">
                    Kembali
                </a>
            </form>
        </div>
    </div>
</div>
</div>
<?php include "../layout/footer.php" ?>