<?php
require_once "db.php";

$tc_no = $_POST['tc_no'];
$visit_type = $_POST['visit_type'];
$visit_date = $_POST['visit_date'];

// Sorgu oluştur
$sql = "INSERT INTO visits (tc_no, visit_type, visit_date) VALUES ('$tc_no', '$visit_type', '$visit_date')";

// Sorguyu çalıştır
if ($conn->query($sql) === TRUE) {
    echo "Yeni ziyaret kaydı başarıyla eklendi.";
    header("Location: details.php?tc_no=$tc_no");
    exit;
} else {
    echo "Hata: " . $conn->error;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="add_visit.php" method="POST">
        <input type="hidden" name="tc_no" value="<?php echo $tc_no; ?>">
        <label for="visit_type">Ziyaret Türü:</label>
        <select name="visit_type" id="visit_type" required>
            <option value="Aşı">Aşı</option>
            <option value="Enjeksiyon">Enjeksiyon</option>
            <option value="Muayene">Muayene</option>
        </select>
        <label for="visit_date">Ziyaret Tarihi:</label>
        <input type="date" name="visit_date" id="visit_date" required>
        <button type="submit">Kaydet</button>
    </form>
</body>

</html>


<?php
require_once "db.php";

if (isset($_POST['tc_no'], $_POST['visit_type'], $_POST['visit_date'])) {
    $tc_no = $_POST['tc_no'];
    $visit_type = $_POST['visit_type'];
    $visit_date = $_POST['visit_date'];

    $sql = "INSERT INTO visits (tc_no, visit_type, visit_date) VALUES ('$tc_no', '$visit_type', '$visit_date')";

    if ($conn->query($sql) === TRUE) {
        header("Location: details.php?tc_no=$tc_no");
        exit;
    } else {
        echo "Hata: " . $conn->error;
    }
} else {
    echo "Eksik bilgi gönderildi.";
}
?>