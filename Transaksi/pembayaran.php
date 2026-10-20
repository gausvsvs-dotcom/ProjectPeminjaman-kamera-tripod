
<?php
session_start();
require_once 'koneksi.php';

/** @var mysqli $conn */

function aman($nilai)
{
    return htmlspecialchars((string) $nilai, ENT_QUOTES, 'UTF-8');
}

function rupiah($nilai)
{
    return 'Rp ' . number_format((float) $nilai, 0, ',', '.');
}

$pesan = '';
$id_peminjaman = (int) ($_SESSION['id_peminjaman_pembayaran'] ?? 0);

if ($id_peminjaman < 1) {
    http_response_code(403);
    exit(
        'Sesi pembayaran tidak ditemukan. Silakan ajukan peminjaman kembali.'
    );
}

// Ambil data pengajuan dan hitung total dari database.
// Jangan mempercayai nominal yang dikirim dari form customer.
$stmt = mysqli_prepare(
    $conn,
    "SELECT p.id_peminjaman, p.nama_peminjam, p.status,
            COALESCE(SUM(d.subtotal), 0) AS total
     FROM peminjaman p
     LEFT JOIN detail_peminjaman d
       ON d.id_peminjaman = p.id_peminjaman
     WHERE p.id_peminjaman = ?
     GROUP BY p.id_peminjaman, p.nama_peminjam, p.status"
);

mysqli_stmt_bind_param($stmt, 'i', $id_peminjaman);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$peminjaman = mysqli_fetch_assoc($hasil);

if (!$peminjaman || $peminjaman['status'] !== 'Diajukan') {
    http_response_code(404);
    exit('Data pengajuan tidak ditemukan atau sudah diproses.');
}

$total = (float) $peminjaman['total'];

// Ambil pembayaran terakhir jika sudah ada.
$stmt = mysqli_prepare(
    $conn,
    "SELECT *
     FROM pembayaran
     WHERE id_peminjaman = ?
     ORDER BY id_pembayaran DESC
     LIMIT 1"
);

mysqli_stmt_bind_param($stmt, 'i', $id_peminjaman);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$pembayaran = mysqli_fetch_assoc($hasil);

// Lokasi folder bukti pembayaran.
$folder_bukti = __DIR__ . '/uploads/bukti_pembayaran/';

if (!is_dir($folder_bukti)) {
    if (!mkdir($folder_bukti, 0755, true) && !is_dir($folder_bukti)) {
        error_log('Gagal membuat folder bukti pembayaran.');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metode = $_POST['metode_pembayaran'] ?? '';

    if (!in_array($metode, ['Transfer', 'QRIS'], true)) {
        $pesan = 'Metode pembayaran tidak valid.';
    } elseif (
        $pembayaran &&
        $pembayaran['status_pembayaran'] === 'Diverifikasi'
    ) {
        $pesan = 'Pembayaran ini sudah diverifikasi.';
    } elseif (
        $pembayaran &&
        $pembayaran['metode_pembayaran'] === 'Tunai'
    ) {
        $pesan = 'Pembayaran tunai sudah dicatat admin.';
    } elseif (
        !isset($_FILES['bukti_pembayaran']) ||
        $_FILES['bukti_pembayaran']['error'] !== UPLOAD_ERR_OK
    ) {
        $pesan = 'Silakan pilih file bukti pembayaran terlebih dahulu.';
    } else {
        $file = $_FILES['bukti_pembayaran'];

        if ($file['size'] > 2 * 1024 * 1024) {
            $pesan = 'Ukuran file maksimal 2 MB.';
        } else {
            // Periksa MIME dari isi file, bukan nama file saja.
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);

            $tipe_diizinkan = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
            ];

            if (!isset($tipe_diizinkan[$mime])) {
                $pesan = 'Bukti harus berupa gambar JPG, PNG, atau WEBP.';
            } elseif (!is_dir($folder_bukti) || !is_writable($folder_bukti)) {
                $pesan = 'Folder upload belum dapat ditulis oleh server.';
            } else {
                $nama_file = bin2hex(random_bytes(16))
                    . '.' . $tipe_diizinkan[$mime];

                $tujuan = $folder_bukti . $nama_file;
                $berhasil_pindah = false;

                try {
                    if (!move_uploaded_file($file['tmp_name'], $tujuan)) {
                        throw new RuntimeException(
                            'Gagal memindahkan file bukti pembayaran.'
                        );
                    }

                    $berhasil_pindah = true;
                    $path_database = 'uploads/bukti_pembayaran/' . $nama_file;

                    mysqli_begin_transaction($conn);

                    // Kunci pengajuan untuk mencegah pemrosesan bersamaan.
                    $stmt = mysqli_prepare(
                        $conn,
                        "SELECT status
                         FROM peminjaman
                         WHERE id_peminjaman = ?
                         FOR UPDATE"
                    );
                    mysqli_stmt_bind_param($stmt, 'i', $id_peminjaman);
                    mysqli_stmt_execute($stmt);

                    $cek = mysqli_fetch_assoc(
                        mysqli_stmt_get_result($stmt)
                    );

                    if (!$cek || $cek['status'] !== 'Diajukan') {
                        throw new RuntimeException(
                            'Status pengajuan sudah berubah.'
                        );
                    }

                    // Periksa ulang pembayaran terbaru di dalam transaksi.
                    $stmt = mysqli_prepare(
                        $conn,
                        "SELECT id_pembayaran, metode_pembayaran,
                                status_pembayaran
                         FROM pembayaran
                         WHERE id_peminjaman = ?
                         ORDER BY id_pembayaran DESC
                         LIMIT 1
                         FOR UPDATE"
                    );
                    mysqli_stmt_bind_param($stmt, 'i', $id_peminjaman);
                    mysqli_stmt_execute($stmt);

                    $terakhir = mysqli_fetch_assoc(
                        mysqli_stmt_get_result($stmt)
                    );

                    if (
                        $terakhir &&
                        (
                            $terakhir['status_pembayaran'] === 'Diverifikasi' ||
                            $terakhir['metode_pembayaran'] === 'Tunai'
                        )
                    ) {
                        throw new RuntimeException(
                            'Pembayaran sudah diproses.'
                        );
                    }

                    if ($terakhir) {
                        $id_bayar = (int) $terakhir['id_pembayaran'];

                        $stmt = mysqli_prepare(
                            $conn,
                            "UPDATE pembayaran
                             SET tgl_bayar = CURDATE(),
                                 total = ?,
                                 metode_pembayaran = ?,
                                 status_pembayaran = 'Menunggu Verifikasi',
                                 keterangan = 'Bukti diunggah customer',
                                 bukti_pembayaran = ?,
                                 tgl_verifikasi = NULL
                             WHERE id_pembayaran = ?"
                        );

                        mysqli_stmt_bind_param(
                            $stmt,
                            'dssi',
                            $total,
                            $metode,
                            $path_database,
                            $id_bayar
                        );
                    } else {
                        $stmt = mysqli_prepare(
                            $conn,
                            "INSERT INTO pembayaran
                             (id_peminjaman, tgl_bayar, total,
                              metode_pembayaran, status_pembayaran,
                              keterangan, bukti_pembayaran)
                             VALUES (?, CURDATE(), ?, ?,
                                     'Menunggu Verifikasi',
                                     'Bukti diunggah customer', ?)"
                        );

                        mysqli_stmt_bind_param(
                            $stmt,
                            'idss',
                            $id_peminjaman,
                            $total,
                            $metode,
                            $path_database
                        );
                    }

                    if (!mysqli_stmt_execute($stmt)) {
                        throw new RuntimeException(
                            'Gagal menyimpan data pembayaran.'
                        );
                    }

                    mysqli_commit($conn);
                    $berhasil_pindah = false;

                    header('Location: pembayaran.php?tersimpan=1');
                    exit;
                } catch (Throwable $e) {
                    try {
                        mysqli_rollback($conn);
                    } catch (Throwable $ignored) {
                        // Tidak ada tindakan tambahan.
                    }

                    if ($berhasil_pindah && is_file($tujuan)) {
                        unlink($tujuan);
                    }

                    error_log('Pembayaran customer: ' . $e->getMessage());
                    $pesan = 'Pembayaran gagal disimpan. Silakan coba kembali.';
                }
            }
        }
    }
}

// Ambil ulang status pembayaran setelah penyimpanan.
$stmt = mysqli_prepare(
    $conn,
    "SELECT *
     FROM pembayaran
     WHERE id_peminjaman = ?
     ORDER BY id_pembayaran DESC
     LIMIT 1"
);

mysqli_stmt_bind_param($stmt, 'i', $id_peminjaman);
mysqli_stmt_execute($stmt);
$pembayaran = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (isset($_GET['tersimpan'])) {
    $pesan = 'Bukti pembayaran berhasil dikirim. Tunggu verifikasi admin.';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran Peminjaman</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 30px 16px;
            background: #FFFBEB;
            color: #332D3B;
            font-family: Arial, sans-serif;
        }

        .card {
            max-width: 620px;
            margin: auto;
            padding: 26px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 5px 24px #332d3b12;
        }

        h1 { color: #8174A0; font-size: 24px; }

        .info {
            padding: 15px;
            background: #F5F0FA;
            border-radius: 10px;
            margin: 16px 0;
            line-height: 1.8;
        }

        label {
            display: block;
            margin: 16px 0 8px;
            font-weight: bold;
        }

        select, input[type=file] {
            width: 100%;
            padding: 12px;
            border: 1px solid #D8CDE4;
            border-radius: 8px;
            background: white;
        }

        button {
            width: 100%;
            margin-top: 20px;
            padding: 13px;
            border: 0;
            border-radius: 9px;
            color: white;
            background: #8174A0;
            font-weight: bold;
            cursor: pointer;
        }

        .pesan {
            padding: 12px;
            border-radius: 8px;
            background: #F5F0FA;
            margin: 12px 0;
        }

        .bukti {
            max-width: 100%;
            max-height: 250px;
            overflow-wrap: anywhere;
        }

        .catatan {
            font-size: 14px;
            line-height: 1.7;
            color: #62596B;
        }
    </style>
</head>
<body>
<div class="card">
    <h1>Pembayaran Peminjaman</h1>

    <?php if ($pesan !== ''): ?>
        <div class="pesan"><?= aman($pesan) ?></div>
    <?php endif; ?>

    <div class="info">
        Nomor pengajuan:
        <strong>#<?= (int) $peminjaman['id_peminjaman'] ?></strong><br>

        Nama:
        <strong><?= aman($peminjaman['nama_peminjam']) ?></strong><br>

        Total pembayaran:
        <strong><?= rupiah($total) ?></strong><br>

        Status pengajuan:
        <strong><?= aman($peminjaman['status']) ?></strong><br>

        Status pembayaran:
        <strong>
            <?= aman($pembayaran['status_pembayaran'] ?? 'Belum Dibayar') ?>
        </strong>
    </div>

    <?php if (
        $pembayaran &&
        in_array(
            $pembayaran['status_pembayaran'],
            ['Diverifikasi', 'Lunas'],
            true
        )
    ): ?>
        <p>Pembayaran telah tercatat. Silakan menunggu proses admin selanjutnya.</p>

    <?php elseif (
        $pembayaran &&
        $pembayaran['metode_pembayaran'] === 'Tunai'
    ): ?>
        <p>Pembayaran tunai sedang ditangani admin.</p>

    <?php else: ?>
        <form method="POST" enctype="multipart/form-data">
            <label for="metode_pembayaran">Metode Pembayaran</label>
            <select name="metode_pembayaran" id="metode_pembayaran" required>
                <option value="">-- Pilih metode --</option>
                <option value="Transfer">Transfer Bank</option>
                <option value="QRIS">QRIS</option>
            </select>

            <div class="info" id="info-transfer" hidden>
                <strong>Informasi Transfer</strong><br>
                Bank: GANTI DENGAN NAMA BANK<br>
                Nomor rekening: GANTI DENGAN NOMOR REKENING<br>
                Atas nama: GANTI DENGAN NAMA PEMILIK REKENING
            </div>

            <div class="info" id="info-qris" hidden>
                <strong>Pembayaran QRIS</strong><br>
                Letakkan gambar QRIS resmi milik penyewaan di folder aset,
                kemudian tampilkan gambarnya di bagian ini.
                <p class="catatan">
                    Jangan gunakan QRIS contoh untuk menerima pembayaran sungguhan.
                </p>
            </div>

            <label for="bukti_pembayaran">Upload Bukti Pembayaran</label>
            <input
                type="file"
                id="bukti_pembayaran"
                name="bukti_pembayaran"
                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                required
            >

            <p class="catatan">
                Format JPG, PNG, atau WEBP. Maksimal 2 MB.
                Bukti akan diperiksa admin.
            </p>

            <button type="submit">Kirim Bukti Pembayaran</button>
        </form>
    <?php endif; ?>
</div>

<script>
const metode = document.getElementById('metode_pembayaran');

if (metode) {
    const bukti = document.getElementById('bukti_pembayaran');
    const transfer = document.getElementById('info-transfer');
    const qris = document.getElementById('info-qris');

    function perbaruiMetode() {
        transfer.hidden = metode.value !== 'Transfer';
        qris.hidden = metode.value !== 'QRIS';
        bukti.required = ['Transfer', 'QRIS'].includes(metode.value);
    }

    metode.addEventListener('change', perbaruiMetode);
    perbaruiMetode();
}
</script>
</body>
</html>
