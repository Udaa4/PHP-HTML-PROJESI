<?php
include("db.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Formdan gelen verileri al
    $tc_no = $_POST['tc_no'];
    $randevu_tarihi = $_POST['randevu_tarihi'];
    $poliklinik = $_POST['poliklinik'];

    $sql = "UPDATE patients SET randevu_tarihi = ?, poliklinik = ? WHERE tc_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $randevu_tarihi, $poliklinik, $tc_no);

    if ($stmt->execute()) {
        echo "Yeni randevu başarıyla eklendi!";
        header(header: "Location: details.php?tc_no=" . $tc_no);  // Sayfayı yeniden yönlendiriyoruz
        exit;
    } else {
        echo "Hata: " . $conn->error;
    }

    // SQL sorgusu
    $sql = "INSERT INTO appointments (tc_no, randevu_tarihi, poliklinik) 
            VALUES ('$tc_no', '$randevu_tarihi', '$poliklinik')";

    // Sorguyu çalıştır ve sonucu kontrol et
    if ($conn->query($sql) === TRUE) {
        echo "<h3>Yeni randevu kaydı başarıyla eklendi!</h3>";
    } else {
        echo "Hata: " . $conn->error; // Hata varsa detayını yazdır
    }
}
?>