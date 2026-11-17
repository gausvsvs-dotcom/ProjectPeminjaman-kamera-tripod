
<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

// VALIDASI ID KONDISI
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header("Location: index.php?pesan=gagal");
    exit;
}

// CEK APAKAH KONDISI MASIH DIGUNAKAN UNIT BARANG
$stmt = mysqli_prepare($conn, "
    SELECT COUNT(*) AS total
    FROM barang_unit
    WHERE id_kondisi = ?
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

// HAPUS KONDISI BARANG
$stmt = mysqli_prepare($conn, "
    DELETE FROM kondisi_barang
    WHERE id_kondisi = ?
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
