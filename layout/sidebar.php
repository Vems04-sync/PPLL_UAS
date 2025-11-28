<?php
// 1. PERBAIKAN SESSION: Cek dulu status session agar tidak error "Notice"
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Pastikan file koneksi sudah di-include di file utama (dashboard.php) sebelum file ini.
// Kita gunakan global $pdo untuk memastikan variabel terbaca
global $pdo; 

$user_id = $_SESSION['user_id'] ?? null;
$user = ['name' => 'User']; // Default value jika user belum login/tidak ditemukan

// Cek apakah $pdo tersedia sebelum query untuk mencegah Fatal Error
if ($user_id && isset($pdo)) {
    try {
        $stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $user = $data;
        }
    } catch (PDOException $e) {
        // Silent error atau log error jika perlu
        // error_log($e->getMessage());
    }
}

// 2. PERBAIKAN FUNGSI: Cek function_exists agar tidak error jika file di-load 2x
if (!function_exists('getInitials')) {
    function getInitials($name) {
        $words = explode(' ', trim($name));
        $initials = '';
        foreach ($words as $w) {
            if (strlen($w) > 0) {
                $initials .= strtoupper($w[0]);
            }
        }
        return substr($initials, 0, 2); 
    }
}
?>

<header class="top-app-bar bg-white shadow-sm">
    <div class="d-flex justify-content-between align-items-center py-3 px-4">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary me-2 d-md-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>

            <div class="logo-mt-bar">MT</div>
            <span class="fw-semibold fs-5 text-dark d-none d-md-inline">Management Task</span>
        </div>

        <div class="avatar-initials" title="<?= htmlspecialchars($user['name']) ?>">
            <?= getInitials($user['name']); ?>
        </div>
    </div>
</header>

<div class="d-flex" id="wrapper">
    <div id="sidebar-wrapper">
        <nav class="sidebar-nav">
            <div class="sidebar-links">

                <a href="dashboard.php"
                    class="sidebar-link <?= basename($_SERVER['SCRIPT_NAME']) == 'index.php' || basename($_SERVER['SCRIPT_NAME']) == 'dashboard.php' ? 'active-sidebar-link' : '' ?>">
                    <i class="bi bi-grid me-2"></i> Dashboard
                </a>

                <a href="categories.php"
                    class="sidebar-link <?= basename($_SERVER['SCRIPT_NAME']) == 'categories.php' ? 'active-sidebar-link' : '' ?>">
                    <i class="bi bi-folder me-2"></i> Categories
                </a>

                <a href="profile.php"
                    class="sidebar-link <?= basename($_SERVER['SCRIPT_NAME']) == 'profile.php' ? 'active-sidebar-link' : '' ?>">
                    <i class="bi bi-person me-2"></i> Profile
                </a>

                <a href="process/auth_login.php?action=logout" class="sidebar-link text-danger" onclick="return confirm('Yakin ingin keluar?');">
                    <i class="bi bi-box-arrow-right me-2 text-danger"></i> Logout
                </a>

            </div>
        </nav>
    </div>

    <div id="page-content-wrapper">
        <main class="p-4">