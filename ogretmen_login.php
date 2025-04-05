<?php
session_start();
include 'db.php';

if (isset($_POST['kullanici_adi'], $_POST['sifre'])) {
    $kullanici_adi = mysqli_real_escape_string($conn, $_POST['kullanici_adi']);
    $sifre = $_POST['sifre'];
    
    // Debug için
    error_log("Giriş denemesi - Kullanıcı: " . $kullanici_adi);
    
    $sql = "SELECT * FROM ogretmenler WHERE kullanici_adi = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $kullanici_adi);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && $result->num_rows > 0) {
        $ogretmen = $result->fetch_assoc();
        
        // Debug için
        error_log("Veritabanından gelen hash: " . $ogretmen['sifre']);
        error_log("Girilen şifre: " . $sifre);
        
        if (password_verify($sifre, $ogretmen['sifre'])) {
            $_SESSION['ogretmen_id'] = $ogretmen['ogretmen_id'];
            header("Location: ogretmen.php");
            exit;
        } else {
            $error = "Hatalı şifre. (Debug: Şifre doğrulaması başarısız)";
            error_log("Şifre doğrulaması başarısız");
        }
    } else {
        $error = "Kullanıcı bulunamadı.";
        error_log("Kullanıcı bulunamadı: " . $kullanici_adi);
    }
    mysqli_stmt_close($stmt);
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğretmen Girişi - Okul Bilgi Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --background-color: #ecf0f1;
            --text-color: #2c3e50;
            --border-radius: 15px;
            --box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--background-color) 0%, #bdc3c7 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 20px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 40px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            backdrop-filter: blur(10px);
            animation: fadeIn 0.5s ease-out;
        }

        .logo-container {
            margin-bottom: 30px;
        }

        .logo {
            width: 120px;
            height: auto;
            border-radius: 50%;
            padding: 10px;
            background: white;
            box-shadow: var(--box-shadow);
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        h1 {
            color: var(--primary-color);
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-label {
            color: var(--text-color);
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control {
            padding: 12px;
            border-radius: var(--border-radius);
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .input-group {
            position: relative;
        }

        .input-group-text {
            background: none;
            border: none;
            padding: 0;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            cursor: pointer;
        }

        .btn-login {
            background: var(--secondary-color);
            color: white;
            padding: 12px;
            border: none;
            border-radius: var(--border-radius);
            width: 100%;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .btn-login:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: var(--text-color);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: var(--secondary-color);
        }

        .error {
            background: #fff5f5;
            color: #c0392b;
            padding: 10px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            font-size: 14px;
            border: 1px solid #fad7d7;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 20px;
            }

            .logo {
                width: 100px;
            }

            h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-container">
            <img src="can lise.png" alt="Okul Logosu" class="logo">
        </div>
        
        <h1>Öğretmen Girişi</h1>
        
        <?php if (isset($error)): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="ogretmen_login.php" method="post">
            <div class="form-group">
                <label for="kullanici_adi" class="form-label">KULLANICI ADI</label>
                <input type="text" class="form-control" id="kullanici_adi" name="kullanici_adi" required 
                       placeholder="Kullanıcı adınızı giriniz">
            </div>

            <div class="form-group">
                <label for="sifre" class="form-label">ŞİFRE</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="sifre" name="sifre" required 
                           placeholder="Şifrenizi giriniz">
                    <span class="input-group-text" onclick="togglePassword()">
                        <i class="fas fa-eye" id="togglePassword"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-chalkboard-teacher me-2"></i> Giriş Yap
            </button>
        </form>

        <a href="OBS.php" class="back-link">
            <i class="fas fa-arrow-left me-1"></i> Ana Sayfaya Dön
        </a>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('sifre');
            const toggleIcon = document.getElementById('togglePassword');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>