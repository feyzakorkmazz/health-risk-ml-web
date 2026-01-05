import pandas as pd

# Ham veriyi oku
df = pd.read_csv("health_raw.csv")

# Doğru sütunları seç ve yeniden adlandır
df = df[
    [
        "SMOKE100",
        "CHCCOPD3",
        "ADDEPEV3",
        "SLEPTIM1",
        "_BMI5",
        "EXERANY2",
        "_AGE80",
        "SEXVAR"
    ]
].rename(columns={
    "SMOKE100": "smoking_status",
    "CHCCOPD3": "copd",
    "ADDEPEV3": "depression",
    "SLEPTIM1": "sleep_hours",
    "_BMI5": "bmi",
    "EXERANY2": "physical_activity",
    "_AGE80": "age",
    "SEXVAR": "gender"
})

# BRFSS kod temizliği
# 1 = Yes, 2 = No, 7/9 = Missing
for c in ["smoking_status", "copd", "depression", "physical_activity"]:
    df[c] = df[c].replace({2: 0, 7: None, 9: None})

# BMI düzeltmesi (_BMI5 = BMI * 100)
df["bmi"] = df["bmi"] / 100

# Uyku saatleri filtre
df.loc[(df["sleep_hours"] < 1) | (df["sleep_hours"] > 24), "sleep_hours"] = None

# Cinsiyet
df["gender"] = df["gender"].replace({1: "M", 2: "F"})

# Eksikleri at
df = df.dropna()

# 🔗 Zincirleme hedef değişken
df["copd_and_depression"] = (
    (df["copd"] == 1) & (df["depression"] == 1)
).astype(int)

# Kaydet
df.to_csv("health_ml_dataset.csv", index=False)

print("✅ ML veri seti hazır: health_ml_dataset.csv")
print(df.head())
print("\nSatır sayısı:", len(df))
