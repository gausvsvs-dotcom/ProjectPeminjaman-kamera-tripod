<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Login Admin - ProjectPeminjaman</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
    <style>

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(
                135deg,
                #c8b5f3,
                #e196ff,
                #ff97b8,
                #aaa2f7
            );
            font-family: Arial, sans-serif;
        }

        .login-card {
            width: 400px;
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .login-header {
            background-color: #8174A0;
            color: white;
            text-align: center;
            padding: 30px;
        }

        .login-header h3 {
            margin: 10px 0 0;
        }

        .login-icon {
            font-size: 50px;
        }

        .login-body {
            background-color: white;
            padding: 30px;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px;
        }

        .btn-login {
            width: 100%;
            background-color: #8174A0;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: bold;
        }

        .btn-login:hover {
            background-color: #A888B5;
            color: white;
        }
    </style>
</head>
<body>
    <div class="card login-card">
        <div class="login-header">
            <div class="login-icon">
                📷
            </div>
            <h3>
                ProjectPeminjaman
            </h3>
            <small>
                Login Admin
            </small>
        </div>
        <div class="login-body">
            <?php
            if (isset($_GET['pesan'])) {

                if ($_GET['pesan'] == 'gagal') {
            ?>
                    <div class="alert alert-danger">
                        Username atau password salah!
                    </div>
            <?php
                }
                if ($_GET['pesan'] == 'logout') {
            ?>
                    <div class="alert alert-success">
                        Berhasil logout.
                    </div>
            <?php
                }
            }
            ?>
            <form action="proses_login.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">
                        Username
                    </label>
                    <input
                        type="text"
                        name="username"
                        class="form-control"
                        placeholder="Masukkan username"
                        required
                    >
                </div>
                <div class="mb-3">
                    <label class="form-label">
                        Password
                    </label>
                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        required
                    >
                </div>

                <button
                    type="submit"
                    name="login"
                    class="btn btn-login">
                    Login
                </button>
            </form>
        </div>
    </div>
</body>
</html>