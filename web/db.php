<?php
/**
 * db.php - Health.AI Veritabanı Bağlantı Yapılandırması
 */

// 1. Hata raporlama ayarını en başa alalım
// Bu ayar, bağlantı hatalarını catch bloğuna fırlatmasını sağlar.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // 2. Veritabanı Bilgileri
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "health_project";

    // 3. Bağlantıyı Kur
    $conn = new mysqli($host, $user, $pass, $db);
    
    // 4. Karakter setini ayarla (Emoji ve Türkçe desteği için en iyisi)
    $conn->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {
    // 5. Hatayı log dosyasına kaydet (Geliştirici için)
    $error_msg = date("Y-m-d H:i:s") . " - DB HATA: " . $e->getMessage() . PHP_EOL;
    error_log($error_msg, 3, "error_log.txt");

    // 6. Kullanıcıya şık ve güvenli bir mesaj göster (Sistem detaylarını gizle)
    die("
    <div style='font-family:sans-serif; text-align:center; padding:100px; background:#f8fafc; color:#1e293b;'>
        <div style='background:white; display:inline-block; padding:40px; border-radius:24px; box-shadow:0 10px 25px rgba(0,0,0,0.05);'>
            <h1 style='color:#ef4444; margin-bottom:10px;'>📡 Sistem Bağlantı Hatası</h1>
            <p style='font-size:18px;'>Şu an veritabanına ulaşılamıyor.</p>
            <p style='color:#64748b; font-size:14px;'>Lütfen MySQL servisinin (XAMPP/WAMP) çalıştığından emin olun.</p>
            <button onclick='location.reload()' style='background:#10b981; color:white; border:none; padding:12px 24px; border-radius:12px; cursor:pointer; font-weight:bold; margin-top:20px;'>Tekrar Dene</button>
        </div>
    </div>");
}
?>