<?php
// Veritabanı bağlantısını al
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Formdan gelen verileri alın
    $tc_no = $_POST['tc_no'];
    $ad_soyad = $_POST['ad_soyad'];
    $telefon_no = $_POST['telefon_no'];
    $doktor_adi = $_POST['doktor_adi'];
    $kan_grubu = $_POST['kan_grubu'];
    $dogum_tarihi = $_POST['dogum_tarihi'];
    $poliklinik = $_POST['poliklinik'];
    $gelis_tarihi = date('Y-m-d');

    // SQL sorgusu
    $sql = "INSERT INTO patients (tc_no, ad_soyad, telefon_no, doktor_adi, kan_grubu, dogum_tarihi, poliklinik, gelis_tarihi) 
            VALUES ('$tc_no', '$ad_soyad','$telefon_no','$doktor_adi', '$kan_grubu',
            '$dogum_tarihi', '$poliklinik', '$gelis_tarihi')";

    // Sorguyu çalıştır ve sonucu kontrol et
    if ($conn->query($sql) === TRUE) {
        echo "<h3>Yeni hasta kaydı başarıyla eklendi!</h3>";
    } else {
        echo "Hata: " . $conn->error; // Hata varsa detayını yazdır
    }

}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Hasta Kaydı</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <div style="text-align: center; margin-top: 20px;">
        <a href="index.php"><button>Ana Sayfaya Dön</button></a>
    </div>


    <div class="container">
        <h1>Yeni Hasta Kaydı</h1>
        <form method="POST">
            <label>TC Kimlik Numarası:</label>
            <input type="text" name="tc_no" required><br>

            <label>Ad Soyad:</label>
            <input type="text" name="ad_soyad" required><br>

            <label>Telefon Numarası:</label>
            <input type="text" name="telefon_no" required><br>

            <label>Doktor Adı:</label>
            <input type="text" name="doktor_adi" required><br>

            <label>Kan Grubu:</label>
            <input type="text" name="kan_grubu"><br>

            <label>Dogum Tarihi:</label>
            <input type="text" name="dogum_tarihi" required><br>

            <label>Poliklinik:</label>
            <textarea name="poliklinik"></textarea><br>

            <label>Tarih:</label>
            <input type="date" name="gelis_tarihi" required><br>

            <input type="submit" value="Yeni Hasta Kaydı Ekle">
        </form>
    </div>

</body>

</html>