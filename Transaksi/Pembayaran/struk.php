<?php
$nama_peminjam = isset($_POST['nama_peminjam']) ? $_POST['nama_peminjam'] : 'Tamu';
$no_hp         = isset($_POST['no_hp']) ? $_POST['no_hp'] : '-';
$nama_alat     = isset($_POST['nama_alat']) ? $_POST['nama_alat'] : 'Kamera & Tripod';
$durasi        = isset($_POST['durasi']) ? $_POST['durasi'] : 1;
$tgl_pinjam    = isset($_POST['tgl_pinjam']) ? $_POST['tgl_pinjam'] : '-';
$tgl_kembali   = isset($_POST['tgl_kembali']) ? $_POST['tgl_kembali'] : '-';
$jenis_pinjam  = isset($_POST['jenis_pinjam']) ? $_POST['jenis_pinjam'] : '-';
$alamat        = isset($_POST['alamat']) ? $_POST['alamat'] : '-';
$metode        = isset($_POST['metode']) ? $_POST['metode'] : 'Tunai';
$total_harga   = isset($_POST['total_harga']) ? $_POST['total_harga'] : '150000';
$tanggal_struk = date('d-m-Y H:i:s');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Bukti Pembayaran</title>
    <style>
        body { background-color: #FFFBEB; font-family: 'Segoe UI', Tahoma, sans-serif; padding: 40px 20px; margin: 0; }
        .card { max-width: 550px; margin: 0 auto; background: #fff; padding: 35px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border-top: 6px solid #8174A0; }
        .card-header { font-size: 18px; font-weight: 600; color: #8174A0; text-align: center; margin-bottom: 10px; }
        .success-badge { text-align: center; background-color: #e2f0d9; color: #385723; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 600; margin-bottom: 20px; display: inline-block; width: 100%; box-sizing: border-box; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px; color: #555; border-bottom: 1px dashed #f0f0f0; padding-bottom: 6px; }
        .info-value { font-weight: 600; color: #333; text-align: right; max-width: 60%; }
        .total-box { background-color: #fcf9f2; border: 1px solid #e8e2cd; padding: 15px; border-radius: 8px; margin: 20px 0; display: flex; justify-content: space-between; align-items: center; }
        .total-box span { font-size: 15px; color: #555; font-weight: 600; }
        .total-box b { font-size: 18px; color: #8174A0; }
        .btn-group { display: flex; gap: 15px; margin-top: 25px; }
        .btn { flex: 1; padding: 12px; border-radius: 8px; text-decoration: none; text-align: center; font-weight: 600; font-size: 14px; cursor: pointer; border: none; display: inline-block; box-sizing: border-box; }
        .btn-riwayat { background-color: #8174A0; color: white; }
        .btn-riwayat:hover { background-color: #6a5d88; }
        .btn-print { background-color: #EFB6C8; color: #555; }
        .btn-print:hover { background-color: #df9fb3; }
    </style>
</head>
<body>

<div class="card">
    <div class="card-header">📄 BUKTI STRUK PEMBAYARAN</div>
    <div class="success-badge">Pembayaran Berhasil Diverifikasi ✅</div>
    
    <div class="info-row">
        <span>Waktu Transaksi:</span>
        <span class="info-value"><?= $tanggal_struk; ?></span>
    </div>
    <div class="info-row">
        <span>Nama Peminjam:</span>
        <span class="info-value"><?= htmlspecialchars($nama_peminjam); ?></span>
    </div>
    <div class="info-row">
        <span>No HP:</span>
        <span class="info-value"><?= htmlspecialchars($no_hp); ?></span>
    </div>
    <div class="info-row">
        <span>Alat yang Dipinjam:</span>
        <span class="info-value"><?= htmlspecialchars($nama_alat); ?></span>
    </div>
    <div class="info-row">
        <span>Durasi:</span>
        <span class="info-value"><?= $durasi; ?> Hari</span>
    </div>
    <div class="info-row">
        <span>Metode Pembayaran:</span>
        <span class="info-value"><?= htmlspecialchars($metode); ?></span>
    </div>

    <div class="total-box">
        <span>Total Dibayar:</span>
        <b>Rp <?= number_format((int)$total_harga, 0, ',', '.'); ?></b>
    </div>

    <!-- Meneruskan data ke riwayat.php di folder utama Transaksi -->
    <form action="../riwayat.php" method="POST">
        <input type="hidden" name="nama_peminjam" value="<?= htmlspecialchars($nama_peminjam); ?>">
        <input type="hidden" name="nama_alat" value="<?= htmlspecialchars($nama_alat); ?>">
        <input type="hidden" name="metode" value="<?= htmlspecialchars($metode); ?>">
        <input type="hidden" name="total_harga" value="<?= $total_harga; ?>">

        <div class="btn-group">
            <button type="button" onclick="window.print()" class="btn btn-print">🖨️ Cetak Struk</button>
            <button type="submit" class="btn btn-riwayat">Lihat Riwayat →</button>
        </div>
    </form>
</div>

</body>
</html>