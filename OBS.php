<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Okul Bilgi Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
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

        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 40px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            backdrop-filter: blur(10px);
        }

        .logo-container {
            margin-bottom: 30px;
            position: relative;
            display: inline-block;
        }

        .logo {
            width: 150px;
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

        .title {
            color: var(--primary-color);
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: none;
            border-radius: var(--border-radius);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-teacher {
            background: var(--secondary-color);
            color: white;
        }

        .btn-teacher:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .btn-student {
            background: var(--accent-color);
            color: white;
        }

        .btn-student:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        .btn-login i {
            font-size: 20px;
        }

        .divider {
            margin: 20px 0;
            text-align: center;
            position: relative;
        }

        .divider::before,
        .divider::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 45%;
            height: 1px;
            background: #ddd;
        }

        .divider::before {
            left: 0;
        }

        .divider::after {
            right: 0;
        }

        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }

            .logo {
                width: 120px;
            }

            .title {
                font-size: 20px;
            }
        }

        /* Animasyon efektleri */
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

        .container {
            animation: fadeIn 0.5s ease-out;
        }

        .btn-login {
            animation: fadeIn 0.5s ease-out;
            animation-fill-mode: both;
        }

        .btn-teacher {
            animation-delay: 0.2s;
        }

        .btn-student {
            animation-delay: 0.4s;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo-container">
            <img src="can lise.png" alt="Okul Logosu" class="logo">
        </div>
        
        <h1 class="title">Okul Bilgi Sistemi</h1>

        <a href="ogretmen_login.php" class="btn-login btn-teacher">
            <i class="fas fa-chalkboard-teacher"></i>
            Öğretmen Girişi
        </a>

        <div class="divider">veya</div>

        <a href="ogrencı_login.php" class="btn-login btn-student">
            <i class="fas fa-user-graduate"></i>
            Öğrenci Girişi
        </a>
    </div>
</body>

</html>