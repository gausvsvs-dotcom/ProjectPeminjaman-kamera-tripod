<?php
require_once 'koneksi.php';

$pesan = "";

// Mengambil data user dan barang untuk dropdown
$users = $pdo->query("SELECT * FROM users ORDER BY nama_user ASC")->fetchAll();
$barang = $pdo->query("SELECT * FROM barang WHERE stok > 0 ORDER BY nama_barang ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_POST['id_user'] ?? '';
    $id_barang = $_POST['id_barang'] ?? '';
    $jumlah = (int)($_POST['jumlah'] ?? 1);
    $tgl_pinjam = $_POST['tgl_pinjam'] ?? '';
    $tgl_kembali_rencana = $_POST['tgl_kembali_rencana'] ?? '';

    if ($id_user && $id_barang && $jumlah > 0 && $tgl_pinjam && $tgl_kembali_rencana) {
        try {
            // Cek stok barang
            $stmt_stok = $pdo->prepare("SELECT stok FROM barang WHERE id_barang = ?");
            $stmt_stok->execute([$id_barang]);
            $stok_tersedia = $stmt_stok->fetchColumn();

            if ($stok_tersedia >= $jumlah) {
                // Simpan transaksi peminjaman
                $sql = "INSERT INTO peminjaman (id_user, id_barang, tgl_pinjam, tgl_kembali_rencana, jumlah, status) 
                        VALUES (?, ?, ?, ?, ?, 'Dipinjam')";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id_user, $id_barang, $tgl_pinjam, $tgl_kembali_rencana, $jumlah]);

                // Kurangi stok barang
                $stmt_update = $pdo->prepare("UPDATE barang SET stok = stok - ? WHERE id_barang = ?");
                $stmt_update->execute([$jumlah, $id_barang]);

                $pesan = "<div class='alert success'>Peminjaman berhasil dicatat!</div>";
                // Refresh list barang
                $barang = $pdo->query("SELECT * FROM barang WHERE stok > 0 ORDER BY nama_barang ASC")->fetchAll();
            } else {
                $pesan = "<div class='alert error'>Stok barang tidak mencukupi!</div>";
            }
        } catch (PDOException $e) {
            $pesan = "<div class='alert error'>Error: " . $e->getMessage() . "</div>";
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
        select, input { 
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
        select::placeholder, input::placeholder {
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
        
        <form method="POST">
            <div class="form-group">
                <label>Nama Peminjam</label>
                <select name="id_user" required>
                    <option value="">-- Pilih Peminjam --</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id_user']; ?>"><?= htmlspecialchars($u['nama_user']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Pilih Barang</label>
                <select name="id_barang" required>
                    <option value="">-- Pilih Barang --</option>
                    <?php foreach ($barang as $b): ?>
                        <option value="<?= $b['id_barang']; ?>"><?= htmlspecialchars($b['nama_barang']); ?> (Stok: <?= $b['stok']; ?>)</option>
                    <?php endforeach; ?>
                </select>
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
                <input type="date" name="tgl_kembali_rencana" value="<?= date('Y-m-d', strtotime('+3 days')); ?>" required>
            </div>

            <button type="submit">Simpan Peminjaman</button>
        </form>

        <div class="nav">
            <a href="riwayat.php">Lihat Riwayat Peminjaman &rarr;</a>
        </div>
    </div>
</div>

</body>
</html>