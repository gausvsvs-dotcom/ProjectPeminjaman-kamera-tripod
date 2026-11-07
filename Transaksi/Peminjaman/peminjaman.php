
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
