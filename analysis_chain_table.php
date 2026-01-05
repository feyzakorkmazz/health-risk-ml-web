<?php
$conn = new mysqli("localhost", "root", "", "health_project");

$total = $conn->query("
    SELECT COUNT(*) as total 
    FROM health_data
")->fetch_assoc()['total'];

$chain = $conn->query("
    SELECT COUNT(*) as count
    FROM health_data
    WHERE smoking_status = 1
      AND copd = 1
      AND depression = 1
")->fetch_assoc()['count'];

$percent = ($total > 0)
    ? round(($chain / $total) * 100, 2)
    : 0;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Zincir Hastalık Analizi</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
table {
    margin:auto;
    border-collapse: collapse;
}
td, th {
    border: 1px solid #444;
    padding: 10px;
    text-align: center;
    font-weight: bold;
    font-style: italic;
    text-decoration: underline;
}
</style>
</head>
<body>

<h2 style="text-align:center;">Sigara – COPD – Depresyon Zinciri</h2>
<hr>

<table>
<tr>
    <th>Toplam Kayıt</th>
    <th>Zincir Etki Olan</th>
    <th>Oran (%)</th>
</tr>
<tr>
    <td><?= $total ?></td>
    <td><?= $chain ?></td>
    <td><?= $percent ?></td>
</tr>
</table>

<br>

<div style="width:300px; margin:auto;">
<canvas id="chainChart"></canvas>
</div>

<script>
new Chart(document.getElementById('chainChart'), {
    type: 'pie',
    data: {
        labels: ['Zincir Etki Var', 'Diğer'],
        datasets: [{
            data: [<?= $chain ?>, <?= $total - $chain ?>],
            backgroundColor: ['#8338ec', '#ced4da']
        }]
    }
});
</script>

<p style="width:70%; margin:auto;">
<b>Akademik Yorum:</b>
Analiz sonuçlarına göre, aktif sigara kullanan,
COPD tanısı almış ve depresyon tanısı bulunan bireylerin
toplam popülasyona oranı <b><?= $percent ?>%</b> olarak hesaplanmıştır.
Bu sonuç, sigara kullanımının solunum ve mental sağlık
üzerindeki zincirleme etkisini desteklemektedir.
</p>

</body>
</html>
