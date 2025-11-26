<?php
session_start();

// 1. Cek apakah user punya akses (sudah lolos OTP)
if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
    echo "<script>alert('Anda belum melakukan verifikasi OTP!'); window.location='login.php';</script>";
    exit();
}

// 2. Cek apakah nomor HP tersimpan di session
if (!isset($_SESSION['reset_phone'])) {
    header("Location: forgot_password.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Management Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/logregforstyle.css">
</head>

<body class="d-flex align-items-center min-vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card auth-card">
                    <div class="logo-mt">MT</div>

                    <div class="text-center mb-4">
                        <h2 class="fw-bold mb-1">Reset Password</h2>
                        <p class="text-muted small">Silakan buat password baru untuk akun Anda.</p>
                    </div>

                    <form action="process/auth_reset_password.php" method="POST">

                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <div class="input-group input-group-custom">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="new_password" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Konfirmasi Password</label>
                            <div class="input-group input-group-custom">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password baru" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-dark-custom btn-primary">Simpan Password Baru</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

</body>

</html>