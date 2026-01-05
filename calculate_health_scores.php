<?php
$conn = new mysqli("localhost", "root", "", "health_project");

if ($conn->connect_error) {
    die("DB bağlantı hatası");
}

$result = $conn->query("SELECT * FROM health_data");

while ($row = $result->fetch_assoc()) {

    $id = $row['id'];

    // Solunum riski
    $resp = 0;
    if ($row['smoking_status'] == 'current') $resp += 4;
    if ($row['smoking_status'] == 'former')  $resp += 2;
    if ($row['copd'] == 1) $resp += 5;

    // Mental yük
    $mental = 0;
    if ($row['depression'] == 1) $mental += 5;
    if ($row['sleep_hours'] < 6) $mental += 3;
    if ($row['physical_activity'] == 'low') $mental += 2;

    // Uyku açığı
    $sleep_deficit = max(0, 8 - $row['sleep_hours']);

    // Sosyal risk
    $social = 0;
    if ($row['physical_activity'] == 'low') $social += 2;
    if ($row['age'] > 60) $social += 2;

    // Kardiyovasküler risk
    $cardio = 0;
    if ($row['bmi'] >= 30) $cardio += 4;
    if ($row['smoking_status'] == 'current') $cardio += 3;

    // Birleşik skor
    $combined =
        ($resp * 0.3) +
        ($mental * 0.25) +
        ($sleep_deficit * 0.15) +
        ($social * 0.15) +
        ($cardio * 0.15);

    // Zincir etkisi
    $chain = ($row['smoking_status'] == 'current' && $row['copd'] == 1 && $row['depression'] == 1) ? 1 : 0;

    $conn->query("
        UPDATE health_data SET
        respiratory_risk_score = $resp,
        mental_burden_score = $mental,
        sleep_deficit_score = $sleep_deficit,
        social_risk_score = $social,
        cardiovascular_risk_score = $cardio,
        combined_health_score = $combined,
        chain_effect = $chain
        WHERE id = $id
    ");
}

echo 'Skorlar başarıyla hesaplandı';
?>
