<?php
session_start();

$error = $_SESSION['pesan_error'] ?? '';
$sukses = $_SESSION['pesan_sukses'] ?? '';

unset($_SESSION['pesan_error'], $_SESSION['pesan_sukses']);

$nama_peminjam = $_SESSION['old']['nama_peminjam'] ?? '';
$no_hp = $_SESSION['old']['no_hp'] ?? '';
$tgl_peminjaman = $_SESSION['old']['tgl_peminjaman'] ?? '';
$tgl_jatuh_tempo = $_SESSION['old']['tgl_jatuh_tempo'] ?? '';
$alamat = $_SESSION['old']['alamat'] ?? '';

unset($_SESSION['old']);

function aman($data) {
    return htmlspecialchars((string) $data, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Form Peminjaman Kamera & Tripod</title>
    <style>
        body { background-color: #FFFBEB; font-family: 'Segoe UI', Tahoma, sans-serif; padding: 40px 20px; margin: 0; }
        .card { max-width: 550px; margin: 0 auto; background: #fff; padding: 35px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border-top: 6px solid #8174A0; }
        h2 { color: #8174A0; margin-top: 0; text-align: center; margin-bottom: 25px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 14px; font-weight: 600; color: #555; margin-bottom: 8px; }
        input, select, textarea { width: 100%; padding: 11px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; background-color: #fafafa; font-size: 14px; }
        input:focus, select:focus, textarea:focus { border-color: #8174A0; outline: none; background-color: #fff; }
        .btn { display: block; width: 100%; background: #8174A0; color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 600; font-size: 15px; cursor: pointer; margin-top: 25px; transition: background 0.2s; }
        .btn:hover { background: #6a5d88; }
    </style>
</head>
<body>
<div class="card">
    <h2>Form Peminjaman Alat</h2>
    <form action="../konfirmasi.php" method="POST">
        <div class="form-group">
            <label>Nama Peminjam</label>
            <input type="text" name="nama_peminjam" placeholder="Masukkan nama lengkap" required>
        </div>
        <div class="form-group">
            <label>No HP</label>
            <input type="text" name="no_hp" placeholder="Contoh: 0821xxxxxxx" required>
        </div>
        <div class="form-group">
            <label>Pilihan Kamera & Tripod</label>
            <select name="nama_alat" required>
                <option value="Kamera DSLR Canon EOS 3000D + Tripod Standart">Kamera DSLR Canon EOS 3000D + Tripod Standart (Rp 100.000/hari)</option>
                <option value="Kamera Mirrorless Sony A6000 + Tripod Pro">Kamera Mirrorless Sony A6000 + Tripod Pro (Rp 150.000/hari)</option>
                <option value="Tripod Saja (Takara / Beike)">Tripod Saja (Takara / Beike) (Rp 30.000/hari)</option>
            </select>
        </div>
        <div class="form-group">
            <label>Durasi Pinjam (Hari)</label>
            <input type="number" name="durasi" min="1" value="1" required>
        </div>
        <div class="form-group">
            <label>Tanggal Peminjaman</label>
            <input type="date" name="tgl_pinjam" required>
        </div>
        <div class="form-group">
            <label>Tanggal Jatuh Tempo (Rencana Kembali)</label>
            <input type="date" name="tgl_kembali" required>
        </div>
        <div class="form-group">
            <label>Jenis Peminjaman</label>
            <select name="jenis_pinjam">
                <option value="Offline">Offline (Datang Langsung)</option>
                <option value="Online">Online (Antar / Kurir)</option>
            </select>
        </div>
        <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat" rows="3" placeholder="Masukkan alamat peminjam" required></textarea>
        </div>
        <button type="submit" class="btn">Lanjut ke Konfirmasi</button>
    <title>Form Peminjaman Barang</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="style_peminjaman.css">
</head>

<body>
<div class="container-form">

    <div class="form-header">
        <div class="header-icon">
            <i class="bi bi-camera-fill"></i>
        </div>
        <div>
            <h2>Form Peminjaman Barang</h2>
            <p>Silakan lengkapi data untuk mengajukan peminjaman.</p>
        </div>
    </div>

    <form action="proses_pinjam.php" method="POST" class="form-body">

        <?php if ($error): ?>
            <div class="alert-error">
                <i class="bi bi-exclamation-circle"></i>
                <?= aman($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($sukses): ?>
            <div class="alert-success">
                <i class="bi bi-check-circle"></i>
                <?= aman($sukses); ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="nama_peminjam">Nama Lengkap</label>
            <input type="text" name="nama_peminjam" id="nama_peminjam"
                placeholder="Masukkan nama lengkap"
                value="<?= aman($nama_peminjam); ?>" required>
        </div>

        <div class="form-group">
            <label for="no_hp">Nomor HP / WhatsApp</label>
            <input type="tel" name="no_hp" id="no_hp"
                placeholder="Contoh: 08xxxxxxxxxx"
                value="<?= aman($no_hp); ?>" required>
        </div>

        <div class="form-group">
            <label for="tgl_peminjaman">Tanggal Peminjaman</label>
            <input type="date" name="tgl_peminjaman" id="tgl_peminjaman"
                value="<?= aman($tgl_peminjaman); ?>" required>
        </div>

        <div class="form-group">
            <label for="tgl_jatuh_tempo">Tanggal Rencana Pengembalian</label>
            <input type="date" name="tgl_jatuh_tempo" id="tgl_jatuh_tempo"
                value="<?= aman($tgl_jatuh_tempo); ?>" required>
        </div>

        <div class="form-group">
            <label for="alamat">Alamat Lengkap</label>
            <textarea name="alamat" id="alamat" rows="3"
                placeholder="Masukkan alamat lengkap"
                required><?= aman($alamat); ?></textarea>
        </div>

        <button type="submit" class="btn-simpan">
            <i class="bi bi-send-fill"></i>
            Ajukan Peminjaman
        </button>

    </form>
</div>
</body>
</html>
