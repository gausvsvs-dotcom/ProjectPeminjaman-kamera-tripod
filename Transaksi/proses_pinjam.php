<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'koneksi.php';

if (isset($_POST['simpan'])) {
    $id_user              = mysqli_real_escape_string($koneksi, $_POST['id_user']);
    $id_barang            = mysqli_real_escape_string($koneksi, $_POST['id_barang']);
    $tgl_pinjam           = mysqli_real_escape_string($koneksi, $_POST['tgl_pinjam']);
    $tgl_kembali_rencana  = mysqli_real_escape_string($koneksi, $_POST['tgl_kembali_rencana']);
    $jumlah               = (int) $_POST['jumlah'];

    // 1. Cek ketersediaan stok barang
    $cek_stok = mysqli_query($koneksi, "SELECT stok FROM barang WHERE id_barang = '$id_barang'");
    $data_barang = mysqli_fetch_assoc($cek_stok);

    if (!$data_barang) {
        echo "<script>
                alert('Barang tidak ditemukan!');
                window.history.back();
              </script>";
        exit;
    }

    if ($data_barang['stok'] < $jumlah) {
        echo "<script>
                alert('Stok tidak mencukupi!');
                window.history.back();
              </script>";
        exit;
    }

    // 2. Simpan data ke tabel peminjaman
    $query_pinjam = "INSERT INTO peminjaman (id_user, id_barang, tgl_pinjam, tgl_kembali_rencana, jumlah, status) 
                     VALUES ('$id_user', '$id_barang', '$tgl_pinjam', '$tgl_kembali_rencana', '$jumlah', 'Dipinjam')";
    
    $simpan = mysqli_query($koneksi, $query_pinjam);

    if ($simpan) {
        // 3. Kurangi stok barang
        mysqli_query($koneksi, "UPDATE barang SET stok = stok - $jumlah WHERE id_barang = '$id_barang'");

        echo "<script>
                alert('Peminjaman berhasil disimpan!');
                window.location.href = 'riwayat.php';
              </script>";
    } else {
        die("Gagal menyimpan data peminjaman: " . mysqli_error($koneksi));
    }
} else {
    header("Location: peminjaman.php");
    exit;
}
?>