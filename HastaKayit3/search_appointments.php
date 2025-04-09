<?php
include 'db.php';

// Varsayılan olarak boş sonuçlar
$appointments = [];

// Form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tc_no = $_POST['tc_no'] ?? '';
    $ad_soyad = $_POST['ad_soyad'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';


    // Sorgu hazırlama
    $sql = "SELECT p.tc_no, p.ad_soyad, p.telefon_no, p.randevu_tarihi, 
    (SELECT visit_type FROM visits v WHERE v.tc_no = p.tc_no ORDER BY visit_date DESC LIMIT 1) AS visit_type 
    FROM patients p WHERE 1=1";
    $params = [];
    $types = '';

    if (!empty($tc_no)) {
        $sql .= " AND tc_no LIKE ?";
        $params[] = "%$tc_no%";
        $types .= "s";
    }

    if (!empty($ad_soyad)) {
        $sql .= " AND ad_soyad LIKE ?";
        $params[] = "%$ad_soyad%";
        $types .= "s";
    }

    if (!empty($start_date)) {
        $sql .= " AND randevu_tarihi >= ?";
        $params[] = $start_date;
        $types .= "s";
    }

    if (!empty($end_date)) {
        $sql .= " AND randevu_tarihi <= ?";
        $params[] = $end_date;
        $types .= "s";
    }

    $stmt = $conn->prepare($sql);

    // Parametreleri dinamik olarak bağlama
    if (!empty($params)) {
        $bind_params = array_merge([$types], $params);
        call_user_func_array([$stmt, 'bind_param'], refValues($bind_params));
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
}

// Referans değerler için yardımcı fonksiyon
function refValues($arr)
{
    $refs = [];
    foreach ($arr as $key => $value) {
        $refs[$key] = &$arr[$key];
    }
    return $refs;
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Randevu Sorgulama</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="header-logo">
        <a href="index.php" class="home-button">Ana Sayfa</a>
        <a href="patient_search.php" class="patientsearch-button">Hasta Sorgulama</a>
    </div>

    <div class="container">
        <h1>Randevu Sorgulama</h1>

        <form method="POST">
            <div>
                <label>TC Kimlik No:</label>
                <input type="text" name="tc_no" placeholder="TC Numarası (isteğe bağlı)">
            </div>
            <div>
                <label>Ad Soyad:</label>
                <input type="text" name="ad_soyad" placeholder="Ad Soyad (isteğe bağlı)">
            </div>
            <div>
                <label>Başlangıç Tarihi:</label>
                <input type="date" name="start_date">
            </div>
            <div>
                <label>Bitiş Tarihi:</label>
                <input type="date" name="end_date">
            </div>
            <button type="submit">Randevuları Sorgula</button>
        </form>

        <?php if (!empty($appointments)): ?>
            <table>
                <thead>
                    <tr>
                        <th>TC Kimlik No</th>
                        <th>Ad Soyad</th>
                        <th>Telefon No</th>
                        <th>Randevu Tarihi</th>
                        <th>Randevu Tipi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?= htmlspecialchars($appointment['tc_no']) ?></td>
                            <td><?= htmlspecialchars($appointment['ad_soyad']) ?></td>
                            <td><?= htmlspecialchars($appointment['telefon_no']) ?></td>
                            <td><?= htmlspecialchars($appointment['randevu_tarihi']) ?></td>
                            <td><?= htmlspecialchars($appointment['visit_type']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
            <p>Herhangi bir randevu bulunamadı.</p>
        <?php endif; ?>
    </div>
</body>

</html>