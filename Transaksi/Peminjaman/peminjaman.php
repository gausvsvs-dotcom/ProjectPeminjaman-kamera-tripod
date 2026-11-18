
<?php
session_start();


// Memanggil koneksi database
require_once __DIR__ . '/../koneksi.php';

// Mengambil ID barang dari tombol Ajukan Peminjaman
$id_barang = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id_barang <= 0) {
    die("ID barang tidak ditemukan. Silakan pilih barang dari landing page.");
}

// Mengambil informasi barang
$stmt_barang = mysqli_prepare(
    $conn,
    "SELECT * FROM barang WHERE id_barang = ?"
);

mysqli_stmt_bind_param($stmt_barang, "i", $id_barang);
mysqli_stmt_execute($stmt_barang);

$result_barang = mysqli_stmt_get_result($stmt_barang);
$barang = mysqli_fetch_assoc($result_barang);

if (!$barang) {
    die("Barang tidak ditemukan.");
}

$pesan = "";

// Proses pengajuan peminjaman
if (isset($_POST['ajukan'])) {

    $nama = trim($_POST['nama_peminjam']);
    $no_hp = trim($_POST['no_hp']);
    $tanggal = $_POST['tgl_peminjaman'];
    $jatuh_tempo = $_POST['tgl_jatuh_tempo'];
    $alamat = trim($_POST['alamat']);

    if (
        $nama == "" ||
        $no_hp == "" ||
        $tanggal == "" ||
        $jatuh_tempo == "" ||
        $alamat == ""
    ) {
        $pesan = "Semua data wajib diisi!";
    } elseif ($jatuh_tempo < $tanggal) {
        $pesan = "Tanggal pengembalian tidak boleh sebelum tanggal peminjaman!";
    } else {

        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO peminjaman
            (id_barang, nama_peminjam, no_hp, tgl_peminjaman,
            tgl_jatuh_tempo, alamat, status)
            VALUES (?, ?, ?, ?, ?, ?, 'Dipinjam')"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "isssss",
            $id_barang,
            $nama,
            $no_hp,
            $tanggal,
            $jatuh_tempo,
            $alamat
        );

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
                alert('Pengajuan peminjaman berhasil!');
                window.location.href = '../../index.php';
            </script>";
            exit;
        } else {
            $pesan = "Gagal menyimpan peminjaman: " .
                     mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Form Peminjaman Kamera & Tripod</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            background-color: #FFFBEB;
            padding: 20px;
        }

        .container {
            width: 80%;
            max-width: 650px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        h2 {
            color: #8174A0;
            text-align: center;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, textarea {
            width: 100%;
            padding: 11px;
            margin-top: 7px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .barang {
            background: #f5f1fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 22px;
            background: #8174A0;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #6d608b;
        }

        .pesan {
            background: #ffe3e3;
            color: #b00020;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .kembali {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #8174A0;
            text-decoration: none;
        }

        @media (max-width: 700px) {
            .container {
                width: 100%;
                padding: 20px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Form Peminjaman Kamera & Tripod</h2>

    <hr>

    <?php if ($pesan != "") { ?>
        <div class="pesan">
            <?= htmlspecialchars($pesan) ?>
        </div>
    <?php } ?>

    <div class="barang">
        <strong>Barang yang akan dipinjam:</strong><br><br>

        Nama Barang:
        <?= htmlspecialchars($barang['nama_barang']) ?><br>

        ID Barang:
        <?= htmlspecialchars($barang['id_barang']) ?><br>

        Jenis Barang:
        <?= htmlspecialchars($barang['jenis_barang']) ?>
    </div>

    <form method="POST"
          action="peminjaman.php?id=<?= $id_barang ?>">

        <label>Nama Peminjam</label>
        <input
            type="text"
            name="nama_peminjam"
            placeholder="Masukkan nama lengkap"
            required
        >

        <label>Nomor HP</label>
        <input
            type="text"
            name="no_hp"
            placeholder="Masukkan nomor HP"
            required
        >

        <label>Tanggal Peminjaman</label>
        <input
            type="date"
            name="tgl_peminjaman"
            required
        >

        <label>Tanggal Pengembalian</label>
        <input
            type="date"
            name="tgl_jatuh_tempo"
            required
        >

        <label>Alamat</label>
        <textarea
            name="alamat"
            rows="4"
            placeholder="Masukkan alamat lengkap"
            required
        ></textarea>

        <button type="submit" name="ajukan">
            Ajukan Peminjaman
        </button>

    </form>

    <a href="javascript:history.back()" class="kembali">
        Kembali
    </a>

</div>

</body>
</html>
