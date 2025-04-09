<?php
header('Content-Type: application/json');

include 'db.php';

$today = date('Y-m-d');
$stmt = $conn->prepare("SELECT tc_no, ad_soyad, telefon_no FROM patients WHERE randevu_tarihi = ?");
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

echo json_encode($appointments);
?>