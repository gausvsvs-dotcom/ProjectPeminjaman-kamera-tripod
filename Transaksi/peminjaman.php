<?php
require_once 'koneksi.php';

$pesan = "";

// Tangkap parameter dari Landing Page
$id_produk = $_GET['id'] ?? null;
$jenis_produk = $_GET['jenis'] ?? null;

// Data referensi nama barang dari landing page
$kamera = [
    1 => 'Canon EOS 700D',
    2 => 'Nikon D7500',
    3 => 'Nikon D5600',
    4 => 'Fujifilm X-S10',
    5 => 'Sony Alpha A6400',
    6 => 'Fujifilm X-T30',
    7 => 'Canon EOS 90D',
    8 => 'Canon EOS 80D'
];

$tripod = [
    1 => 'ECO 196A',
    2 => 'Weifeng WT-3110A',
    3 => 'Weifeng WT-3520'
];

// Tentukan nama barang yang dipilih dari landing page
$nama_barang_terpilih = "";
if ($jenis_produk === 'kamera' && isset($kamera[$id_produk])) {
    $nama_barang_terpilih = $kamera[$id_produk];
} elseif ($jenis_produk === 'tripod' && isset($tripod[$id_produk])) {
    $nama_barang_terpilih = $tripod[$id_produk];
}

// Mengambil data pengguna untuk dropdown
$users_result = mysqli_query($conn, "SELECT * FROM pengguna ORDER BY nama_user ASC");
$users = $users_result ? mysqli_fetch_all($users_result, MYSQLI_ASSOC) : [];

// Mengambil data barang dari database
$barang_result = mysqli_query($conn, "SELECT * FROM barang WHERE stok > 0 ORDER BY nama_barang ASC");
$barang = $barang_result ? mysqli_fetch_all($barang_result, MYSQLI_ASSOC) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_POST['id_user'] ?? '';
    $id_barang = $_POST['id_barang'] ?? '';
    $jumlah = (int)($_POST['jumlah'] ?? 1);
    $tgl_pinjam = $_POST['tgl_pinjam'] ?? '';
    $tgl_jatuh_tempo = $_POST['tgl_jatuh_tempo'] ?? '';
    $no_hp = trim($_POST['no_hp'] ?? '-');
    $alamat = trim($_POST['alamat'] ?? '-');

    if ($id_user && $id_barang && $jumlah > 0 && $tgl_pinjam && $tgl_jatuh_tempo) {
        $stmt_stok = mysqli_prepare($conn, "SELECT stok FROM barang WHERE id_barang = ?");
        mysqli_stmt_bind_param($stmt_stok, "i", $id_barang);
        mysqli_stmt_execute($stmt_stok);
        $result_stok = mysqli_stmt_get_result($stmt_stok);
        $row_stok = mysqli_fetch_assoc($result_stok);
        $stok_tersedia = $row_stok['stok'] ?? 0;

        if ($stok_tersedia >= $jumlah) {
            $stmt_u = mysqli_prepare($conn, "SELECT nama_user FROM pengguna WHERE id_user = ?");
            mysqli_stmt_bind_param($stmt_u, "i", $id_user);
            mysqli_stmt_execute($stmt_u);
            $res_u = mysqli_stmt_get_result($stmt_u);
            $row_u = mysqli_fetch_assoc($res_u);
            $nama_peminjam = $row_u['nama_user'] ?? 'Peminjam';

            $stmt = mysqli_prepare($conn, "INSERT INTO peminjaman (id_user, nama_peminjam, no_hp, tgl_jatuh_tempo, status, tgl_peminjaman, jenis_peminjaman, Alamat) VALUES (?, ?, ?, ?, 'Dipinjam', ?, 'online', ?)");
            mysqli_stmt_bind_param($stmt, "isssss", $id_user, $nama_peminjam, $no_hp, $tgl_jatuh_tempo, $tgl_pinjam, $alamat);

            if (mysqli_stmt_execute($stmt)) {
                $stmt_update = mysqli_prepare($conn, "UPDATE barang SET stok = stok - ? WHERE id_barang = ?");
                mysqli_stmt_bind_param($stmt_update, "ii", $jumlah, $id_barang);
                mysqli_stmt_execute($stmt_update);

                $pesan = "<div class='alert success'>Peminjaman berhasil dicatat!</div>";
                
                $barang_result = mysqli_query($conn, "SELECT * FROM barang WHERE stok > 0 ORDER BY nama_barang ASC");
                $barang = $barang_result ? mysqli_fetch_all($barang_result, MYSQLI_ASSOC) : [];
            } else {
                $pesan = "<div class='alert error'>Gagal menyimpan: " . mysqli_error($conn) . "</div>";
            }
        } else {
            $pesan = "<div class='alert error'>Stok barang tidak mencukupi!</div>";
        }
    } else {
        $pesan = "<div class='alert error'>Lengkapi semua data form!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Peminjaman Barang</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #FFFBEB; 
            margin: 0; 
            padding: 40px 20px; 
            color: #333;
        }
        .card { 
            max-width: 500px; 
            margin: 0 auto; 
            border-radius: 16px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.08); 
            overflow: hidden;
        }
        .card-header {
            background-color: #8174A0;
            color: #ffffff;
            padding: 20px 25px;
        }
        .card-header h2 {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 600;
        }
        .card-body {
            background-color: #EFB6C8;
            padding: 25px;
        }
        .form-group { 
            margin-bottom: 18px; 
        }
        label { 
            display: block; 
            margin-bottom: 8px; 
            color: #2D2738; 
            font-weight: 700; 
            font-size: 0.95rem;
        }
        select, input, textarea { 
            width: 100%; 
            padding: 12px 14px; 
            border: none; 
            border-radius: 10px; 
            box-sizing: border-box; 
            background-color: #A888B5;
            color: #ffffff;
            font-size: 0.95rem;
            outline: none;
        }
        select::placeholder, input::placeholder, textarea::placeholder {
            color: #E2D9EE;
        }
        select option {
            background-color: #ffffff;
            color: #333333;
        }
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }
        button { 
            background-color: #8174A0; 
            color: white; 
            border: none; 
            padding: 14px; 
            width: 100%; 
            border-radius: 10px; 
            font-weight: bold; 
            font-size: 1rem;
            cursor: pointer; 
            transition: background-color 0.2s;
            margin-top: 10px;
        }
        button:hover { 
            background-color: #6C5F8A; 
        }
        .alert { 
            padding: 12px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            font-weight: bold; 
            text-align: center;
            font-size: 0.9rem;
        }
        .success { 
            background-color: #ffffff; 
            color: #512B3A; 
        }
        .error { 
            background-color: #f8d7da; 
            color: #721c24; 
        }
        .nav { 
            text-align: center; 
            margin-top: 20px; 
        }
        .nav a { 
            color: #8174A0; 
            text-decoration: none; 
            font-weight: bold; 
            font-size: 0.95rem;
        }
        .nav a:hover {
            color: #6C5F8A;
        }
    </style>
</head>
<body>

<div class="card">
    <div class="card-header">
        <h2>Form Peminjaman Barang</h2>
    </div>

    <div class="card-body">
        <?= $pesan; ?>

        <?php if ($nama_barang_terpilih): ?>
            <div class="alert success mb-3">
                Barang Dipilih dari Katalog: <strong><?= htmlspecialchars($nama_barang_terpilih); ?></strong> (<?= ucfirst($jenis_produk); ?>)
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Pengelola / Admin Pencatat</label>
                <select name="id_user" required>
                    <option value="">-- Pilih Pengelola --</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id_user']; ?>"><?= htmlspecialchars($u['nama_user']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Pilih Barang di Database</label>
                <select name="id_barang" required>
                    <option value="">-- Pilih Barang --</option>
                    <?php foreach ($barang as $b): ?>
                        <option value="<?= $b['id_barang']; ?>" <?= ($nama_barang_terpilih && stripos($b['nama_barang'], $nama_barang_terpilih) !== false) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($b['nama_barang']); ?> (Stok: <?= $b['stok']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Nomor HP Peminjam</label>
                <input type="text" name="no_hp" placeholder="Contoh: 081234567890" required>
            </div>

            <div class="form-group">
                <label>Jumlah Unit</label>
                <input type="number" name="jumlah" min="1" value="1" required>
            </div>

            <div class="form-group">
                <label>Tanggal Pinjam</label>
                <input type="date" name="tgl_pinjam" value="<?= date('Y-m-d'); ?>" required>
            </div>

            <div class="form-group">
                <label>Tanggal Rencana Kembali</label>
                <input type="date" name="tgl_jatuh_tempo" value="<?= date('Y-m-d', strtotime('+3 days')); ?>" required>
            </div>

            <div class="form-group">
                <label>Alamat Peminjam</label>
                <textarea name="alamat" rows="2" placeholder="Alamat lengkap peminjam"></textarea>
            </div>

            <button type="submit">Simpan Peminjaman</button>
        </form>

        <div class="nav">
            <a href="riwayat.php">&larr; Lihat Riwayat Peminjaman</a>
        </div>
    </div>
</div>

</body>
</html>