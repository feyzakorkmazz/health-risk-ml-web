<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Ziyaretçi Bilgisi</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<h2>Ziyaretçi Bilgi Formu</h2>

<form action="set_cookie.php" method="post">
    <label>Ad Soyad:</label><br>
    <input type="text" name="fullname" required><br><br>

    <label>Yaş:</label><br>
    <input type="number" name="age" required><br><br>

    <label>Sigara Kullanıyor mu?</label><br>
    <select name="smoking">
        <option value="1">Evet</option>
        <option value="0">Hayır</option>
    </select><br><br>

    <input type="submit" value="Kaydet">
</form>

</body>
</html>
