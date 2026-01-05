<?php
include 'db.php'; // Veritabanı bağlantısı

// 1. Veritabanından Canlı İstatistikleri Alalım
$stats_query = $conn->query("SELECT AVG(bmi) as avg_bmi, AVG(age) as avg_age, COUNT(*) as total FROM health_data");
$stats = $stats_query->fetch_assoc();

// 2. ML Verilerini JSON'dan çekiyoruz
$json_path = "reports/ml_summary.json";
$data = file_exists($json_path) ? json_decode(file_get_contents($json_path), true) : [
    "best_model" => "XGBoost", 
    "recall_class_1" => "0.72", 
    "accuracy" => "0.80",
    "auc_score" => "0.85"
];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teknik Analiz Raporu | Health.AI</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-emerald: #10b981;
            --dark-slate: #0f172a;
            --accent-coral: #fb7185;
            --soft-bg: #f8fafc;
            --sidebar-width: 260px;
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--soft-bg); color: #1e293b; }
        .sidebar { width: var(--sidebar-width); background: #ffffff; min-height: 100vh; position: fixed; border-right: 1px solid #e2e8f0; padding: 1.5rem; z-index: 1000; }
        .main-content { margin-left: var(--sidebar-width); }
        .hero-section { background: linear-gradient(135deg, var(--dark-slate) 0%, #1e293b 100%); color: white; padding: 60px 0; border-bottom: 6px solid var(--primary-emerald); }
        .card { border: none; border-radius: 20px; background: white; box-shadow: 0 10px 25px rgba(0,0,0,0.03); }
        .metric-value { font-size: 2.2rem; font-weight: 800; color: var(--dark-slate); }
        .section-title { position: relative; padding-bottom: 10px; margin-bottom: 30px; font-weight: 800; color: var(--dark-slate); margin-top: 40px; }
        .section-title::after { content: ''; position: absolute; left: 0; bottom: 0; width: 60px; height: 5px; background: var(--primary-emerald); border-radius: 10px; }
        .chart-container { background: white; padding: 25px; border-radius: 24px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); height: 100%; text-align: center; border: 1px solid #f1f5f9; }
        .chart-img { border-radius: 15px; width: 100%; height: auto; max-height: 320px; object-fit: contain; transition: 0.3s; }
        .live-stat-banner { background: #fff; border-left: 5px solid var(--primary-emerald); padding: 20px; border-radius: 15px; margin-bottom: 30px; }
    </style>
</head>
<body>

<nav class="sidebar d-none d-md-block">
    <div class="d-flex align-items-center mb-5">
        <div class="bg-dark rounded-3 p-2 me-2"><i class="fa-solid fa-heart-pulse text-white"></i></div>
        <span class="fw-800 fs-5">HEALTH.AI</span>
    </div>
    <div class="nav-menu">
        <a href="index.php" class="btn btn-white w-100 text-start mb-2 rounded-3"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a>
        <a href="risk_form.php" class="btn btn-white w-100 text-start mb-2 rounded-3"><i class="fa-solid fa-stethoscope me-2"></i> Risk Analizi</a>
        <a href="analysis.php" class="btn btn-light w-100 text-start mb-2 rounded-3 fw-bold text-success"><i class="fa-solid fa-microscope me-2"></i> Teknik Rapor</a>
    </div>
</nav>

<div class="main-content">
    <header class="hero-section text-center text-white">
        <div class="container">
            <h1 class="display-4 fw-bold">Makine Öğrenmesi Teknik Analizi</h1>
            <p class="lead opacity-75">Model Karşılaştırmaları ve Tahmin Performansı</p>
        </div>
    </header>

    <main class="container my-5">
        
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card p-4 text-center border-bottom border-4 border-primary">
                    <h6 class="text-uppercase text-muted fw-bold small">En İyi Model</h6>
                    <div class="metric-value"><?php echo $data['best_model']; ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 text-center border-bottom border-4 border-success">
                    <h6 class="text-uppercase text-muted fw-bold small">Genel Doğruluk</h6>
                    <div class="metric-value text-success">%<?php echo ($data['accuracy'] * 100); ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 text-center border-bottom border-4 border-danger">
                    <h6 class="text-uppercase text-muted fw-bold small">Recall (Sınıf 1)</h6>
                    <div class="metric-value text-danger">%<?php echo (floatval($data['recall_class_1']) * 100); ?></div>
                </div>
            </div>
        </div>

        <h3 class="section-title">Hata Matrisi Analizi (Confusion Matrix)</h3>
        <div class="row g-4 mb-5">
            <div class="col-lg-6">
                <div class="chart-container">
                    <h6 class="fw-bold mb-3">XGBoost Heatmap</h6>
                    <img src="images/xgboost_confusion.png" alt="XGBoost Confusion Matrix" class="chart-img">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="chart-container">
                    <h6 class="fw-bold mb-3">Random Forest Heatmap</h6>
                    <img src="images/random_confusion.png" alt="Random Forest Confusion Matrix" class="chart-img">
                </div>
            </div>
        </div>

        <h3 class="section-title">Değişken Önem Analizi (Feature Importance)</h3>
        <div class="row g-4 mb-5 text-center">
            <div class="col-lg-6">
                <div class="chart-container">
                    <h6 class="fw-bold mb-3">XGBoost Önem Sıralaması</h6>
                    <img src="images/xgboost.png" alt="XGBoost Feature Importance" class="chart-img">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="chart-container">
                    <h6 class="fw-bold mb-3">Random Forest Önem Sıralaması</h6>
                    <img src="images/feature_importance_random.png" alt="Random Forest Feature Importance" class="chart-img">
                </div>
            </div>
        </div>

        <h3 class="section-title">Tahmin Ayırt Edicilik (ROC Curve)</h3>
        <div class="row g-4 mb-5">
            <div class="col-lg-12">
                <div class="chart-container">
                    <h5 class="fw-bold mb-3 text-danger">ROC Eğrisi</h5>
                    <img src="images/roc_curve.png" alt="ROC Curve" class="chart-img" style="max-height: 400px;">
                    <p class="small text-muted mt-2">AUC: <strong><?php echo $data['auc_score'] ?? '0.85'; ?></strong></p>
                </div>
            </div>
        </div>

        <section class="mb-5">
            <h3 class="section-title text-center">Model Kıyaslama Tablosu</h3>
            <div class="table-responsive shadow-sm rounded-4 overflow-hidden">
                <table class="table table-hover align-middle mb-0 bg-white">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4">Algoritma</th>
                            <th>Accuracy</th>
                            <th>Recall</th>
                            <th>F1-Skor</th>
                            <th class="text-center">Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-success fw-bold">
                            <td class="ps-4">XGBoost</td>
                            <td>80.2%</td>
                            <td>0.72</td>
                            <td>0.75</td>
                            <td class="text-center"><span class="badge bg-success">Optimal</span></td>
                        </tr>
                        <tr>
                            <td class="ps-4">Random Forest</td>
                            <td>79.8%</td>
                            <td>0.62</td>
                            <td>0.70</td>
                            <td class="text-center"><span class="badge bg-secondary">Alternatif</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>