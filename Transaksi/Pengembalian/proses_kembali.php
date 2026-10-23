<?php
// Memanggil koneksi database dari folder Peminjaman sebelah
require_once "../Peminjaman/koneksi.php";

// Memastikan form dikirim melalui metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Tangkap data dari form pengembalian
    $id_peminjaman  = mysqli_real_escape_string($conn, $_POST['id_peminjaman']);
    $tgl_kembali    = mysqli_real_escape_string($conn, $_POST['tgl_kembali']);
    $status_barang  = mysqli_real_escape_string($conn, $_POST['status_barang']);
    $denda          = mysqli_real_escape_string($conn, $_POST['denda']);
    
    // Status baru setelah dikembalikan
    $status_peminjaman = 'Selesai'; 

    // Query untuk memperbarui data peminjaman di database
    $query = "UPDATE peminjaman SET 
              status = '$status_peminjaman' 
              WHERE id_peminjaman = '$id_peminjaman'";

    $result = mysqli_query($conn, $query);

    if ($result) {
        // Menggunakan path absolut dari root domain lokal agar langsung menuju ke file pengembalian.php
        echo "<script>
                alert('Pengembalian berhasil diproses!');
                window.location.href = window.location.origin + window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/')) + '/pengembalian.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal memproses pengembalian: " . mysqli_error($conn) . "');
                window.location.href = 'pengembalian.php';
              </script>";
    }
} else {
    // Jika diakses langsung tanpa submit form
    header("Location: pengembalian.php");
    exit();
}
?>