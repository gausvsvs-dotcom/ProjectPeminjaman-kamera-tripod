<?php
include 'koneksi.php';

// 1. LOGIKA SIMPAN DATA (Jika tombol simpan diklik)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['simpan'])) {
    
    $kode_peminjaman         = isset($_POST['id_peminjaman']) ? $_POST['id_peminjaman'] : ('PMJ-' . date('YmdHis'));
    $id_pengguna             = !empty($_POST['id_pengguna']) ? $_POST['id_pengguna'] : '1'; 
    $id_barang               = isset($_POST['id_barang']) ? $_POST['id_barang'] : '';
    $nama_peminjam           = isset($_POST['nama_peminjam']) ? mysqli_real_escape_string($koneksi, $_POST['nama_peminjam']) : '';
    $tanggal_peminjaman      = isset($_POST['tgl_pinjam']) ? $_POST['tgl_pinjam'] : date('Y-m-d');
    $tanggal_rencana_kembali = isset($_POST['tgl_jatuh_tempo']) ? $_POST['tgl_jatuh_tempo'] : '';
    $jumlah                  = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 1;
    $jenis_peminjaman        = 'offline';
    $status                  = 'dipinjam';
    
    $keterangan              = "ID Barang: " . $id_barang . " | Jumlah: " . $jumlah;

    // QUERY INSERT DENGAN MENGIRIM $id_pengguna
    $query = "INSERT INTO peminjaman 
              (kode_peminjaman, id_pengguna, nama_peminjam, tanggal_peminjaman, tanggal_rencana_kembali, jenis_peminjaman, status, keterangan) 
              VALUES 
              ('$kode_peminjaman', '$id_pengguna', '$nama_peminjam', '$tanggal_peminjaman', '$tanggal_rencana_kembali', '$jenis_peminjaman', '$status', '$keterangan')";

    $simpan = mysqli_query($koneksi, $query);

    if ($simpan) {
        if (!empty($id_barang)) {
            mysqli_query($koneksi, "UPDATE barang SET stok = stok - $jumlah WHERE id_barang = '$id_barang'");
        }

        echo "<script>
            alert('Peminjaman Berhasil Disimpan!');
            window.location.href = '" . $_SERVER['PHP_SELF'] . "';
        </script>";
        exit;
    } else {
        echo "<script>
            alert('Gagal menyimpan data! Error: " . addslashes(mysqli_error($koneksi)) . "');
        </script>";
    }
}

// 2. KODE PENGENAAN ID OTOMATIS DAN TANGGAL DEFAULT
$kolom_id_utama = "kode_peminjaman"; 
$today_ymd = date("Ymd");
$query_max_id = mysqli_query($koneksi, "SELECT max($kolom_id_utama) as max_id FROM peminjaman WHERE $kolom_id_utama LIKE 'P$today_ymd%'");
$data_max_id = mysqli_fetch_array($query_max_id);
$last_id = isset($data_max_id['max_id']) ? $data_max_id['max_id'] : '';

$no_urut = (int) substr($last_id, 9, 3);
$no_urut++;
$id_peminjaman_otomatis = "P" . $today_ymd . sprintf("%03s", $no_urut);

$tgl_pinjam_default = date('Y-m-d');
$tgl_jatuh_tempo_default = date('Y-m-d', strtotime('+7 days'));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form & Daftar Peminjaman Kamera & Tripod</title>
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

        .form-select-custom option {
            background-color: #A888B5;
            color: #FFFFFF;
        }

        .form-control-custom:focus,
        .form-select-custom:focus {
            box-shadow: 0 0 0 0.25rem rgba(129, 116, 160, 0.4) !important;
            border-color: #8174A0 !important;
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

        .btn-simpan, .btn-batal {
            background-color: #8174A0 !important;
            color: #FFFFFF !important;
            border: none !important;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s ease-in-out;
        }

        .btn-simpan:hover, .btn-batal:hover {
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
    <!-- CARD 1: FORM PEMINJAMAN -->
    <div class="card card-custom mb-5">
        <div class="card-header card-header-custom">
            <h4 class="mb-0 fw-semibold">Form Peminjaman Kamera & Tripod</h4>
        </div>
        
        <div class="card-body card-body-custom">
            <form action="" method="POST">
                
                <div class="row g-3 mb-2">
                    <div class="col-md-4">
                        <label class="form-label form-label-custom">ID / No. Peminjaman</label>
                        <input type="text" name="id_peminjaman" class="form-control form-control-custom" value="<?= $id_peminjaman_otomatis; ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label form-label-custom">Tanggal Pinjam</label>
                        <input type="date" name="tgl_pinjam" class="form-control form-control-custom" value="<?= $tgl_pinjam_default; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label form-label-custom">Tanggal Jatuh Tempo</label>
                        <input type="date" name="tgl_jatuh_tempo" class="form-control form-control-custom" value="<?= $tgl_jatuh_tempo_default; ?>">
                    </div>
                </div>

                <hr class="custom-hr">

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Nama Peminjam</label>
                        <input type="text" name="nama_peminjam" class="form-control form-control-custom" placeholder="Masukkan nama peminjam..." required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Pilih Barang (Kamera/Tripod)</label>
                        <select name="id_barang" class="form-select form-select-custom" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php
                            $query_barang = mysqli_query($koneksi, "SELECT * FROM barang");
                            if ($query_barang && mysqli_num_rows($query_barang) > 0) {
                                while ($b = mysqli_fetch_array($query_barang)) {
                                    $id_b = isset($b['id_barang']) ? $b['id_barang'] : $b[0];
                                    $nama_b = isset($b['nama_barang']) ? $b['nama_barang'] : (isset($b['nama']) ? $b['nama'] : $b[1]);
                                    $stok_b = isset($b['stok']) ? $b['stok'] : '-';
                                    echo "<option value='".$id_b."'>".$id_b." - ".$nama_b." (Stok: ".$stok_b.")</option>";
                                }
                            } else {
                                echo "<option value=''>Data Barang Kosong / Tabel Tidak Ditemukan</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" name="simpan" class="btn btn-simpan">Simpan Peminjaman</button>
                    <a href="" class="btn btn-batal">Batal / Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- CARD 2: TABEL DAFTAR DATA PEMINJAMAN -->
    <div class="card card-custom">
        <div class="card-header card-header-custom">
            <h4 class="mb-0 fw-semibold">Daftar Peminjaman</h4>
        </div>
        <div class="card-body card-body-custom">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Kode Peminjaman</th>
                            <th>Tanggal Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Nama Peminjam</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query_tampil = mysqli_query($koneksi, "SELECT peminjaman.*, pengguna.nama AS nama_peminjam_user 
                                                                FROM peminjaman 
                                                                LEFT JOIN pengguna ON peminjaman.id_pengguna = pengguna.id_pengguna 
                                                                ORDER BY peminjaman.id_peminjaman DESC");

                        if ($query_tampil && mysqli_num_rows($query_tampil) > 0) {
                            while ($row = mysqli_fetch_array($query_tampil)) {
                                $peminjam = !empty($row['nama_peminjam']) ? $row['nama_peminjam'] : $row['nama_peminjam_user'];
                                
                                echo "<tr>";
                                echo "<td><strong>" . $row['kode_peminjaman'] . "</strong></td>";
                                echo "<td>" . date('d-m-Y', strtotime($row['tanggal_peminjaman'])) . "</td>";
                                echo "<td>" . date('d-m-Y', strtotime($row['tanggal_rencana_kembali'])) . "</td>";
                                echo "<td>" . $peminjam . "</td>";
                                echo "<td><span class='badge bg-warning text-dark'>" . ucfirst($row['status']) . "</span></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center text-muted py-3'>Belum ada data peminjaman yang tersimpan.</td></tr>";
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