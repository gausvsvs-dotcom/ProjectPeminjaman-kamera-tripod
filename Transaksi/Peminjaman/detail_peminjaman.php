
<?php
session_start();

require_once "koneksi.php";

// Ambil ID peminjaman dari URL
$id_peminjaman = $_GET['id'] ?? '';

if (empty($id_peminjaman)) {
    echo "<script>
            alert('ID peminjaman tidak ditemukan!');
            window.location.href = 'index.php';
          </script>";
    exit;
}

// Ambil data peminjaman berdasarkan ID
$query = "SELECT * FROM peminjaman WHERE id_peminjaman = ?";
$stmt = mysqli_prepare($koneksi, $query);

mysqli_stmt_bind_param($stmt, "i", $id_peminjaman);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "<script>
            alert('Data peminjaman tidak ditemukan!');
            window.location.href = 'index.php';
          </script>";
    exit;
}

// Fungsi format tanggal Indonesia
function formatTanggal($tanggal)
{
    if (empty($tanggal) || $tanggal == '0000-00-00') {
        return '-';
    }

    return date('d-m-Y', strtotime($tanggal));
}

// Fungsi untuk mengamankan output
function aman($data)
{
    return htmlspecialchars($data ?? '-', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Peminjaman</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="style_peminjaman.css">
</head>

<body>

    <div class="container-detail">

        <!-- Header -->
        <div class="detail-header">
            <div class="header-icon">
                <i class="bi bi-clipboard-check"></i>
            </div>

            <div>
                <h2>Detail Peminjaman</h2>
                <p>Informasi lengkap pengajuan peminjaman barang</p>
            </div>
        </div>

        <!-- Body -->
        <div class="detail-body">

            <!-- ID Peminjaman -->
            <div class="id-peminjaman">
                <span>ID Peminjaman</span>
                <strong>#<?= aman($data['id_peminjaman']); ?></strong>
            </div>

            <!-- Data Peminjam -->
            <h3 class="section-title">
                <i class="bi bi-person-fill"></i>
                Data Peminjam
            </h3>

            <div class="detail-group">
                <div class="detail-item">
                    <span>Nama Peminjam</span>
                    <strong><?= aman($data['nama_peminjam']); ?></strong>
                </div>

                <div class="detail-item">
                    <span>Nomor HP</span>
                    <strong><?= aman($data['no_hp']); ?></strong>
                </div>

                <div class="detail-item">
                <span>Alamat</span>
                <strong><?= aman($data['Alamat']); ?></strong>
                </div>
            </div>

            <!-- Informasi Peminjaman -->
            <h3 class="section-title">
                <i class="bi bi-calendar-check"></i>
                Informasi Peminjaman
            </h3>

            <div class="detail-group">
                <div class="detail-item">
                    <span>Tanggal Peminjaman</span>
                    <strong><?= formatTanggal($data['tgl_peminjaman']); ?></strong>
                </div>

                <div class="detail-item">
                    <span>Tanggal Jatuh Tempo</span>
                    <strong><?= formatTanggal($data['tgl_jatuh_tempo']); ?></strong>
                </div>

                <div class="detail-item">
                    <span>Status Peminjaman</span>

                    <?php
                    $status = $data['status'] ?? 'Menunggu Konfirmasi';

                    $classStatus = 'status-menunggu';

                    if ($status == 'Disetujui') {
                        $classStatus = 'status-disetujui';
                    } elseif ($status == 'Ditolak') {
                        $classStatus = 'status-ditolak';
                    } elseif ($status == 'Dipinjam') {
                        $classStatus = 'status-dipinjam';
                    } elseif ($status == 'Dikembalikan') {
                        $classStatus = 'status-dikembalikan';
                    }
                    ?>

                    <span class="status-badge <?= $classStatus; ?>">
                        <?= aman($status); ?>
                    </span>
                </div>
            </div>

            <!-- Tombol Kembali -->
            <a href="index.php" class="btn-kembali">
                <i class="bi bi-arrow-left"></i>
                Kembali
            </a>

        </div>
    </div>

</body>
</html>
