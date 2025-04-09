<?php include 'db.php'; ?>
<?php
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM patients WHERE id=$id");
$patient = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Hasta Düzenle</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Hasta Düzenle</h1>
    <form method="POST" action="">
        <label>TC Kimlik No:</label>
        <input type="text" name="tcno" value="<?= $patient['tcno'] ?>" maxlength="11" required><br>
        <label>Ad Soyad:</label>
        <input type="text" name="name" value="<?= $patient['name'] ?>" required><br>
        <label>Doktor Adı:</label>
        <input type="text" name="doctor_name" value="<?= $patient['doctor_name'] ?>" required><br>
        <label>Kan Grubu:</label>
        <select name="blood_type" required>
            <option value="A+" <?= $patient['blood_type'] == 'A+' ? 'selected' : '' ?>>A+</option>
            <option value="A-" <?= $patient['blood_type'] == 'A-' ? 'selected' : '' ?>>A-</option>
            
        </select><br>
        <label>Randevu Tarihi:</label>
        <input type="date" name="appointment_date" value="<?= $patient['appointment_date'] ?>" required><br>
        <label>Tanı:</label>
        <textarea name="diagnosis" required><?= $patient['diagnosis'] ?></textarea><br>
        <button type="submit" name="update">Güncelle</button>
    </form>
    <a href="index.php">Geri Dön</a>

    <?php
    if (isset($_POST['update'])) {
        $tcno = $_POST['tcno'];
        $name = $_POST['name'];
        $doctor_name = $_POST['doctor_name'];
        $blood_type = $_POST['blood_type'];
        $appointment_date = $_POST['appointment_date'];
        $diagnosis = $_POST['diagnosis'];

        $sql = "UPDATE patients SET tcno='$tcno', name='$name', doctor_name='$doctor_name', 
                blood_type='$blood_type', appointment_date='$appointment_date', diagnosis='$diagnosis' 
                WHERE id=$id";
        if ($conn->query($sql)) {
            header("Location: index.php");
        } else {
            echo "Hata: " . $conn->error;
        }
    }
    ?>
</body>
</html>