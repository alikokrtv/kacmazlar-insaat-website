<?php
/**
 * Kacmazlar İnşaat CMS - Admin Giriş Sayfası
 */

require_once 'includes/Database.php';
require_once 'includes/Auth.php';

$auth = new Auth();

// Zaten giriş yapmışsa admin paneline yönlendir
if ($auth->isLoggedIn()) {
    header('Location: admin.php');
    exit;
}

$error = '';
$success = '';

// Form gönderildi mi?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Kullanıcı adı ve şifre gereklidir.';
    } else {
        if ($auth->login($username, $password)) {
            header('Location: admin.php');
            exit;
        } else {
            $error = 'Kullanıcı adı veya şifre hatalı.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Giriş - Kacmazlar İnşaat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #004E89, #FF6B35);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            background: linear-gradient(135deg, #004E89, #FF6B35);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .login-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .login-header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .login-form {
            padding: 40px 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input:focus {
            outline: none;
            border-color: #004E89;
            background: white;
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper input {
            padding-left: 45px;
        }

        .login-btn {
            width: 100%;
            background: linear-gradient(135deg, #004E89, #FF6B35);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .login-footer {
            text-align: center;
            padding: 20px 30px;
            background: #f8f9fa;
            border-top: 1px solid #e1e5e9;
        }

        .login-footer p {
            color: #666;
            font-size: 12px;
        }

        .login-footer a {
            color: #004E89;
            text-decoration: none;
            font-weight: 600;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .back-to-site {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        .back-to-site:hover {
            background: rgba(255,255,255,0.3);
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                margin: 10px;
            }

            .login-header {
                padding: 30px 20px;
            }

            .login-form {
                padding: 30px 20px;
            }

            .login-footer {
                padding: 15px 20px;
            }
        }

        /* Loading animation */
        .loading {
            display: none;
        }

        .loading.show {
            display: inline-block;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid #ffffff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <a href="index.html" class="back-to-site">
        <i class="fas fa-arrow-left"></i> Ana Siteye Dön
    </a>

    <div class="login-container">
        <div class="login-header">
            <h1>Admin Panel</h1>
            <p>Kacmazlar İnşaat CMS</p>
        </div>

        <form class="login-form" method="POST" id="loginForm">
            <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="username">Kullanıcı Adı veya E-posta</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" required 
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                           placeholder="Kullanıcı adınızı girin">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Şifre</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" required 
                           placeholder="Şifrenizi girin">
                </div>
            </div>

            <button type="submit" class="login-btn" id="loginBtn">
                <span class="loading">
                    <span class="spinner"></span>
                </span>
                <span class="btn-text">
                    <i class="fas fa-sign-in-alt"></i> Giriş Yap
                </span>
            </button>
        </form>

        <div class="login-footer">
            <p>Varsayılan giriş bilgileri:</p>
            <p><strong>Kullanıcı:</strong> admin | <strong>Şifre:</strong> admin123</p>
            <p><a href="index.html">Ana siteye dön</a></p>
        </div>
    </div>

    <script>
        // Form submission with loading animation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            const loading = btn.querySelector('.loading');
            const btnText = btn.querySelector('.btn-text');
            
            loading.classList.add('show');
            btnText.style.display = 'none';
            btn.disabled = true;
        });

        // Auto focus on username field
        document.getElementById('username').focus();

        // Enter key navigation
        document.getElementById('username').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('password').focus();
            }
        });
    </script>
</body>
</html>
