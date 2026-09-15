<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Sistem Peminjaman Kamera</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">

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
            <div class="row align-items-center g-5">
                 <div class="col-lg-6">
                    <h2 class="tentang-heading"> Syarat Login </h2>
                    
                    <p class="tentang-text">
                        Sebelum melakukan peminjaman kamera atau tripod,
                        pengguna harus melakukan login terlebih dahulu.                
                        Login digunakan untuk memastikan data pengguna                
                         tercatat dalam sistem.  </p>             

                    <ol class="tentang-list">                
                        <li> Pengguna harus memiliki akun google yang telah terdaftar.</li>
                        <li> Masukkan username dan password.</li>                
                        <li> Pastikan data akun yang digunakan sesuai dengan data pengguna.</li>
                        <li> Setelah berhasil login, pengguana dapat melanjutkan 
                            ke proses peminjaman kamera atau tripod.</li>
                        </ol>
                    </div>                
                    
                    <div class="col-lg-6">
                        <div class="tentang-image">
                            <img src="assets/images/login.jpg" alt="Syarat login" class="img-fluif">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="tentang-section py-5">
            <div class="container">
                <div class="row align-items-center g-5">
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

            <div class="col-lg-6">
                <div class="tentang-image">
                    <img src="assets/images/pengembalian.jpg"
                         alt="Persyaratan Pengembalian"
                         class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php' ?>
</body>
</html>