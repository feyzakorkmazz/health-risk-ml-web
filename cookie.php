<?php
// Cookie oluştur
setcookie(
    "last_visit",
    date("Y-m-d H:i:s"),
    time() + 86400,
    "/"
);

// Log klasörü yoksa oluştur
$logDir = __DIR__ . "/logs";
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}

// Log dosyasına yaz
$logFile = $logDir . "/cookie_log.txt";

$logMessage =
    date("Y-m-d H:i:s") .
    " | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN') .
    " | User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN') .
    " | Cookie last_visit set\n";

file_put_contents($logFile, $logMessage, FILE_APPEND);

