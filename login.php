<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // First, try to find user in admin table
    $stmt = $conn->prepare("SELECT * FROM `admin` WHERE `username` = ? AND `password` = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Admin user found
        $_SESSION['id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = 'admin';
        header('Location: dashboard.php');
        exit;
    } else {
        // Try to find user in users table
        $stmt = $conn->prepare("SELECT * FROM `users` WHERE `username` = ? AND `password` = ?");
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Regular user found
            $_SESSION['id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'] ?? $user['username']; // if fullname doesn't exist, fallback username
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = 'cashier';
            header('Location: cashier_dashboard.php');
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUXURY JEWELRY - Login</title>
    
    <!-- Existing CSS -->
    <link rel="stylesheet" href="./assets/compiled/css/app.css">
    <link rel="stylesheet" href="./assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="./assets/compiled/css/auth.css">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        /* Background gradient + smooth fade */
        body {
            background: radial-gradient(circle at top left, #f8e1e7, #fff);
            overflow: hidden;
            animation: bgShift 8s ease-in-out infinite alternate;
        }

        @keyframes bgShift {
            0% { background-position: 0% 0%; }
            100% { background-position: 100% 100%; }
        }

        #auth {
            animation: fadeInUp 1s ease forwards;
        }

        /* Subtle fade and slide-in animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Floating luxury logo */
        .auth-logo h1 {
            color: #752738;
            font-weight: bold;
            animation: floatLogo 3s ease-in-out infinite;
        }

        @keyframes floatLogo {
            0% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0); }
        }

        /* Left section animation */
        #auth-left {
            animation: fadeInLeft 1.2s ease forwards;
        }

        /* Right background animation */
        #auth-right {
            animation: zoomBg 15s ease-in-out infinite alternate;
        }

        @keyframes zoomBg {
            0% { transform: scale(1); }
            100% { transform: scale(1.05); }
        }

        /* Button hover */
        .btn-primary {
            background-color: #752738 !important;
            border-color: #752738 !important;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(117, 39, 56, 0.4);
        }

        .auth-title {
            color: #752738;
            font-weight: 600;
        }

        .alert-danger {
            animation: shakeError 0.5s ease;
        }

        @keyframes shakeError {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-6px); }
            75% { transform: translateX(6px); }
        }

        /* Responsive fix for mobile view */
        @media (max-width: 768px) {
            #auth-right {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div id="auth" class="animate__animated animate__fadeIn">
        <div class="row h-100">
            <!-- LEFT SIDE -->
            <div class="col-lg-5 col-12 d-flex align-items-center justify-content-center">
                <div id="auth-left" class="text-center p-4 animate__animated animate__fadeInLeft animate__faster">
                    <div class="auth-logo mb-4">
                        <h1 class="animate__animated animate__bounceInDown">LUXURY JEWELRY</h1>
                    </div>
                    <h2 class="auth-title animate__animated animate__fadeInUp animate__delay-1s mb-4">Welcome Back</h2>
                    <p class="text-muted mb-4 animate__animated animate__fadeInUp animate__delay-1s">
                        Please sign in to your account
                    </p>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" class="animate__animated animate__fadeInUp animate__delay-1s">
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" name="username" class="form-control form-control-xl" placeholder="Username" required>
                            <div class="form-control-icon">
                                <i class="bi bi-person"></i>
                            </div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" name="password" class="form-control form-control-xl" placeholder="Password" required>
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                        </div>
                        <div class="form-check form-check-lg d-flex align-items-end mb-3">
                            <input class="form-check-input me-2" type="checkbox" id="flexCheckDefault">
                            <label class="form-check-label text-gray-600" for="flexCheckDefault">
                                Keep me logged in
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-3">
                            Log in
                        </button>
                    </form>
                </div>
            </div>

            <!-- RIGHT SIDE -->
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right" 
                    class="animate__animated animate__fadeInRightBig"
                    style="background-image: url('assets/images/banner-login.jpg');
                           background-size: cover;
                           background-position: center;
                           background-repeat: no-repeat;
                           height: 100vh;">
                </div>
            </div>
        </div>
    </div>

    <!-- Optional fade-in JS -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        document.getElementById('auth').style.opacity = '0';
        setTimeout(() => {
            document.getElementById('auth').style.transition = 'opacity 0.8s ease';
            document.getElementById('auth').style.opacity = '1';
        }, 100);
    });
    </script>
</body>
</html>
