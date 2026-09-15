<?php
if (isset($_GET['status']) && $_GET['status'] == 'error') {
    $error = "Username atau password salah!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Peminjaman Kamera dan Tripod</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php' ?>

    <section class="login-section">
        <div class="container">
            <div class="login-container">
                <div class="login-card">
                    <div class="text-center">
                        <img src="assets/images/profil.png" alt="Logo" class="login-logo">
                        <h1 class="login-title">Login</h1>

                        <p class="login-subtitle">Silahkan masuk untuk melanjutkan</p>

                        <?php if (isset($error)) : ?>
                            
                            <div class="alert alert-danger text-center">
                                <?= $error; ?>
                            </div>
                        
                        <?php endif; ?>
                    </div>

                    

                    <form action="proses_login.php" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control login-input" id="username" name="username"
                            placeholder="Masukkan username" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                Password
                            </label>

                            <input type="password" name="password" id="password" class="form-control login-input"
                            placeholder="Masukkan passwrod" required>
                        </div>

                        <button type="submit" class="btn login-button w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php' ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>