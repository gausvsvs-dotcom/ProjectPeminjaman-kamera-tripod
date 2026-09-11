<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    mysqli_query($conn, "DELETE FROM kondisi_barang
        WHERE id_kondisi = '$id'");
}

header("Location: index.php");
exit;
?>