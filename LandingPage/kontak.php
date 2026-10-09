<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Peminjaman Kamera & Tripod</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

    <link rel="stylesheet"href="assets/css/style.css">

</head>

<script>
function tutupNotifikasi() {
    const notifikasi = document.getElementById('notifikasiSukses');

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
                <h1 class="kontak-title">Kontak Kami</h1>

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

                    <div class="kontak-info">
                        <h5>Alamat</h5>
                        <p>
                            Jl. SMK PGRI 03 MALANG No. 123
                        </p>
                    </div>

                    <div class="kontak-info">
                        <h5>Telepon</h5>

                        <p>
                            081234557770</p>
                    </div>

                    <div class="kontak-info">
                        <h5>Email</h5>

                        <p>
                            info@peminjamankamera.com
                        </p>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="kontak-form">
                        <h2 class="kontak-heading"> Hubungi Kami</h2>

                        <?php 
                        if (isset($_GET['status']) && $_GET['status'] == 'success') : ?>
                        
                        <div id="notifikasiSukses" class="notifikasi-sukses">
                            <div class="notifikasi-isi">
                                
                            <div class="notifikasi-teks">
                                <strong>Pesan berhasil dikirim!</strong>
                                <p>Terima kasih, pesan Anda telah kami terima.</p>
                            </div>

                    <div class="notifikasi-icon">✓</div>

                </div>

                <button type="button"
                        class="notifikasi-close"
                        onclick="tutupNotifikasi()">
                    ×
                </button>

            </div>

        <?php endif; ?>

                        <form action="proses_kontak.php" method="POST">
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
                                       placeholder="Masukkan email Anda" required>

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

                            <button type="submit" class="btn btn-primary px-4 rounded-pill"> Kirim Pesan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>