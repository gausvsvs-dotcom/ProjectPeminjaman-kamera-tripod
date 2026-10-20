```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Sistem Peminjaman Kamera</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=101">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/navbar.php' ?>

    <section class="tentang-hero">
        <div class="container">
            <div class="text-center">
                <h1 class="tentang-title">Tentang Kami</h1>
                <p class="tentang-subtitle">~~ Sistem Peminjaman Kamera & Tripod ~~</p>
            </div>
        </div>
    </section>

    <!-- 1. Sistem Peminjaman: Gambar kiri, teks kanan -->
    <section class="tentang-section py-5">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="tentang-image">
                        <img src="assets/images/gambar1.png" alt="kamera dan tripod" class="img-fluid">
                    </div>
                </div>

                <div class="col-lg-6">
                    <h2 class="tentang-heading">Sistem Peminjaman Kamera & Tripod</h2>
                    <p class="tentang-text">
                        Kami membuat Website ini untuk memudahkan para pengguna 
                        untuk melihat informasi kamera dan tripod yang 
                        tersedia untuk dipinjam.</p> 

                    <p class="tentang-text">
                        Pengguna dapat melihat berbagai macam kamera dan tripod 
                        yang tersedia, mengetahui kondisi barang, serta mendapatkan
                        informasi detail tentang barang yang akan dipinjam.
                    </p>

                    <p class="tentang-text">
                        Dengan adanyaa Sistem Peminjaman Kamera & Tripod,
                        proses pencarian dan peminjaman barang menjadi
                        lebih mudah, cepat, dan terorganisir.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="tentang-section py-5">
        <div class="container">
            <div class="row align-items-center g-5 flex-lg-row-reverse">
                <div class="col-lg-6">
                    <div class="tentang-image">
                        <img src="assets/images/peminjaman.jpg" alt="Persyaratan Peminjaman" class="img-fluid">
                    </div>
                </div>

                <div class="col-lg-6">
                    <h2 class="tentang-heading">Persyaratan Peminjaman</h2>

                    <p class="tentang-text">Setelah berhasil login, pengguna dapat melakukan
                        peminjaman kamera atau tripod yang tersedia pada sistem.</p>

                    <ol class="tentang-list">
                        <li> Pengguna memilih kamera atau tripod yang
                            ingin dipinjam.</li>

                        <li> Pengguna mengisi formulir pengajuan peminjaman
                            dengan data yang lengkap dan benar.</li>

                        <li> Pengguna memastikan barang yang dipilih
                            dalam kondisi tersedia.</li>

                        <li> Pengguna bertanggung jawab terhadap barang
                            selama masa peminjaman.</li>

                        <li> Pengajuan peminjaman akan diproses sesuai
                            dengan ketentuan yang berlaku.</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="tentang-section py-5">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="tentang-image">
                        <img src="assets/images/pengembalian.jpg"
                             alt="Persyaratan Pengembalian"
                             class="img-fluid">
                    </div>
                </div>

                <div class="col-lg-6">
                    <h2 class="tentang-heading">Persyaratan Pengembalian</h2>

                    <p class="tentang-text">
                        Setelah selesai menggunakan kamera atau tripod,
                        pengguna wajib mengembalikan barang sesuai dengan
                        waktu dan ketentuan yang telah ditetapkan.
                    </p>

                    <ol class="tentang-list">
                        <li>
                            Barang harus dikembalikan sesuai dengan
                            jadwal yang telah ditentukan.
                        </li>

                        <li>
                            Kamera atau tripod harus dikembalikan dalam
                            kondisi baik dan lengkap.
                        </li>

                        <li>
                            Pengguna wajib memastikan seluruh perlengkapan
                            yang dipinjam telah dikembalikan.
                        </li>

                        <li>
                            Jika terdapat kerusakan atau kehilangan,
                            pengguna wajib melaporkannya kepada pihak
                            pengelola.
                        </li>

                        <li>
                            Setelah barang dikembalikan dan diperiksa,
                            status peminjaman akan diperbarui oleh sistem.
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="tentang-section py-5">
        <div class="container">
            <div class="row align-items-center g-5 flex-lg-row-reverse">
                <div class="col-lg-6">
                    <div class="tentang-image">
                        <img src="assets/images/carapinjam.jpg"
                             alt="Cara Peminjaman Kamera dan Tripod"
                             class="img-fluid">
                    </div>
                </div>

                <div class="col-lg-6">
                    <h2 class="tentang-heading">Cara Peminjaman</h2>

                    <p class="tentang-text">
                        Ikuti langkah-langkah berikut untuk mengajukan
                        peminjaman kamera atau tripod melalui website.
                    </p>

                    <ol class="tentang-list">
                        <li>
                            Pilih kamera atau tripod yang ingin dipinjam
                            pada halaman <strong>Kamera dan Tripod</strong>.
                        </li>
                        <li>
                            Klik tombol <strong>Lihat Detail</strong>
                            untuk melihat informasi peralatan.
                        </li>
                        <li>
                            Klik <strong>Ajukan Peminjaman</strong>,
                            kemudian isi formulir peminjaman dengan lengkap
                            dan benar.
                        </li>
                        <li>
                            Tunggu konfirmasi dari admin mengenai
                            pengajuan peminjaman.
                        </li>
                        <li>
                            Setelah pengajuan disetujui, ikuti petunjuk
                            selanjutnya untuk mengambil peralatan.
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="tentang-section py-5">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="tentang-image">
                        <img src="assets/images/carabayar.jpg"
                             alt="Cara Pembayaran Peminjaman"
                             class="img-fluid">
                    </div>
                </div>

                <div class="col-lg-6">
                    <h2 class="tentang-heading">Cara Pembayaran</h2>

                    <p class="tentang-text">
                        Setelah mendapatkan informasi peminjaman,
                        lakukan pembayaran sesuai petunjuk dari admin.
                    </p>

                    <ol class="tentang-list">
                        <li>
                            Buka halaman <strong>Kontak atau Layanan
                            Konsumen</strong> untuk mendapatkan informasi
                            pembayaran.
                        </li>
                        <li>
                            Ikuti petunjuk pembayaran yang diberikan
                            oleh admin.
                        </li>
                        <li>
                            Lakukan pembayaran sesuai nominal dan metode
                            yang telah ditentukan.
                        </li>
                        <li>
                            Simpan bukti pembayaran dan kirimkan kepada
                            admin jika diminta.
                        </li>
                        <li>
                            Tunggu konfirmasi pembayaran dari admin.
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="tentang-section py-5">
        <div class="container">
            <div class="row align-items-center g-5 flex-lg-row-reverse">
                <div class="col-lg-6">
                    <div class="tentang-image">
                        <img src="assets/images/carapengembalian.jpg"
                             alt="Cara Pengembalian Kamera dan Tripod"
                             class="img-fluid">
                    </div>
                </div>

                <div class="col-lg-6">
                    <h2 class="tentang-heading">Cara Pengembalian</h2>

                    <p class="tentang-text">
                        Setelah selesai menggunakan kamera atau tripod,
                        ikuti langkah-langkah berikut untuk mengembalikan
                        peralatan.
                    </p>

                    <ol class="tentang-list">
                        <li>
                            Kembalikan kamera atau tripod sesuai tanggal
                            jatuh tempo yang telah disepakati.
                        </li>
                        <li>
                            Pastikan peralatan dikembalikan dalam kondisi
                            baik dan lengkap.
                        </li>
                        <li>
                            Serahkan peralatan kepada petugas untuk
                            diperiksa.
                        </li>
                        <li>
                            Jika terdapat kerusakan atau keterlambatan,
                            ikuti ketentuan yang berlaku.
                        </li>
                        <li>
                            Pastikan proses pengembalian telah
                            dikonfirmasi oleh admin.
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php' ?>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const elemen = document.querySelectorAll(
            '.tentang-image, .tentang-heading, ' +
            '.tentang-text, .tentang-list'
        );

        let posisiSebelumnya = window.scrollY;
        let arahScroll = 'bawah';

        elemen.forEach(function (item) {
            item.classList.add('tentang-scroll-item');
            item.classList.remove('tentang-terlihat');
        });

        const pengamat = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                const item = entry.target;

                if (entry.isIntersecting) {
                    if (arahScroll === 'bawah') {
                        // Jalankan animasi saat masuk layar
                        item.classList.remove('tentang-terlihat');
                        void item.offsetWidth;
                        item.classList.add('tentang-terlihat');
                    } else {
                        // Saat scroll ke atas, tampilkan tanpa animasi
                        item.classList.add('tentang-terlihat');
                    }
                } else {
                    // Reset ketika elemen keluar dari layar
                    // agar bisa dianimasikan lagi
                    item.classList.remove('tentang-terlihat');
                }
            });
        }, {
            threshold: 0.1
        });

        elemen.forEach(function (item) {
            pengamat.observe(item);
        });

        window.addEventListener('scroll', function () {
            const posisiSekarang = window.scrollY;

            if (posisiSekarang > posisiSebelumnya) {
                arahScroll = 'bawah';
                document.body.classList.remove('tentang-scroll-up');
            } else if (posisiSekarang < posisiSebelumnya) {
                arahScroll = 'atas';
                document.body.classList.add('tentang-scroll-up');

                // Pastikan elemen yang terlihat tetap tampil
                elemen.forEach(function (item) {
                    const posisi = item.getBoundingClientRect();

                    if (posisi.top < window.innerHeight &&
                        posisi.bottom > 0) {
                        item.classList.add('tentang-terlihat');
                    }
                });
            }

            posisiSebelumnya = posisiSekarang;
        }, { passive: true });
    });
    </script>

</body>
</html>
```