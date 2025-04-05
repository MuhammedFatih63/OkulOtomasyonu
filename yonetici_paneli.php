<?php
session_start();
include 'db.php';

// Çıkış işlemi
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: yonetici_login.php");
    exit;
}

// Yönlendirme kontrolü
if (!isset($_SESSION['yonetici_id'])) {
    header("Location: yonetici_login.php");
    exit;
}

$yonetici_id = $_SESSION['yonetici_id'];

if (!$conn) {
    die("Bağlantı hatası: " . mysqli_connect_error());
}

// Veritabanı bağlantısından hemen sonra ekleyin
mysqli_set_charset($conn, "utf8mb4");

// POST isteği kontrolü
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Öğrenci ekleme
    if (isset($_POST['ekle_ogrenci'])) {
        $isim = mysqli_real_escape_string($conn, $_POST['isim']);
        $soyisim = mysqli_real_escape_string($conn, $_POST['soyisim']);
        $okul_no = mysqli_real_escape_string($conn, $_POST['okul_no']);
        $sifre = password_hash($_POST['sifre'], PASSWORD_DEFAULT);
        $sinif_adi = mysqli_real_escape_string($conn, $_POST['sinif_adi']);

        $sql = "SELECT sinif_id FROM siniflar WHERE sinif_adi = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $sinif_adi);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $sinif = mysqli_fetch_assoc($result);
        $sinif_id = $sinif['sinif_id'] ?? null;

        if ($sinif_id) {
            $insert_sql = "INSERT INTO ogrenciler (isim, soyisim, okul_no, sifre, sinif_id) VALUES (?, ?, ?, ?, ?)";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, "ssssi", $isim, $soyisim, $okul_no, $sifre, $sinif_id);
            
            if (mysqli_stmt_execute($insert_stmt)) {
                echo "<script>alert('Öğrenci başarıyla eklendi.');</script>";
            } else {
                echo "<script>alert('Öğrenci eklenirken bir hata oluştu.');</script>";
            }
        } else {
            echo "<script>alert('Geçersiz sınıf adı.');</script>";
        }
    }

    // Öğrenci silme
    if (isset($_POST['sil_ogrenci'])) {
        $ogrenci_id = mysqli_real_escape_string($conn, $_POST['ogrenci_id']);
        $sql = "DELETE FROM ogrenciler WHERE ogrenci_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $ogrenci_id);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Öğrenci başarıyla silindi.');</script>";
        } else {
            echo "<script>alert('Öğrenci silinirken bir hata oluştu.');</script>";
        }
    }

    // Öğrenci güncelleme
    if (isset($_POST['guncelle_ogrenci'])) {
        $ogrenci_id = mysqli_real_escape_string($conn, $_POST['ogrenci_id']);
        $okul_no = mysqli_real_escape_string($conn, $_POST['okul_no']);
        $sifre = !empty($_POST['sifre']) ? mysqli_real_escape_string($conn, $_POST['sifre']) : null;
        $sinif_adi = mysqli_real_escape_string($conn, $_POST['sinif_adi']);

        // Sınıf ID'sini al
        $sinif_sql = "SELECT sinif_id FROM siniflar WHERE sinif_adi = ?";
        $sinif_stmt = mysqli_prepare($conn, $sinif_sql);
        mysqli_stmt_bind_param($sinif_stmt, "s", $sinif_adi);
        mysqli_stmt_execute($sinif_stmt);
        $sinif_result = mysqli_stmt_get_result($sinif_stmt);
        $sinif = mysqli_fetch_assoc($sinif_result);
        $sinif_id = $sinif['sinif_id'];

        if ($sinif_id) {
            // Şifre değiştirilecek mi kontrolü
            if ($sifre !== null) {
                $update_sql = "UPDATE ogrenciler SET okul_no = ?, sifre = ?, sinif_id = ? WHERE ogrenci_id = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($update_stmt, "isii", $okul_no, $sifre, $sinif_id, $ogrenci_id);
            } else {
                $update_sql = "UPDATE ogrenciler SET okul_no = ?, sinif_id = ? WHERE ogrenci_id = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($update_stmt, "iii", $okul_no, $sinif_id, $ogrenci_id);
            }
            
            if (mysqli_stmt_execute($update_stmt)) {
                echo "<script>alert('Öğrenci bilgileri başarıyla güncellendi.');</script>";
            } else {
                echo "<script>alert('Öğrenci bilgileri güncellenirken bir hata oluştu.');</script>";
            }
        } else {
            echo "<script>alert('Geçersiz sınıf adı.');</script>";
        }
    }

    // Devamsızlık güncelleme
    if (isset($_POST['guncelle_devamsizlik'])) {
        $ogrenci_id = mysqli_real_escape_string($conn, $_POST['ogrenci_id']);
        $devamsizlik_bilgisi = mysqli_real_escape_string($conn, $_POST['devamsızlık']);

        $check_sql = "SELECT * FROM devamsızlık WHERE ogrenci_id = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "i", $ogrenci_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {
            $update_sql = "UPDATE devamsızlık SET devamsızlık = ? WHERE ogrenci_id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "si", $devamsizlik_bilgisi, $ogrenci_id);
            
            if (mysqli_stmt_execute($update_stmt)) {
                echo "<script>alert('Devamsızlık bilgisi başarıyla güncellendi.');</script>";
            } else {
                echo "<script>alert('Devamsızlık bilgisi güncellenirken bir hata oluştu.');</script>";
            }
        } else {
            echo "<script>alert('Belirtilen öğrenci için devamsızlık kaydı bulunamadı.');</script>";
        }
    }

    // Öğretmen ekleme
    if (isset($_POST['ekle_ogretmen'])) {
        $kullanici_adi = mysqli_real_escape_string($conn, $_POST['kullanici_adi']);
        $sifre = password_hash($_POST['sifre'], PASSWORD_DEFAULT);
        $ders_id = mysqli_real_escape_string($conn, $_POST['ders_id']);

        $sql = "INSERT INTO ogretmenler (kullanici_adi, sifre, ders_id) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $kullanici_adi, $sifre, $ders_id);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Öğretmen başarıyla eklendi.');</script>";
        } else {
            echo "<script>alert('Öğretmen eklenirken bir hata oluştu.');</script>";
        }
    }

    // Öğretmen silme
    if (isset($_POST['sil_ogretmen'])) {
        $ogretmen_id = mysqli_real_escape_string($conn, $_POST['ogretmen_id']);
        
        $sql = "DELETE FROM ogretmenler WHERE ogretmen_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $ogretmen_id);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Öğretmen başarıyla silindi.');</script>";
        } else {
            echo "<script>alert('Öğretmen silinirken bir hata oluştu.');</script>";
        }
    }

    // Öğretmen güncelleme
    if (isset($_POST['guncelle_ogretmen'])) {
        $ogretmen_id = mysqli_real_escape_string($conn, $_POST['ogretmen_id']);
        $ders_id = mysqli_real_escape_string($conn, $_POST['ders_id']);
        
        if (!empty($_POST['sifre'])) {
            $sifre = password_hash($_POST['sifre'], PASSWORD_DEFAULT);
            $sql = "UPDATE ogretmenler SET sifre = ?, ders_id = ? WHERE ogretmen_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sii", $sifre, $ders_id, $ogretmen_id);
        } else {
            $sql = "UPDATE ogretmenler SET ders_id = ? WHERE ogretmen_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $ders_id, $ogretmen_id);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Öğretmen bilgileri başarıyla güncellendi.');</script>";
        } else {
            echo "<script>alert('Öğretmen bilgileri güncellenirken bir hata oluştu.');</script>";
        }
    }
}

// Öğrenci listesi sorgusunu güncelle
$students_sql = "SELECT o.ogrenci_id, o.isim, o.soyisim, o.okul_no, s.sinif_adi, o.sinif_id 
                FROM ogrenciler o 
                LEFT JOIN siniflar s ON o.sinif_id = s.sinif_id";
$students = mysqli_query($conn, $students_sql);

$classes_sql = "SELECT sinif_id, sinif_adi FROM siniflar";
$classes = mysqli_prepare($conn, $classes_sql);
mysqli_stmt_execute($classes);
$classes = mysqli_stmt_get_result($classes);

$dersler_sql = "SELECT ders_id, ders_adi FROM dersler";
$dersler = mysqli_prepare($conn, $dersler_sql);
mysqli_stmt_execute($dersler);
$dersler = mysqli_stmt_get_result($dersler);

$ogretmenler_sql = "SELECT ogretmen_id, kullanici_adi FROM ogretmenler";
$ogretmenler = mysqli_prepare($conn, $ogretmenler_sql);
mysqli_stmt_execute($ogretmenler);
$ogretmenler = mysqli_stmt_get_result($ogretmenler);

mysqli_close($conn);

function executeStatement($sql, $success_message, $error_message)
{
    global $conn;
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('$success_message');</script>";
    } else {
        echo "<script>alert('$error_message');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Ana stiller */
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
        }

        body {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar stilleri */
        #sidebar {
            width: 250px;
            background: var(--primary-color);
            color: white;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
        }

        #sidebar .sidebar-header {
            padding: 20px;
            background: rgba(0,0,0,0.1);
        }

        #sidebar ul {
            padding: 0;
            list-style: none;
        }

        #sidebar ul li a {
            padding: 15px 20px;
            display: block;
            color: white;
            text-decoration: none;
            transition: 0.3s;
        }

        #sidebar ul li a:hover,
        #sidebar ul li a.active {
            background: var(--secondary-color);
        }

        /* İçerik alanı stilleri */
        #content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }

        .section {
            display: none;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .section.active {
            display: block;
        }

        /* Form stilleri */
        .form-group {
            margin-bottom: 20px;
        }

        .btn {
            padding: 10px 20px;
        }

        /* Mobil uyumluluk */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
            }
            
            #sidebar.active {
                margin-left: 0;
            }

            #content {
                margin-left: 0;
                width: 100%;
            }

            #sidebarCollapse {
                display: block;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-user-shield"></i> Yönetici Paneli</h3>
        </div>

        <ul class="list-unstyled">
            <li>
                <a href="#" class="nav-link active" data-section="ekle_ogrenci">
                    <i class="fas fa-user-plus"></i> Öğrenci Ekle
                </a>
            </li>
            <li>
                <a href="#" class="nav-link" data-section="sil_ogrenci">
                    <i class="fas fa-user-minus"></i> Öğrenci Sil
                </a>
            </li>
            <li>
                <a href="#" class="nav-link" data-section="guncelle_ogrenci">
                    <i class="fas fa-user-edit"></i> Öğrenci Güncelle
                </a>
            </li>
            <li>
                <a href="#" class="nav-link" data-section="guncelle_devamsizlik">
                    <i class="fas fa-calendar-check"></i> Devamsızlık
                </a>
            </li>
            <li>
                <a href="#" class="nav-link" data-section="ekle_ogretmen">
                    <i class="fas fa-chalkboard-teacher"></i> Öğretmen Ekle
                </a>
            </li>
            <li>
                <a href="#" class="nav-link" data-section="sil_ogretmen">
                    <i class="fas fa-user-slash"></i> Öğretmen Sil
                </a>
            </li>
            <li>
                <a href="#" class="nav-link" data-section="guncelle_ogretmen">
                    <i class="fas fa-user-cog"></i> Öğretmen Güncelle
                </a>
            </li>
            <li>
                <a href="?action=logout">
                    <i class="fas fa-sign-out-alt"></i> Çıkış
                </a>
            </li>
        </ul>
    </nav>

    <!-- İçerik Alanı -->
    <div id="content">
        <!-- Öğrenci Ekleme Formu -->
        <div id="ekle_ogrenci" class="section">
            <h2><i class="fas fa-user-plus me-2"></i>Öğrenci Ekle</h2>
            <form method="post">
                <div class="form-group">
                    <label for="isim" class="form-label">İsim:</label>
                    <input type="text" class="form-control" name="isim" required>
                </div>
                <div class="form-group">
                    <label for="soyisim" class="form-label">Soyisim:</label>
                    <input type="text" class="form-control" name="soyisim" required>
                </div>
                <div class="form-group">
                    <label for="okul_no" class="form-label">Okul Numarası:</label>
                    <input type="text" class="form-control" name="okul_no" required>
                </div>
                <div class="form-group">
                    <label for="sifre" class="form-label">Şifre:</label>
                    <input type="password" class="form-control" name="sifre" required>
                </div>
                <div class="form-group">
                    <label for="sinif_adi" class="form-label">Sınıf:</label>
                    <select class="form-select" name="sinif_adi" required>
                        <option value="">Seçiniz...</option>
                        <?php 
                        mysqli_data_seek($classes, 0);
                        while ($row = mysqli_fetch_assoc($classes)) : 
                        ?>
                            <option value="<?= htmlspecialchars($row['sinif_adi']) ?>">
                                <?= htmlspecialchars($row['sinif_adi']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" name="ekle_ogrenci">
                    <i class="fas fa-plus-circle me-2"></i>Öğrenci Ekle
                </button>
            </form>
        </div>

        <!-- Öğrenci Silme Formu -->
        <div id="sil_ogrenci" class="section">
            <h2><i class="fas fa-user-minus me-2"></i>Öğrenci Sil</h2>
            <form method="post">
                <div class="form-group">
                    <label for="ogrenci_id" class="form-label">Öğrenci Seçin:</label>
                    <select class="form-select" name="ogrenci_id" required>
                        <option value="">Seçiniz...</option>
                        <?php 
                        mysqli_data_seek($students, 0);
                        while ($row = mysqli_fetch_assoc($students)) : 
                        ?>
                            <option value="<?= htmlspecialchars($row['ogrenci_id']) ?>">
                                <?= htmlspecialchars($row['isim'] . ' ' . $row['soyisim']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-danger" name="sil_ogrenci" onclick="return confirm('Bu öğrenciyi silmek istediğinizden emin misiniz?')">
                    <i class="fas fa-trash me-2"></i>Öğrenciyi Sil
                </button>
            </form>
        </div>

        <!-- Öğrenci Güncelleme Formu -->
        <div id="guncelle_ogrenci" class="section">
            <h2><i class="fas fa-user-edit me-2"></i>Öğrenci Güncelle</h2>
            <form method="post">
                <div class="form-group">
                    <label for="ogrenci_id" class="form-label">Öğrenci Seçin:</label>
                    <select class="form-select" name="ogrenci_id" id="ogrenci_select" required>
                        <option value="">Seçiniz...</option>
                        <?php 
                        mysqli_data_seek($students, 0);
                        while ($row = mysqli_fetch_assoc($students)) : 
                        ?>
                            <option value="<?= htmlspecialchars($row['ogrenci_id']) ?>"
                                    data-okul-no="<?= htmlspecialchars($row['okul_no']) ?>"
                                    data-sinif-adi="<?= htmlspecialchars($row['sinif_adi']) ?>">
                                <?= htmlspecialchars($row['isim'] . ' ' . $row['soyisim']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="okul_no" class="form-label">Okul Numarası:</label>
                    <input type="text" class="form-control" name="okul_no" id="okul_no" required>
                </div>
                <div class="form-group">
                    <label for="sifre" class="form-label">Yeni Şifre:</label>
                    <input type="password" class="form-control" name="sifre">
                    <small class="text-muted">Şifreyi değiştirmek istemiyorsanız boş bırakın.</small>
                </div>
                <div class="form-group">
                    <label for="sinif_adi" class="form-label">Sınıf:</label>
                    <select class="form-select" name="sinif_adi" id="sinif_adi" required>
                        <option value="">Seçiniz...</option>
                        <?php 
                        mysqli_data_seek($classes, 0);
                        while ($row = mysqli_fetch_assoc($classes)) : 
                        ?>
                            <option value="<?= htmlspecialchars($row['sinif_adi']) ?>">
                                <?= htmlspecialchars($row['sinif_adi']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-warning" name="guncelle_ogrenci">
                    <i class="fas fa-save me-2"></i>Öğrenci Bilgilerini Güncelle
                </button>
            </form>
        </div>

        <!-- Devamsızlık Güncelleme Formu -->
        <div id="guncelle_devamsizlik" class="section">
            <h2><i class="fas fa-calendar-check me-2"></i>Devamsızlık Güncelle</h2>
            <form method="post">
                <div class="form-group">
                    <label for="ogrenci_id" class="form-label">Öğrenci Seçin:</label>
                    <select class="form-select" name="ogrenci_id" required>
                        <option value="">Seçiniz...</option>
                        <?php 
                        mysqli_data_seek($students, 0);
                        while ($row = mysqli_fetch_assoc($students)) : 
                        ?>
                            <option value="<?= htmlspecialchars($row['ogrenci_id']) ?>">
                                <?= htmlspecialchars($row['isim'] . ' ' . $row['soyisim']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="devamsızlık" class="form-label">Devamsızlık Sayısı:</label>
                    <input type="number" class="form-control" name="devamsızlık" required>
                </div>
                <button type="submit" class="btn btn-info" name="guncelle_devamsizlik">
                    <i class="fas fa-save me-2"></i>Devamsızlık Bilgisini Güncelle
                </button>
            </form>
        </div>

        <!-- Öğretmen Ekleme Formu -->
        <div id="ekle_ogretmen" class="section">
            <h2><i class="fas fa-chalkboard-teacher me-2"></i>Öğretmen Ekle</h2>
            <form method="post">
                <div class="form-group">
                    <label for="kullanici_adi" class="form-label">Kullanıcı Adı:</label>
                    <input type="text" class="form-control" name="kullanici_adi" required>
                </div>
                <div class="form-group">
                    <label for="sifre" class="form-label">Şifre:</label>
                    <input type="password" class="form-control" name="sifre" required>
                </div>
                <div class="form-group">
                    <label for="ders_id" class="form-label">Ders:</label>
                    <select class="form-select" name="ders_id" required>
                        <option value="">Seçiniz...</option>
                        <?php 
                        mysqli_data_seek($dersler, 0);
                        while ($row = mysqli_fetch_assoc($dersler)) : 
                        ?>
                            <option value="<?= htmlspecialchars($row['ders_id']) ?>">
                                <?= htmlspecialchars($row['ders_adi']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" name="ekle_ogretmen">
                    <i class="fas fa-plus-circle me-2"></i>Öğretmen Ekle
                </button>
            </form>
        </div>

        <!-- Öğretmen Silme Formu -->
        <div id="sil_ogretmen" class="section">
            <h2><i class="fas fa-user-slash me-2"></i>Öğretmen Sil</h2>
            <form method="post">
                <div class="form-group">
                    <label for="ogretmen_id" class="form-label">Öğretmen Seçin:</label>
                    <select class="form-select" name="ogretmen_id" required>
                        <option value="">Seçiniz...</option>
                        <?php 
                        mysqli_data_seek($ogretmenler, 0);
                        while ($row = mysqli_fetch_assoc($ogretmenler)) : 
                        ?>
                            <option value="<?= htmlspecialchars($row['ogretmen_id']) ?>">
                                <?= htmlspecialchars($row['kullanici_adi']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-danger" name="sil_ogretmen" onclick="return confirm('Bu öğretmeni silmek istediğinizden emin misiniz?')">
                    <i class="fas fa-trash me-2"></i>Öğretmeni Sil
                </button>
            </form>
        </div>

        <!-- Öğretmen Güncelleme Formu -->
        <div id="guncelle_ogretmen" class="section">
            <h2><i class="fas fa-user-cog me-2"></i>Öğretmen Güncelle</h2>
            <form method="post">
                <div class="form-group">
                    <label for="ogretmen_id" class="form-label">Öğretmen Seçin:</label>
                    <select class="form-select" name="ogretmen_id" required>
                        <option value="">Seçiniz...</option>
                        <?php 
                        mysqli_data_seek($ogretmenler, 0);
                        while ($row = mysqli_fetch_assoc($ogretmenler)) : 
                        ?>
                            <option value="<?= htmlspecialchars($row['ogretmen_id']) ?>">
                                <?= htmlspecialchars($row['kullanici_adi']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="sifre" class="form-label">Yeni Şifre:</label>
                    <input type="password" class="form-control" name="sifre">
                    <small class="text-muted">Şifreyi değiştirmek istemiyorsanız boş bırakın.</small>
                </div>
                <div class="form-group">
                    <label for="ders_id" class="form-label">Ders:</label>
                    <select class="form-select" name="ders_id" required>
                        <option value="">Seçiniz...</option>
                        <?php 
                        mysqli_data_seek($dersler, 0);
                        while ($row = mysqli_fetch_assoc($dersler)) : 
                        ?>
                            <option value="<?= htmlspecialchars($row['ders_id']) ?>">
                                <?= htmlspecialchars($row['ders_adi']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-warning" name="guncelle_ogretmen">
                    <i class="fas fa-save me-2"></i>Öğretmen Bilgilerini Güncelle
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sayfa yüklendiğinde ilk sekmeyi göster
            document.querySelector('.section').classList.add('active');
            
            // Sekme değiştirme işlevi
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    if(this.getAttribute('data-section')) {
                        e.preventDefault();
                        
                        // Tüm sekmeleri gizle
                        document.querySelectorAll('.section').forEach(section => {
                            section.classList.remove('active');
                        });
                        
                        // Tüm linklerin active class'ını kaldır
                        document.querySelectorAll('.nav-link').forEach(navLink => {
                            navLink.classList.remove('active');
                        });
                        
                        // Seçilen sekmeyi göster
                        const targetId = this.getAttribute('data-section');
                        document.getElementById(targetId).classList.add('active');
                        this.classList.add('active');
                    }
                });
            });
        });
    </script>
</body>

</html>