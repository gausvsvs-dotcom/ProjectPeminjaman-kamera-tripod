
<?php
session_start();
require_once 'koneksi.php';

/** @var mysqli $conn */

$pesan = '';
$nama_barang_terpilih = '';
$jenis_produk = $_GET['jenis'] ?? '';
$id_produk = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

function aman($nilai)
{
    return htmlspecialchars((string) $nilai, ENT_QUOTES, 'UTF-8');
}

// Ambil barang dari database
$barang = [];
$result_barang = mysqli_query(
    $conn,
    "SELECT id_barang, nama_barang, stok, harga_sewa
     FROM barang
     WHERE stok > 0
     ORDER BY nama_barang ASC"
);

if ($result_barang) {
    $barang = mysqli_fetch_all($result_barang, MYSQLI_ASSOC);
}

// Cari barang yang dipilih dari katalog berdasarkan nama di database.
// ID pada landing page belum tentu sama dengan id_barang di database.
if ($id_produk && in_array($jenis_produk, ['kamera', 'tripod'], true)) {
    $pola = $jenis_produk === 'kamera' ? '%kamera%' : '%tripod%';

    // Nama barang katalog digunakan sebagai bantuan pencarian.
    $nama_katalog = [
        'kamera' => [
            1 => 'Canon EOS 700D',
            2 => 'Nikon D7500',
            3 => 'Nikon D5600',
            4 => 'Fujifilm X-S10',
            5 => 'Sony Alpha A6400',
            6 => 'Fujifilm X-T30',
            7 => 'Canon EOS 90D',
            8 => 'Canon EOS 80D',
        ],
        'tripod' => [
            1 => 'ECO 196A',
            2 => 'Weifeng WT-3110A',
            3 => 'Weifeng WT-3520',
        ],
    ];

    $nama_barang_terpilih =
        $nama_katalog[$jenis_produk][$id_produk] ?? '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_peminjam = trim($_POST['nama_peminjam'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $id_barang = filter_input(
        INPUT_POST,
        'id_barang',
        FILTER_VALIDATE_INT
    );
    $jumlah = filter_input(
        INPUT_POST,
        'jumlah',
        FILTER_VALIDATE_INT
    );
    $tgl_pinjam = $_POST['tgl_pinjam'] ?? '';
    $tgl_jatuh_tempo = $_POST['tgl_jatuh_tempo'] ?? '';

    $tanggal_pinjam = DateTime::createFromFormat('!Y-m-d', $tgl_pinjam);
    $tanggal_kembali = DateTime::createFromFormat('!Y-m-d', $tgl_jatuh_tempo);
    $tanggal_sekarang = new DateTime('today');

    if (
        $nama_peminjam === '' ||
        $no_hp === '' ||
        $alamat === '' ||
        !$id_barang ||
        !$jumlah ||
        $jumlah < 1 ||
        !$tanggal_pinjam ||
        !$tanggal_kembali ||
        $tanggal_pinjam->format('Y-m-d') !== $tgl_pinjam ||
        $tanggal_kembali->format('Y-m-d') !== $tgl_jatuh_tempo
    ) {
        $pesan = "<div class='alert error'>Periksa kembali semua data yang wajib diisi.</div>";
    } elseif (
        $tanggal_pinjam < $tanggal_sekarang ||
        $tanggal_kembali <= $tanggal_pinjam
    ) {
        $pesan = "<div class='alert error'>Tanggal pinjam tidak boleh di masa lalu dan tanggal kembali harus setelah tanggal pinjam.</div>";
    } elseif (!preg_match('/^[0-9+\-\s()]{8,20}$/', $no_hp)) {
        $pesan = "<div class='alert error'>Format nomor HP tidak valid.</div>";
    } else {
        $transaksi_dimulai = false;

        try {
            mysqli_begin_transaction($conn);
            $transaksi_dimulai = true;

            // Kunci baris barang agar stok dapat diperiksa kembali
            // ketika ada beberapa pengajuan bersamaan.
            $stmt_barang = mysqli_prepare(
                $conn,
                "SELECT nama_barang, stok, harga_sewa
                 FROM barang
                 WHERE id_barang = ?
                 FOR UPDATE"
            );

            mysqli_stmt_bind_param($stmt_barang, 'i', $id_barang);
            mysqli_stmt_execute($stmt_barang);
            $hasil_barang = mysqli_stmt_get_result($stmt_barang);
            $data_barang = mysqli_fetch_assoc($hasil_barang);

            if (!$data_barang) {
                throw new Exception('Barang tidak ditemukan.');
            }

            if ((int) $data_barang['stok'] < $jumlah) {
                throw new Exception(
                    'Stok saat ini tidak mencukupi jumlah yang diajukan.'
                );
            }

            // Harga diasumsikan sebagai harga sewa per hari.
            $selisih_hari = $tanggal_pinjam->diff($tanggal_kembali)->days;
            $lama_sewa = max(1, $selisih_hari);
            $harga_sewa = (float) $data_barang['harga_sewa'];
            $subtotal = $harga_sewa * $jumlah * $lama_sewa;

            /*
             * Pengajuan berasal dari pelanggan tanpa login.
             * Karena itu id_user diisi NULL, bukan ID admin.
             * Kolom peminjaman.id_user harus mengizinkan NULL.
             */
            $stmt_pinjam = mysqli_prepare(
                $conn,
                "INSERT INTO peminjaman
                    (id_user, nama_peminjam, no_hp, tgl_jatuh_tempo,
                     status, tgl_peminjaman, jenis_peminjaman, Alamat)
                 VALUES
                    (NULL, ?, ?, ?, 'Diajukan', ?, 'online', ?)"
            );

            mysqli_stmt_bind_param(
                $stmt_pinjam,
                'sssss',
                $nama_peminjam,
                $no_hp,
                $tgl_jatuh_tempo,
                $tgl_pinjam,
                $alamat
            );

if (!mysqli_stmt_execute($stmt_pinjam)) {
    throw new Exception(
        'Gagal menyimpan peminjaman: '
        . mysqli_stmt_error($stmt_pinjam)
    );
}

            $id_peminjaman = mysqli_insert_id($conn);
            

            // Simpan barang dan harga ke detail peminjaman.
            $stmt_detail = mysqli_prepare(
                $conn,
                "INSERT INTO detail_peminjaman
                    (id_peminjaman, id_barang, jumlah, harga_sewa, subtotal)
                 VALUES (?, ?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt_detail,
                'iiidd',
                $id_peminjaman,
                $id_barang,
                $jumlah,
                $harga_sewa,
                $subtotal
            );

if (!mysqli_stmt_execute($stmt_detail)) {
    throw new Exception(
        'Gagal menyimpan detail: '
        . mysqli_stmt_error($stmt_detail)
    );
}

            // Belum mengurangi stok di tahap pengajuan.
            mysqli_commit($conn);
            $transaksi_dimulai = false;

            $pesan = "<div class='alert success'>
                Pengajuan berhasil dikirim!<br>
                Nomor pengajuan: <strong>" .
                aman($id_peminjaman) .
                "</strong><br>
                Barang: <strong>" .
                aman($data_barang['nama_barang']) .
                "</strong><br>
                Lama sewa: <strong>" .
                aman($lama_sewa) .
                " hari</strong><br>
                Total perkiraan: <strong>Rp " .
                number_format($subtotal, 0, ',', '.') .
                "</strong><br>
                Status: <strong>Diajukan</strong><br>
                Pengajuan menunggu proses admin.
            </div>";
        } catch (Throwable $e) {
    if ($transaksi_dimulai) {
        mysqli_rollback($conn);
    }

    error_log(
        'Pengajuan peminjaman: ' . $e->getMessage()
    );

    $pesan = "<div class='alert error'>"
        . "Pengajuan gagal disimpan. "
        . "Silakan periksa log error PHP."
        . "</div>";
}
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Peminjaman Barang</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #FFFBEB;
            margin: 0;
            padding: 40px 20px;
            color: #333;
        }

        .card {
            max-width: 520px;
            margin: 0 auto;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .card-header {
            background-color: #8174A0;
            color: #fff;
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

        select,
        input,
        textarea {
            width: 100%;
            padding: 12px 14px;
            border: none;
            border-radius: 10px;
            background-color: #A888B5;
            color: #fff;
            font-family: inherit;
            font-size: 0.95rem;
            outline: none;
        }

        input::placeholder,
        textarea::placeholder {
            color: #F5EAF7;
        }

        select option {
            background-color: #fff;
            color: #333;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
        }

        .help-text {
            display: block;
            margin-top: 6px;
            color: #51445F;
            font-size: 0.82rem;
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
            font-weight: 500;
            text-align: center;
            font-size: 0.9rem;
            line-height: 1.7;
            overflow-wrap: anywhere;
        }

        .success {
            background-color: #fff;
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
            color: #51445F;
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
        <?= $pesan ?>

        <?php if ($nama_barang_terpilih): ?>
            <div class="alert success">
                Barang dari katalog:
                <strong><?= aman($nama_barang_terpilih) ?></strong>
                (<?= aman(ucfirst($jenis_produk)) ?>)
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="nama_peminjam">Nama Peminjam</label>
                <input
                    type="text"
                    id="nama_peminjam"
                    name="nama_peminjam"
                    maxlength="100"
                    placeholder="Masukkan nama lengkap"
                    value="<?= aman($_POST['nama_peminjam'] ?? '') ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="id_barang">Pilih Barang</label>
                <select id="id_barang" name="id_barang" required>
                    <option value="">-- Pilih Barang --</option>

                    <?php foreach ($barang as $b): ?>
                        <option
                            value="<?= (int) $b['id_barang'] ?>"
                            <?= (string)($id_produk ?? '') ===
                                (string)$b['id_barang'] &&
                                !empty($nama_barang_terpilih)
                                ? 'selected'
                                : '' ?>
                        >
                            <?= aman($b['nama_barang']) ?>
                            (Stok: <?= (int) $b['stok'] ?>)
                            — Rp <?= number_format(
                                (float)$b['harga_sewa'],
                                0,
                                ',',
                                '.'
                            ) ?>/hari
                        </option>
                    <?php endforeach; ?>
                </select>

                <span class="help-text">
                    Harga dihitung per hari sesuai harga di database.
                </span>
            </div>

            <div class="form-group">
                <label for="no_hp">Nomor HP</label>
                <input
                    type="tel"
                    id="no_hp"
                    name="no_hp"
                    maxlength="20"
                    placeholder="Contoh: 081234567890"
                    value="<?= aman($_POST['no_hp'] ?? '') ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah Unit</label>
                <input
                    type="number"
                    id="jumlah"
                    name="jumlah"
                    min="1"
                    value="<?= aman($_POST['jumlah'] ?? '1') ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="tgl_pinjam">Tanggal Pinjam</label>
                <input
                    type="date"
                    id="tgl_pinjam"
                    name="tgl_pinjam"
                    min="<?= date('Y-m-d') ?>"
                    value="<?= aman($_POST['tgl_pinjam'] ?? date('Y-m-d')) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="tgl_jatuh_tempo">Tanggal Rencana Kembali</label>
                <input
                    type="date"
                    id="tgl_jatuh_tempo"
                    name="tgl_jatuh_tempo"
                    min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                    value="<?= aman(
                        $_POST['tgl_jatuh_tempo']
                        ?? date('Y-m-d', strtotime('+3 days'))
                    ) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="alamat">Alamat Peminjam</label>
                <textarea
                    id="alamat"
                    name="alamat"
                    rows="3"
                    maxlength="1000"
                    placeholder="Masukkan alamat lengkap"
                    required
                ><?= aman($_POST['alamat'] ?? '') ?></textarea>
            </div>

            <button type="submit">Ajukan Peminjaman</button>
        </form>

        <div class="nav">
            <a href="riwayat.php">&larr; Lihat Riwayat Peminjaman</a>
        </div>
    </div>
</div>

<script>
    const tanggalPinjam = document.getElementById('tgl_pinjam');
    const tanggalKembali = document.getElementById('tgl_jatuh_tempo');

    tanggalPinjam.addEventListener('change', function () {
        if (this.value) {
            const berikutnya = new Date(this.value + 'T00:00:00');
            berikutnya.setDate(berikutnya.getDate() + 1);

            const tahun = berikutnya.getFullYear();
            const bulan = String(berikutnya.getMonth() + 1).padStart(2, '0');
            const hari = String(berikutnya.getDate()).padStart(2, '0');
            const minimalKembali = `${tahun}-${bulan}-${hari}`;

            tanggalKembali.min = minimalKembali;

            if (!tanggalKembali.value ||
                tanggalKembali.value <= this.value) {
                tanggalKembali.value = minimalKembali;
            }
        }
    });
</script>
</body>
</html>
