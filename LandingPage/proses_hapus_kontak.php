<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include 'includes/koneksi.php';

$id = $_POST['id'];

$query = "DELETE FROM kontak WHERE id = $id";

mysqli_query($conn, $query);

header("Location: data_kontak.php?status=deleted");
exit;

?>