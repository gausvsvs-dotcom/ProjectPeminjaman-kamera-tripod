
<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

// VALIDASI ID KATEGORI
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header("Location: index.php?pesan=gagal");
    exit;
}

// CEK APAKAH KATEGORI MASIH DIGUNAKAN BARANG
$stmt = mysqli_prepare($conn, "
    SELECT COUNT(*) AS total
    FROM barang
    WHERE id_kategori = ?
");

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

// JIKA MASIH DIGUNAKAN, BATALKAN HAPUS
if ($data['total'] > 0) {
    header("Location: index.php?pesan=digunakan");
    exit;
}

// HAPUS KATEGORI
$stmt = mysqli_prepare($conn, "
    DELETE FROM kategori
    WHERE id_kategori = ?
");

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
    header("Location: index.php?pesan=hapus");
} else {
    header("Location: index.php?pesan=gagal");
}

mysqli_stmt_close($stmt);
exit;
?>
