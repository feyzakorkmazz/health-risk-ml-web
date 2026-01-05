<?php
session_start();
require_once("db.php");

/* Veritabanı İstatistikleri */
// Tablo isminin 'health_data' olduğunu varsayıyoruz
$query = "SELECT COUNT(*) AS total, SUM(CASE WHEN copd = 1 THEN 1 ELSE 0 END) AS copd_count, 
          SUM(CASE WHEN depression = 1 THEN 1 ELSE 0 END) AS depression_count,
          SUM(CASE WHEN smoking = 1 THEN 1 ELSE 0 END) AS smoker_count FROM health_data";
$result = $conn->query($query);
$db_data = $result->fetch_assoc();

$visitCount = $_SESSION['visit_count'] ?? 1;
$userName = $_COOKIE['user_name'] ?? "Misafir Kullanıcı";
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analitik Dashboard | Health.AI</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-emerald: #10b981;
            --dark-slate: #0f172a;
            --soft-bg: #f8fafc;
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--soft-bg); color: #1e293b; }
        
        .header-bg { background: var(--dark-slate); color: white; padding: 40px 0 100px 0; margin-bottom: -70px; }
        
        .stat-card {
            border: none; border-radius: 20px; background: #fff;
            padding: 1.25rem; box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            height: 100%; transition: 0.3s;
        }
        
        .chart-box { 
            background: white; border-radius: 24px; padding: 1.5rem; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.04); height: 100%;
        }
        
        .img-analysis { 
            max-width: 100%; 
            height: 220px; 
            object-fit: contain; 
            border-radius: 12px;
            margin-top: 10px;
        }

        .icon-sm { width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px; }
        .bg-emerald-soft { background: #ecfdf5; color: var(--primary-emerald); }
        .bg-blue-soft { background: #eff6ff; color: #3b82f6; }
    </style>
</head>
<body>

<div class="header-bg">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-800 mb-0 text-white">Sağlık Analitik Paneli</h2>
            <p class="opacity-75 small text-white">Lider Algoritma: <span class="text-emerald fw-bold" style="color: var(--primary-emerald);">XGBoost (%72 Recall Başarısı)</span></p>
        </div>
        <div class="text-end d-none d-md-block">
            <span class="badge bg-success rounded-pill px-3 py-2">Model Durumu: XGBoost Aktif</span>
        </div>
    </div>
</div>

<div class="container">
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card shadow-sm border-bottom border-4 border-success">
                <div class="d-flex align-items-center mb-2">
                    <div class="icon-sm bg-emerald-soft"><i class="fa-solid fa-bolt"></i></div>
                    <span class="small text-muted fw-bold">FINAL MODEL (XGBOOST)</span>
                </div>
                <h3 class="fw-800 mb-0">0.72</h3>
                <small class="text-success small fw-bold">En Yüksek Hassasiyet</small>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card shadow-sm">
                <div class="d-flex align-items-center mb-2">
                    <div class="icon-sm bg-blue-soft"><i class="fa-solid fa-tree"></i></div>
                    <span class="small text-muted fw-bold">RANDOM FOREST</span>
                </div>
                <h3 class="fw-800 mb-0">0.62</h3>
                <small class="text-muted small">Kıyaslama Skoru</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card shadow-sm">
                <div class="d-flex align-items-center mb-2">
                    <div class="icon-sm bg-light text-dark"><i class="fa-solid fa-database"></i></div>
                    <span class="small text-muted fw-bold">TOPLAM VERİ</span>
                </div>
                <h3 class="fw-800 mb-0"><?php echo number_format($db_data['total'] ?? 445132); ?></h3>
                <small class="text-muted small">CDC BRFSS 2022</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card shadow-sm">
                <div class="d-flex align-items-center mb-2">
                    <div class="icon-sm bg-light text-dark"><i class="fa-solid fa-user-circle"></i></div>
                    <span class="small text-muted fw-bold">AKTİF OTURUM</span>
                </div>
                <h6 class="fw-bold mb-0 text-truncate text-primary"><?php echo htmlspecialchars($userName); ?></h6>
                <small class="text-muted small">Ziyaret: <?php echo $visitCount; ?></small>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="chart-box border-top border-4 border-success">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold m-0"><i class="fa-solid fa-microchip text-success me-2"></i>XGBoost Değişken Analizi</h6>
                    <span class="badge bg-success small">En İyi Model</span>
                </div>
                <p class="text-muted small mb-2">Model Karar Mekanizması (Gain Analizi)</p>
                <div class="text-center">
                    <img src="images/feature_importance.png" class="img-analysis" alt="XGBoost Feature Importance">
                </div>
                <div class="mt-3 p-2 bg-light rounded-3 small text-secondary">
                    <b>Yorum:</b> XGBoost modeli, sigara kullanımını %40'ın üzerinde bir ağırlıkla en kritik risk faktörü olarak tanımlar.
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="chart-box">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold m-0"><i class="fa-solid fa-layer-group text-primary me-2"></i>Random Forest Analizi</h6>
                    <span class="badge bg-secondary small">Alternatif</span>
                </div>
                <p class="text-muted small mb-2">Hiyerarşik Değişken Dağılımı</p>
                <div class="text-center">
                    <img src="images/feature_importance_random.png" class="img-analysis" alt="Random Forest Feature Importance">
                </div>
                <div class="mt-3 p-2 bg-light rounded-3 small text-secondary">
                    <b>Yorum:</b> Random Forest modelinde yaş ve BMI gibi demografik veriler daha yüksek bir frekansla dağılmıştır.
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="chart-box">
                <h6 class="fw-bold mb-4"><i class="fa-solid fa-chart-area me-2 text-primary"></i>Hata Analizi (ROC Curve)</h6>
                
                

                <div class="row align-items-center">
                    <div class="col-md-7 text-center">
                        <img src="images/roc_curve.png" class="img-analysis" style="height: 250px;" alt="ROC Curve Performance">
                    </div>
                    <div class="col-md-5">
                        <div class="alert alert-success border-0 rounded-4 small py-3 shadow-sm">
                            <h6 class="fw-bold mb-2">XGBoost Tercih Sebebi</h6>
                            XGBoost (AUC: 0.85), hasta bireyleri yakalama kapasitesinde (Recall) Random Forest'a göre klinik açıdan anlamlı bir üstünlük sağlamaktadır.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="stat-card shadow-sm p-4 bg-dark text-white">
                <h5 class="fw-bold mb-3 text-emerald" style="color: var(--primary-emerald);"><i class="fa-solid fa-user-doctor me-2"></i>Klinik Sonuç</h5>
                <p class="small opacity-75">Yapılan analizler sonucunda, Tip II hataları (hastayı sağlıklı sanma) en aza indiren <strong>XGBoost (%72)</strong> algoritması projenin final modeli olarak belirlenmiştir.</p>
                <hr class="opacity-25">
                <div class="d-grid gap-2">
                    <a href="risk_form.php" class="btn btn-emerald text-white fw-bold py-2 rounded-pill shadow" style="background: var(--primary-emerald); border:none;">Yeni Analiz Başlat</a>
                    <a href="analysis.php" class="btn btn-outline-light btn-sm rounded-pill">Teknik Rapor Detayları</a>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="mt-5 py-4 text-center text-muted small border-top bg-white">
    © 2024 Sağlık Analitik Dashboard | Veri Bilimi Projesi - Feyza Korkmaz
</footer>

</body>
</html>