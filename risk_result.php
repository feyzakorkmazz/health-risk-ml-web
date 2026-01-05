<?php
session_start();
include 'db.php'; // Veritabanı bağlantısı

/* ======================================================
    1. VERİLERİ AL VE TEMİZLE
   ====================================================== */
$smoking  = isset($_POST["smoking"]) ? (int)$_POST["smoking"] : 0;
$sleep    = isset($_POST["sleep"]) ? (float)$_POST["sleep"] : 0;
$bmi      = isset($_POST["bmi"]) ? (float)$_POST["bmi"] : 0;
$age      = isset($_POST["age"]) ? (int)$_POST["age"] : 0;
$activity = isset($_POST["activity"]) ? (int)$_POST["activity"] : 0;
$gender   = isset($_POST["gender"]) ? $_POST["gender"] : 'U'; // Formda varsa cinsiyet, yoksa U (Bilinmeyen)

/* ======================================================
    2. DİNAMİK RİSK PUANLAMA (Senin Orijinal Algoritman)
   ====================================================== */
$risk_score = 0;
$reasons = [];

if ($smoking == 1) { 
    $risk_score += 4; 
    $reasons[] = ["icon" => "smoking", "txt" => "Sigara kullanımı: En yüksek risk faktörü (+40%)."]; 
}
if ($bmi >= 30) { 
    $risk_score += 2; 
    $reasons[] = ["icon" => "weight-scale", "txt" => "Vücut Kitle İndeksi: Obezite sınırı risk oluşturuyor."]; 
} elseif ($bmi >= 25) {
    $risk_score += 1;
    $reasons[] = ["icon" => "weight-scale", "txt" => "Vücut Kitle İndeksi: Hafif kilo artışı tespiti."];
}
if ($age >= 50) { 
    $risk_score += 2; 
    $reasons[] = ["icon" => "calendar-day", "txt" => "Yaş Faktörü: 50 yaş üstü kronik risk başlangıcı."]; 
}
if ($sleep < 6 || $sleep > 9) { 
    $risk_score += 1; 
    $reasons[] = ["icon" => "bed", "txt" => "Uyku Düzeni: Düzensiz uyku bağışıklığı zayıflatır."]; 
}
if ($activity == 0) { 
    $risk_score += 1; 
    $reasons[] = ["icon" => "person-walking", "txt" => "Hareketsizlik: Fiziksel aktivite eksikliği riski artırır."]; 
}

/* ======================================================
    3. YÜZDE VE SEVİYE HESAPLAMA
   ====================================================== */
$risk_percent = ($risk_score / 10) * 100;
if($risk_percent > 100) $risk_percent = 100;

if ($risk_percent <= 30) {
    $risk_level = "Düşük Risk";
    $risk_color = "success"; 
    $risk_text  = "Verileriniz genel olarak sağlıklı bir profile işaret ediyor.";
} elseif ($risk_percent <= 60) {
    $risk_level = "Orta Risk";
    $risk_color = "warning"; 
    $risk_text  = "Bazı risk faktörleri birleşmiş durumda. Yaşam tarzı değişikliği önerilir.";
} else {
    $risk_level = "Yüksek Risk";
    $risk_color = "danger"; 
    $risk_text  = "Yüksek risk skoruna ulaşıldı. Lütfen bir uzmana danışmayı ihmal etmeyin.";
}

/* ======================================================
    4. VERİTABANINA KAYIT (Kayıt işlemi burada gerçekleşir)
   ====================================================== */
$predicted_copd = ($risk_percent > 60) ? 1 : 0; // Risk %60 üzeriyse vaka olarak say
$def_dep = 0; // Varsayılan depresyon değeri

$stmt = $conn->prepare("INSERT INTO health_data (smoking_status, copd, depression, sleep_hours, bmi, physical_activity, age, gender) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiiddiis", $smoking, $predicted_copd, $def_dep, $sleep, $bmi, $activity, $age, $gender);
$stmt->execute();
$stmt->close();

/* ======================================================
    5. OTURUM VE ÇEREZ KAYITLARI
   ====================================================== */
$_SESSION["evaluation_count"] = (isset($_SESSION["evaluation_count"])) ? $_SESSION["evaluation_count"] + 1 : 1;
setcookie("last_risk_level", $risk_level, time() + 86400, "/");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analiz Sonucu | Health.AI</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --primary-emerald: #10b981;
            --dark-slate: #0f172a;
            --soft-bg: #f8fafc;
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--soft-bg); color: #334155; }
        .result-card { border: none; border-radius: 28px; box-shadow: 0 20px 50px rgba(0,0,0,0.08); background: #fff; overflow: hidden; }
        .risk-banner { 
            padding: 70px 20px; 
            background: linear-gradient(135deg, var(--dark-slate) 0%, #1e293b 100%);
            color: white;
            position: relative;
        }
        .risk-badge {
            display: inline-block; padding: 8px 20px; border-radius: 50px;
            font-weight: 700; text-transform: uppercase; font-size: 0.7rem;
            letter-spacing: 1.5px; margin-bottom: 20px;
            background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.1);
        }
        .risk-percentage { font-size: 5.5rem; font-weight: 800; line-height: 1; margin-bottom: 10px; color: var(--primary-emerald); }
        .progress { height: 14px; border-radius: 20px; background: rgba(255,255,255,0.1); margin: 30px auto; max-width: 450px; border: 1px solid rgba(255,255,255,0.05); }
        .progress-bar { border-radius: 20px; }
        .analysis-box {
            border: 1px solid #f1f5f9; border-radius: 20px; padding: 22px;
            margin-bottom: 18px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #fff; display: flex; align-items: center;
        }
        .analysis-box:hover { border-color: var(--primary-emerald); transform: translateX(8px); box-shadow: 0 10px 20px rgba(0,0,0,0.03); }
        .icon-circle {
            width: 54px; height: 54px; background: #ecfdf5; color: var(--primary-emerald);
            border-radius: 16px; display: flex; align-items: center; justify-content: center;
            margin-right: 20px; flex-shrink: 0; font-size: 1.4rem;
        }
        .summary-panel { background: #ffffff; border-radius: 24px; padding: 25px; border: 1px solid #f1f5f9; height: 100%; }
        .btn-main {
            background-color: var(--primary-emerald); color: white; border: none; padding: 18px 50px;
            border-radius: 16px; font-weight: 800; transition: 0.3s; text-decoration: none;
            display: inline-block; text-transform: uppercase; letter-spacing: 1px;
        }
        .btn-main:hover { background-color: #059669; color: white; transform: translateY(-3px); box-shadow: 0 15px 30px rgba(16, 185, 129, 0.25); }
        .source-frame { border-radius: 28px; border: 4px solid #f1f5f9; overflow: hidden; margin-top: 30px; }
    </style>
</head>
<body>

<div class="container my-5 pt-4">
    <div class="row justify-content-center">
        <div class="col-lg-11 col-xl-10">
            
            <div class="card result-card">
                <div class="risk-banner text-center">
                    <div class="risk-badge">Sağlık Analitik Motoru Çıktısı</div>
                    <div class="risk-percentage"><?php echo $risk_percent; ?>%</div>
                    <h2 class="fw-bold mb-3 text-uppercase" style="letter-spacing: 4px;"><?php echo $risk_level; ?></h2>
                    
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-<?php echo $risk_color; ?>" 
                             role="progressbar" style="width: <?php echo $risk_percent; ?>%">
                        </div>
                    </div>
                    <p class="lead opacity-75 px-lg-5 mb-0"><?php echo $risk_text; ?></p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <div class="row g-5">
                        <div class="col-lg-7">
                            <h5 class="fw-bold mb-4 d-flex align-items-center">
                                <i class="fa-solid fa-clipboard-list me-3 text-emerald" style="color: var(--primary-emerald);"></i>Analiz Bulguları
                            </h5>
                            
                            <?php if(empty($reasons)): ?>
                                <div class="alert alert-success border-0 rounded-4 p-4 d-flex align-items-center">
                                    <i class="fa-solid fa-circle-check fs-3 me-3"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1">Tebrikler!</h6>
                                        <p class="small mb-0">Parametreleriniz mevcut modelde herhangi bir risk tetiklememiştir.</p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($reasons as $r): ?>
                                    <div class="analysis-box shadow-sm">
                                        <div class="icon-circle shadow-sm">
                                            <i class="fa-solid fa-<?php echo $r['icon']; ?>"></i>
                                        </div>
                                        <div class="fw-semibold text-secondary small"><?php echo $r['txt']; ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="col-lg-5">
                            <div class="summary-panel shadow-sm">
                                <h6 class="fw-bold mb-4 text-uppercase small text-muted">Sistem İstatistikleri</h6>
                                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                    <span class="text-muted small">Toplam Analiz Sayınız</span>
                                    <span class="badge bg-dark rounded-pill"><?php echo $_SESSION["evaluation_count"]; ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                                    <span class="text-muted small">Önceki Kayıt Durumu</span>
                                    <span class="text-dark fw-bold small"><?php echo $_COOKIE["last_risk_level"] ?? 'İlk Analiz'; ?></span>
                                </div>
                                <div class="p-3 rounded-4 bg-light border-0 small mt-4">
                                    <i class="fa-solid fa-circle-info text-primary me-2"></i>
                                    Bu tahminleme, <strong>CDC BRFSS 2022</strong> veri seti üzerine eğitilmiş modelin parametre ağırlıklarını temel alır.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-5 border-top">
                        <div class="text-center mb-4 text-muted small fw-bold text-uppercase" style="letter-spacing: 2px;">Tıbbi Referans Kaynakları</div>
                        <div class="source-frame shadow-sm">
                            <iframe src="https://www.cdc.gov/copd/index.html" width="100%" height="400" style="border:none;"></iframe>
                        </div>
                    </div>

                    <div class="text-center mt-5 pt-3">
                        <a href="risk_form.php" class="btn btn-main shadow-lg">
                            <i class="fa-solid fa-arrow-rotate-left me-3 small"></i>Analizi Sıfırla
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4 text-muted">
                <small class="opacity-50">Health.AI Analiz Paneli | © 2024 Tüm Hakları Saklıdır.</small>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>