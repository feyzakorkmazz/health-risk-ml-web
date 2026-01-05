<?php
$conn = new mysqli("localhost", "root", "", "health_project");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$sql = "SELECT * FROM health_data";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $respiratory_risk_score = 0.4 * getSmokingValue($row['smoking_status']) + 0.3 * $row['bmi'] + 0.3 * $row['copd'];
        $mental_burden_score = 0.5 * $row['depression'] + 0.25 * getSleepDeficit($row['sleep_hours']) + 0.25 * getActivityValue($row['physical_activity']);
        $chain_effect = ($row['smoking_status'] == 'Current' && $row['copd']==1 && $row['depression']==1) ? 1 : 0;

        $update_sql = "UPDATE health_data SET 
            respiratory_risk_score=$respiratory_risk_score,
            mental_burden_score=$mental_burden_score,
            chain_effect=$chain_effect
            WHERE id=".$row['id'];
        $conn->query($update_sql);

        echo "ID ".$row['id']." güncellendi.<br>";
    }
}

function getSmokingValue($status) { return ($status=='Never') ? 0 : (($status=='Former') ? 0.5 : 1); }
function getSleepDeficit($hours) { return max(0, 7 - $hours); }
function getActivityValue($activity) { return ($activity=='Low') ? 1 : (($activity=='Medium') ? 0.5 : 0); }
?>
