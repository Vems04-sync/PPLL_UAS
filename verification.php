<?php
require_once 'functions/auth.php';
// session_start(); <--- Baris ini DIHAPUS karena auth.php sudah menjalankannya

// Cek apakah user sudah melewati tahap forgot password
if (!isset($_SESSION['reset_phone'])) {
    header("Location: forgot_password.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <title>Verifikasi OTP - Management Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/logregforstyle.css">
</head>

<body class="d-flex align-items-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card auth-card p-4">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold">Verifikasi OTP</h3>
                        <p class="text-muted small">Masukkan 6 digit kode yang dikirim ke WhatsApp Anda.</p>
                    </div>

                    <form action="process/auth_verify_otp.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kode OTP</label>
                            <input type="text" name="otp_code" class="form-control text-center fs-4 letter-spacing-2" placeholder="XXXXXX" maxlength="6" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">Verifikasi Token</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="process/auth_forgot.php?resend=true" class="small text-muted">Kirim Ulang OTP</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>