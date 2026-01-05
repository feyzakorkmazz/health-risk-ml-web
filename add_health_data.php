<?php
require_once "db.php";
require_once "security.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // XSS temizleme + güvenli alma
    $smoking_status = clean_input($_POST["smoking_status"] ?? 0);
    $copd          = clean_input($_POST["copd"] ?? 0);
    $depression    = clean_input($_POST["depression"] ?? 0);

    // Prepared Statement (SQL Injection koruması)
    $stmt = $conn->prepare(
        "INSERT INTO health_data (smoking_status, copd, depression)
         VALUES (?, ?, ?)"
    );

    $stmt->bind_param(
        "iii",
        $smoking_status,
        $copd,
        $depression
    );

    if ($stmt->execute()) {
        echo "Kayıt güvenli şekilde eklendi.";
    } else {
        echo "Kayıt eklenemedi.";
    }

    $stmt->close();
    $conn->close();
}
