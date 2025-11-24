<?php
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Management Task</title>
    
    <!-- BOOTSTRAP & ICONS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #8264ff, #6b4cff);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
        }
        .form-control:focus {
            border-color: #8264ff;
            box-shadow: 0 0 0 0.2rem rgba(130, 100, 255, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #8264ff, #6b4cff);
            border: none;
            padding: 12px;
            font-weight: 600;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #6b4cff, #5a3ce6);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(130, 100, 255, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card">
            <div class="login-header">
                <div class="mb-3">
                    <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 15px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold;">
                        MT
                    </div>
                </div>
                <h3 class="mb-0">Management Task</h3>
                <p class="mb-0 opacity-75">Sign in to your account</p>
            </div>
            
            <div class="login-body">
                <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($error_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form action="login_process.php" method="POST">
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">
                            <i class="bi bi-phone me-2"></i>Phone Number
                        </label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" 
                               placeholder="Enter your phone number" required autofocus>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock me-2"></i>Password
                        </label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Enter your password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-login w-100">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                    </button>
                </form>
                
                <div class="text-center mt-4">
                    <p class="text-muted small mb-0">
                        Don't have an account? 
                        <a href="register.php" class="text-decoration-none">Register here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


