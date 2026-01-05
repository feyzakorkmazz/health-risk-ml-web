<?php
$conn = new mysqli("localhost", "root", "", "health_project");

if ($conn->connect_error) {
    die("Bağlantı hatası");
}

$copd_yes = $conn->query("
    SELECT AVG(respiratory_risk_score) AS avg_risk
    FROM health_data
    WHERE copd = 1
")->fetch_assoc()['avg_risk'];

$copd_no = $conn->query("
    SELECT AVG(respiratory_risk_score) AS avg_risk
    FROM health_data
    WHERE copd = 0
")->fetch_assoc()['avg_risk'];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>KOAH Risk Karşılaştırması</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<h2 style="text-align:center;">KOAH (COPD) Durumuna Göre Solunum Riski</h2>
<hr>

<div style="width:320px; margin:auto;">
<canvas id="riskChart"></canvas>
</div>

<script>
new Chart(document.getElementById('riskChart'), {
    type: 'bar',
    data: {
        labels: ['COPD Var', 'COPD Yok'],
        datasets: [{
            data: [<?= $copd_yes ?>, <?= $copd_no ?>],
            backgroundColor: ['#d62828', '#2a9d8f']
        }]
    },
    options: {
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>

<p style="width:70%; margin:auto; margin-top:20px;">
<b>Yorum:</b>
COPD tanısı bulunan bireylerde ortalama solunum riski skorunun,
COPD tanısı bulunmayan bireylere kıyasla daha yüksek olduğu
gözlemlenmiştir. Bu durum, kronik solunum hastalıklarının
genel sağlık riski üzerindeki etkisini desteklemektedir.
</p>

</body>
</html>
