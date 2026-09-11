<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $query = mysqli_query($conn, "DELETE FROM merk
        WHERE id_merk = '$id'");

    if (!$query) {
        die("Data tidak bisa dihapus: " . mysqli_error($conn));
    }
}

header("Location: index.php?pesan=hapus");
exit;
?>