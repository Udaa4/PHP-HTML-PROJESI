<?php
include("db.php");

if (isset($_GET['tc_no'])) {
    $tc_no = $_GET['tc_no'];

    // Hasta bilgilerini al
    $stmt = $conn->prepare("SELECT tc_no, ad_soyad, telefon_no, doktor_adi, kan_grubu, dogum_tarihi, poliklinik, gelis_tarihi,randevu_tarihi FROM patients WHERE tc_no = ?");
    $stmt->bind_param("s", $tc_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();
    } else {
        echo "<p style='color: red; text-align: center;'>Bu TC Kimlik Numarası ile hasta bulunamadı!</p>";
        exit;
    }

    if (empty($patient['randevu_tarihi'])) {
        $patient['randevu_tarihi'] = '';
    }


    // Hasta ziyaret geçmişini al
    $stmt_visits = $conn->prepare("SELECT visit_type, visit_date FROM visits WHERE tc_no = ? ORDER BY visit_date DESC");
    $stmt_visits->bind_param("s", $tc_no);
    $stmt_visits->execute();
    $visits_result = $stmt_visits->get_result();
} else {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasta Detayları</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="header-logo">
        <a href="index.php" class="home-button">Ana Sayfa</a>
    </div>

    <div class="details-container">
        <div class="patient-details">
            <h2>Hasta Bilgileri</h2>
            <p><strong>Ad ve Soyad:</strong> <?php echo $patient['ad_soyad']; ?></p>
            <p><strong>TC Kimlik No:</strong> <?php echo $patient['tc_no']; ?></p>
            <p><strong>Telefon Numarası:</strong><?php echo $patient['telefon_no']; ?></p>
            <p><strong>Doktor Adı:</strong> <?php echo $patient['doktor_adi']; ?></p>
            <p><strong>Kan Grubu:</strong> <?php echo $patient['kan_grubu']; ?></p>
            <p><strong>Dogum Tarihi:</strong> <?php echo $patient['dogum_tarihi']; ?></p>
            <p><strong>Poliklinik:</strong> <?php echo htmlspecialchars(string: $patient['poliklinik']); ?></p>
            <p><strong>Tarih:</strong> <?php echo $patient['gelis_tarihi'] ?: date(format: 'Y-m-d'); ?></p>
            <p><strong>Yeni Randevu Tarihi:</strong> <?php echo $patient['randevu_tarihi']; ?></p>



        </div>



        <div class="patient-history">
            <h2>Hasta Geçmişi</h2>
            <?php if ($visits_result->num_rows > 0): ?>
                <ul>
                    <?php while ($visit = $visits_result->fetch_assoc()): ?>
                        <li>
                            <strong>Ziyaret Türü:</strong> <?php echo $visit['visit_type']; ?> <br>
                            <strong>Tarih:</strong> <?php echo $visit['visit_date']; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <h1>Bu hasta daha önce hastanemizi ziyaret etmemiş.</h1>

            <?php endif; ?>
            <?php ?>

            <hr>
            <h2>Yeni Ziyaret Ekle</h2>
            <form action="add_visit.php" method="POST">
                <input type="hidden" name="tc_no" value="<?php echo $tc_no; ?>">

                <label for="visit_type">Ziyaret Türü:</label>
                <select name="visit_type" id="visit_type" required>
                    <option value="Aşı">Aşı</option>
                    <option value="Enjeksiyon">Enjeksiyon</option>
                    <option value="Muayene">Muayene</option>
                </select>
                <br><br>

                <label for="visit_date">Ziyaret Tarihi:</label>
                <input type="date" name="visit_date" id="visit_date" required>
                <br><br>

                <button type="submit">Yeni Ziyaret Ekle</button>
            </form>
            <h2>Yeni Randevu Ekle</h2>
            <form action="add_appointment.php" method="POST">
                <input type="hidden" name="tc_no" value="<?php echo $tc_no; ?>">

                <label for="randevu_tarihi">Yeni Randevu Tarihi:</label>
                <input type="date" name="randevu_tarihi" id="randevu_tarihi" required>
                <br><br>

                <label for="poliklinik">Poliklinik Seçin:</label>
                <select name="poliklinik" id="poliklinik" required>
                    <option value="Genel">Genel</option>
                    <option value="Dahiliye">Dahiliye</option>
                    <option value="Cerrahi">Cerrahi</option>
                    <option value="Kardiyoloji">Kardiyoloji</option>
                    <option value="Kulak, Burun,Boğaz">KBB</option>
                    <option value="Onkoloji">Onkoloji</option>
                    <option value="Dermatoloji">Dermatoloji</option>

                    <!-- Diğer poliklinik seçeneklerini ekleyebilirsiniz -->
                </select>
                <br><br>

                <button type="submit">Yeni Randevu Ekle</button>
            </form>

</body>

</html>