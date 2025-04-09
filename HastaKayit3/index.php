<?php
include 'db.php';
session_start();

// Güvenlik kontrolü (isteğe bağlı)
// Eğer kullanıcı girişi mekanizmanız varsa
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

// Dashboard için gerekli istatistikleri çek
$today = date('Y-m-d');

// Toplam hasta sayısı
$total_patients_query = "SELECT COUNT(*) as total FROM patients";
$total_patients_result = $conn->query($total_patients_query);
$total_patients = $total_patients_result->fetch_assoc()['total'];

// Bugünün randevuları
$today_appointments_query = "SELECT * FROM patients WHERE randevu_tarihi = '$today'";
$today_appointments_result = $conn->query($today_appointments_query);
$today_appointments_count = $today_appointments_result->num_rows;

// Son eklenen hastalar
$recent_patients_query = "SELECT * FROM patients ORDER BY gelis_tarihi DESC LIMIT 5";
$recent_patients_result = $conn->query($recent_patients_query);

// Poliklinik dağılımı
$clinic_distribution_query = "
    SELECT poliklinik, COUNT(*) as hasta_sayisi 
    FROM patients 
    WHERE poliklinik != '' 
    GROUP BY poliklinik 
    ORDER BY hasta_sayisi DESC 
    LIMIT 5";
$clinic_distribution_result = $conn->query($clinic_distribution_query);

// Aylık ziyaret istatistikleri
$monthly_visits_query = "
    SELECT 
        MONTH(visit_date) as ay, 
        COUNT(*) as ziyaret_sayisi 
    FROM visits 
    WHERE YEAR(visit_date) = YEAR(CURRENT_DATE) 
    GROUP BY ay 
    ORDER BY ay";
$monthly_visits_result = $conn->query($monthly_visits_query);
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Hastane Yönetim Sistemi Dashboard</title>
    <link rel="stylesheet" href="style1.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="header-logo">
        <a href="index.php" class="home-button">Ana Sayfa</a>
        <a href="patient_search.php" class="patientsearch-button">Hasta Sorgulama</a>
        <a href="search_appointments.php" class="appointments-button">Randevular</a>
        <a href="add.php" class="newpatients-button">Yeni Hasta Kaydı</a>
    </div>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Hastane Yönetim Sistemi Dashboard</h1>
        </div>

        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Toplam Hasta Sayısı</h3>
                <p><?php echo $total_patients; ?></p>
            </div>
            <div class="stat-card">
                <h3>Bugünün Randevuları</h3>
                <p><?php echo $today_appointments_count; ?></p>
            </div>
        </div>

        <div class="dashboard-content">
            <div class="recent-patients">
                <h2>Son Eklenen Hastalar</h2>
                <table>
                    <thead>
                        <tr>
                            <th>TC No</th>
                            <th>Ad Soyad</th>
                            <th>Geliş Tarihi</th>
                            <th>Poliklinik</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($patient = $recent_patients_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $patient['tc_no']; ?></td>
                                <td><?php echo $patient['ad_soyad']; ?></td>
                                <td><?php echo $patient['gelis_tarihi']; ?></td>
                                <td><?php echo $patient['poliklinik']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="clinic-chart">
                <h2>Poliklinik Dağılımı</h2>
                <canvas id="clinicChart"></canvas>
            </div>
        </div>

        <div class="visits-chart">
            <h2>Aylık Ziyaret İstatistikleri</h2>
            <canvas id="visitsChart"></canvas>
        </div>
    </div>
    <div class="header-logo">
        <div class="hamburger-menu" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <nav class="menu-items">
            <a href="index.php" class="home-button">Ana Sayfa</a>
            <a href="patient_search.php" class="patientsearch-button">Hasta Sorgulama</a>
            <a href="search_appointments.php" class="appointments-button">Randevular</a>
            <a href="add.php" class="newpatients-button">Yeni Hasta Kaydı</a>
        </nav>
    </div>

    <script>
        // Poliklinik Dağılımı Grafiği
        const clinicCtx = document.getElementById('clinicChart').getContext('2d');
        const clinicData = {
            labels: [
                <?php
                $clinic_labels = [];
                $clinic_counts = [];
                mysqli_data_seek($clinic_distribution_result, 0);
                while ($row = $clinic_distribution_result->fetch_assoc()) {
                    $clinic_labels[] = "'" . $row['poliklinik'] . "'";
                    $clinic_counts[] = $row['hasta_sayisi'];
                }
                echo implode(',', $clinic_labels);
                ?>
            ],
            datasets: [{
                label: 'Hasta Sayısı',
                data: [<?php echo implode(',', $clinic_counts); ?>],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)'
                ]
            }]
        };
        new Chart(clinicCtx, {
            type: 'pie',
            data: clinicData
        });

        // Aylık Ziyaret Grafiği
        const visitsCtx = document.getElementById('visitsChart').getContext('2d');
        const monthlyVisits = [
            <?php
            $monthly_data = [];
            mysqli_data_seek($monthly_visits_result, 0);
            while ($row = $monthly_visits_result->fetch_assoc()) {
                $monthly_data[$row['ay']] = $row['ziyaret_sayisi'];
            }

            for ($i = 1; $i <= 12; $i++) {
                echo isset($monthly_data[$i]) ? $monthly_data[$i] : 0;
                if ($i < 12)
                    echo ',';
            }
            ?>
        ];

        new Chart(visitsCtx, {
            type: 'bar',
            data: {
                labels: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
                    'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
                datasets: [{
                    label: 'Ziyaret Sayısı',
                    data: monthlyVisits,
                    backgroundColor: 'rgba(0, 34, 87, 0.5)'
                }]
            }
        });
    </script>
    <script>
        function toggleMenu() {
            const hamburger = document.querySelector('.hamburger-menu');
            const menuItems = document.querySelector('.menu-items');

            hamburger.classList.toggle('active');
            menuItems.classList.toggle('active');
        }
    </script>


</body>

</html>