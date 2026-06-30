"""
=============================================================
  Prediksi Volume Sampah Kota Surakarta
  Metode  : Random Forest Regression
  Dataset : Januari 2016 – Desember 2025 (120 observasi)
  Kelompok 1 — Informatika, Universitas Sebelas Maret
=============================================================

FITUR YANG DIGUNAKAN (11 total):
  Kependudukan & Kontekstual (8):
    X1 Jumlah_Penduduk       — tahunan, konstan per tahun
    X2 Kepadatan_Penduduk    — tahunan, konstan per tahun
    X3 Jumlah_Rumah_Tangga   — semesteran, konstan per semester
    X4 Jumlah_Rumah_Makan    — tahunan, konstan per tahun
    X5 Tingkat_Hunian_Hotel  — bulanan
    X6 Jumlah_Wisatawan      — tahunan, konstan per tahun
    X7 Curah_Hujan           — bulanan (missing → mean imputation)
    X8 Jumlah_Hari_Besar     — bulanan

  Fitur Kalender / Musiman (2):
    Bulan_Sin, Bulan_Cos     — Fourier encoding siklus bulanan
                               agar model tahu Desember ≈ Januari

  Fitur Kontekstual Tambahan (1):
    lag_12                   — volume sampah bulan yang sama tahun
                               sebelumnya, untuk menangkap pola
                               repetitif tahunan timbulan sampah.

CATATAN DATA:
  - Desember 2022 (19.158,19 ton) dipertahankan sesuai data resmi
    DLH Kota Surakarta dan dicatat sebagai anomali.
  - Curah hujan 2025 seluruhnya NaN → mean imputation.
  - Jumlah Wisatawan 2016 dibagi 12 (366.296/bulan).
  - lag_12 menyebabkan Jan–Des 2016 di-drop (tidak ada referensi
    tahun sebelumnya). Dataset efektif: 108 observasi (Jan 2017–Des 2025).

SPLIT & EVALUASI:
  - Train : Jan 2017 – Des 2023 (84 observasi, ~78%)
  - Test  : Jan 2024 – Des 2025 (24 observasi, ~22%)
  - Split dilakukan secara berurutan (temporal).
  - Evaluasi utama: 5-fold TimeSeriesSplit CV pada data train.
  - Evaluasi final: metrik pada test set (hold-out).

FORECASTING 2026:
  - Variabel tahunan (X1,X2,X4,X6): linear extrapolation 2016–2025.
  - Variabel semesteran (X3): linear extrapolation per semester.
  - Variabel bulanan (X5,X7,X8): rata-rata per bulan historis.
  - lag_12: diambil langsung dari data aktual Jan–Des 2025.
  - Fitur kalender dihitung otomatis.
=============================================================
"""

import warnings
import numpy as np
import pandas as pd
import matplotlib.pyplot as plt
import matplotlib.ticker as mticker
import matplotlib.gridspec as gridspec
import seaborn as sns

from sklearn.ensemble      import RandomForestRegressor
from sklearn.preprocessing import MinMaxScaler
from sklearn.model_selection import (
    GridSearchCV, TimeSeriesSplit, cross_val_score
)
from sklearn.metrics import (
    mean_absolute_error, mean_squared_error, r2_score
)
from sklearn.inspection import permutation_importance

warnings.filterwarnings('ignore')

# ══════════════════════════════════════════════════════════════
# 0. KONFIGURASI OUTPUT
# ══════════════════════════════════════════════════════════════

OUTPUT_DIR = 'C:\\Users\\Hp\\Documents\\KECERDASAN BUATAN'

SPLIT_IDX   = 84          # train Jan 2017 – Des 2023 (setelah drop 2016 akibat lag_12)
RANDOM_SEED = 42
N_SPLITS_CV = 5           # TimeSeriesSplit folds

# ══════════════════════════════════════════════════════════════
# 1. DATA MENTAH (120 observasi, Jan 2016 – Des 2025)
# ══════════════════════════════════════════════════════════════

data_raw = {
    'Tahun': [*[2016]*12, *[2017]*12, *[2018]*12, *[2019]*12,
              *[2020]*12, *[2021]*12, *[2022]*12, *[2023]*12,
              *[2024]*12, *[2025]*12],
    'Bulan': list(range(1, 13)) * 10,

    # Y — Volume sampah masuk TPA Putri Cempo (ton/bulan)
    # Sumber: DLH Kota Surakarta
    # Catatan: Des 2022 (19.158,19) dipertahankan sesuai data resmi
    'Volume_Sampah': [
        8393.73, 8699.22, 9178.07, 8680.27, 9336.46, 9164.74,
        9082.43, 9260.58, 8871.44, 9555.47, 9409.41, 9650.89,
        9619.05, 8913.03, 9646.49, 8762.76, 9216.57, 8514.12,
        8397.59, 7947.26, 7579.10, 8683.12, 9303.01, 9696.76,
        10257,   9617,    10177,   9529,    9201,    8620,
        8667,    8881,    8729,    9171,    9311,    9677,
        10541.02,9587.45, 10451.47,9431.60, 9328.70, 8097.98,
        8820.23, 8591.63, 8241.92, 8701.58, 9028.42, 10071.88,
        10027,   9548,    9921,    8307,    8410,    9359,
        9036,    9395,    8593,    8552,    8388,    8337,
        7945.59, 6886.63, 7759.57, 8498.64, 10292.97,10334.08,
        8995.07, 9120.92, 8929.10, 9247.10, 10715.16,10573.10,
        10232.03,7263.32, 10843.28,11001.22,10973.61,10479.79,
        10360.51,10822.86,10602.59,11565.99,12183.10,19158.19,
        12781.08,10994.63,12493.64,11528.46,12131.13,10647.38,
        10901.68,10560.04,10161.49,11172.21,11248.81,11376.61,
        13393,   11768,   12333,   12542,   12191,   11331,
        11460,   11213,   11190,   11527,   12205,   12541,
        11949,   10522,   11358,   9544,    9799,    8871,
        9413,    9354,    9187,    10242,   10438,   10828,
    ],

    # X1 — Jumlah penduduk (jiwa), BPS, tahunan
    'Jumlah_Penduduk': [
        *[514171]*12, *[516102]*12, *[517887]*12, *[519587]*12,
        *[522364]*12, *[522728]*12, *[523008]*12, *[526870]*12,
        *[528044]*12, *[529079]*12,
    ],

    # X2 — Kepadatan penduduk (jiwa/km²), BPS, tahunan
    'Kepadatan_Penduduk': [
        *[11674.93]*12, *[11718.65]*12, *[11759.31]*12, *[11798]*12,
        *[11861]*12,    *[11188]*12,    *[11194]*12,    *[11277]*12,
        *[11302]*12,    *[11324]*12,
    ],

    # X3 — Jumlah rumah tangga, BPS, semesteran
    'Jumlah_Rumah_Tangga': [
        *[180027]*6, *[180027]*6,
        *[184547]*6, *[178175]*6,
        *[177105]*6, *[183544]*6,
        *[185055]*6, *[187684]*6,
        *[192713]*6, *[192764]*6,
        *[193761]*6, *[194898]*6,
        *[193177]*6, *[196106]*6,
        *[197046]*6, *[198615]*6,
        *[199560]*6, *[201858]*6,
        *[201979]*6, *[203284]*6,
    ],

    # X4 — Jumlah rumah makan (unit), BPS, tahunan
    'Jumlah_Rumah_Makan': [
        *[134]*12,  *[678]*12,  *[778]*12,  *[693]*12,
        *[727]*12,  *[716]*12,  *[1148]*12, *[724]*12,
        *[548]*12,  *[811]*12,
    ],

    # X5 — Tingkat hunian hotel (%), BPS, bulanan
    'Tingkat_Hunian_Hotel': [
        38.37, 40.88, 43.95, 45.36, 50.88, 38.82, 53.05, 53.22, 49.01, 47.80, 45.98, 56.77,
        39.38, 41.92, 46.24, 51.49, 49.10, 46.10, 52.19, 51.10, 48.07, 46.61, 55.95, 61.35,
        42.65, 45.50, 42.49, 45.50, 40.99, 44.75, 48.48, 45.42, 46.89, 48.43, 49.54, 54.26,
        44.06, 47.37, 49.25, 51.95, 36.49, 59.67, 55.73, 52.61, 52.98, 55.04, 57.47, 63.31,
        45.38, 51.05, 30.19, 12.70, 13.68, 19.87, 26.14, 29.76, 28.79, 35.95, 36.00, 31.92,
        23.33, 27.19, 31.61, 32.13, 25.35, 34.92, 16.76, 23.85, 34.68, 45.28, 46.17, 48.09,
        34.87, 38.10, 44.39, 32.56, 54.04, 48.26, 49.41, 42.18, 45.97, 45.72, 55.10, 58.19,
        44.39, 45.16, 45.24, 42.71, 49.01, 52.60, 51.82, 47.48, 51.13, 45.43, 52.92, 57.74,
        41.69, 42.20, 34.61, 46.36, 51.85, 49.35, 50.19, 44.64, 44.77, 50.81, 46.15, 54.00,
        46.72, 39.91, 28.18, 45.35, 43.97, 46.71, 45.45, 43.00, 43.32, 46.44, 43.52, 51.03,
    ],

    # X6 — Jumlah wisatawan (orang), BPS
    # 2016: data hanya tersedia sebagai agregat tahunan (4.395.550),
    #        diasumsikan terdistribusi merata → 4.395.550 / 12 = 366.296/bulan
    # 2017–2025: data bulanan aktual
    'Jumlah_Wisatawan': [
        *[366296]*12,
        232249, 199996, 221351, 240749, 250500, 260191,
        282942, 242696, 250940, 286039, 259838, 336840,
        230931, 207660, 216354, 234730, 224939, 336415,
        259855, 259590, 273921, 276257, 275833, 466170,
        260372, 239220, 248822, 268625, 204706, 341769,
        276164, 240943, 273852, 307569, 312322, 464323,
        140770, 147418,  29147,      0,      0,      0,
          6654,   9908,   6692,   5590,   4254,   3673,
          5304,   8860,  18693,  21905,  48025,  60382,
           199,      0,   8711,  40920,  68568,  97525,
         88793,  60252,  67516,  29980, 213750,1772521,
        146284, 113823,  27575, 133753,  26849,  36351,
         35562,  76362, 382798, 538255, 340133, 470145,
        427437, 322940, 345164, 307293, 286310, 491409,
        265527, 301855, 303715, 454986, 404796, 397908,
        492485, 328090, 389435, 338670, 322098, 397519,
        456377, 363347, 295959, 526577, 416627, 424552,
        474122, 305223, 354270, 415169, 308204, 361386,
    ],

    # X7 — Curah hujan (mm), BMKG, bulanan
    # Missing value (np.nan) → mean imputation di preprocessing
    'Curah_Hujan': [
         72.0,  164.0,  143.0,   52.0,  104.0,  123.0,
        113.0,   21.0,   79.0,   77.0,  148.0,   91.0,
         13.6,   19.9,    7.8,    9.8,    1.0,    4.4,
          0.0,    4.7,    2.6,    3.2,   12.8,    5.3,
         13.6,   19.9,    7.8,    9.8,    1.0,    4.4,
          0.0,    4.7,    2.6,    3.2,   12.8,    5.3,
        573.9,  334.8,  360.5,  172.6,   36.0, np.nan,
       np.nan, np.nan, np.nan, np.nan,   90.2,  247.7,
        275.3,  199.4,  175.7,  131.12, 182.55,   0.0,
          5.3,   34.3,    5.6,  256.3,  249.2,  187.7,
        581.8,  276.0,  265.0,  164.1,   65.1,  240.2,
          0.0,  542.0,   61.1,   74.0,  303.4,  232.0,
         15.0,   15.0,   14.0,   15.0,   13.0,   14.0,
       np.nan, np.nan, np.nan, np.nan, np.nan, np.nan,
        279.2,  465.0,  493.1,  166.2,  150.0, np.nan,
       np.nan, np.nan, np.nan,   42.0,   66.0,   79.9,
        470.2,  397.2,  259.3,  397.2,   86.7,   36.0,
       np.nan, np.nan,  130.0,   24.0,  293.0,  355.0,
       np.nan, np.nan, np.nan, np.nan, np.nan, np.nan,
       np.nan, np.nan, np.nan, np.nan, np.nan, np.nan,
    ],

    # X8 — Jumlah hari besar nasional, BPS/Pemerintah, bulanan
    'Jumlah_Hari_Besar': [
        1, 1, 3, 0, 4, 0, 5, 1, 1, 2, 0, 5,
        2, 0, 1, 3, 3, 3, 0, 1, 2, 1, 0, 3,
        1, 1, 2, 2, 3,11, 0, 2, 1, 0, 2, 3,
        1, 1, 1, 4, 3, 6, 2, 0, 1, 1, 1, 3,
        2, 0, 2, 0, 0, 1, 1, 3, 0, 3, 1, 4,
        1, 1, 3, 2, 9, 1, 5, 1, 0, 2, 1, 4,
        1, 2, 1, 3, 8, 1, 2, 1, 0, 2, 0, 4,
        3, 1, 3, 9, 2, 4, 1, 1, 1, 0, 1, 4,
        1, 4, 5, 6, 5, 3, 8, 1, 1, 0, 1, 4,
        4, 0, 4, 7, 5, 4, 0, 2, 1, 1, 0, 4,
    ],
}

# ══════════════════════════════════════════════════════════════
# 2. PREPROCESSING
# ══════════════════════════════════════════════════════════════

def preprocess(data_raw):
    df = pd.DataFrame(data_raw)
    df['Tanggal'] = pd.to_datetime(
        {'year': df['Tahun'], 'month': df['Bulan'], 'day': 1}
    )
    df = df.sort_values('Tanggal').reset_index(drop=True)

    # --- Mean imputation untuk missing value (Curah Hujan) ---
    n_missing = df['Curah_Hujan'].isna().sum()
    df['Curah_Hujan'] = df['Curah_Hujan'].fillna(df['Curah_Hujan'].mean())
    print(f"    Missing value Curah_Hujan : {n_missing} nilai → mean imputation "
          f"({df['Curah_Hujan'].mean():.2f} mm)")

    # --- Fitur kalender: Fourier encoding siklus bulanan ---
    df['Bulan_Sin'] = np.sin(2 * np.pi * df['Bulan'] / 12)
    df['Bulan_Cos'] = np.cos(2 * np.pi * df['Bulan'] / 12)

    # --- lag_12: volume bulan yang sama tahun sebelumnya ---
    # Menangkap pola repetitif tahunan timbulan sampah.
    # 12 baris pertama (Jan–Des 2016) akan menjadi NaN dan di-drop.
    df['lag_12'] = df['Volume_Sampah'].shift(12)

    # Drop baris NaN akibat lag_12 (Jan–Des 2016)
    df = df.dropna(subset=['lag_12']).reset_index(drop=True)
    print(f"    Dataset efektif: {len(df)} observasi "
          f"(Jan {df['Tahun'].min()} – Des {df['Tahun'].max()}) "
          f"setelah drop 2016 akibat lag_12")

    return df


# ══════════════════════════════════════════════════════════════
# 3. FEATURE DEFINITION & SCALING
# ══════════════════════════════════════════════════════════════

FITUR_COLS = [
    'Jumlah_Penduduk',     # X1
    'Kepadatan_Penduduk',  # X2
    'Jumlah_Rumah_Tangga', # X3
    'Jumlah_Rumah_Makan',  # X4
    'Tingkat_Hunian_Hotel',# X5
    'Jumlah_Wisatawan',    # X6
    'Curah_Hujan',         # X7
    'Jumlah_Hari_Besar',   # X8
    'Bulan_Sin',           # kalender
    'Bulan_Cos',           # kalender
    'lag_12',              # kontekstual: pola repetitif tahunan
]

FITUR_LABEL = [
    'Jml. Penduduk', 'Kepadatan Pddk', 'Jml. Rmh Tangga',
    'Jml. Rmh Makan', 'Hunian Hotel', 'Jml. Wisatawan',
    'Curah Hujan', 'Hari Besar',
    'Bulan (Sin)', 'Bulan (Cos)', 'Lag-12 (Vol. thn lalu)',
]

TARGET_COL = 'Volume_Sampah'


def split_and_scale(df):
    X = df[FITUR_COLS].values
    y = df[TARGET_COL].values

    # Temporal split: train = 80% pertama, test = 20% terakhir
    X_train, X_test = X[:SPLIT_IDX], X[SPLIT_IDX:]
    y_train, y_test = y[:SPLIT_IDX], y[SPLIT_IDX:]

    # MinMax scaler fit HANYA pada train (cegah data leakage dari test)
    scaler_X = MinMaxScaler()
    scaler_y = MinMaxScaler()

    X_train_s = scaler_X.fit_transform(X_train)
    X_test_s  = scaler_X.transform(X_test)

    y_train_s = scaler_y.fit_transform(y_train.reshape(-1, 1)).ravel()
    y_test_s  = scaler_y.transform(y_test.reshape(-1, 1)).ravel()

    return (X_train_s, X_test_s, y_train_s, y_test_s,
            y_train, y_test, X, y, scaler_X, scaler_y)


# ══════════════════════════════════════════════════════════════
# 4. HYPERPARAMETER TUNING (GridSearchCV + TimeSeriesSplit)
# ══════════════════════════════════════════════════════════════

def tune(X_train_s, y_train_s):
    """
    GridSearchCV dengan TimeSeriesSplit sebagai CV strategy.
    TimeSeriesSplit digunakan (bukan KFold biasa) karena data
    bersifat deret waktu — fold selalu berurutan maju (expanding
    window), sehingga tidak ada kebocoran informasi masa depan
    ke data latih dalam setiap fold.
    """
    tscv = TimeSeriesSplit(n_splits=N_SPLITS_CV)

    param_grid = {
        'n_estimators':      [100, 200, 300],
        'max_depth':         [None, 10, 20],
        'min_samples_split': [2, 5],
        'min_samples_leaf':  [1, 2],
        'max_features':      ['sqrt', None],
    }

    gs = GridSearchCV(
        RandomForestRegressor(random_state=RANDOM_SEED),
        param_grid,
        cv=tscv,
        scoring='neg_mean_absolute_error',
        n_jobs=-1,
        verbose=0,
    )
    gs.fit(X_train_s, y_train_s)

    print(f"    Best CV MAE (scaled) : {-gs.best_score_:.6f}")
    print(f"    Best params          :")
    for k, v in gs.best_params_.items():
        print(f"      {k:<22}: {v}")

    return gs.best_params_


# ══════════════════════════════════════════════════════════════
# 5. TRAINING MODEL FINAL + CV EVALUATION
# ══════════════════════════════════════════════════════════════

def train(X_train_s, y_train_s, best_params):
    model = RandomForestRegressor(
        **best_params, random_state=RANDOM_SEED, n_jobs=-1
    )
    model.fit(X_train_s, y_train_s)

    # CV score pada train set — dilaporkan sebagai evaluasi utama
    tscv = TimeSeriesSplit(n_splits=N_SPLITS_CV)
    cv_r2  = cross_val_score(model, X_train_s, y_train_s,
                              cv=tscv, scoring='r2')
    cv_mae = cross_val_score(model, X_train_s, y_train_s,
                              cv=tscv, scoring='neg_mean_absolute_error')

    print(f"\n    CV R²  (5-fold TimeSeriesSplit) : "
          f"{cv_r2.mean():.4f} ± {cv_r2.std():.4f}")
    print(f"    CV MAE (scaled, 5-fold)         : "
          f"{(-cv_mae.mean()):.6f} ± {cv_mae.std():.6f}")

    return model, cv_r2


# ══════════════════════════════════════════════════════════════
# 6. EVALUASI METRIK
# ══════════════════════════════════════════════════════════════

def hitung_metrik(y_true, y_pred, label):
    mae  = mean_absolute_error(y_true, y_pred)
    rmse = np.sqrt(mean_squared_error(y_true, y_pred))
    mape = np.mean(np.abs((y_true - y_pred) / y_true)) * 100
    r2   = r2_score(y_true, y_pred)
    print(f"  [{label}]  MAE={mae:,.2f}  RMSE={rmse:,.2f}  "
          f"MAPE={mape:.2f}%  R²={r2:.4f}")
    return {'label': label, 'MAE': mae, 'RMSE': rmse,
            'MAPE': mape, 'R2': r2}


# ══════════════════════════════════════════════════════════════
# 7. FEATURE IMPORTANCE
# ══════════════════════════════════════════════════════════════

def importance(model, X_test_s, y_test_s):
    """
    MDI (Mean Decrease in Impurity) — sesuai paper.
    Permutation Importance dihitung sebagai cross-check
    karena MDI cenderung bias pada fitur dengan kardinalitas tinggi.
    """
    mdi = model.feature_importances_

    perm = permutation_importance(
        model, X_test_s, y_test_s,
        n_repeats=30, random_state=RANDOM_SEED, n_jobs=-1
    )

    imp_df = pd.DataFrame({
        'Fitur':      FITUR_LABEL,
        'Kode':       FITUR_COLS,
        'MDI':        mdi,
        'Perm_Mean':  perm.importances_mean,
        'Perm_Std':   perm.importances_std,
    }).sort_values('MDI', ascending=False).reset_index(drop=True)

    return imp_df


# ══════════════════════════════════════════════════════════════
# 8. FORECASTING 2026
# ══════════════════════════════════════════════════════════════

def forecast_2026(df, model, scaler_X, scaler_y):
    """
    Forecasting 2026 dengan linear extrapolation per variabel.

    lag_12 untuk setiap bulan 2026 diambil langsung dari
    data aktual 2025 yang sudah tersedia di dataset —
    tidak perlu estimasi atau asumsi tambahan.
    """

    years = np.arange(2016, 2026)

    # --- Extrapolasi variabel tahunan ---
    def extrap_tahunan(kolom):
        vals = df.groupby('Tahun')[kolom].first().values
        yr   = np.arange(df['Tahun'].min(), df['Tahun'].max() + 1)
        slope = np.polyfit(yr, vals, 1)
        return np.polyval(slope, 2026)

    pop_2026 = extrap_tahunan('Jumlah_Penduduk')
    kpd_2026 = extrap_tahunan('Kepadatan_Penduduk')
    rm_2026  = extrap_tahunan('Jumlah_Rumah_Makan')
    wis_2026 = extrap_tahunan('Jumlah_Wisatawan')

    # --- Extrapolasi variabel semesteran ---
    sem_years = np.arange(df['Tahun'].min(), df['Tahun'].max() + 1)
    s1_vals = df[df['Bulan'] == 1].groupby('Tahun')['Jumlah_Rumah_Tangga'].first().values
    s2_vals = df[df['Bulan'] == 7].groupby('Tahun')['Jumlah_Rumah_Tangga'].first().values
    rt_s1_2026 = np.polyval(np.polyfit(sem_years, s1_vals, 1), 2026)
    rt_s2_2026 = np.polyval(np.polyfit(sem_years, s2_vals, 1), 2026)

    # --- Rata-rata bulanan untuk variabel bulanan ---
    monthly_hotel = df.groupby('Bulan')['Tingkat_Hunian_Hotel'].mean()
    monthly_hujan = df.groupby('Bulan')['Curah_Hujan'].mean()
    monthly_hari  = df.groupby('Bulan')['Jumlah_Hari_Besar'].mean()

    # --- lag_12 untuk 2026: ambil dari data aktual 2025 ---
    vol_2025 = df[df['Tahun'] == 2025].set_index('Bulan')['Volume_Sampah']

    records = []
    for bulan in range(1, 13):
        rt_2026 = rt_s1_2026 if bulan <= 6 else rt_s2_2026

        row = {
            'Tahun': 2026, 'Bulan': bulan,
            'Jumlah_Penduduk':     pop_2026,
            'Kepadatan_Penduduk':  kpd_2026,
            'Jumlah_Rumah_Tangga': rt_2026,
            'Jumlah_Rumah_Makan':  rm_2026,
            'Tingkat_Hunian_Hotel':monthly_hotel[bulan],
            'Jumlah_Wisatawan':    wis_2026,
            'Curah_Hujan':         monthly_hujan[bulan],
            'Jumlah_Hari_Besar':   monthly_hari[bulan],
            'Bulan_Sin': np.sin(2 * np.pi * bulan / 12),
            'Bulan_Cos': np.cos(2 * np.pi * bulan / 12),
            'lag_12':    vol_2025[bulan],  # ← data aktual 2025, tidak perlu estimasi
        }
        records.append(row)

    pred_df = pd.DataFrame(records)
    X_2026  = pred_df[FITUR_COLS].values
    X_2026s = scaler_X.transform(X_2026)

    y_2026s = model.predict(X_2026s)
    y_2026  = scaler_y.inverse_transform(
        y_2026s.reshape(-1, 1)
    ).ravel()

    pred_df['Prediksi_Volume'] = np.round(y_2026, 2)
    pred_df['Tanggal'] = pd.to_datetime(
        pred_df['Tahun'].astype(str) + '-' +
        pred_df['Bulan'].astype(str) + '-01'
    )

    return pred_df


# ══════════════════════════════════════════════════════════════
# 9. VISUALISASI
# ══════════════════════════════════════════════════════════════

def visualisasi(df, y_train, y_test,
                pred_train, pred_test,
                m_train, m_test,
                imp_df, pred_df, cv_r2):

    BULAN_LABEL = ['Jan','Feb','Mar','Apr','Mei','Jun',
                   'Jul','Agt','Sep','Okt','Nov','Des']

    C_BIRU   = '#1565C0'
    C_MERAH  = '#C62828'
    C_HIJAU  = '#2E7D32'
    C_ORANGE = '#E65100'
    C_UNGU   = '#6A1B9A'
    C_GRAY   = '#78909C'

    all_dates   = df['Tanggal'].values
    train_dates = df['Tanggal'].iloc[:SPLIT_IDX].values
    test_dates  = df['Tanggal'].iloc[SPLIT_IDX:].values
    y_all       = df[TARGET_COL].values

    fig = plt.figure(figsize=(20, 28))
    fig.patch.set_facecolor('white')
    fig.suptitle(
        'Prediksi Volume Sampah Kota Surakarta\n'
        'Random Forest Regression — Kelompok 1 Informatika UNS',
        fontsize=16, fontweight='bold', y=0.99
    )
    gs_main = gridspec.GridSpec(
        4, 2, figure=fig, hspace=0.45, wspace=0.30
    )

    fmt_ton = mticker.FuncFormatter(lambda x, _: f'{x:,.0f}')

    # ── Plot 1 (span 2 kolom): Timeline historis + prediksi ──
    ax1 = fig.add_subplot(gs_main[0, :])
    ax1.plot(all_dates, y_all, color=C_BIRU, lw=1.8,
             label='Aktual', zorder=3)
    ax1.plot(train_dates, pred_train, color=C_HIJAU, lw=1.3,
             ls='--', alpha=0.85, label='Prediksi (Train)', zorder=2)
    ax1.plot(test_dates, pred_test, color=C_MERAH, lw=2.2,
             label='Prediksi (Test)', zorder=4)
    ax1.axvline(pd.Timestamp('2024-01-01'), color=C_GRAY,
                lw=1.5, ls=':', zorder=1)
    ax1.axvline(pd.Timestamp('2022-01-01'), color=C_ORANGE,
                lw=1.2, ls='--', alpha=0.6, zorder=1)
    ax1.text(pd.Timestamp('2022-02-01'), ax1.get_ylim()[1],
             'Lonjakan 2022+', fontsize=8, color=C_ORANGE, va='top')
    ax1.set_title('Volume Sampah Aktual vs Prediksi (2016–2025)',
                  fontweight='bold', fontsize=13)
    ax1.set_ylabel('Volume Sampah (Ton)')
    ax1.yaxis.set_major_formatter(fmt_ton)
    ax1.legend(fontsize=9); ax1.grid(True, alpha=0.3)

    # ── Plot 2: Detail Test Set ──
    ax2 = fig.add_subplot(gs_main[1, 0])
    ax2.plot(test_dates, y_test, 'o-', color=C_BIRU, lw=2,
             ms=5.5, label='Aktual')
    ax2.plot(test_dates, pred_test, 's--', color=C_MERAH, lw=2,
             ms=5.5, label='Prediksi')
    ax2.fill_between(test_dates, y_test, pred_test,
                     alpha=0.12, color=C_GRAY)
    ax2.set_xlim(pd.Timestamp('2024-01-01'), pd.Timestamp('2025-12-31'))
    ax2.set_title(
        f'Detail Test Set (Jan 2024 – Des 2025)\n'
        f'MAPE = {m_test["MAPE"]:.2f}%   R² = {m_test["R2"]:.4f}',
        fontweight='bold', fontsize=11
    )
    ax2.set_ylabel('Volume Sampah (Ton)')
    ax2.yaxis.set_major_formatter(fmt_ton)
    ax2.legend(fontsize=9); ax2.grid(True, alpha=0.3)
    plt.setp(ax2.xaxis.get_majorticklabels(), rotation=30, ha='right')

    # ── Plot 3: Scatter aktual vs prediksi (test) ──
    ax3 = fig.add_subplot(gs_main[1, 1])
    ax3.scatter(y_test, pred_test, color=C_UNGU, alpha=0.75,
                s=60, edgecolors='white', lw=0.5, label='Test set')
    lo = min(y_test.min(), pred_test.min()) - 300
    hi = max(y_test.max(), pred_test.max()) + 300
    ax3.plot([lo, hi], [lo, hi], color=C_MERAH, lw=1.8,
             ls='--', label='Ideal (y=x)')
    ax3.set_title(f'Scatter Aktual vs Prediksi\nR² = {m_test["R2"]:.4f}',
                  fontweight='bold', fontsize=11)
    ax3.set_xlabel('Aktual (Ton)'); ax3.set_ylabel('Prediksi (Ton)')
    ax3.xaxis.set_major_formatter(fmt_ton)
    ax3.yaxis.set_major_formatter(fmt_ton)
    ax3.legend(fontsize=9); ax3.grid(True, alpha=0.3)

    # ── Plot 4: Feature Importance (MDI) ──
    ax4 = fig.add_subplot(gs_main[2, 0])
    top_imp = imp_df.sort_values('MDI').tail(11)
    colors_fi = [
        C_ORANGE if k in ['Bulan_Sin', 'Bulan_Cos', 'lag_12']
        else C_BIRU
        for k in top_imp['Kode']
    ]
    bars = ax4.barh(top_imp['Fitur'], top_imp['MDI'],
                    color=colors_fi, edgecolor='white', height=0.65)
    for bar in bars:
        w = bar.get_width()
        ax4.text(w + 0.002, bar.get_y() + bar.get_height() / 2,
                 f'{w:.4f}', va='center', fontsize=8.5)
    from matplotlib.patches import Patch
    ax4.legend(handles=[
        Patch(color=C_ORANGE, label='Fitur Tambahan'),
        Patch(color=C_BIRU,   label='Fitur Kependudukan'),
    ], fontsize=8)
    ax4.set_title('Feature Importance (MDI)', fontweight='bold', fontsize=11)
    ax4.set_xlabel('Importance Score')
    ax4.grid(True, alpha=0.3, axis='x')

    # ── Plot 5: Residual plot (test set) ──
    ax5 = fig.add_subplot(gs_main[2, 1])
    residuals = y_test - pred_test
    ax5.scatter(pred_test, residuals, color=C_UNGU, alpha=0.75,
                s=60, edgecolors='white', lw=0.5)
    ax5.axhline(0, color=C_MERAH, lw=1.8, ls='--')
    ax5.fill_between(
        [pred_test.min() - 200, pred_test.max() + 200],
        [-500, -500], [500, 500],
        alpha=0.08, color=C_HIJAU, label='±500 ton'
    )
    ax5.set_title('Residual Plot (Test Set)', fontweight='bold', fontsize=11)
    ax5.set_xlabel('Nilai Prediksi (Ton)')
    ax5.set_ylabel('Residual (Ton)')
    ax5.xaxis.set_major_formatter(fmt_ton)
    ax5.yaxis.set_major_formatter(fmt_ton)
    ax5.legend(fontsize=9); ax5.grid(True, alpha=0.3)

    # ── Plot 6: Forecasting 2026 ──
    ax6 = fig.add_subplot(gs_main[3, 0])
    hist_tail = df.tail(24)
    ax6.plot(hist_tail['Tanggal'], hist_tail[TARGET_COL],
             'o-', color=C_BIRU, lw=2, ms=4.5,
             label='Historis (2024–2025)')
    ax6.plot(pred_df['Tanggal'], pred_df['Prediksi_Volume'],
             's--', color=C_HIJAU, lw=2, ms=4.5,
             label='Prediksi 2026')
    ax6.axvline(pd.Timestamp('2026-01-01'), color=C_GRAY,
                lw=1.5, ls=':', label='Batas historis')
    ax6.set_title('Prediksi Volume Sampah 2026', fontweight='bold', fontsize=11)
    ax6.set_ylabel('Volume Sampah (Ton)')
    ax6.yaxis.set_major_formatter(fmt_ton)
    ax6.legend(fontsize=9); ax6.grid(True, alpha=0.3)
    plt.setp(ax6.xaxis.get_majorticklabels(), rotation=30, ha='right')

    # ── Plot 7: Pola musiman historis ──
    ax7 = fig.add_subplot(gs_main[3, 1])
    monthly_avg = df.groupby('Bulan')[TARGET_COL].mean()
    monthly_std = df.groupby('Bulan')[TARGET_COL].std()
    ax7.plot(range(1, 13), monthly_avg.values, 'o-',
             color=C_BIRU, lw=2, ms=7)
    ax7.fill_between(
        range(1, 13),
        monthly_avg.values - monthly_std.values,
        monthly_avg.values + monthly_std.values,
        alpha=0.15, color=C_BIRU, label='±1 Std Dev'
    )
    ax7.set_xticks(range(1, 13))
    ax7.set_xticklabels(BULAN_LABEL, rotation=45)
    ax7.set_title('Pola Musiman Volume Sampah\n(Rata-rata ± Std per Bulan, 2016–2025)',
                  fontweight='bold', fontsize=11)
    ax7.set_ylabel('Volume Sampah (Ton)')
    ax7.yaxis.set_major_formatter(fmt_ton)
    ax7.legend(fontsize=9); ax7.grid(True, alpha=0.3)

    plt.savefig(f'{OUTPUT_DIR}/rf_sampah_hasil.png',
                dpi=150, bbox_inches='tight', facecolor='white')
    plt.close()
    print(f"\n    Visualisasi → rf_sampah_hasil.png")

    # ── Visualisasi 2: Analisis tambahan ──
    fig2, axes2 = plt.subplots(1, 2, figsize=(16, 6))
    fig2.suptitle('Analisis Tambahan — Volume Sampah Kota Surakarta',
                  fontsize=14, fontweight='bold')

    # Heatmap
    ax = axes2[0]
    pivot = df.pivot_table(
        values=TARGET_COL, index='Tahun', columns='Bulan', aggfunc='mean'
    )
    pivot.columns = BULAN_LABEL
    sns.heatmap(pivot, ax=ax, cmap='YlOrRd', annot=True, fmt='.0f',
                annot_kws={'size': 7}, linewidths=0.4,
                cbar_kws={'label': 'Ton'})
    ax.set_title('Heatmap Volume Sampah per Tahun & Bulan',
                 fontweight='bold', fontsize=12)
    ax.set_xlabel('Bulan'); ax.set_ylabel('Tahun')

    # CV scores per fold
    ax = axes2[1]
    ax.bar(range(1, N_SPLITS_CV + 1), cv_r2,
           color=C_BIRU, alpha=0.8, edgecolor='white')
    ax.axhline(cv_r2.mean(), color=C_MERAH, lw=2, ls='--',
               label=f'Rata-rata CV R² = {cv_r2.mean():.4f}')
    ax.set_title(f'R² per Fold (5-fold TimeSeriesSplit)\nTrain Set',
                 fontweight='bold', fontsize=12)
    ax.set_xlabel('Fold ke-'); ax.set_ylabel('R²')
    ax.set_xticks(range(1, N_SPLITS_CV + 1))
    ax.legend(fontsize=9); ax.grid(True, alpha=0.3, axis='y')
    ax.set_ylim(min(cv_r2) - 0.1, 1.0)

    plt.tight_layout()
    plt.savefig(f'{OUTPUT_DIR}/rf_sampah_analisis.png',
                dpi=150, bbox_inches='tight', facecolor='white')
    plt.close()
    print(f"    Visualisasi → rf_sampah_analisis.png")


# ══════════════════════════════════════════════════════════════
# 10. SIMPAN OUTPUT CSV
# ══════════════════════════════════════════════════════════════

def simpan_csv(df, y_train, y_test, pred_train, pred_test,
               m_train, m_test, imp_df, pred_df, cv_r2):

    # CSV 1: Aktual vs Prediksi — Train Set
    df_train_out = df.iloc[:SPLIT_IDX][['Tanggal','Tahun','Bulan', TARGET_COL]].copy()
    df_train_out['Prediksi'] = np.round(pred_train, 2)
    df_train_out['Error_Ton'] = np.round(y_train - pred_train, 2)
    df_train_out['Error_%']   = np.round(
        np.abs((y_train - pred_train) / y_train) * 100, 2
    )
    df_train_out.to_csv(f'{OUTPUT_DIR}/rf_aktual_prediksi_train.csv', index=False)

    # CSV 2: Aktual vs Prediksi — Test Set
    df_test_out = df.iloc[SPLIT_IDX:][['Tanggal','Tahun','Bulan', TARGET_COL]].copy()
    df_test_out['Prediksi'] = np.round(pred_test, 2)
    df_test_out['Error_Ton'] = np.round(y_test - pred_test, 2)
    df_test_out['Error_%']   = np.round(
        np.abs((y_test - pred_test) / y_test) * 100, 2
    )
    df_test_out.to_csv(f'{OUTPUT_DIR}/rf_aktual_prediksi_test.csv', index=False)

    # CSV 3: Prediksi 2026
    pred_df[['Tanggal','Tahun','Bulan','Prediksi_Volume']].to_csv(
        f'{OUTPUT_DIR}/rf_prediksi_2026.csv', index=False
    )

    # CSV 4: Feature Importance
    imp_df.to_csv(f'{OUTPUT_DIR}/rf_feature_importance.csv', index=False)

    # CSV 5: Ringkasan metrik train & test (lengkap, tanpa CV)
    pd.DataFrame([
        {'Set': 'Train (Hold-out)', 'R2': m_train['R2'], 'MAE': m_train['MAE'],
         'RMSE': m_train['RMSE'], 'MAPE_%': m_train['MAPE']},
        {'Set': 'Test (Hold-out)',  'R2': m_test['R2'],  'MAE': m_test['MAE'],
         'RMSE': m_test['RMSE'],  'MAPE_%': m_test['MAPE']},
    ]).to_csv(f'{OUTPUT_DIR}/rf_ringkasan_metrik.csv', index=False)

    # CSV 6: CV score per fold (terpisah karena berbeda jenis informasi)
    cv_rows = [{'Fold': f'Fold {i+1}', 'R2': round(s, 4)}
               for i, s in enumerate(cv_r2)]
    cv_rows.append({'Fold': 'Mean', 'R2': round(cv_r2.mean(), 4)})
    cv_rows.append({'Fold': 'Std',  'R2': round(cv_r2.std(),  4)})
    pd.DataFrame(cv_rows).to_csv(f'{OUTPUT_DIR}/rf_cv_score.csv', index=False)

    files = ['rf_aktual_prediksi_train.csv', 'rf_aktual_prediksi_test.csv',
             'rf_prediksi_2026.csv', 'rf_feature_importance.csv',
             'rf_ringkasan_metrik.csv', 'rf_cv_score.csv']
    for f in files:
        print(f"    CSV → {f}")


# ══════════════════════════════════════════════════════════════
# MAIN
# ══════════════════════════════════════════════════════════════

if __name__ == '__main__':
    SEP = "═" * 60

    print(SEP)
    print("  RANDOM FOREST REGRESSION — PREDIKSI SAMPAH SURAKARTA")
    print("  Kelompok 1 | Informatika UNS")
    print(SEP)

    # 1. Preprocessing
    print("\n[1] PREPROCESSING")
    df = preprocess(data_raw)
    print(f"    Dataset    : {len(df)} observasi efektif "
          f"(Jan {df['Tahun'].min()} – Des {df['Tahun'].max()})")
    print(f"    Fitur      : {len(FITUR_COLS)} kolom")
    print(f"    Split      : {SPLIT_IDX} train / {len(df)-SPLIT_IDX} test")

    # 2. Split & Scale
    print("\n[2] SPLIT & NORMALISASI")
    (X_train_s, X_test_s, y_train_s, y_test_s,
     y_train, y_test, X_all, y_all,
     scaler_X, scaler_y) = split_and_scale(df)
    print(f"    Scaler     : MinMaxScaler, fit pada train saja")

    # 3. Hyperparameter tuning
    print("\n[3] HYPERPARAMETER TUNING (GridSearchCV + TimeSeriesSplit)")
    best_params = tune(X_train_s, y_train_s)

    # 4. Training + CV
    print("\n[4] TRAINING MODEL FINAL")
    model, cv_r2 = train(X_train_s, y_train_s, best_params)

    # 5. Prediksi
    pred_train_s = model.predict(X_train_s)
    pred_test_s  = model.predict(X_test_s)
    pred_train   = scaler_y.inverse_transform(
        pred_train_s.reshape(-1, 1)).ravel()
    pred_test    = scaler_y.inverse_transform(
        pred_test_s.reshape(-1, 1)).ravel()

    # 6. Evaluasi
    print("\n[5] EVALUASI METRIK")
    print(f"  {'Metrik':<8} {'Train':>14} {'Test':>14}")
    print(f"  {'-'*38}")
    m_train = hitung_metrik(y_train, pred_train, 'Train')
    m_test  = hitung_metrik(y_test,  pred_test,  'Test ')
    print(f"\n  CV R² (5-fold TimeSeriesSplit, train set):")
    for i, s in enumerate(cv_r2):
        print(f"    Fold {i+1}: {s:.4f}")
    print(f"  Rata-rata CV R² : {cv_r2.mean():.4f} ± {cv_r2.std():.4f}")

    # 7. Feature Importance
    print("\n[6] FEATURE IMPORTANCE")
    imp_df = importance(model, X_test_s, y_test_s)
    print(f"\n  {'No':<4} {'Fitur':<22} {'MDI':>8} {'Perm':>8}")
    print(f"  {'-'*46}")
    for i, row in imp_df.iterrows():
        print(f"  {i+1:<4} {row['Fitur']:<22} "
              f"{row['MDI']:>8.4f} {row['Perm_Mean']:>8.4f}")

    # 8. Forecasting 2026
    print("\n[7] FORECASTING 2026")
    pred_df = forecast_2026(df, model, scaler_X, scaler_y)
    print(f"\n  {'Bulan':<12} {'Prediksi (Ton)':>16}")
    print(f"  {'-'*30}")
    bulan_nama = ['Januari','Februari','Maret','April','Mei','Juni',
                  'Juli','Agustus','September','Oktober','November','Desember']
    for _, row in pred_df.iterrows():
        print(f"  {bulan_nama[int(row['Bulan'])-1]:<12} "
              f"{row['Prediksi_Volume']:>16,.2f}")
    print(f"  {'─'*30}")
    print(f"  {'Rata-rata':<12} "
          f"{pred_df['Prediksi_Volume'].mean():>16,.2f}")
    print(f"  {'Total':<12} "
          f"{pred_df['Prediksi_Volume'].sum():>16,.2f}")

    # 9. Visualisasi
    print("\n[8] VISUALISASI")
    visualisasi(df, y_train, y_test, pred_train, pred_test,
                m_train, m_test, imp_df, pred_df, cv_r2)

    # 10. Simpan CSV
    print("\n[9] SIMPAN CSV")
    simpan_csv(df, y_train, y_test, pred_train, pred_test,
               m_train, m_test, imp_df, pred_df, cv_r2)

    print(f"\n{SEP}")
    print("  SELESAI — semua output tersimpan di outputs/")
    print(SEP)
