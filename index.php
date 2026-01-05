<?php
// Veritabanı bağlantısı
include 'db.php'; 

// 1. Veritabanından Canlı Verileri Çekelim
try {
    // Toplam Kayıt
    $total_query = $conn->query("SELECT COUNT(*) as total FROM health_data");
    $db_dataset_size = $total_query->fetch_assoc()['total'];

    // Toplam KOAH Vakası
    $copd_query = $conn->query("SELECT COUNT(*) as total FROM health_data WHERE copd = 1");
    $db_copd_count = $copd_query->fetch_assoc()['total'];

    // Sigara Kullananlar
    $smoker_query = $conn->query("SELECT COUNT(*) as total FROM health_data WHERE smoking_status = 1");
    $db_smoker_count = $smoker_query->fetch_assoc()['total'];

    // Ortalama Uyku Süresi (Yeni bir metrik ekleyelim)
    $sleep_query = $conn->query("SELECT AVG(sleep_hours) as avg_sleep FROM health_data");
    $avg_sleep = round($sleep_query->fetch_assoc()['avg_sleep'], 1);

} catch (Exception $e) {
    die("Veri çekme hatası: " . $e->getMessage());
}

// 2. ML Model Verileri (JSON)
$json_path = "reports/ml_summary.json";
$ml_data = file_exists($json_path) ? json_decode(file_get_contents($json_path), true) : [
    "best_model" => "XGBoost", 
    "recall_class_1" => "0.72", 
    "top_feature" => "Sigara Kullanımı",
    "auc_score" => "0.85"
];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health.AI | Yönetim Paneli</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root { 
            --primary-emerald: #10b981; 
            --dark-slate: #0f172a; 
            --soft-bg: #f8fafc; 
            --sidebar-width: 260px; 
        }
        
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--soft-bg); color: #1e293b; overflow-x: hidden; }
        
        /* Sidebar */
        .sidebar { width: var(--sidebar-width); background: #ffffff; min-height: 100vh; position: fixed; border-right: 1px solid #e2e8f0; padding: 1.5rem; z-index: 1000; }
        .nav-link-custom { display: flex; align-items: center; padding: 0.8rem 1rem; border-radius: 12px; text-decoration: none; color: #64748b; font-weight: 600; transition: 0.3s; margin-bottom: 0.5rem; }
        .nav-link-custom:hover, .nav-link-custom.active { background: #f1f5f9; color: var(--primary-emerald); }
        .nav-link-custom i { margin-right: 12px; width: 20px; text-align: center; }

        /* Main Content */
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; }
        
        /* Header */
        header { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); padding: 1.25rem 2rem; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 999; }
        
        /* Stat Cards */
        .stat-card { background: #ffffff; border: 1px solid #f1f5f9; border-radius: 24px; padding: 1.5rem; transition: all 0.3s ease; height: 100%; position: relative; overflow: hidden; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05); border-color: var(--primary-emerald); }
        .icon-box { width: 54px; height: 54px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1.25rem; }
        
        .stat-value { font-size: 1.75rem; font-weight: 800; color: var(--dark-slate); letter-spacing: -0.5px; }
        .stat-label { font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Charts & Panels */
        .chart-box { background: #ffffff; border-radius: 28px; padding: 2rem; border: 1px solid #f1f5f9; height: 100%; }
        .img-graph { border-radius: 16px; width: 100%; max-height: 300px; object-fit: contain; background: #fafafa; padding: 10px; }
        
        .summary-card { background: var(--dark-slate); color: white; border-radius: 28px; padding: 2rem; position: relative; }
        .summary-card .btn-action { background: var(--primary-emerald); color: white; border: none; padding: 12px; border-radius: 14px; font-weight: 700; transition: 0.3s; width: 100%; }
        .summary-card .btn-action:hover { background: #059669; transform: scale(1.02); }

        .pulse-indicator { width: 10px; height: 10px; background: var(--primary-emerald); border-radius: 50%; display: inline-block; margin-right: 8px; box-shadow: 0 0 0 rgba(16, 185, 129, 0.4); animation: pulse 2s infinite; }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0px rgba(16, 185, 129, 0.7); } 70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); } 100% { box-shadow: 0 0 0 0px rgba(16, 185, 129, 0); } }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="d-flex align-items-center mb-5 px-2">
        <div class="bg-dark rounded-3 p-2 me-3 text-white shadow-sm"><i class="fa-solid fa-heart-pulse"></i></div>
        <span class="fw-800 fs-4 tracking-tight">HEALTH.AI</span>
    </div>
    
    <nav>
        <p class="text-muted small fw-bold text-uppercase mb-3 px-2">Menü</p>
        <a href="index.php" class="nav-link-custom active"><i class="fa-solid fa-grid-2"></i> Dashboard</a>
        <a href="risk_form.php" class="nav-link-custom"><i class="fa-solid fa-notes-medical"></i> Risk Analizi</a>
        <a href="analysis.php" class="nav-link-custom"><i class="fa-solid fa-chart-line"></i> Teknik Rapor</a>
        <div class="mt-5 px-2">
            <div class="p-3 bg-light rounded-4">
                <p class="small text-muted mb-0">Versiyon</p>
                <p class="fw-bold mb-0 text-dark">v1.0.5 <span class="badge bg-success-subtle text-success ms-1">Stable</span></p>
            </div>
        </div>
    </nav>
</aside>

<div class="main-content">
    <header class="d-flex justify-content-between align-items-center shadow-sm">
        <div>
            <h4 class="fw-800 mb-0">Sağlık Analitik Paneli</h4>
            <div class="d-flex align-items-center mt-1">
                <span class="pulse-indicator"></span>
                <small class="text-muted fw-semibold">Canlı Veritabanı Bağlantısı: <span class="text-success">Aktif</span></small>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <div class="text-end me-3 d-none d-sm-block">
                <p class="mb-0 fw-bold small">Hoş Geldin, Feyza</p>
                <p class="mb-0 text-muted smaller" style="font-size: 0.7rem;">Sistem Yöneticisi</p>
            </div>
            <div class="bg-white border rounded-circle p-2 shadow-sm"><i class="fa-solid fa-user-doctor text-primary"></i></div>
        </div>
    </header>

    <main class="p-4 p-lg-5">
        <div class="row g-4 mb-5">
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="icon-box bg-blue-soft"><i class="fa-solid fa-folder-tree"></i></div>
                    <div class="stat-label">Toplam Örneklem</div>
                    <div class="stat-value"><?php echo number_format($db_dataset_size); ?></div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card border-bottom border-4 border-success">
                    <div class="icon-box bg-emerald-soft"><i class="fa-solid fa-lungs"></i></div>
                    <div class="stat-label">KOAH Tanılı</div>
                    <div class="stat-value text-success"><?php echo number_format($db_copd_count); ?></div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="icon-box bg-red-soft"><i class="fa-solid fa-ban-smoking"></i></div>
                    <div class="stat-label">Aktif İçici</div>
                    <div class="stat-value text-danger"><?php echo number_format($db_smoker_count); ?></div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card border-bottom border-4 border-primary">
                    <div class="icon-box bg-info-subtle text-info"><i class="fa-solid fa-bed"></i></div>
                    <div class="stat-label">Ort. Uyku (Saat)</div>
                    <div class="stat-value text-info"><?php echo $avg_sleep; ?>h</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="chart-box">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-800 mb-1">Değişken Etki Analizi</h5>
                            <p class="text-muted small">KOAH riskini en çok tetikleyen faktörlerin ağırlığı</p>
                        </div>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-bold">XGBoost Algoritması</span>
                    </div>
                    <div class="text-center">
                        <img src="images/xgboost.png" class="img-graph shadow-sm border" alt="Feature Importance Graph">
                        <div class="mt-4 p-3 bg-light rounded-4 text-start">
                            <p class="small text-secondary mb-0">
                                <i class="fa-solid fa-lightbulb text-warning me-2"></i>
                                <strong>Analiz Sonucu:</strong> Model verilerine göre <strong><?php echo $ml_data['top_feature']; ?></strong>, hastalığın öngörülmesinde en kritik eşik değeridir.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="summary-card shadow-lg h-100 d-flex flex-column">
                    <div class="mb-auto">
                        <div class="d-flex align-items-center mb-4">
                            <i class="fa-solid fa-brain fs-3 text-success me-3"></i>
                            <h5 class="fw-800 mb-0">ML Model Karar Özeti</h5>
                        </div>
                        <p class="small opacity-75 mb-4">SMOTE ile dengelenmiş veri seti üzerinde eğitilen modelin performans çıktıları aşağıdadır.</p>
                        
                        <div class="list-group list-group-flush bg-transparent">
                            <div class="list-group-item bg-transparent text-white px-0 d-flex justify-content-between border-secondary opacity-75">
                                <span class="small">Ana Model</span>
                                <span class="fw-bold"><?php echo $ml_data['best_model']; ?></span>
                            </div>
                            <div class="list-group-item bg-transparent text-white px-0 d-flex justify-content-between border-secondary">
                                <span class="small">Duyarlılık (Recall)</span>
                                <span class="fw-bold text-success">%<?php echo ($ml_data["recall_class_1"] * 100); ?></span>
                            </div>
                            <div class="list-group-item bg-transparent text-white px-0 d-flex justify-content-between border-secondary opacity-75">
                                <span class="small">Hata Payı (AUC)</span>
                                <span class="fw-bold"><?php echo $ml_data['auc_score']; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5">
                        <p class="smaller opacity-50 mb-3 text-center" style="font-size: 0.7rem;">Yeni bir risk profili oluşturmak ister misiniz?</p>
                        <a href="risk_form.php" class="btn btn-action shadow-sm text-decoration-none d-block text-center">
                            ANALİZ BAŞLAT <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>