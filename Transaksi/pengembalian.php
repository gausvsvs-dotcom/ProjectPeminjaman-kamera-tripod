<?php
require_once 'koneksi.php';

// Mendukung pembacaan parameter id dari URL (?id=...) atau (?id_peminjaman=...)
$id_peminjaman = $_GET['id'] ?? $_GET['id_peminjaman'] ?? null;

if (!$id_peminjaman) {
    echo "<script>
            alert('ID Peminjaman tidak valid atau tidak terkirim!');
            window.location.href = 'riwayat.php';
          </script>";
    exit;
}

try {
    // 1. Ambil data transaksi peminjaman
    $stmt_check = $pdo->prepare("SELECT id_barang, jumlah, status FROM peminjaman WHERE id_peminjaman = ?");
    $stmt_check->execute([$id_peminjaman]);
    $data = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($data && $data['status'] === 'Dipinjam') {
        $tgl_kembali_real = date('Y-m-d');

        // 2. Update status transaksi menjadi Dikembalikan & catat tanggal pengembalian
        $stmt_update = $pdo->prepare("UPDATE peminjaman SET status = 'Dikembalikan', tgl_kembali_real = ? WHERE id_peminjaman = ?");
        $stmt_update->execute([$tgl_kembali_real, $id_peminjaman]);

        // 3. Kembalikan/tambahkan stok barang
        $stmt_stok = $pdo->prepare("UPDATE barang SET stok = stok + ? WHERE id_barang = ?");
        $stmt_stok->execute([$data['jumlah'], $data['id_barang']]);

        echo "<script>
                alert('Barang berhasil dikembalikan & stok telah ditambahkan!');
                window.location.href = 'riwayat.php';
              </script>";
        exit;
    } else {
        echo "<script>
                alert('Data tidak ditemukan atau barang sudah dikembalikan sebelumnya!');
                window.location.href = 'riwayat.php';
              </script>";
        exit;
    }
} catch (PDOException $e) {
    echo "Gagal memproses pengembalian: " . $e->getMessage();
    exit;
}
?>