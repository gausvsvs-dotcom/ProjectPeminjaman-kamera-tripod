<?php

session_start();

include 'includes/koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = "SELECT * FROM admin
          WHERE username = '$username'
          AND password = '$password'";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {

    $data = mysqli_fetch_assoc($result);

    $_SESSION['admin'] = $data['username'];

    header("Location: dashboard_admin.php");
    exit;

} else {

    header("Location: login.php?status=error");
    exit;

}
?>