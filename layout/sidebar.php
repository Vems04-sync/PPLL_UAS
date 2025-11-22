<?php
session_start();
$user_id = $_SESSION['user_id'] ?? null;

$user = ['name' => 'User']; // default

if ($user_id) {
    $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// FUNGSI AMBIL INISIAL
function getInitials($name) {
    $words = explode(' ', trim($name));
    $initials = '';

    foreach ($words as $w) {
        if (strlen($w) > 0) {
            $initials .= strtoupper($w[0]);
        }
    }

    return substr($initials, 0, 2); // hanya ambil 2 huruf saja
}
?>

<header class="top-app-bar bg-white shadow-sm">
    <div class="d-flex justify-content-between align-items-center py-3 px-4">

        <div class="d-flex align-items-center gap-2">

            <!-- TOGGLE BUTTON MOBILE -->
            <button class="btn btn-outline-secondary me-2 d-md-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>

           <div class="logo-mt-bar">MT</div>
            <span class="fw-semibold fs-5 text-dark d-none d-md-inline">Management Task</span>
           </div>

        <div class="avatar-initials">
            <?= getInitials($user['name']); ?>
        </div>
    </div>
</header>

<div class="d-flex" id="wrapper">

    <div id="sidebar-wrapper">
        <nav class="sidebar-nav">
            <div class="sidebar-links">

                <a href="index.php"
                   class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active-sidebar-link' : '' ?>">
                    <i class="bi bi-grid me-2"></i> Dashboard
                </a>

                <a href="categories.php"
                   class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active-sidebar-link' : '' ?>">
                    <i class="bi bi-folder me-2"></i> Categories
                </a>

                <a href="profile.php"
                   class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active-sidebar-link' : '' ?>">
                    <i class="bi bi-person me-2"></i> Profile
                </a>

                <a href="#" class="sidebar-link text-danger">
                    <i class="bi bi-box-arrow-right me-2 text-danger"></i> Logout
                </a>

            </div>
        </nav>
    </div>

    <div id="page-content-wrapper">
        <main class="p-4">