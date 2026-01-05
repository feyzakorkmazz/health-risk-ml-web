<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sağlık Analiz Formu | Health.AI</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-emerald: #10b981;
            --dark-slate: #0f172a;
            --soft-bg: #f8fafc;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--soft-bg); 
            color: #1e293b; 
        }

        .form-container { max-width: 700px; margin: 80px auto; }

        /* Kart Tasarımı */
        .card { 
            border: none; 
            border-radius: 28px; 
            box-shadow: 0 20px 50px rgba(0,0,0,0.05); 
            background: white;
            overflow: hidden; 
        }

        /* Header Tasarımı */
        .card-header { 
            background: linear-gradient(135deg, var(--dark-slate) 0%, #1e293b 100%); 
            color: white; 
            padding: 40px; 
            border: none; 
            text-align: center; 
        }
        .card-header .icon-bg {
            width: 70px; height: 70px;
            background: rgba(16, 185, 129, 0.15);
            color: var(--primary-emerald);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
        }

        /* Form Elemanları */
        .form-label { font-weight: 700; color: #475569; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .input-group-text {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-right: none;
            border-radius: 14px 0 0 14px;
            color: #64748b;
            padding-left: 1.2rem;
            padding-right: 1.2rem;
        }

        .form-control, .form-select { 
            background: #ffffff;
            padding: 14px 18px; 
            border-radius: 0 14px 14px 0 !important; 
            border: 1px solid #e2e8f0; 
            font-weight: 500;
        }

        .form-control:focus, .form-select:focus { 
            border-color: var(--primary-emerald); 
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
            background: white;
        }

        /* Buton */
        .btn-calculate { 
            background: var(--primary-emerald);
            color: white;
            padding: 18px; 
            font-weight: 800; 
            text-transform: uppercase; 
            letter-spacing: 1.5px; 
            border-radius: 16px; 
            border: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            margin-top: 20px;
        }
        .btn-calculate:hover { 
            background: #059669;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
            color: white;
        }

        /* Bilgi Kutusu */
        .info-alert {
            background: #f0fdf4;
            border: 1px solid #dcfce7;
            border-radius: 16px;
            padding: 15px;
            color: #166534;
            font-size: 0.85rem;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>

<div class="container">
    <div class="form-container">
        
        <div class="card">
            <div class="card-header">
                <div class="icon-bg">
                    <i class="fa-solid fa-heart-pulse"></i>
                </div>
                <h2 class="fw-800 mb-1">Sağlık Analiz Formu</h2>
                <p class="mb-0 opacity-75">BRFSS 2022 veritabanı destekli risk değerlendirme</p>
            </div>

            <div class="card-body p-4 p-md-5">
                
                <div class="info-alert d-flex align-items-center mb-5">
                    <i class="fa-solid fa-circle-info me-3 fs-4"></i>
                    <div>
                        <strong>Not:</strong> Girdiğiniz veriler XGBoost modelimiz tarafından işlenerek KOAH ve depresyon birlikteliği riskinizi hesaplar.
                    </div>
                </div>

                <form action="risk_result.php" method="post">
                    
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label">Sigara Kullanım Durumu</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-smoking"></i></span>
                                <select name="smoking" class="form-select" required>
                                    <option value="" selected disabled>Seçiniz...</option>
                                    <option value="1">Evet, kullanıyorum</option>
                                    <option value="0">Hayır, kullanmıyorum</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Yaşınız</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-calendar-check"></i></span>
                                <input type="number" name="age" class="form-control" placeholder="Örn: 35" min="18" max="100" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">BMI (Vücut Kitle İndeksi)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-weight-scale"></i></span>
                                <input type="number" step="0.1" name="bmi" class="form-control" placeholder="Örn: 24.5" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Günlük Uyku Süresi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-clock"></i></span>
                                <input type="number" name="sleep" class="form-control" placeholder="Saat" min="1" max="24" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Fiziksel Aktivite</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-person-running"></i></span>
                                <select name="activity" class="form-select" required>
                                    <option value="" selected disabled>Seçiniz...</option>
                                    <option value="1">Düzenli Aktivite Var</option>
                                    <option value="0">Aktivite Yok / Kısıtlı</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-calculate w-100 shadow-sm">
                                <i class="fa-solid fa-wand-magic-sparkles me-2"></i>Analizi Başlat
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <div class="text-center mt-4">
            <p class="text-muted small">
                <i class="fa-solid fa-shield-halved me-1"></i> Verileriniz anonim olarak işlenir.
            </p>
            <a href="index.php" class="text-decoration-none text-muted small fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i> Dashboard'a Geri Dön
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>