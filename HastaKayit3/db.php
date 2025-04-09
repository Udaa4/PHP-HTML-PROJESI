<?php
// Veri tabanı bağlantısı için gerekli bilgiler
$host = 'localhost';  // XAMPP için genelde 'localhost' kullanılır
$username = 'root';   // Varsayılan kullanıcı adı
$password = '';       // XAMPP'de varsayılan şifre boş bırakılır
$dbname = 'hastakayit1'; // Veri tabanınızın adı

// Veri tabanı bağlantısını oluştur
$conn = new mysqli($host, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Veri tabanı bağlantısı başarısız: " . $conn->connect_error);
}

// Türkçe karakter desteği için charset ayarla
$conn->set_charset("utf8");
?>
