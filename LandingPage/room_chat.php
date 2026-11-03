<?php
session_start();
require_once 'includes/koneksi.php';

// Memastikan konsumen sudah memasukkan kode chat
if (!isset($_SESSION['id_kontak_chat'])) {
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $isi_pesan = trim($_POST['isi_pesan'] ?? '');

        if ($isi_pesan === '') {
            $error = "Pesan tidak boleh kosong.";
        } else {
            $stmt_kirim = mysqli_prepare(
                $conn,
                "INSERT INTO pesan_chat (id_kontak, pengirim, isi_pesan)
                VALUES (?, 'konsumen', ?)"
            );

            mysqli_stmt_bind_param(
                $stmt_kirim,
                "is",
                $id_kontak,
                $isi_pesan
            );

            if (mysqli_stmt_execute($stmt_kirim)) {
                mysqli_stmt_close($stmt_kirim);

                header("Location: room_chat.php");
                exit;
            } else {
                $error = "Pesan gagal dikirim. Silakan coba lagi.";
                mysqli_stmt_close($stmt_kirim);
            }
        }
    }

    header("Location: pesan_saya.php");
    exit;
}

$id_kontak = $_SESSION['id_kontak_chat'];

// Mengambil data kontak
$stmt = mysqli_prepare(
    $conn,
    "SELECT nama, pesan, created_at
     FROM kontak
     WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id_kontak);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$kontak = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$kontak) {
    session_destroy();
    header("Location: pesan_saya.php");
    exit;
}

// Mengambil seluruh riwayat percakapan
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
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Room Chat</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="assets/css/style.css?v=100">
    <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>

<?php include 'includes/navbar.php'; ?>

<section class="kontak-hero">
    <div class="container text-center">
        <h1 class="kontak-title">Room Chat</h1>

        <p class="kontak-subtitle">
            Halo, <?= htmlspecialchars($kontak['nama']); ?>!
            Silakan lihat percakapan Anda dengan admin.
        </p>
    </div>
</section>

<section class="container py-5">

    <div class="kontak-form mx-auto" style="max-width: 750px;">

        <h2 class="kontak-heading">Percakapan dengan Admin</h2>

        <?php if ($error !== '') : ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>


        <div class="room-chat-box"
             style="background: #f8f5fa; padding: 20px; border-radius: 15px;">

            <!-- Pesan awal konsumen -->
            <div class="chat-bubble konsumen">
                <strong>Anda</strong>

                <p>
                    <?= nl2br(htmlspecialchars($kontak['pesan'])); ?>
                </p>

                <small>
                    <?= htmlspecialchars($kontak['created_at']); ?>
                </small>
            </div>

            <!-- Riwayat balasan admin dan konsumen -->
            <?php while ($chat = mysqli_fetch_assoc($result_chat)) : ?>

                <div class="chat-bubble <?= $chat['pengirim'] === 'admin' ? 'admin' : 'konsumen'; ?>">

                    <strong>
                        <?= $chat['pengirim'] === 'admin' ? 'Admin' : 'Anda'; ?>
                    </strong>

                    <p>
                        <?= nl2br(htmlspecialchars($chat['isi_pesan'])); ?>
                    </p>

                    <small>
                        <?= htmlspecialchars($chat['waktu_kirim']); ?>
                    </small>

                </div>

            <?php endwhile; ?>

        </div>


        <div class="mt-4">

            <form action="" method="POST">

                <div class="mb-3">
                    <label for="isi_pesan" class="form-label">
                        Tulis Pesan
                    </label>

                    <textarea
                        name="isi_pesan"
                        id="isi_pesan"
                        class="form-control"
                        rows="3"
                        placeholder="Ketik pesan Anda di sini..."
                        required></textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center">

                    <a href="pesan_saya.php"
                    class="btn btn-outline-secondary rounded-pill px-4">
                        Kembali
                    </a>

                    <button type="submit"
                            class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-send-fill"></i>
                        Kirim Pesan
                    </button>

                </div>

            </form>

        </div>


    </div>

</section>

</body>
</html>
