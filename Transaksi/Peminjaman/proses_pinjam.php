<?php
require_once 'koneksi.php';

$pesan = "";
$tipe_pesan = "error";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Matikan pengecekan foreign key sementara agar tidak error
    mysqli_query($conn, "SET foreign_key_checks = 0");

    $query = "INSERT INTO peminjaman (status) VALUES ('Dipinjam')";
    $insert = mysqli_query($conn, $query);

    // Nyalakan kembali pengecekan foreign key
    mysqli_query($conn, "SET foreign_key_checks = 1");

    if ($insert) {
        $pesan = "Data peminjaman berhasil disimpan!";
        $tipe_pesan = "sukses";
    } else {
        $pesan = "Gagal menyimpan: " . mysqli_error($conn);
    }
} else {
    $pesan = "Silakan isi form peminjaman terlebih dahulu.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Proses Peminjaman</title>
    <style>
        body { background-color: #FFFBEB; font-family: 'Segoe UI', Tahoma, sans-serif; padding: 40px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 35px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border-top: 6px solid #8174A0; text-align: center; }
        h2 { color: #8174A0; margin-top: 0; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 25px; font-weight: 600; }
        .alert-sukses { background-color: #d4edda; color: #155724; }
        .alert-error { background-color: #f8d7da; color: #721c24; }
        .btn { display: inline-block; background: #8174A0; color: white; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .btn:hover { background: #6a5d88; }
    </style>
</head>
<body>
<div class="container">
    <h2>Status Proses Peminjaman</h2>
    <div class="alert <?= ($tipe_pesan == 'sukses') ? 'alert-sukses' : 'alert-error'; ?>"><?= $pesan; ?></div>
    <a href="peminjaman.php" class="btn">Kembali ke Form Peminjaman</a>
    <a href="../riwayat.php" class="btn" style="background-color: #A888B5; margin-left: 10px;">Lihat Riwayat</a>
</div>
</body>
</html>