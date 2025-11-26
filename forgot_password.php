<?php
require_once 'functions/auth.php';
checkAlreadyLogin(); // Redirect ke dashboard jika sudah login
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Management Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/logregforstyle.css">
</head>

<body class="d-flex align-items-center min-vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">

                <div class="text-center mb-4">
                    <div class="logo-mt mb-3">MT</div>
                    <h2 class="fw-bold mb-2">Forgot Password?</h2>
                    <p class="text-muted small">We'll send you an OTP code to reset it</p>
                </div>

                <div class="card auth-card">
                    <form action="process/auth_forgot.php" method="POST">
                        <div class="mb-4">
                            <label class="form-label">Enter your WhatsApp Number</label>
                            <div class="input-group input-group-custom">
                                <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                <input type="text" name="phone" class="form-control" placeholder="+62 812 3456 7890" required>
                            </div>
                            <div class="form-text text-muted small mt-2">
                                Enter the number associated with your account
                            </div>
                        </div>

                        <button type="submit" class="btn btn-dark-custom btn-primary mb-3">Send OTP Code</button>

                        <div class="text-center">
                            <a href="login.php" class="text-muted small text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i> Back to Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>