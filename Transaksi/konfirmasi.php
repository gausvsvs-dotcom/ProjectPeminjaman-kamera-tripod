<?php
$nama_peminjam = isset($_POST['nama_peminjam']) ? $_POST['nama_peminjam'] : 'Tamu';
$no_hp         = isset($_POST['no_hp']) ? $_POST['no_hp'] : '-';
$nama_alat     = isset($_POST['nama_alat']) ? $_POST['nama_alat'] : 'Kamera & Tripod';
$durasi        = isset($_POST['durasi']) ? (int)$_POST['durasi'] : 1;
$tgl_pinjam    = isset($_POST['tgl_pinjam']) ? $_POST['tgl_pinjam'] : '-';
$tgl_kembali   = isset($_POST['tgl_kembali']) ? $_POST['tgl_kembali'] : '-';
$jenis_pinjam  = isset($_POST['jenis_pinjam']) ? $_POST['jenis_pinjam'] : '-';
$alamat        = isset($_POST['alamat']) ? $_POST['alamat'] : '-';

// Hitung harga per hari berdasarkan alat yang dipilih
$harga_per_hari = 100000; // Default Canon EOS 3000D
if (strpos($nama_alat, 'Sony A6000') !== false) {
    $harga_per_hari = 150000;
} elseif (strpos($nama_alat, 'Tripod Saja') !== false) {
    $harga_per_hari = 30000;
}

// Total harga = harga per hari dikali durasi hari
$total_harga = $harga_per_hari * $durasi;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Data Peminjaman</title>
    <style>
        body { background-color: #FFFBEB; font-family: 'Segoe UI', Tahoma, sans-serif; padding: 40px 20px; margin: 0; }
        .card { max-width: 550px; margin: 0 auto; background: #fff; padding: 35px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border-top: 6px solid #8174A0; }
        .card-header { font-size: 18px; font-weight: 600; color: #8174A0; text-align: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #eee; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: #555; border-bottom: 1px dashed #f0f0f0; padding-bottom: 8px; }
        .info-value { font-weight: 600; color: #333; text-align: right; max-width: 60%; word-break: break-word; }
        .total-box { background-color: #fcf9f2; border: 1px solid #e8e2cd; padding: 15px; border-radius: 8px; margin: 25px 0; display: flex; justify-content: space-between; align-items: center; }
        .total-box span { font-size: 15px; color: #555; font-weight: 600; }
        .total-box b { font-size: 18px; color: #8174A0; }
        .btn-group { display: flex; gap: 15px; margin-top: 25px; }
        .btn { flex: 1; padding: 12px; border-radius: 8px; text-decoration: none; text-align: center; font-weight: 600; font-size: 14px; cursor: pointer; border: none; display: inline-block; box-sizing: border-box; }
        .btn-lanjut { background-color: #8174A0; color: white; }
        .btn-lanjut:hover { background-color: #6a5d88; }
        .btn-batal { background-color: #EFB6C8; color: #555; }
        .btn-batal:hover { background-color: #df9fb3; }
    </style>
</head>
<body>

<div class="card">
    <div class="card-header">📋 Konfirmasi Data Peminjaman Alat</div>
    
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
        <span>Durasi Pinjam:</span>
        <span class="info-value"><?= $durasi; ?> Hari</span>
    </div>
    <div class="info-row">
        <span>Tanggal Pinjam:</span>
        <span class="info-value"><?= htmlspecialchars($tgl_pinjam); ?></span>
    </div>
    <div class="info-row">
        <span>Tanggal Kembali:</span>
        <span class="info-value"><?= htmlspecialchars($tgl_kembali); ?></span>
    </div>
    <div class="info-row">
        <span>Jenis Peminjaman:</span>
        <span class="info-value"><?= htmlspecialchars($jenis_pinjam); ?></span>
    </div>
    <div class="info-row">
        <span>Alamat:</span>
        <span class="info-value"><?= htmlspecialchars($alamat); ?></span>
    </div>

    <div class="total-box">
        <span>Estimasi Tagihan:</span>
        <b>Rp <?= number_format($total_harga, 0, ',', '.'); ?></b>
    </div>

    <form action="Pembayaran/pembayaran.php" method="POST">
        <input type="hidden" name="nama_peminjam" value="<?= htmlspecialchars($nama_peminjam); ?>">
        <input type="hidden" name="no_hp" value="<?= htmlspecialchars($no_hp); ?>">
        <input type="hidden" name="nama_alat" value="<?= htmlspecialchars($nama_alat); ?>">
        <input type="hidden" name="durasi" value="<?= $durasi; ?>">
        <input type="hidden" name="tgl_pinjam" value="<?= htmlspecialchars($tgl_pinjam); ?>">
        <input type="hidden" name="tgl_kembali" value="<?= htmlspecialchars($tgl_kembali); ?>">
        <input type="hidden" name="jenis_pinjam" value="<?= htmlspecialchars($jenis_pinjam); ?>">
        <input type="hidden" name="alamat" value="<?= htmlspecialchars($alamat); ?>">
        <input type="hidden" name="total_harga" value="<?= $total_harga; ?>">

        <div class="btn-group">
            <a href="Peminjaman/peminjaman.php" class="btn btn-batal">Kembali</a>
            <button type="submit" class="btn btn-lanjut">Lanjut ke Pembayaran</button>
        </div>
    </form>
</div>

</body>
</html>