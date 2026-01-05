<ul class="nav flex-column gap-2">
    <li class="nav-item mb-2">
        <small class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Navigasyon</small>
    </li>

    <li class="nav-item">
        <a class="nav-link rounded-3 px-3 py-2 fw-semibold <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-emerald text-white shadow-sm' : 'text-secondary'; ?>" href="index.php">
            <i class="fa-solid fa-gauge-high me-2"></i> Ana Dashboard
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link rounded-3 px-3 py-2 fw-semibold <?php echo basename($_SERVER['PHP_SELF']) == 'analysis.php' ? 'bg-emerald text-white shadow-sm' : 'text-secondary'; ?>" href="analysis.php">
            <i class="fa-solid fa-chart-line me-2"></i> Teknik Analiz
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link rounded-3 px-3 py-2 fw-semibold <?php echo basename($_SERVER['PHP_SELF']) == 'risk_form.php' ? 'bg-emerald text-white shadow-sm' : 'text-secondary'; ?>" href="risk_form.php">
            <i class="fa-solid fa-stethoscope me-2"></i> Risk Hesapla
        </a>
    </li>

    <hr class="my-3 opacity-10">

    <li class="nav-item">
        <a class="nav-link text-muted small" href="#" onclick="alert('Raporlama modülü çok yakında!')">
            <i class="fa-solid fa-file-pdf me-2"></i> PDF Rapor Al
        </a>
    </li>
</ul>

<style>
    /* Menüdeki Emerald (Zümrüt) rengi vurgusu */
    .bg-emerald { background-color: #10b981 !important; }
    .nav-link { transition: all 0.2s ease; border: 1px solid transparent; }
    .nav-link:hover:not(.bg-emerald) { background-color: #f1f5f9; color: #10b981 !important; }
</style>