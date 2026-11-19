<?php
$nama_peminjam = isset($_POST['nama_peminjam']) ? $_POST['nama_peminjam'] : 'Tamu';
$no_hp         = isset($_POST['no_hp']) ? $_POST['no_hp'] : '-';
$nama_alat     = isset($_POST['nama_alat']) ? $_POST['nama_alat'] : 'Kamera & Tripod';
$durasi        = isset($_POST['durasi']) ? $_POST['durasi'] : 1;
$tgl_pinjam    = isset($_POST['tgl_pinjam']) ? $_POST['tgl_pinjam'] : '-';
$tgl_kembali   = isset($_POST['tgl_kembali']) ? $_POST['tgl_kembali'] : '-';
$jenis_pinjam  = isset($_POST['jenis_pinjam']) ? $_POST['jenis_pinjam'] : '-';
$alamat        = isset($_POST['alamat']) ? $_POST['alamat'] : '-';
$total_harga   = isset($_POST['total_harga']) ? $_POST['total_harga'] : '150000';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilihan Pembayaran</title>
    <style>
        body { background-color: #FFFBEB; font-family: 'Segoe UI', Tahoma, sans-serif; padding: 40px 20px; margin: 0; }
        .card { max-width: 550px; margin: 0 auto; background: #fff; padding: 35px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border-top: 6px solid #8174A0; }
        .card-header { font-size: 18px; font-weight: 600; color: #8174A0; text-align: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #eee; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; color: #555; }
        .info-value { font-weight: 600; color: #333; text-align: right; max-width: 60%; }
        .total-box { background-color: #fcf9f2; border: 1px solid #e8e2cd; padding: 15px; border-radius: 8px; margin: 20px 0; display: flex; justify-content: space-between; align-items: center; }
        .total-box span { font-size: 15px; color: #555; font-weight: 600; }
        .total-box b { font-size: 18px; color: #8174A0; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 14px; font-weight: 600; color: #555; margin-bottom: 8px; }
        select { width: 100%; padding: 11px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; background-color: #fafafa; font-size: 14px; }
        .btn-submit { display: block; width: 100%; background-color: #8174A0; color: white; padding: 12px; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; text-align: center; transition: background 0.2s; }
        .btn-submit:hover { background-color: #6a5d88; }
        .back-link { text-align: center; margin-top: 15px; }
        .back-link a { font-size: 13px; color: #8174A0; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="card">
    <div class="card-header">💳 Pilih Metode Pembayaran</div>
    
    <div class="info-row">
        <span>Nama Peminjam:</span>
        <span class="info-value"><?= htmlspecialchars($nama_peminjam); ?></span>
    </div>
    <div class="info-row">
        <span>Alat:</span>
        <span class="info-value"><?= htmlspecialchars($nama_alat); ?></span>
    </div>
    <div class="info-row">
        <span>Total Tagihan:</span>
        <span class="info-value">Rp <?= number_format((int)$total_harga, 0, ',', '.'); ?></span>
    </div>

    <div class="total-box">
        <span>Status:</span>
        <b style="color: #e67e22; font-size: 15px;">Menunggu Pembayaran</b>
    </div>

    <!-- Mengarah ke struk.php di dalam folder yang sama -->
    <form action="struk.php" method="POST">
        <input type="hidden" name="nama_peminjam" value="<?= htmlspecialchars($nama_peminjam); ?>">
        <input type="hidden" name="no_hp" value="<?= htmlspecialchars($no_hp); ?>">
        <input type="hidden" name="nama_alat" value="<?= htmlspecialchars($nama_alat); ?>">
        <input type="hidden" name="durasi" value="<?= $durasi; ?>">
        <input type="hidden" name="tgl_pinjam" value="<?= htmlspecialchars($tgl_pinjam); ?>">
        <input type="hidden" name="tgl_kembali" value="<?= htmlspecialchars($tgl_kembali); ?>">
        <input type="hidden" name="jenis_pinjam" value="<?= htmlspecialchars($jenis_pinjam); ?>">
        <input type="hidden" name="alamat" value="<?= htmlspecialchars($alamat); ?>">
        <input type="hidden" name="total_harga" value="<?= $total_harga; ?>">

        <div class="form-group">
            <label>Metode Pembayaran</label>
            <select name="metode" required>
                <option value="Tunai (Cash)">Tunai (Bayar di Tempat)</option>
                <option value="Transfer Bank">Transfer Bank (BCA/BRI/Mandiri)</option>
                <option value="E-Wallet">E-Wallet (GoPay/OVO/DANA)</option>
            </select>
        </div>

        <button type="submit" class="btn-submit">Selesaikan Pembayaran</button>
    </form>

    <div class="back-link">
        <a href="../konfirmasi.php">← Kembali ke Halaman Konfirmasi</a>
    </div>
</div>

</body>
</html>