<?php
session_start();

if (!isset($_SESSION['visit_count'])) {
    $_SESSION['visit_count'] = 1;
} else {
    $_SESSION['visit_count']++;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Session Takibi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h2>Oturum Bilgileri</h2>

<p>
Bu siteyi bu oturumda
<b><?php echo $_SESSION['visit_count']; ?></b>
kez ziyaret ettiniz.
</p>

<p>
<b>Session ID:</b> <?php echo session_id(); ?>
</p>

<a href="index.php">Ana sayfaya dön</a>

</body>
</html>
