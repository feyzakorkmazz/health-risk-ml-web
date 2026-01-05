<?php
$conn = new mysqli("localhost", "root", "", "health_project");

$total = $conn->query("
    SELECT COUNT(*) as total 
    FROM health_data
")->fetch_assoc()['total'];

$chain = $conn->query("
    SELECT COUNT(*) as total 
    FROM health_data 
    WHERE chain_effect = 1
")->fetch_assoc()['total'];

$percentage = ($total > 0)
    ? round(($chain / $total) * 100, 2)
    : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Zincir Etki Analizi</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<h2 style="text-align:center;">Zincir Etki Analizi</h2>
<hr>

<div style="width:320px; margin:auto;">
    <canvas id="chainChart"></canvas>
</div>

<script>
const ctx = document.getElementById('chainChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Zincir Etki Var', 'Zincir Etki Yok'],
        datasets: [{
            data: [<?= $chain ?>, <?= $total - $chain ?>],
            backgroundColor: ['#6a4c93', '#adb5bd']
        }]
    },
    options: {
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<p style="width:70%; margin:auto; margin-top:20px;">
<b>Analiz:</b>
Çalışma grubunda yer alan bireylerin
<b><?= $percentage ?>%</b>’sinde
aktif sigara kullanımı, KOAH tanısı ve depresyonun
birlikte görüldüğü zincir etki durumu tespit edilmiştir.
Bu bulgu, kronik solunum hastalıklarının
mental sağlık üzerindeki etkisini desteklemektedir.
</p>

</body>
</html>
