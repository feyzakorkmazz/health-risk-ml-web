import pandas as pd

# XPT dosyası
xpt_path = "LLCP2022.XPT"

# XPT oku (encoding sorunlarını otomatik yönetir)
df = pd.read_sas(
    xpt_path,
    format="xport",
    encoding="latin-1"
)

# CSV olarak kaydet
df.to_csv("health_raw.csv", index=False)

print("Dönüştürme tamamlandı: health_raw.csv")
