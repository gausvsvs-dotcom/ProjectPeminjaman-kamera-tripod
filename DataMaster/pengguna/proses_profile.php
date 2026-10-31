
<?php
session_start();
require_once "../config/koneksi.php";

/** @var mysqli $conn */

// Pastikan sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

$id = $_SESSION['id_user'];

$nama = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');

// Validasi
if ($nama == "" || $username == "") {
    echo "<script>
        alert('Nama dan username wajib diisi!');
        window.location.href = '../dashboard/index.php';
    </script>";
    exit;
}

// Ambil foto lama
$query = mysqli_query(
    $conn,
    "SELECT foto FROM pengguna WHERE id_user = '$id'"
);

$data = mysqli_fetch_assoc($query);
$foto = $data['foto'] ?? 'mutia.jpg';

// Proses upload foto jika ada
if (isset($_FILES['foto']) && $_FILES['foto']['name'] != "") {

    $namaFile = $_FILES['foto']['name'];
    $tmpFile = $_FILES['foto']['tmp_name'];

    $ekstensi = strtolower(
        pathinfo($namaFile, PATHINFO_EXTENSION)
    );

    $ekstensiValid = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ekstensi, $ekstensiValid)) {
        echo "<script>
            alert('Format foto harus JPG, JPEG, PNG, atau WEBP!');
            window.location.href = '../dashboard/index.php';
        </script>";
        exit;
    }

    $namaFoto = "profil_" . time() . "." . $ekstensi;

    $folder = __DIR__ . "/../assets/img/";

    if (move_uploaded_file($tmpFile, $folder . $namaFoto)) {
        $foto = $namaFoto;
    }
}

// Update data pengguna
$query = mysqli_query(
    $conn,
    "UPDATE pengguna SET
        nama_user = '$nama',
        username = '$username',
        foto = '$foto'
    WHERE id_user = '$id'"
);

if ($query) {

    $_SESSION['nama'] = $nama;
    $_SESSION['username'] = $username;
    $_SESSION['foto'] = $foto;

    echo "<script>
        alert('Profil berhasil diperbarui!');
        window.location.href = '../dashboard/index.php';
    </script>";

} else {
    echo "Gagal memperbarui profil: " . mysqli_error($conn);
}
?>
