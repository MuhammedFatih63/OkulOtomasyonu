<?php
// Session başlat
session_start();
// Veritabanı bağlantısını yap
include 'db.php';

// Eğer oturumda öğretmen kimliği tanımlı değilse, öğretmen giriş sayfasına yönlendir
if (!isset($_SESSION['ogretmen_id'])) {
    header("Location: ogretmen_login.php");
    exit;
}

// Oturumda tanımlı öğretmen kimliğini al
$ogretmen_id = $_SESSION['ogretmen_id'];

// Öğretmenin hangi derslere sahip olduğunu sorgula
$sql_ders = "SELECT ders_id FROM ogretmenler WHERE ogretmen_id = '$ogretmen_id'";
$result_ders = mysqli_query($conn, $sql_ders);
$row_ders = mysqli_fetch_array($result_ders);
$ogretmen_ders = $row_ders['ders_id'];

// Eğer form gönderilmiş ve gerekli alanlar doldurulmuşsa
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ogrenci_id'], $_POST['yazili_notu'], $_POST['sozlu_notu'], $_POST['performans_notu'])) {
    // Post verilerini al
    $ogrenci_id = $_POST['ogrenci_id'];
    $yazili_notu = $_POST['yazili_notu'];
    $sozlu_notu = $_POST['sozlu_notu'];
    $performans_notu = $_POST['performans_notu'];

    // Notları ekle veya güncelle
    $sql_check = "SELECT * FROM notlar WHERE ogrenci_id = '$ogrenci_id' AND ders_id = '$ogretmen_ders'";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
        $sql_update = "UPDATE notlar SET yazili_notu = '$yazili_notu', sozlu_notu = '$sozlu_notu', performans_notu = '$performans_notu' WHERE ogrenci_id = '$ogrenci_id' AND ders_id = '$ogretmen_ders'";
        if (mysqli_query($conn, $sql_update)) {
            $success_message = "Notlar başarıyla güncellendi.";
        } else {
            $error_message = "Notlar güncellenirken bir hata oluştu: " . mysqli_error($conn);
        }
    } else {
        $sql_insert = "INSERT INTO notlar (ogrenci_id, ders_id, yazili_notu, sozlu_notu, performans_notu) VALUES ('$ogrenci_id', '$ogretmen_ders', '$yazili_notu', '$sozlu_notu', '$performans_notu')";
        if (mysqli_query($conn, $sql_insert)) {
            $success_message = "Notlar başarıyla eklendi.";
        } else {
            $error_message = "Notlar eklenirken bir hata oluştu: " . mysqli_error($conn);
        }
    }
}

// Öğrenci listesini al
$sql_students = "SELECT ogrenci_id, isim, soyisim FROM ogrenciler";
$result_students = mysqli_query($conn, $sql_students);

// Veritabanı bağlantısını kapat
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğretmen Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --background-color: #ecf0f1;
            --text-color: #2c3e50;
            --border-radius: 10px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: var(--background-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        #sidebar {
            width: 250px;
            background: var(--primary-color);
            color: white;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            box-shadow: var(--box-shadow);
        }

        #sidebar .sidebar-header {
            padding: 20px;
            background: rgba(0,0,0,0.1);
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .teacher-info {
            padding: 20px;
            text-align: center;
            background: rgba(0,0,0,0.1);
        }

        #content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control, .form-select {
            border-radius: var(--border-radius);
            padding: 12px;
            border: 1px solid #ddd;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .btn-submit {
            background-color: var(--secondary-color);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            margin-top: 20px;
        }

        .btn-submit:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .logout-btn {
            width: calc(100% - 40px);
            margin: 20px;
            padding: 12px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: all 0.3s;
        }

        .logout-btn:hover {
            background-color: #c0392b;
        }

        .alert {
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            padding: 15px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
            }
            
            #content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-chalkboard-teacher"></i> Öğretmen Paneli</h3>
        </div>
        <div class="teacher-info">
            <div class="teacher-name">
                <i class="fas fa-user-circle fa-2x mb-2"></i>
                <h5><?php echo $_SESSION['ogretmen_adi'] ?? 'Öğretmen'; ?></h5>
            </div>
        </div>
        <form action="logout.php" method="post">
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Çıkış Yap
            </button>
        </form>
    </nav>

    <!-- İçerik Alanı -->
    <div id="content">
        <div class="container">
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <h2 class="mb-4"><i class="fas fa-edit"></i> Not Girişi</h2>
            
            <form action="ogretmen.php" method="post">
                <div class="form-group">
                    <label for="ogrenci_id" class="form-label">Öğrenci Seçin:</label>
                    <select class="form-select" id="ogrenci_id" name="ogrenci_id" required>
                        <option value="">Seçiniz...</option>
                        <?php while ($student = mysqli_fetch_array($result_students)) : ?>
                            <option value="<?php echo htmlspecialchars($student['ogrenci_id']); ?>">
                                <?php echo htmlspecialchars($student['isim'] . " " . $student['soyisim']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="yazili_notu" class="form-label">Yazılı Notu:</label>
                            <input type="number" class="form-control" id="yazili_notu" name="yazili_notu" 
                                   min="0" max="100" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="sozlu_notu" class="form-label">Sözlü Notu:</label>
                            <input type="number" class="form-control" id="sozlu_notu" name="sozlu_notu" 
                                   min="0" max="100" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="performans_notu" class="form-label">Performans Notu:</label>
                            <input type="number" class="form-control" id="performans_notu" name="performans_notu" 
                                   min="0" max="100" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Notları Kaydet
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>