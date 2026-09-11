\<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    mysqli_query($conn, "DELETE FROM pengguna
        WHERE id_pengguna = '$id'");
}

header("Location: index.php?pesan=hapus");
exit;
?>