<?php
// Memanggil file koneksi database
require_once '../Peminjaman/koneksi.php';

$pesan = "";
$tipe_pesan = "error";

// Cek apakah data dikirim melalui metode POST dari form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_peminjaman'])) {
    $id_peminjaman = mysqli_real_escape_string($conn, $_POST['id_peminjaman']);
    $tanggal_kembali_aktual = isset($_POST['tanggal_kembali']) ? $_POST['tanggal_kembali'] : date('Y-m-d');
    $kondisi_barang = isset($_POST['kondisi_barang']) ? $_POST['kondisi_barang'] : 'Baik / Normal';

    // 1. Ambil data peminjaman berdasarkan ID
    $query_cek = mysqli_query($conn, "SELECT * FROM peminjaman WHERE id_peminjaman = '$id_peminjaman'");
    $data = mysqli_fetch_assoc($query_cek);

    if ($data) {
        $tanggal_rencana = isset($data['tanggal_kembali_rencana']) ? $data['tanggal_kembali_rencana'] : (isset($data['tgl_kembali']) ? $data['tgl_kembali'] : date('Y-m-d'));

        // 2. Hitung selisih keterlambatan (dalam hari)
        $timestamp_rencana = strtotime($tanggal_rencana);
        $timestamp_aktual = strtotime($tanggal_kembali_aktual);

        $selisih_detik = $timestamp_aktual - $timestamp_rencana;
        $selisih_hari = floor($selisih_detik / (60 * 60 * 24));

        $denda = 0;
        $tarif_per_hari = 5000;

        if ($selisih_hari > 0) {
            $denda = $selisih_hari * $tarif_per_hari;
        }

        if ($kondisi_barang == 'Rusak Ringan') {
            $denda += 25000;
        } elseif ($kondisi_barang == 'Rusak Berat / Hilang') {
            $denda += 100000;
        }

        // 3. Update status peminjaman menjadi 'Dikembalikan'
        $update = mysqli_query($conn, "UPDATE peminjaman SET status = 'Dikembalikan', denda = '$denda' WHERE id_peminjaman = '$id_peminjaman'");

        if ($update) {
            $pesan = "Pengembalian berhasil diproses! Total Denda: Rp " . number_format($denda, 0, ',', '.');
            $tipe_pesan = "sukses";
        } else {
            $pesan = "Gagal memperbarui database!";
        }
    } else {
        $pesan = "ID Peminjaman #$id_peminjaman tidak ditemukan di database!";
    }
} else {
    // Jika diakses langsung tanpa lewat form
    $pesan = "Halaman ini khusus untuk pemrosesan data. Silakan isi form pengembalian terlebih dahulu.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Pengembalian</title>
    <style>
        body {
            background-color: #FFFBEB; /* Cream Soft */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            margin: 0;
            padding: 40px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            border-top: 6px solid #8174A0;
            text-align: center;
        }
        h2 {
            color: #8174A0;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 15px;
            font-weight: 600;
        }
        .alert-sukses {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .btn-kembali {
            display: inline-block;
            background-color: #8174A0;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 10px;
            transition: background-color 0.2s;
        }
        .btn-kembali:hover {
            background-color: #6a5d88;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Status Proses Pengembalian</h2>

    <div class="alert <?= ($tipe_pesan == 'sukses') ? 'alert-sukses' : 'alert-error'; ?>">
        <?= $pesan; ?>
    </div>

    <a href="pengembalian.php" class="btn-kembali">Kembali ke Form Pengembalian</a>
    <a href="../riwayat.php" class="btn-kembali" style="background-color: #A888B5; margin-left: 10px;">Lihat Riwayat</a>
</div>

</body>
</html>