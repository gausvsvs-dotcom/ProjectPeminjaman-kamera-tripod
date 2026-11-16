
<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

// VALIDASI ID BARANG
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header("Location: index.php");
    exit;
}

// MULAI TRANSAKSI DATABASE
mysqli_begin_transaction($conn);

try {

    // AMBIL DATA BARANG
    $stmt = mysqli_prepare($conn, "
        SELECT id_barang, foto
        FROM barang
        WHERE id_barang = ?
        FOR UPDATE
    ");

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if (!$data) {
        throw new Exception("Data barang tidak ditemukan.");
    }

    // CEK APAKAH BARANG MASIH DIGUNAKAN DALAM TRANSAKSI
    $stmt_cek = mysqli_prepare($conn, "
        SELECT COUNT(*) AS jumlah
        FROM detail_peminjaman
        WHERE id_barang = ?
    ");

    mysqli_stmt_bind_param($stmt_cek, "i", $id);
    mysqli_stmt_execute($stmt_cek);

    $hasil_cek = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_cek));
    mysqli_stmt_close($stmt_cek);

    if ((int) $hasil_cek["jumlah"] > 0) {
        throw new Exception(
            "Barang tidak dapat dihapus karena masih memiliki riwayat peminjaman."
        );
    }

    // HAPUS UNIT BARANG TERLEBIH DAHULU
    $stmt_unit = mysqli_prepare($conn, "
        DELETE FROM barang_unit
        WHERE id_barang = ?
    ");

    mysqli_stmt_bind_param($stmt_unit, "i", $id);
    mysqli_stmt_execute($stmt_unit);
    mysqli_stmt_close($stmt_unit);

    // HAPUS DATA BARANG UTAMA
    $stmt_barang = mysqli_prepare($conn, "
        DELETE FROM barang
        WHERE id_barang = ?
    ");

    mysqli_stmt_bind_param($stmt_barang, "i", $id);
    mysqli_stmt_execute($stmt_barang);

    if (mysqli_stmt_affected_rows($stmt_barang) !== 1) {
        throw new Exception("Data barang gagal dihapus.");
    }

    mysqli_stmt_close($stmt_barang);

    // SIMPAN PERUBAHAN
    mysqli_commit($conn);

    // HAPUS FOTO SETELAH DATABASE BERHASIL DIHAPUS
    if (!empty($data["foto"])) {

        $file = "../uploads/barang/" . basename($data["foto"]);

        if (is_file($file)) {
            unlink($file);
        }
    }

    header("Location: index.php?pesan=hapus");
    exit;

} catch (Exception $e) {

    // BATALKAN SEMUA PERUBAHAN JIKA TERJADI ERROR
    mysqli_rollback($conn);

    die(
        "Data barang gagal dihapus: " .
        htmlspecialchars($e->getMessage(), ENT_QUOTES, "UTF-8") .
        ' <br><a href="index.php">Kembali</a>'
    );
}
?>
