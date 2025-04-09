<?php
// Veritabanı bağlantısını yapıyoruz
include 'db.php';

// Eğer TC Kimlik Numarası sorgulanmışsa
if (isset($_GET['tc_no'])) {
    $tc_no = $_GET['tc_no'];

    // Veritabanında bu TC'ye sahip hastayı sorgulama
    $sql = "SELECT * FROM patients WHERE tc_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $tc_no);
    $stmt->execute();
    $result = $stmt->get_result();

    // Eğer hasta bulunursa, bilgilerini göster
    if ($result->num_rows > 0) {
        // Hasta bilgileri bulunduysa hasta detay sayfasına yönlendir
        header("Location: details.php?tc_no=$tc_no");
        exit;

    } else {
        // Hasta bulunamazsa, yeni hasta kaydı için link göster
        $patient_info = "<p>Bu TC Kimlik Numarası ile hasta bulunamadı. Yeni hasta kaydı eklemek için <br>
        <a href='add.php?tc_no=$tc_no'>buraya tıklayın</a>.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasta Sorgulama</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <!-- Ana Sayfa Butonu -->
    <div class="header-logo">
        <a href="index.php" class="home-button">Ana Sayfa</a>
        <a href="search_appointments.php" class="appointments-button">Randevular</a>
    </div>

    <div class="container">
        <h1>Hasta Sorgulama</h1>
        <form method="GET" action="patient_search.php">
            <label for="tc_no">TC Kimlik Numarası:</label>
            <input type="text" name="tc_no" required>
            <button type="submit">Sorgula</button>
        </form>


        <div class="patient-info">
            <?php
            // Hasta bilgileri ya da hata mesajı burada gösterilecektir.
            if (isset($patient_info)) {
                echo $patient_info;
            }
            ?>
        </div>
    </div>
    <div class="welcome-box">
        <h3>Hoş Geldiniz!</h3>
        <button id="randevuButton">Bugünün Randevuları</button>
    </div>

    <div id="randevuModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Bugünün Randevuları</h2>
            <div id="randevuListesi"></div>
        </div>
    </div>

    <script>
        document.getElementById('randevuButton').addEventListener('click', function () {
            // AJAX ile bugünün randevularını çekme
            fetch('get_today_appointments.php')
                .then(response => response.json())
                .then(data => {
                    const liste = document.getElementById('randevuListesi');
                    liste.innerHTML = ''; // Önceki içeriği temizle

                    if (data.length > 0) {
                        data.forEach(randevu => {
                            liste.innerHTML += `
                            <p>
                                TC No: ${randevu.tc_no}<br>
                                Ad Soyad: ${randevu.ad_soyad}<br>
                                Telefon: ${randevu.telefon_no}
                            </p>
                            <hr>
                        `;
                        });
                    } else {
                        liste.innerHTML = '<p>Bugün için randevu bulunmuyor.</p>';
                    }

                    document.getElementById('randevuModal').style.display = 'block';
                });
        });

        // Modal kapatma
        document.querySelector('.close').addEventListener('click', function () {
            document.getElementById('randevuModal').style.display = 'none';
        });

        // Modal dışına tıklandığında kapatma
        window.addEventListener('click', function (event) {
            const modal = document.getElementById('randevuModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });
    </script>






</body>

</html>