<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kullanıcı adı ve şifre kontrolü
    if (isset($_POST['y_username']) && isset($_POST['y_password'])) {
        $y_username = mysqli_real_escape_string($conn, $_POST['y_username']);
        $Y_password = $_POST['y_password'];

        // Kullanıcıyı veritabanından al
        $sql = "SELECT * FROM yonetici WHERE y_username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $y_username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Şifreyi kontrol et
            if (password_verify($Y_password, $row['y_password'])) {
                $_SESSION['yonetici_id'] = $row['yonetici_id'];
                header("Location: yonetici_paneli.php");
                exit;
            } else {
                $error = "Hatalı kullanıcı adı veya şifre.";
            }
        } else {
            $error = "Kullanıcı bulunamadı.";
        }
        mysqli_stmt_close($stmt);
    }

    // Yeni kayıt işlemi
    if (isset($_POST['new_username']) && isset($_POST['new_password'])) {
        $new_username = mysqli_real_escape_string($conn, $_POST['new_username']);
        $new_password = $_POST['new_password'];

        // Yeni yönetici kaydı oluştur
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $insert_sql = "INSERT INTO yonetici (y_username, y_password) VALUES (?, ?)";
        $insert_stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($insert_stmt, "ss", $new_username, $hashed_password);
        
        if (mysqli_stmt_execute($insert_stmt)) {
            // Kayıt başarılı, hemen giriş yap
            $_SESSION['yonetici_id'] = mysqli_insert_id($conn); // Yeni yönetici ID'sini al
            header("Location: yonetici_paneli.php");
            exit;
        } else {
            $error = "Yeni yönetici kaydı oluşturulurken bir hata oluştu.";
        }
        mysqli_stmt_close($insert_stmt);
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Girişi - Okul Bilgi Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1a237e;
            --secondary-color: #303f9f;
            --background-color: #e8eaf6;
            --text-color: #1a237e;
            --border-radius: 15px;
            --box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--background-color) 0%, #c5cae9 100%);
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
            box-shadow: 0 0 0 0.2rem rgba(48, 63, 159, 0.25);
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
            background: var(--primary-color);
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
        
        <h1>Yönetici Girişi</h1>
        
        <?php if (isset($error)): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="yonetici_login.php" method="post">
            <div class="form-group">
                <label for="y_username" class="form-label">KULLANICI ADI</label>
                <input type="text" class="form-control" id="y_username" name="y_username" required 
                       placeholder="Kullanıcı adınızı giriniz">
            </div>

            <div class="form-group">
                <label for="y_password" class="form-label">ŞİFRE</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="y_password" name="y_password" required 
                           placeholder="Şifrenizi giriniz">
                    <span class="input-group-text" onclick="togglePassword()">
                        <i class="fas fa-eye" id="togglePassword"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-user-shield me-2"></i> Giriş Yap
            </button>
        </form>

        <h5 class="mt-4">Yeni Yönetici Kaydı</h5>
        <button class="btn btn-secondary" id="newRegisterButton">Yeni Kayıt</button>

        <div id="newRegisterForm" style="display: none;">
            <form action="yonetici_login.php" method="post">
                <div class="mb-3">
                    <label for="new_username" class="form-label">Kullanıcı Adı</label>
                    <input type="text" class="form-control" id="new_username" name="new_username" required>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">Şifre</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>
                <button type="submit" class="btn btn-primary">Kaydet</button>
            </form>
        </div>

        <a href="OBS.php" class="back-link">
            <i class="fas fa-arrow-left me-1"></i> Ana Sayfaya Dön
        </a>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('y_password');
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

        document.getElementById('newRegisterButton').addEventListener('click', function() {
            const registerForm = document.getElementById('newRegisterForm');
            if (registerForm.style.display === "none") {
                registerForm.style.display = "block";
            } else {
                registerForm.style.display = "none";
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>