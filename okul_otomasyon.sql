-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 13 Şub 2025, 12:01:46
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `okul_otomasyon`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `dersler`
--

CREATE TABLE `dersler` (
  `ders_id` int(11) NOT NULL,
  `ders_adi` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `dersler`
--

INSERT INTO `dersler` (`ders_id`, `ders_adi`) VALUES
(1, 'Türkçe'),
(2, 'matematik'),
(3, 'ingilizce'),
(4, 'fen');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `devamsızlık`
--

CREATE TABLE `devamsızlık` (
  `ogrenci_id` int(11) NOT NULL,
  `devamsızlık` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `notlar`
--

CREATE TABLE `notlar` (
  `ogrenci_id` int(11) NOT NULL,
  `ders_id` int(11) DEFAULT NULL,
  `yazili_notu` int(11) DEFAULT NULL,
  `sozlu_notu` int(11) DEFAULT NULL,
  `performans_notu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `notlar`
--

INSERT INTO `notlar` (`ogrenci_id`, `ders_id`, `yazili_notu`, `sozlu_notu`, `performans_notu`) VALUES
(23, 2, 30, 40, 90);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `ogrenciler`
--

CREATE TABLE `ogrenciler` (
  `ogrenci_id` int(11) NOT NULL,
  `isim` varchar(50) NOT NULL,
  `soyisim` varchar(50) NOT NULL,
  `okul_no` int(11) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `sinif_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `ogrenciler`
--

INSERT INTO `ogrenciler` (`ogrenci_id`, `isim`, `soyisim`, `okul_no`, `sifre`, `sinif_id`) VALUES
(23, 'abdullah ', 'kakım', 123, '$2y$10$QZFZK5tPZ6BYbOCNbPLyWOyXu/JV8X0RLwTz75fORJrqAFIRj5t22', 7);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `ogretmenler`
--

CREATE TABLE `ogretmenler` (
  `ogretmen_id` int(11) NOT NULL,
  `kullanici_adi` varchar(50) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `ders_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `ogretmenler`
--

INSERT INTO `ogretmenler` (`ogretmen_id`, `kullanici_adi`, `sifre`, `ders_id`) VALUES
(26, 'mehmet oğuz', '$2y$10$DLT22HLqn1cDeYl7hcf0DOXZdDzr178J0j.lr2/mWriYLAzHyyR.m', 2);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `siniflar`
--

CREATE TABLE `siniflar` (
  `sinif_id` int(11) NOT NULL,
  `sinif_adi` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `siniflar`
--

INSERT INTO `siniflar` (`sinif_id`, `sinif_adi`) VALUES
(4, '12-b'),
(5, '12-a'),
(6, '12-c'),
(7, '12-d');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `yonetici`
--

CREATE TABLE `yonetici` (
  `yonetici_id` int(11) NOT NULL,
  `y_username` varchar(255) NOT NULL,
  `y_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `yonetici`
--

INSERT INTO `yonetici` (`yonetici_id`, `y_username`, `y_password`) VALUES
(3, 'root', '$2y$10$wrOTUYeE.SueVt.Cj/n4IOgKV8Qr8x/e2qskVHjkBVSH5p1yqTg5y');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `dersler`
--
ALTER TABLE `dersler`
  ADD PRIMARY KEY (`ders_id`);

--
-- Tablo için indeksler `devamsızlık`
--
ALTER TABLE `devamsızlık`
  ADD PRIMARY KEY (`ogrenci_id`),
  ADD UNIQUE KEY `ogrenci_id` (`ogrenci_id`),
  ADD KEY `ogrenci_id_2` (`ogrenci_id`);

--
-- Tablo için indeksler `notlar`
--
ALTER TABLE `notlar`
  ADD PRIMARY KEY (`ogrenci_id`),
  ADD UNIQUE KEY `ogrenci_id` (`ogrenci_id`),
  ADD KEY `ders_id` (`ders_id`),
  ADD KEY `ogrenci_id_2` (`ogrenci_id`);

--
-- Tablo için indeksler `ogrenciler`
--
ALTER TABLE `ogrenciler`
  ADD PRIMARY KEY (`ogrenci_id`),
  ADD UNIQUE KEY `okul_no` (`okul_no`),
  ADD UNIQUE KEY `okul_no_2` (`okul_no`),
  ADD KEY `ogrenciler_ibfk_1` (`sinif_id`),
  ADD KEY `ogrenci_id` (`ogrenci_id`);

--
-- Tablo için indeksler `ogretmenler`
--
ALTER TABLE `ogretmenler`
  ADD PRIMARY KEY (`ogretmen_id`),
  ADD KEY `ders_id` (`ders_id`);

--
-- Tablo için indeksler `siniflar`
--
ALTER TABLE `siniflar`
  ADD PRIMARY KEY (`sinif_id`);

--
-- Tablo için indeksler `yonetici`
--
ALTER TABLE `yonetici`
  ADD PRIMARY KEY (`yonetici_id`),
  ADD UNIQUE KEY `y_username` (`y_username`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `dersler`
--
ALTER TABLE `dersler`
  MODIFY `ders_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `devamsızlık`
--
ALTER TABLE `devamsızlık`
  MODIFY `ogrenci_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Tablo için AUTO_INCREMENT değeri `ogrenciler`
--
ALTER TABLE `ogrenciler`
  MODIFY `ogrenci_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Tablo için AUTO_INCREMENT değeri `ogretmenler`
--
ALTER TABLE `ogretmenler`
  MODIFY `ogretmen_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Tablo için AUTO_INCREMENT değeri `siniflar`
--
ALTER TABLE `siniflar`
  MODIFY `sinif_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `yonetici`
--
ALTER TABLE `yonetici`
  MODIFY `yonetici_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `devamsızlık`
--
ALTER TABLE `devamsızlık`
  ADD CONSTRAINT `fk_ogrenci_id` FOREIGN KEY (`ogrenci_id`) REFERENCES `ogrenciler` (`ogrenci_id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `notlar`
--
ALTER TABLE `notlar`
  ADD CONSTRAINT `notlar_ibfk_2` FOREIGN KEY (`ders_id`) REFERENCES `dersler` (`ders_id`),
  ADD CONSTRAINT `notlar_ibfk_3` FOREIGN KEY (`ogrenci_id`) REFERENCES `ogrenciler` (`ogrenci_id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `ogrenciler`
--
ALTER TABLE `ogrenciler`
  ADD CONSTRAINT `ogrenciler_ibfk_1` FOREIGN KEY (`sinif_id`) REFERENCES `siniflar` (`sinif_id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `ogretmenler`
--
ALTER TABLE `ogretmenler`
  ADD CONSTRAINT `ogretmenler_ibfk_1` FOREIGN KEY (`ders_id`) REFERENCES `dersler` (`ders_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
