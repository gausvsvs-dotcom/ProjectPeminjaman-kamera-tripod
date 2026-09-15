<?php
include 'koneksi.php';

$data_peminjaman = null;
$error_msg = "";
$denda = 0;
$keterlambatan = 0;

// 1. CARI DATA PEMINJAMAN BERDASARKAN KODE
if (isset($_GET['kode']) && !empty($_GET['kode'])) {
    $kode_cari = mysqli_real_escape_string($koneksi, $_GET['kode']);
    $query_cari = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE kode_peminjaman = '$kode_cari' AND status = 'dipinjam'");
    
    if (mysqli_num_rows($query_cari) > 0) {
        $data_peminjaman = mysqli_fetch_array($query_cari);
        
        // Hitung denda jika melewati tanggal jatuh tempo
        $tgl_kembali_real = date('Y-m-d');
        $tgl_jatuh_tempo = $data_peminjaman['tanggal_rencana_kembali'];
        
        if ($tgl_kembali_real > $tgl_jatuh_tempo) {
            $selisih = strtotime($tgl_kembali_real) - strtotime($tgl_jatuh_tempo);
            $keterlambatan = floor($selisih / (60 * 60 * 24)); // dalam hitungan hari
            $tarif_denda = 5000; // Contoh: Denda Rp 5.000 / hari
            $denda = $keterlambatan * $tarif_denda;
        }
    } else {
        $error_msg = "Kode peminjaman tidak ditemukan atau barang sudah dikembalikan!";
    }
}

// 2. LOGIKA PROSES PENGEMBALIAN
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['proses_kembali'])) {
    $id_peminjaman = $_POST['id_peminjaman'];
    $tgl_dikembalikan = $_POST['tgl_dikembalikan'];
    $denda_dibayar = (int)$_POST['denda'];
    $keterangan_tambahan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    
    // Ambil data barang dari keterangan peminjaman untuk update stok
    $q_info = mysqli_query($koneksi, "SELECT keterangan FROM peminjaman WHERE id_peminjaman = '$id_peminjaman'");
    $info = mysqli_fetch_array($q_info);
    
    // Extract ID barang dan Jumlah dari teks keterangan
    $id_barang = "";
    $jumlah_kembali = 1;
    if (preg_match('/ID Barang:\s*(\w+)/', $info['keterangan'], $match_id)) {
        $id_barang = $match_id[1];
    }
    if (preg_match('/Jumlah:\s*(\d+)/', $info['keterangan'], $match_jml)) {
        $jumlah_kembali = (int)$match_jml[1];
    }

    // Update status peminjaman menjadi 'dikembalikan'
    $ket_akhir = $info['keterangan'] . " | Dikembalikan Tgl: " . $tgl_dikembalikan . " | Denda: Rp" . number_format($denda_dibayar,0,',','.') . " | Ket: " . $keterangan_tambahan;
    
    $update_peminjaman = mysqli_query($koneksi, "UPDATE peminjaman SET 
                                                 status = 'dikembalikan', 
                                                 keterangan = '$ket_akhir' 
                                                 WHERE id_peminjaman = '$id_peminjaman'");

    if ($update_peminjaman) {
        // Kembalikan stok barang ke tabel barang
        if (!empty($id_barang)) {
            mysqli_query($koneksi, "UPDATE barang SET stok = stok + $jumlah_kembali WHERE id_barang = '$id_barang'");
        }

        echo "<script>
            alert('Pengembalian Barang Berhasil Diproses!');
            window.location.href = 'pengembalian.php';
        </script>";
        exit;
    } else {
        echo "<script>alert('Gagal memproses pengembalian: " . addslashes(mysqli_error($koneksi)) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form & Daftar Pengembalian Kamera & Tripod</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #FFFBEB !important;
            color: #333333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card-custom {
            background-color: #EFB6C8 !important;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .card-header-custom {
            background-color: #8174A0 !important;
            color: #FFFFFF !important;
            padding: 18px 24px;
            border-bottom: none;
        }

        .card-body-custom {
            padding: 28px;
        }

        .form-label-custom {
            color: #333333 !important;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .form-control-custom,
        .form-select-custom {
            background-color: #A888B5 !important;
            color: #FFFFFF !important;
            border: 1px solid #91719E !important;
            border-radius: 8px;
            padding: 10px 14px;
        }

        .form-control-custom::placeholder {
            color: #E2D4E7 !important;
        }

        .form-control-custom[readonly] {
            background-color: #A888B5 !important;
            color: #FFFFFF !important;
            opacity: 0.9;
        }

        hr.custom-hr {
            border-top: 1px solid #D8A0B2;
            opacity: 1;
            margin: 25px 0;
        }

        .btn-simpan, .btn-batal, .btn-cari {
            background-color: #8174A0 !important;
            color: #FFFFFF !important;
            border: none !important;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s ease-in-out;
        }

        .btn-simpan:hover, .btn-batal:hover, .btn-cari:hover {
            background-color: #6C5F87 !important;
            color: #FFFFFF !important;
        }

        .table-custom {
            border-radius: 8px;
            overflow: hidden;
        }

        .table-custom thead {
            background-color: #8174A0 !important;
            color: #FFFFFF !important;
        }

        .table-custom tbody tr {
            background-color: #FFFFFF !important;
        }
    </style>
</head>
<body>

<div class="container my-5">
    
    <!-- CARD 1: CARI KODE PEMINJAMAN -->
    <div class="card card-custom mb-4">
        <div class="card-header card-header-custom">
            <h4 class="mb-0 fw-semibold">Cari Data Peminjaman</h4>
        </div>
        <div class="card-body card-body-custom">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-9">
                    <label class="form-label form-label-custom">Masukkan Kode Peminjaman (Contoh: P20260212001)</label>
                    <input type="text" name="kode" class="form-control form-control-custom" placeholder="Ketik Kode Peminjaman..." value="<?= isset($_GET['kode']) ? $_GET['kode'] : ''; ?>" required>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-cari w-100">Cari Peminjaman</button>
                </div>
            </form>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger mt-3 mb-0"><?= $error_msg; ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- CARD 2: FORM PROSES PENGEMBALIAN (Muncul jika kode ditemukan) -->
    <?php if ($data_peminjaman): ?>
    <div class="card card-custom mb-5">
        <div class="card-header card-header-custom">
            <h4 class="mb-0 fw-semibold">Form Pengembalian Barang</h4>
        </div>
        <div class="card-body card-body-custom">
            <form action="" method="POST">
                <input type="hidden" name="id_peminjaman" value="<?= $data_peminjaman['id_peminjaman']; ?>">

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label form-label-custom">Kode Peminjaman</label>
                        <input type="text" class="form-control form-control-custom" value="<?= $data_peminjaman['kode_peminjaman']; ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label form-label-custom">Nama Peminjam</label>
                        <input type="text" class="form-control form-control-custom" value="<?= $data_peminjaman['nama_peminjam']; ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label form-label-custom">Tanggal Pinjam</label>
                        <input type="text" class="form-control form-control-custom" value="<?= date('d-m-Y', strtotime($data_peminjaman['tanggal_peminjaman'])); ?>" readonly>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label form-label-custom">Tanggal Jatuh Tempo</label>
                        <input type="text" class="form-control form-control-custom" value="<?= date('d-m-Y', strtotime($data_peminjaman['tanggal_rencana_kembali'])); ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label form-label-custom">Tanggal Kembali (Hari Ini)</label>
                        <input type="date" name="tgl_dikembalikan" class="form-control form-control-custom" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label form-label-custom">Denda (Terlambat <?= $keterlambatan; ?> Hari)</label>
                        <input type="number" name="denda" class="form-control form-control-custom" value="<?= $denda; ?>">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label form-label-custom">Kondisi / Keterangan Barang</label>
                    <input type="text" name="keterangan" class="form-control form-control-custom" placeholder="Contoh: Lengkap, kondisi baik / lensa tergores..." required>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" name="proses_kembali" class="btn btn-simpan">Proses Pengembalian</button>
                    <a href="pengembalian.php" class="btn btn-batal">Batal</a>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- CARD 3: TABEL RIWAYAT PENGEMBALIAN -->
    <div class="card card-custom">
        <div class="card-header card-header-custom">
            <h4 class="mb-0 fw-semibold">Riwayat Barang Dikembalikan</h4>
        </div>
        <div class="card-body card-body-custom">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Kode Peminjaman</th>
                            <th>Nama Peminjam</th>
                            <th>Tanggal Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                            <th>Keterangan Pengembalian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query_kembali = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE status = 'dikembalikan' ORDER BY id_peminjaman DESC");

                        if ($query_kembali && mysqli_num_rows($query_kembali) > 0) {
                            while ($row = mysqli_fetch_array($query_kembali)) {
                                echo "<tr>";
                                echo "<td><strong>" . $row['kode_peminjaman'] . "</strong></td>";
                                echo "<td>" . $row['nama_peminjam'] . "</td>";
                                echo "<td>" . date('d-m-Y', strtotime($row['tanggal_peminjaman'])) . "</td>";
                                echo "<td>" . date('d-m-Y', strtotime($row['tanggal_rencana_kembali'])) . "</td>";
                                echo "<td><span class='badge bg-success'>" . ucfirst($row['status']) . "</span></td>";
                                echo "<td><small>" . $row['keterangan'] . "</small></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center text-muted py-3'>Belum ada data pengembalian barang.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>