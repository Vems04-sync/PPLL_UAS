<?php
// Pastikan file helper di-load
require_once 'functions/auth.php';

// // Cek apakah fungsi tersedia sebelum dipanggil (Untuk menghindari error fatal)
if (function_exists('checkAlreadyLogin')) {
    checkAlreadyLogin();
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Management Task</title>
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
                        <h2 class="fw-bold mb-1">Welcome Back</h2>
                        <p class="text-muted small">Sign in to Management Task</p>
                    </div>

                    <form action="process/auth_login.php?action=login" method="POST">

                        <div class="mb-3">
                            <label class="form-label">WhatsApp Number</label>
                            <div class="input-group input-group-custom">
                                <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                <input type="text" name="phone" class="form-control" placeholder="+62 812 3456 7890" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group input-group-custom">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                            </div>
                        </div>

                        <div class="text-end mb-4">
                            <a href="forgot_password.php" class="small link-primary">Forgot Password?</a>
                        </div>

                        <button type="submit" class="btn btn-dark-custom btn-primary">Login</button>
                    </form>

                    <div class="text-center mt-4 small text-muted">
                        Don't have an account? <a href="register.php" class="link-primary fw-semibold">Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>