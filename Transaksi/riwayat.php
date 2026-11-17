<?php
$nama_peminjam = isset($_POST['nama_peminjam']) ? $_POST['nama_peminjam'] : 'SayAntika';
$nama_alat     = isset($_POST['nama_alat']) ? $_POST['nama_alat'] : 'Kamera DSLR Canon';
$metode        = isset($_POST['metode']) ? $_POST['metode'] : 'Tunai';
$total_harga   = isset($_POST['total_harga']) ? $_POST['total_harga'] : '150000';
$tanggal_transaksi = date('d-m-Y H:i');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman</title>
    <style>
        body { background-color: #FFFBEB; font-family: 'Segoe UI', Tahoma, sans-serif; padding: 40px 20px; margin: 0; }
        .card { max-width: 800px; margin: 0 auto; background: #fff; padding: 35px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border-top: 6px solid #8174A0; }
        .card-header { font-size: 18px; font-weight: 600; color: #8174A0; text-align: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #eee; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 13px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #f0f0f0; }
        th { background-color: #fcf9f2; color: #8174A0; font-weight: 600; }
        td { color: #555; }
        .badge { background-color: #e2f0d9; color: #385723; padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
        .btn-action { display: inline-block; background-color: #EFB6C8; color: #555; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; }
        .btn-action:hover { background-color: #df9fb3; }
        .btn-home { display: block; width: 100%; background-color: #8174A0; color: white; padding: 12px; border-radius: 8px; text-decoration: none; text-align: center; font-weight: 600; font-size: 14px; margin-top: 25px; box-sizing: border-box; }
        .btn-home:hover { background-color: #6a5d88; }
    </style>
</head>
<body>

<div class="card">
    <div class="card-header">📜 Riwayat & Status Peminjaman Kamera & Tripod</div>
    
    <table>
        <thead>
            <tr>
                <th>Peminjam</th>
                <th>Alat yang Dipinjam</th>
                <th>Waktu</th>
                <th>Metode</th>
                <th>Total</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><b><?= htmlspecialchars($nama_peminjam); ?></b></td>
                <td><?= htmlspecialchars($nama_alat); ?></td>
                <td><?= $tanggal_transaksi; ?></td>
                <td><?= htmlspecialchars($metode); ?></td>
                <td>Rp <?= number_format((int)$total_harga, 0, ',', '.'); ?></td>
                <td><span class="badge">Dipinjam</span></td>
                <td>
                    <a href="Pengembalian/pengembalian.php?nama=<?= urlencode($nama_peminjam); ?>&alat=<?= urlencode($nama_alat); ?>" class="btn-action">Kembalikan</a>
                </td>
            </tr>
        </tbody>
    </table>

    <a href="Peminjaman/peminjaman.php" class="btn-home">← Buat Peminjaman Baru</a>
</div>

</body>
</html>