<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'koneksi.php';

// Menangkap ID dari GET atau POST dengan beberapa opsi parameter
$id_peminjaman = $_GET['id'] ?? $_GET['id_peminjaman'] ?? $_POST['id_peminjaman'] ?? $_POST['id'] ?? null;

if (!empty($id_peminjaman)) {
    $id_peminjaman = mysqli_real_escape_string($koneksi, $id_peminjaman);
    $tgl_kembali_real = date('Y-m-d');

    // Cek data peminjaman
    $query_cek = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE id_peminjaman = '$id_peminjaman'");
    $data = mysqli_fetch_assoc($query_cek);

    if ($data) {
        if ($data['status'] == 'Dikembalikan') {
            echo "<script>
                    alert('Barang ini sudah dikembalikan sebelumnya!');
                    window.location.href = 'riwayat.php';
                  </script>";
            exit;
        }

        $id_barang = $data['id_barang'];
        $jumlah    = $data['jumlah'];

        // Update status & tanggal pengembalian
        $query_update = "UPDATE peminjaman 
                         SET status = 'Dikembalikan', tgl_kembali_real = '$tgl_kembali_real' 
                         WHERE id_peminjaman = '$id_peminjaman'";
        
        $update = mysqli_query($koneksi, $query_update);

        if ($update) {
            // Tambahkan kembali stok barang
            mysqli_query($koneksi, "UPDATE barang SET stok = stok + $jumlah WHERE id_barang = '$id_barang'");

            echo "<script>
                    alert('Barang berhasil dikembalikan!');
                    window.location.href = 'riwayat.php';
                  </script>";
        } else {
            die("Gagal mengembalikan barang: " . mysqli_error($koneksi));
        }
    } else {
        echo "<script>
                alert('Data peminjaman tidak ditemukan di database!');
                window.location.href = 'riwayat.php';
              </script>";
    }
} else {
    echo "<script>
            alert('ID Peminjaman tidak valid atau tidak terkirim!');
            window.location.href = 'riwayat.php';
          </script>";
}
?>