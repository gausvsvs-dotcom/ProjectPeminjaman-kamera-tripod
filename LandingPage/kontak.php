<?php

require_once 'includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama  = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');

    $isAjax = isset($_POST['ajax']) && $_POST['ajax'] === '1';

    // Validasi data
    $error = '';

    if ($nama === '') {
        $error = 'Nama harus diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (!preg_match('/^[A-Za-z0-9._%+-]+@gmail\.com$/', $email)) {
        $error = 'Gunakan alamat email Gmail yang benar.';
    } elseif ($pesan === '') {
        $error = 'Pesan harus diisi.';
    }

    if ($error !== '') {

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'pesan' => $error
        ]);
        exit;
    }

    header("Location: kontak.php?status=error");
    exit;
    }
    
    // Menyimpan pesan ke database
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO kontak (nama, email, pesan)
         VALUES (?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sss",
        $nama,
        $email,
        $pesan
    );

    if (mysqli_stmt_execute($stmt)) {

        mysqli_stmt_close($stmt);

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'pesan' => 'Pesan berhasil terkirim!'
            ]);
            exit;
        }

        header("Location: kontak.php?status=success");
        exit;
    }

    mysqli_stmt_close($stmt);

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'pesan' => 'Pesan gagal terkirim. Silakan coba lagi.'
        ]);
        exit;
    }

    header("Location: kontak.php?status=error");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Peminjaman Kamera & Tripod</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=100">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<script>
function tutupNotifikasi() {
    const notifikasi = document.querySelector(
        '#notifikasiSukses, #notifikasiGagal'
    );

    if (notifikasi) {
        notifikasi.remove();
    }
}
</script>

<body>
    <?php include 'includes/navbar.php' ?>

    <section class="kontak-hero">
        <div class="container">
            <div class="text-center">
                <h1 class="kontak-title">Hubungi Kami</h1>

                <p class="kontak-subtitle">
                    Hubungi kami untuk mendapatkan informasi
                    mengenai peminjaman kamera dan tripod.
                </p>
            </div>
        </div>
    </section>

    <section class="kontak-section py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-5">
                    <h2 class="kontak-heading">Informasi Kontak</h2>

                    <p class="kontak-text">
                        Jika Anda memiliki pertanyaan mengenai
                        kamera, tripod, atau proses peminjaman,
                        silakan hubungi kami melalui informasi
                        berikut.
                    </p>

                    <div class="kontak-info mb-3">
                        <h5><i class="fa-solid fa-location-dot me-2" style="color: #8174A0;"></i> Alamat</h5>
                        <p class="ms-4 ps-1">
                            Jl. SMK PGRI 03 MALANG No. 123
                        </p>
                    </div>

                    <div class="kontak-info mb-3">
                        <h5><i class="fa-brands fa-whatsapp me-2" style="color: #8174A0;"></i> WhatsApp</h5>
                        <p class="ms-4 ps-1">
                            081234557770
                        </p>
                    </div>

                    <div class="kontak-info mb-3">
                        <h5><i class="fa-solid fa-envelope me-2" style="color: #8174A0;"></i> Email</h5>
                        <p class="ms-4 ps-1">
                            info@peminjamank&t.com
                        </p>
                    </div>

                    <div class="kontak-info mb-3">
                        <h5><i class="fa-brands fa-instagram me-2" style="color: #8174A0;"></i> Instagram</h5>
                        <p class="ms-4 ps-1">
                            @PKamera&tripod_offic
                        </p>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="kontak-form">
                        <h2 class="kontak-heading">Hubungi Kami</h2>

                        <div id="areaNotifikasi"></div>

                        <form action="kontak.php" method="POST" id="formKontak">
                            <div class="mb-3">
                                <label class="form-label">
                                    Nama
                                </label>
                                <input type="text"
                                       name="nama"
                                       class="form-control"
                                       placeholder="Masukkan nama Anda" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Email
                                </label>
                                <input type="email"
                                name="email"
                                class="form-control"
                                placeholder="Contoh: nama@gmail.com"
                                pattern="[A-Za-z0-9._%+-]+@gmail\.com"
                                title="Masukkan alamat email Gmail yang lengkap, contoh: nama@gmail.com"required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Pesan
                                </label>
                                <textarea name="pesan"
                                          class="form-control"
                                          rows="5"
                                          placeholder="Tuliskan pesan Anda" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary px-4 rounded-pill">Kirim Pesan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
const formKontak = document.getElementById('formKontak');
const areaNotifikasi = document.getElementById('areaNotifikasi');

// Menampilkan notifikasi
function tampilkanNotifikasi(status, pesan) {

    areaNotifikasi.innerHTML = '';

    const notifikasi = document.createElement('div');

    if (status === 'success') {
        notifikasi.id = 'notifikasiSukses';
        notifikasi.className = 'notifikasi-sukses';
    } else {
        notifikasi.id = 'notifikasiGagal';
        notifikasi.className = 'notifikasi-gagal';
    }

    notifikasi.innerHTML = `
        <div class="notifikasi-isi">
            <div class="notifikasi-teks">
                <strong></strong>
                <p></p>
            </div>

            <div class="notifikasi-icon">
                ${status === 'success' ? '✓' : '✕'}
            </div>
        </div>

        <button type="button"
                class="notifikasi-close"
                onclick="tutupNotifikasi()">
            ×
        </button>
    `;

    notifikasi.querySelector('strong').textContent =
        status === 'success'
        ? 'Pesan berhasil terkirim!'
        : 'Pesan gagal terkirim!';

    notifikasi.querySelector('p').textContent = pesan;

    areaNotifikasi.appendChild(notifikasi);
}

// Mengirim pesan tanpa refresh halaman
formKontak.addEventListener('submit', function(e) {

    e.preventDefault();

    const data = new FormData(formKontak);
    data.append('ajax', '1');

    fetch('kontak.php', {
        method: 'POST',
        body: data
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Server error');
        }

        return response.json();
    })
    .then(hasil => {

        tampilkanNotifikasi(hasil.status, hasil.pesan);

        if (hasil.status === 'success') {
            formKontak.reset();
        }

    })
    .catch(() => {
        tampilkanNotifikasi(
            'error',
            'Maaf, terjadi kesalahan. Silakan coba lagi.'
        );
    });

});
</script>
</body>
</html>