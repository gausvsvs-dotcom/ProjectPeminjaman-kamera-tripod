<?php
require_once "config/koneksi.php";

header("Content-Type: application/json; charset=UTF-8");

$keyword_asli = trim($_GET['q'] ?? '');

if ($keyword_asli === '') {
    echo json_encode([]);
    exit;
}

$keyword = "%" . $keyword_asli . "%";
$data = [];
$stmt_barang = mysqli_prepare($conn, "
    SELECT id_barang, kode_barang, nama_barang, jenis_barang
    FROM barang
    WHERE kode_barang LIKE ?
       OR nama_barang LIKE ?
       OR jenis_barang LIKE ?
       OR deskripsi LIKE ?
    ORDER BY nama_barang ASC");

mysqli_stmt_bind_param(
    $stmt_barang,
    "ssss",
    $keyword,
    $keyword,
    $keyword,
    $keyword);

mysqli_stmt_execute($stmt_barang);

$result_barang = mysqli_stmt_get_result($stmt_barang);

while ($row = mysqli_fetch_assoc($result_barang)) {
    $data[] = [
        "kategori" => "Barang",
        "nama" => $row['nama_barang'],
        "kode" => $row['kode_barang'],
        "jenis" => ucfirst($row['jenis_barang']),
        "url" => "barang/detail.php?id=" . $row['id_barang']
    ];
}

mysqli_stmt_close($stmt_barang);

$stmt_unit = mysqli_prepare($conn, "
    SELECT
        bu.id_barang,
        bu.kode_unit,
        bu.keterangan_kondisi,
        bu.status_unit,
        b.nama_barang,
        kb.nama_kondisi
    FROM barang_unit bu
    INNER JOIN barang b
        ON bu.id_barang = b.id_barang
    LEFT JOIN kondisi_barang kb
        ON bu.id_kondisi = kb.id_kondisi
    WHERE bu.kode_unit LIKE ?
       OR bu.keterangan_kondisi LIKE ?
       OR bu.status_unit LIKE ?
       OR b.nama_barang LIKE ?
       OR b.kode_barang LIKE ?
       OR kb.nama_kondisi LIKE ?
    ORDER BY b.nama_barang ASC, bu.kode_unit ASC");

mysqli_stmt_bind_param(
    $stmt_unit,
    "ssssss",
    $keyword,
    $keyword,
    $keyword,
    $keyword,
    $keyword,
    $keyword);

mysqli_stmt_execute($stmt_unit);

$result_unit = mysqli_stmt_get_result($stmt_unit);

while ($row = mysqli_fetch_assoc($result_unit)) {
    $data[] = [
        "kategori" => "Unit Barang",
        "nama" => $row['nama_barang'],
        "kode" => $row['kode_unit'],
        "jenis" => "Unit - " . $row['status_unit'],
        "url" => "barang/unit.php?id=" . $row['id_barang']
    ];
}

mysqli_stmt_close($stmt_unit);

$stmt_kategori = mysqli_prepare($conn, "
    SELECT id_kategori, nama_kategori, keterangan
    FROM kategori
    WHERE nama_kategori LIKE ?
       OR keterangan LIKE ?
    ORDER BY nama_kategori ASC");

mysqli_stmt_bind_param(
    $stmt_kategori,
    "ss",
    $keyword,
    $keyword);

mysqli_stmt_execute($stmt_kategori);

$result_kategori = mysqli_stmt_get_result($stmt_kategori);

while ($row = mysqli_fetch_assoc($result_kategori)) {
    $data[] = [
        "kategori" => "Kategori",
        "nama" => $row['nama_kategori'],
        "kode" => "Kategori",
        "jenis" => "Data Kategori",
        "url" => "kategori/detail.php?id=" . $row['id_kategori']
    ];
}

mysqli_stmt_close($stmt_kategori);

$stmt_kondisi = mysqli_prepare($conn, "
    SELECT id_kondisi, nama_kondisi, keterangan
    FROM kondisi_barang
    WHERE nama_kondisi LIKE ?
       OR keterangan LIKE ?
    ORDER BY nama_kondisi ASC");

mysqli_stmt_bind_param(
    $stmt_kondisi,
    "ss",
    $keyword,
    $keyword);

mysqli_stmt_execute($stmt_kondisi);

$result_kondisi = mysqli_stmt_get_result($stmt_kondisi);

while ($row = mysqli_fetch_assoc($result_kondisi)) {
    $data[] = [
        "kategori" => "Kondisi Barang",
        "nama" => $row['nama_kondisi'],
        "kode" => "Kondisi",
        "jenis" => $row['keterangan'],
        "url" => "kondisi_barang/detail.php?id=" . $row['id_kondisi']
    ];
}

mysqli_stmt_close($stmt_kondisi);

$stmt_merk = mysqli_prepare($conn, "
    SELECT id_merk, nama_merk, keterangan
    FROM merk
    WHERE nama_merk LIKE ?
       OR keterangan LIKE ?
    ORDER BY nama_merk ASC");

mysqli_stmt_bind_param(
    $stmt_merk,
    "ss",
    $keyword,
    $keyword);

mysqli_stmt_execute($stmt_merk);

$result_merk = mysqli_stmt_get_result($stmt_merk);

while ($row = mysqli_fetch_assoc($result_merk)) {
    $data[] = [
        "kategori" => "Merk",
        "nama" => $row['nama_merk'],
        "kode" => "Merk",
        "jenis" => $row['keterangan'] ?? "-",
        "url" => "merk/detail.php?id=" . $row['id_merk']
    ];
}

mysqli_stmt_close($stmt_merk);

$stmt_pengguna = mysqli_prepare($conn, "
    SELECT id_user, nama_user, username, role
    FROM pengguna
    WHERE nama_user LIKE ?
       OR username LIKE ?
       OR role LIKE ?
    ORDER BY nama_user ASC");

mysqli_stmt_bind_param(
    $stmt_pengguna,
    "sss",
    $keyword,
    $keyword,
    $keyword);

mysqli_stmt_execute($stmt_pengguna);

$result_pengguna = mysqli_stmt_get_result($stmt_pengguna);

while ($row = mysqli_fetch_assoc($result_pengguna)) {
    $data[] = [
        "kategori" => "Pengguna",
        "nama" => $row['nama_user'],
        "kode" => $row['username'],
        "jenis" => ucfirst($row['role']),
        "url" => "pengguna/detail.php?id=" . $row['id_user']
    ];
}

mysqli_stmt_close($stmt_pengguna);

echo json_encode($data);
