<?php
require_once "functions/auth.php";
checkLogin(); // Pastikan user sudah login
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Management Task</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #F8F9FA;
            font-family: sans-serif;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-white bg-white shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#">Management Task</a>
            <div class="d-flex">
                <span class="navbar-text me-3">
                    Halo, <strong><?php echo $_SESSION['name']; ?></strong>
                </span>
                <a href="process/auth_login.php?action=logout" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin keluar?');">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h1 class="h3">Selamat Datang!</h1>
                <p class="text-muted">Ini adalah halaman dashboard utama.</p>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>