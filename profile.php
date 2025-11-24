<?php
// session_start();
require_once "config/database.php";

// Untuk demo/testing tanpa session, gunakan user_id = 1 sebagai default
// $user_id = $_SESSION['user_id'] ?? 1;
$user_id = 1;

// Pastikan user dengan id = 1 ada (untuk demo/testing)
$check_user = $pdo->prepare("SELECT id FROM users WHERE id = ?");
$check_user->execute([$user_id]);
if (!$check_user->fetch()) {
    // Buat user dummy jika belum ada
    $create_user = $pdo->prepare("INSERT INTO users (id, name, phone_number, password, created_at, updated_at) VALUES (1, 'Demo User', '081234567890', ?, NOW(), NOW())");
    $hashed_password = password_hash('demo123', PASSWORD_DEFAULT);
    $create_user->execute([$hashed_password]);
}

// Ambil data user
$user_query = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user_query->execute([$user_id]);
$user = $user_query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found");
}

include "layout/header.php";
include "layout/sidebar.php";
?>

<!-- CSS khusus untuk halaman Profile -->
<link rel="stylesheet" href="assets/css/profile.css">

<div class="container-fluid profile-page-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <h2 class="fw-bold mb-1">Profile Settings</h2>
                    <p class="text-muted mb-0">Manage your account information</p>
                </div>
            </div>

            <!-- Alert untuk feedback -->
            <?php // if (isset($_SESSION['success_message'])): ?>
                <?php /* <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div> */ ?>
            <?php // endif; ?>

            <?php // if (isset($_SESSION['error_message'])): ?>
                <?php /* <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div> */ ?>
            <?php // endif; ?>

            <div class="row g-4">
                <!-- Edit Nama & Password -->
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-semibold">
                                <i class="bi bi-person me-2"></i>Account Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="process/profile_update.php" method="POST">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" 
                                           placeholder="Enter current password to change password">
                                    <div class="form-text">Leave blank if you don't want to change password</div>
                                </div>

                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" 
                                           placeholder="Enter new password">
                                </div>

                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                           placeholder="Confirm new password">
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-check-circle me-2"></i>Update Profile
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Ganti Nomor WA -->
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-semibold">
                                <i class="bi bi-whatsapp me-2 text-success"></i>WhatsApp Number
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Current Number</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-phone"></i>
                                    </span>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['phone_number']); ?>" readonly>
                                </div>
                            </div>

                            <button type="button" class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#changePhoneModal">
                                <i class="bi bi-pencil me-2"></i>Ubah Nomor
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0 fw-semibold">
                                <i class="bi bi-exclamation-triangle me-2"></i>Danger Zone
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">
                                Once you delete your account, there is no going back. Please be certain.
                            </p>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                <i class="bi bi-trash me-2"></i>Delete Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ganti Nomor WA -->
<div class="modal fade" id="changePhoneModal" tabindex="-1" aria-labelledby="changePhoneModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePhoneModalLabel">Ubah Nomor WhatsApp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="changePhoneForm" action="process/change_phone.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new_phone" class="form-label">Nomor WhatsApp Baru <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="new_phone" name="new_phone" 
                               placeholder="08xxxxxxxxxx" required>
                        <div class="form-text">Masukkan nomor WhatsApp baru yang ingin digunakan</div>
                    </div>

                    <div id="otpSection" style="display: none;">
                        <div class="mb-3">
                            <label for="otp_code" class="form-label">Kode OTP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="otp_code" name="otp_code" 
                                   placeholder="Masukkan 6 digit kode OTP" maxlength="6">
                            <div class="form-text">
                                Kode OTP telah dikirim ke nomor baru. 
                                <a href="#" id="resendOtpLink" onclick="resendOTP(event)">Kirim ulang OTP</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="submitPhoneBtn">
                        <i class="bi bi-check-circle me-2"></i>Verifikasi & Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Akun -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAccountModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Hapus Akun
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteAccountForm" action="process/delete_account.php" method="POST">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan. Semua data Anda akan dihapus permanen.
                    </div>

                    <div class="mb-3">
                        <label for="delete_otp" class="form-label">Kode OTP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="delete_otp" name="otp_code" 
                               placeholder="Masukkan 6 digit kode OTP" maxlength="6" required>
                        <div class="form-text">
                            Kode OTP telah dikirim ke nomor WhatsApp Anda. 
                            <a href="#" onclick="sendDeleteOTP(event)">Kirim ulang OTP</a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>Hapus Akun Permanen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Handle form ganti nomor WA
document.getElementById('changePhoneForm').addEventListener('submit', function(e) {
    const newPhone = document.getElementById('new_phone').value;
    const otpSection = document.getElementById('otpSection');
    const submitBtn = document.getElementById('submitPhoneBtn');
    
    // Jika OTP section belum muncul, kirim OTP dulu
    if (otpSection.style.display === 'none') {
        e.preventDefault();
        sendOTPForPhoneChange(newPhone);
    }
});

function sendOTPForPhoneChange(phone) {
    fetch('process/otp_send.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=change_phone&phone_number=' + encodeURIComponent(phone)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('otpSection').style.display = 'block';
            document.getElementById('new_phone').readOnly = true;
            alert('Kode OTP telah dikirim ke nomor ' + phone);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengirim OTP');
    });
}

function resendOTP(e) {
    e.preventDefault();
    const phone = document.getElementById('new_phone').value;
    sendOTPForPhoneChange(phone);
}

// Kirim OTP saat modal hapus akun dibuka
document.getElementById('deleteAccountModal').addEventListener('show.bs.modal', function() {
    sendDeleteOTP();
});

function sendDeleteOTP(e) {
    if (e) e.preventDefault();
    
    fetch('process/otp_send.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=delete_account&user_id=<?php echo $user_id; ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Kode OTP telah dikirim ke nomor WhatsApp Anda');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengirim OTP');
    });
}
</script>

<?php 
include "layout/footer.php";
?>

