<?php
$conn = new mysqli("localhost", "root", "", "health_project");

if ($conn->connect_error) {
    die("Bağlantı hatası");
}

$result = $conn->query("SELECT * FROM health_data");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sağlık Veri Analizi</title>
</head>
<body>

<h1>Sigara – KOAH – Depresyon Analizi</h1>
<hr>

<table border="1" width="100%">
    <tr>
        <th>Sigara</th>
        <th>KOAH</th>
        <th>Depresyon</th>
        <th>Solunum Riski</th>
        <th>Mental Yük</th>
    </tr>

<?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><b><i><u><?php echo $row['smoking_status']; ?></u></i></b></td>
        <td align="bottom"><b><?php echo $row['copd']; ?></b></td>
        <td align="bottom"><i><?php echo $row['depression']; ?></i></td>
        <td><b><?php echo $row['respiratory_risk_score']; ?></b></td>
        <td><b><?php echo $row['mental_burden_score']; ?></b></td>
    </tr>
<?php } ?>

</table>

</body>
</html>
