<?php
session_start();
require_once 'includes/koneksi.php';

$error = '';
$chat_terbuka = isset($_SESSION['id_kontak_chat']);
$id_kontak = $_SESSION['id_kontak_chat'] ?? null;
$kontak = null;
$result_chat = null;

// Validasi kode percakapan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kode_chat'])) {
    $kode_chat = trim($_POST['kode_chat']);

    if ($kode_chat === '') {
        $error = "Silakan masukkan kode percakapan.";
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, nama
             FROM kontak
             WHERE kode_chat = ?"
        );

        mysqli_stmt_bind_param($stmt, "s", $kode_chat);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data_kontak = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($data_kontak) {
            $_SESSION['id_kontak_chat'] = $data_kontak['id'];
            $_SESSION['nama_kontak_chat'] = $data_kontak['nama'];

            $id_kontak = $data_kontak['id'];
            $kontak = $data_kontak;
            $chat_terbuka = true;
        } else {
            $error = "Kode percakapan tidak ditemukan.";
        }
    }
}

// Jika chat sudah terbuka, ambil data kontak
if ($chat_terbuka && $id_kontak) {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, nama, pesan, created_at
         FROM kontak
         WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id_kontak);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $kontak = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$kontak) {
        unset($_SESSION['id_kontak_chat']);
        unset($_SESSION['nama_kontak_chat']);

        $chat_terbuka = false;
        $id_kontak = null;
    }
}

// Ambil riwayat chat
if ($chat_terbuka && $id_kontak) {
    $stmt_chat = mysqli_prepare(
        $conn,
        "SELECT pengirim, isi_pesan, waktu_kirim
         FROM pesan_chat
         WHERE id_kontak = ?
         ORDER BY waktu_kirim ASC"
    );

    mysqli_stmt_bind_param($stmt_chat, "i", $id_kontak);
    mysqli_stmt_execute($stmt_chat);

    $result_chat = mysqli_stmt_get_result($stmt_chat);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pesan Saya</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="assets/css/style.css?v=100">
    <link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

<?php include 'includes/navbar.php'; ?>

<section class="pesan-hero">
    <div class="container">
        <span class="pesan-label">CUSTOMER SUPPORT</span>
        <h1>Pusat Bantuan</h1>
        <p>Kami siap membantu menjawab pertanyaan seputar peminjaman kamera dan tripod.</p>
    </div>
</section>

<section class="pesan-section">
    <div class="container">
        <div class="row align-items-center g-5">

            <!-- Bagian kiri: Pusat Bantuan -->
            <div class="col-lg-6">
                <div class="pusat-bantuan">

                    <div class="bantuan-ilustrasi">
                        <i class="fa-solid fa-headset"></i>
                    </div>

                    <h2>Ada yang bisa kami bantu?</h2>

                    <p>
                        Jangan ragu untuk menghubungi kami.
                        Admin akan membantu menjawab pertanyaan
                        dan memberikan informasi yang kamu butuhkan.
                    </p>

                    <div class="bantuan-info">
                        <div class="bantuan-info-item">
                            <i class="fa-solid fa-comments"></i>
                            <div>
                                <strong>Live Chat</strong>
                                <span>Hubungi admin melalui room chat.</span>
                            </div>
                        </div>

                        <div class="bantuan-info-item">
                            <i class="fa-solid fa-camera"></i>
                            <div>
                                <strong>Informasi Peminjaman</strong>
                                <span>Tanyakan ketersediaan kamera dan tripod.</span>
                            </div>
                        </div>

                        <div class="bantuan-info-item">
                            <i class="fa-solid fa-clock"></i>
                            <div>
                                <strong>Jam Operasional</strong>
                                <span>Senin–Sabtu, 08.00–16.00 WIB</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Bagian kanan: Petunjuk chat -->
            <div class="col-lg-6">
                <div class="pesan-card">

                    <div class="pesan-card-icon">
                        <i class="fa-regular fa-message"></i>
                    </div>

                    <h3>Mulai Percakapan</h3>

                    <p>
                        Masukkan kode percakapan yang kamu dapatkan
                        setelah mengirim pesan melalui halaman Kontak.
                    </p>

                    <?php if ($error !== '') : ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">

                        <label class="form-label">Kode Percakapan</label>

                        <input type="text"
                               name="kode_chat"
                               class="form-control pesan-input"
                               placeholder="Contoh: 1c543f8d84"
                               required>

                        <button type="submit"
                                class="btn pesan-btn w-100 rounded-pill">
                            Buka Room Chat
                            <i class="fa-solid fa-arrow-right ms-2"></i>
                        </button>

                    </form>

                    <div class="pesan-card-footer">
                        <i class="fa-solid fa-lock me-1"></i>
                        Percakapan kamu bersifat pribadi.
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>


</body>
</html>
