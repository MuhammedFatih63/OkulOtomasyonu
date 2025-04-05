<?php
session_start();
include 'db.php';

if (!isset($_SESSION['ogrenci_id'])) {
    header("Location: login.php");
    exit;
}

$ogrenci_id = $_SESSION['ogrenci_id'];

// Öğrenci adını ve soyadını alalım
$sql_ad_soyad = "SELECT isim, soyisim FROM ogrenciler WHERE ogrenci_id = $ogrenci_id";
$result_ad_soyad = mysqli_query($conn, $sql_ad_soyad);

if ($result_ad_soyad) {
    $ad_soyad = mysqli_fetch_assoc($result_ad_soyad);
    $ad = $ad_soyad['isim'];
    $soyad = $ad_soyad['soyisim'];
} else {
    die("Öğrenci bilgisi bulunamadı.");
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğrenci Paneli</title>
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

        /* Sidebar stilleri */
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

        .student-info {
            padding: 20px;
            text-align: center;
            background: rgba(0,0,0,0.1);
        }

        .student-name {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 10px;
            color: #fff;
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
            transition: all 0.3s;
        }

        #sidebar ul li a:hover,
        #sidebar ul li a.active {
            background: var(--secondary-color);
            color: white;
        }

        #sidebar ul li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* İçerik alanı stilleri */
        #content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 20px;
            display: none;
        }

        .container.active {
            display: block;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Tablo stilleri */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        /* Başlık stilleri */
        h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--secondary-color);
        }

        /* Çıkış butonu */
        .logout-btn {
            width: 100%;
            padding: 10px;
            background-color: #e74c3c;
            color: white;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }

        .logout-btn:hover {
            background-color: #c0392b;
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
            <h3><i class="fas fa-user-graduate"></i> Öğrenci Paneli</h3>
        </div>
        <div class="student-info">
            <div class="student-name">
                <i class="fas fa-user-circle"></i>
                <?php echo $ad . " " . $soyad; ?>
            </div>
        </div>
        <ul class="list-unstyled">
            <li>
                <a href="#" class="nav-link active" data-section="notlar">
                    <i class="fas fa-book"></i> Notlar
                </a>
            </li>
            <li>
                <a href="#" class="nav-link" data-section="devamsizlik">
                    <i class="fas fa-calendar-check"></i> Devamsızlık Bilgisi
                </a>
            </li>
            <li>
                <a href="#" class="nav-link" data-section="sinif">
                    <i class="fas fa-school"></i> Sınıf Bilgisi
                </a>
            </li>
            <li>
                <form action="logout.php" method="post">
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    <!-- İçerik Alanı -->
    <div id="content">
        <div id="notlar" class="container active">
            <h2><i class="fas fa-book me-2"></i>Notlar</h2>
            <?php
            $sql_notlar = "SELECT dersler.ders_adi, notlar.yazili_notu, notlar.sozlu_notu, notlar.performans_notu
                        FROM notlar 
                        JOIN dersler ON notlar.ders_id = dersler.ders_id 
                        WHERE notlar.ogrenci_id = $ogrenci_id";
            $result_notlar = mysqli_query($conn, $sql_notlar);

            if (mysqli_num_rows($result_notlar) > 0) {
                echo "<div class='table-responsive'>";
                echo "<table class='table'>";
                echo "<thead><tr><th>Ders</th><th>Yazılı Notu</th><th>Sözlü Notu</th><th>Performans Notu</th></tr></thead>";
                echo "<tbody>";
                while ($row = mysqli_fetch_assoc($result_notlar)) {
                    echo "<tr>";
                    echo "<td>{$row['ders_adi']}</td>";
                    echo "<td>{$row['yazili_notu']}</td>";
                    echo "<td>{$row['sozlu_notu']}</td>";
                    echo "<td>{$row['performans_notu']}</td>";
                    echo "</tr>";
                }
                echo "</tbody></table></div>";
            } else {
                echo "<div class='alert alert-info'>Not bilgisi bulunamadı.</div>";
            }
            ?>
        </div>

        <div id="devamsizlik" class="container">
            <h2><i class="fas fa-calendar-check me-2"></i>Devamsızlık Bilgisi</h2>
            <?php
            $sql_devamsizlik = "SELECT devamsızlık FROM devamsızlık WHERE ogrenci_id = $ogrenci_id";
            $result_devamsizlik = mysqli_query($conn, $sql_devamsizlik);

            if (mysqli_num_rows($result_devamsizlik) > 0) {
                $row_devamsizlik = mysqli_fetch_assoc($result_devamsizlik);
                echo "<div class='alert alert-info'>";
                echo "<i class='fas fa-info-circle me-2'></i>";
                echo "Toplam devamsızlık: <strong>{$row_devamsizlik['devamsızlık']} gün</strong>";
                echo "</div>";
            } else {
                echo "<div class='alert alert-info'>Devamsızlık bilgisi bulunamadı.</div>";
            }
            ?>
        </div>

        <div id="sinif" class="container">
            <h2><i class="fas fa-school me-2"></i>Sınıf Bilgisi</h2>
            <?php
            $sql_sinif = "SELECT siniflar.sinif_adi
                          FROM siniflar 
                          JOIN ogrenciler ON siniflar.sinif_id = ogrenciler.sinif_id
                          WHERE ogrenciler.ogrenci_id = $ogrenci_id";
            $result_sinif = mysqli_query($conn, $sql_sinif);

            if (mysqli_num_rows($result_sinif) > 0) {
                $row_sinif = mysqli_fetch_assoc($result_sinif);
                echo "<div class='alert alert-info'>";
                echo "<i class='fas fa-info-circle me-2'></i>";
                echo "Sınıfınız: <strong>{$row_sinif['sinif_adi']}</strong>";
                echo "</div>";
            } else {
                echo "<div class='alert alert-info'>Sınıf bilgisi bulunamadı.</div>";
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sayfa yüklendiğinde ilk sekmeyi göster
            document.querySelector('.container').classList.add('active');
            
            // Sekme değiştirme işlevi
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    if(this.getAttribute('data-section')) {
                        e.preventDefault();
                        
                        // Tüm sekmeleri gizle
                        document.querySelectorAll('.container').forEach(container => {
                            container.classList.remove('active');
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