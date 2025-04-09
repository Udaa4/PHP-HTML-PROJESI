<?php
// Veritabanı bağlantısını ekleyin
include 'db.php';

// TC kimlik numarasını al
if (isset($_GET['tc_no'])) {
    $tc_no = $_GET['tc_no'];

    // Veritabanında bu TC'ye sahip hastayı silme sorgusu
    $sql = "DELETE FROM patients WHERE tc_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $tc_no);
    
    if ($stmt->execute()) {
        echo "Hasta kaydı başarıyla silindi.";
    } else {
        echo "Hata: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasta Silme</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <h1>Hasta Silme</h1>
    <form method="GET" action="delete.php">
        <label for="tc_no">TC Kimlik Numarası:</label>
        <input type="text" name="tc_no" required>
        <button type="submit">Sil</button>
    </form>
</div>

</body>
</html>