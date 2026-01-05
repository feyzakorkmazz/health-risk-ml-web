<?php
$conn = new mysqli("localhost", "root", "", "health_project");

$total_smokers = $conn->query("
    SELECT COUNT(*) as total 
    FROM health_data 
    WHERE smoking_status = 'current'
")->fetch_assoc()['total'];

$copd_smokers = $conn->query("
    SELECT COUNT(*) as total 
    FROM health_data 
    WHERE smoking_status = 'current' AND copd = 1
")->fetch_assoc()['total'];

$percentage = ($total_smokers > 0) 
    ? round(($copd_smokers / $total_smokers) * 100, 2) 
    : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sigara - KOAH Analizi</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<h2>Aktif Sigara İçenlerde KOAH Oranı</h2>

<canvas id="copdChart" width="400" height="400"></canvas>

<script>
const ctx = document.getElementById('copdChart');

new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['KOAH Var', 'KOAH Yok'],
        datasets: [{
            data: [<?= $copd_smokers ?>, <?= $total_smokers - $copd_smokers ?>]
        }]
    }
});
</script>

<p>
<b>Analiz:</b> Aktif sigara içen bireyler arasında KOAH tanısı olanların oranı
<b><?= $percentage ?>%</b> olarak hesaplanmıştır.
Bu durum sigara kullanımının solunum yolu hastalıkları üzerindeki
olumsuz etkisini desteklemektedir.
</p>

</body>
</html>
