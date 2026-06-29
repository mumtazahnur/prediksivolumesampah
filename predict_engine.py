import sys
import json
import numpy as np
import pandas as pd
from sklearn.ensemble import RandomForestRegressor
from sklearn.preprocessing import MinMaxScaler

# --- KONSTANTA ---
# SPLIT_IDX = 84 menetapkan data latih (Train) sebanyak 84 baris pertama (2016-2022)
# dan sisanya sebagai data uji (Test) (2023-2025) secara kronologis
SPLIT_IDX = 84
# RANDOM_SEED digunakan agar hasil pembuatan Random Forest selalu konsisten/sama tiap dijalankan
RANDOM_SEED = 42

# --- DATASET HISTORIS (120 Bulan: Tahun 2016 - 2025) ---
# Data mentah statistik Kota Surakarta yang dikumpulkan per bulan
data_raw = {
    'Tahun': [*[2016]*12, *[2017]*12, *[2018]*12, *[2019]*12,
              *[2020]*12, *[2021]*12, *[2022]*12, *[2023]*12,
              *[2024]*12, *[2025]*12],
    'Bulan': list(range(1, 13)) * 10,
    'Volume_Sampah': [
        8393.73, 8699.22, 9178.07, 8680.27, 9336.46, 9164.74,
        9082.43, 9260.58, 8871.44, 9555.47, 9409.41, 9650.89,
        9619.05, 8913.03, 9646.49, 8762.76, 9216.57, 8514.12,
        8397.59, 7947.26, 7579.10, 8683.12, 9303.01, 9696.76,
        10257.0, 9617.0, 10177.0, 9529.0, 9201.0, 8620.0,
        8667.0, 8881.0, 8729.0, 9171.0, 9311.0, 9677.0,
        10541.02, 9587.45, 10451.47, 9431.60, 9328.70, 8097.98,
        8820.23, 8591.63, 8241.92, 8701.58, 9028.42, 10071.88,
        10027.0, 9548.0, 9921.0, 8307.0, 8410.0, 9359.0,
        9036.0, 9395.0, 8593.0, 8552.0, 8388.0, 8337.0,
        7945.59, 6886.63, 7759.57, 8498.64, 10292.97, 10334.08,
        8995.07, 9120.92, 8929.10, 9247.10, 10715.16, 10573.10,
        10232.03, 7263.32, 10843.28, 11001.22, 10973.61, 10479.79,
        10360.51, 10822.86, 10602.59, 11565.99, 12183.10, 19158.19,
        12781.08, 10994.63, 12493.64, 11528.46, 12131.13, 10647.38,
        10901.68, 10560.04, 10161.49, 11172.21, 11248.81, 11376.61,
        13393.0, 11768.0, 12333.0, 12542.0, 12191.0, 11331.0,
        11460.0, 11213.0, 11190.0, 11527.0, 12205.0, 12541.0,
        11949.0, 10522.0, 11358.0, 9544.0, 9799.0, 8871.0,
        9413.0, 9354.0, 9187.0, 10242.0, 10438.0, 10828.0,
    ],
    'Jumlah_Penduduk': [
        *[514171]*12, *[516102]*12, *[517887]*12, *[519587]*12,
        *[522364]*12, *[522728]*12, *[523008]*12, *[526870]*12,
        *[528044]*12, *[529079]*12,
    ],
    'Kepadatan_Penduduk': [
        *[11674.93]*12, *[11718.65]*12, *[11759.31]*12, *[11798.0]*12,
        *[11861.0]*12,    *[11188.0]*12,    *[11194.0]*12,    *[11277.0]*12,
        *[11302.0]*12,    *[11324.0]*12,
    ],
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
    'Jumlah_Rumah_Makan': [
        *[134]*12,  *[678]*12,  *[778]*12,  *[693]*12,
        *[727]*12,  *[716]*12,  *[1148]*12, *[724]*12,
        *[548]*12,  *[811]*12,
    ],
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
    'Jumlah_Wisatawan': [
        *[366296]*12,
        232249, 199996, 221351, 240749, 250500, 260191, 282942, 242696, 250940, 286039, 259838, 336840,
        230931, 207660, 216354, 234730, 224939, 336415, 259855, 259590, 273921, 276257, 275833, 466170,
        260372, 239220, 248822, 268625, 204706, 341769, 276164, 240943, 273852, 307569, 312322, 464323,
        140770, 147418,  29147,      0,      0,      0,   6654,   9908,   6692,   5590,   4254,   3673,
          5304,   8860,  18693,  21905,  48025,  60382,    199,      0,   8711,  40920,  68568,  97525,
         88793,  60252,  67516,  29980, 213750,1772521, 146284, 113823,  27575, 133753,  26849,  36351,
         35562,  76362, 382798, 538255, 340133, 470145, 427437, 322940, 345164, 307293, 286310, 491409,
        265527, 301855, 303715, 454986, 404796, 397908, 492485, 328090, 389435, 338670, 322098, 397519,
        456377, 363347, 295959, 526577, 416627, 424552, 474122, 305223, 354270, 415169, 308204, 361386,
    ],
    'Curah_Hujan': [
         72.0,  164.0,  143.0,   52.0,  104.0,  123.0, 113.0,   21.0,   79.0,   77.0,  148.0,   91.0,
         13.6,   19.9,    7.8,    9.8,    1.0,    4.4,   0.0,    4.7,    2.6,    3.2,   12.8,    5.3,
         13.6,   19.9,    7.8,    9.8,    1.0,    4.4,   0.0,    4.7,    2.6,    3.2,   12.8,    5.3,
        573.9,  334.8,  360.5,  172.6,   36.0, np.nan, np.nan, np.nan, np.nan, np.nan,   90.2,  247.7,
        275.3,  199.4,  175.7,  131.12, 182.55,   0.0,   5.3,   34.3,    5.6,  256.3,  249.2,  187.7,
        581.8,  276.0,  265.0,  164.1,   65.1,  240.2,   0.0,  542.0,   61.1,   74.0,  303.4,  232.0,
         15.0,   15.0,   14.0,   15.0,   13.0,   14.0, np.nan, np.nan, np.nan, np.nan, np.nan, np.nan,
        279.2,  465.0,  493.1,  166.2,  150.0, np.nan, np.nan, np.nan, np.nan,   42.0,   66.0,   79.9,
        470.2,  397.2,  259.3,  397.2,   86.7,   36.0, np.nan, np.nan,  130.0,   24.0,  293.0,  355.0,
        np.nan, np.nan, np.nan, np.nan, np.nan, np.nan, np.nan, np.nan, np.nan, np.nan, np.nan, np.nan,
    ],
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

# Kolom-kolom fitur penentu prediksi volume sampah
FITUR_COLS = [
    'Jumlah_Penduduk',    
    'Kepadatan_Penduduk',  
    'Jumlah_Rumah_Tangga', 
    'Jumlah_Rumah_Makan',  
    'Tingkat_Hunian_Hotel',
    'Jumlah_Wisatawan',   
    'Curah_Hujan',      
    'Jumlah_Hari_Besar',  
    'Bulan_Sin',         
    'Bulan_Cos',          
    'lag_12',             
]

# Label nama fitur dalam Bahasa Indonesia untuk presentasi data
FITUR_LABEL = [
    'Jml. Penduduk', 'Kepadatan Pddk', 'Jml. Rmh Tangga',
    'Jml. Rmh Makan', 'Hunian Hotel', 'Jml. Wisatawan',
    'Curah Hujan', 'Hari Besar', 'Siklus Bulan (Sin)',
    'Siklus Bulan (Cos)', 'Volume Sampah Lag-12'
]

# --- 1. PREPROCESSING DATA ---
# Mengubah data_raw ke DataFrame Pandas
df = pd.DataFrame(data_raw)
# Menggabungkan Tahun & Bulan menjadi kolom tanggal index agar berurutan kronologis
df['Tanggal'] = pd.to_datetime({'year': df['Tahun'], 'month': df['Bulan'], 'day': 1})
df = df.sort_values('Tanggal').reset_index(drop=True)

# Mengisi missing value (NaN) pada Curah Hujan menggunakan imputasi rata-rata (Mean Imputation)
df['Curah_Hujan'] = df['Curah_Hujan'].fillna(df['Curah_Hujan'].mean())

# Fitur Rekayasa Siklus Bulan menggunakan Sinus & Cosinus (agar pola musiman 1-12 bulan berulang)
df['Bulan_Sin'] = np.sin(2 * np.pi * df['Bulan'] / 12)
df['Bulan_Cos'] = np.cos(2 * np.pi * df['Bulan'] / 12)

# Fitur Lag-12: Mengambil volume sampah dari 12 bulan yang lalu (Musiman tahun sebelumnya)
df['lag_12'] = df['Volume_Sampah'].shift(12)
# Menghapus baris kosong akibat efek pembuatan fitur lag 12 bulan pertama (Tahun 2016 dihapus)
df_effective = df.dropna(subset=['lag_12']).reset_index(drop=True)

# --- 2. PEMBAGIAN KRONOLOGIS & NORMALISASI DATA ---
X = df_effective[FITUR_COLS].values
y = df_effective['Volume_Sampah'].values

# Split data: Train (0-84) untuk melatih model, Test (84-akhir) untuk pengujian
X_train = X[:SPLIT_IDX]
y_train = y[:SPLIT_IDX]
X_test = X[SPLIT_IDX:]
y_test = y[SPLIT_IDX:]

# Normalisasi menggunakan MinMaxScaler (mengubah nilai ke rentang 0 sampai 1)
# Skala dihitung (fit) HANYA pada data latih (X_train) untuk mencegah kebocoran data (data leakage)
scaler_X = MinMaxScaler()
scaler_y = MinMaxScaler()

X_train_s = scaler_X.fit_transform(X_train)
y_train_s = scaler_y.fit_transform(y_train.reshape(-1, 1)).ravel()
X_test_s = scaler_X.transform(X_test)
y_test_s = scaler_y.transform(y_test.reshape(-1, 1)).ravel()

# --- 3. TRAINING MODEL RANDOM FOREST REGRESSION ---
# Hyperparameter terbaik hasil pencarian Grid Search
best_params = {
    'max_depth': 10,
    'max_features': None, # Menggunakan semua fitur
    'min_samples_leaf': 1,
    'min_samples_split': 2,
    'n_estimators': 200
}
model = RandomForestRegressor(**best_params, random_state=RANDOM_SEED, n_jobs=-1)
model.fit(X_train_s, y_train_s)

# Menghitung prediksi pada data latih & data uji
pred_train = scaler_y.inverse_transform(model.predict(X_train_s).reshape(-1, 1)).ravel()
pred_test = scaler_y.inverse_transform(model.predict(X_test_s).reshape(-1, 1)).ravel()

# Menghitung Metrik Evaluasi Model (MAE, RMSE, R²)
train_mae = float(np.mean(np.abs(y_train - pred_train)))
train_rmse = float(np.sqrt(np.mean((y_train - pred_train) ** 2)))
train_r2 = float(model.score(X_train_s, y_train_s))

test_mae = float(np.mean(np.abs(y_test - pred_test)))
test_rmse = float(np.sqrt(np.mean((y_test - pred_test) ** 2)))
test_r2_score = float(1 - (np.sum((y_test - pred_test)**2) / np.sum((y_test - np.mean(y_test))**2)))

# --- 4. EKSTRAPOLASI DRIVER TAHUNAN ---
# Melakukan regresi linear (polyfit) untuk meramal nilai populasi, kepadatan, wisatawan dll di masa depan
def extrap_tahunan(df_data, kolom, target_year):
    vals = df_data.groupby('Tahun')[kolom].first().values
    yr = np.arange(df_data['Tahun'].min(), df_data['Tahun'].max() + 1)
    slope = np.polyfit(yr, vals, 1)
    return float(np.polyval(slope, target_year))

# --- 5. BINDING CLI & SIMULASI REKURSIF AUTO-REGRESSIVE ---
# Mengambil tahun target prediksi dari parameter yang dilempar PHP CLI
try:
    target_year = int(sys.argv[1])
except Exception:
    target_year = 2026

if target_year < 2026:
    target_year = 2026

# Copy dataset historis untuk tempat penyimpanan baris hasil prediksi masa depan
current_df = df.copy()

# Rata-rata bulanan untuk variabel musiman (Hotel, Curah Hujan, Hari Libur)
monthly_hotel = df.groupby('Bulan')['Tingkat_Hunian_Hotel'].mean()
monthly_hujan = df.groupby('Bulan')['Curah_Hujan'].mean()
monthly_hari = df.groupby('Bulan')['Jumlah_Hari_Besar'].mean()

# Proses Peramalan Bertahap (Recursive Forecasting) per tahun s/d tahun target
for yr in range(2026, target_year + 1):
    # Ekstrapolasi tren linear untuk driver utama
    pop_yr = extrap_tahunan(current_df, 'Jumlah_Penduduk', yr)
    kpd_yr = extrap_tahunan(current_df, 'Kepadatan_Penduduk', yr)
    rm_yr = extrap_tahunan(current_df, 'Jumlah_Rumah_Makan', yr)
    wis_yr = extrap_tahunan(current_df, 'Jumlah_Wisatawan', yr)
    
    # Ekstrapolasi tren Jumlah Rumah Tangga (dibagi Semester 1 & Semester 2)
    sem_years = np.arange(current_df['Tahun'].min(), current_df['Tahun'].max() + 1)
    s1_vals = current_df[current_df['Bulan'] == 1].groupby('Tahun')['Jumlah_Rumah_Tangga'].first().values
    s2_vals = current_df[current_df['Bulan'] == 7].groupby('Tahun')['Jumlah_Rumah_Tangga'].first().values
    rt_s1_yr = float(np.polyval(np.polyfit(sem_years, s1_vals, 1), yr))
    rt_s2_yr = float(np.polyval(np.polyfit(sem_years, s2_vals, 1), yr))
    
    # Lag-12 bulanan diambil dari volume sampah pada (Tahun Berjalan - 1)
    vol_prev = current_df[current_df['Tahun'] == (yr - 1)].set_index('Bulan')['Volume_Sampah']
    
    records = []
    # Membangun 12 baris data baru untuk tahun target
    for bulan in range(1, 13):
        rt_yr = rt_s1_yr if bulan <= 6 else rt_s2_yr
        
        row = {
            'Tahun': yr,
            'Bulan': bulan,
            'Jumlah_Penduduk': pop_yr,
            'Kepadatan_Penduduk': kpd_yr,
            'Jumlah_Rumah_Tangga': rt_yr,
            'Jumlah_Rumah_Makan': rm_yr,
            'Tingkat_Hunian_Hotel': monthly_hotel[bulan],
            'Jumlah_Wisatawan': wis_yr,
            'Curah_Hujan': monthly_hujan[bulan],
            'Jumlah_Hari_Besar': monthly_hari[bulan],
            'Bulan_Sin': np.sin(2 * np.pi * bulan / 12),
            'Bulan_Cos': np.cos(2 * np.pi * bulan / 12),
            'lag_12': vol_prev[bulan],
            'Volume_Sampah': 0.0 # Placeholder sementara
        }
        records.append(row)
        
    pred_df = pd.DataFrame(records)
    X_yr = pred_df[FITUR_COLS].values
    
    # Scaling input baru & prediksi menggunakan model RandomForest
    X_yrs = scaler_X.transform(X_yr)
    y_yrs = model.predict(X_yrs)
    # Inverse scaling hasil prediksi agar kembali ke satuan Ton asli
    y_yr = scaler_y.inverse_transform(y_yrs.reshape(-1, 1)).ravel()
    
    pred_df['Volume_Sampah'] = np.round(y_yr, 2)
    pred_df['Tanggal'] = pd.to_datetime(pred_df['Tahun'].astype(str) + '-' + pred_df['Bulan'].astype(str) + '-01')
    
    # Menggabungkan data prediksi tahun berjalan ke dataset historis.
    # Data ini penting agar tahun berikutnya bisa mengambil nilai prediksi ini sebagai fitur lag_12!
    current_df = pd.concat([current_df, pred_df], ignore_index=True)

# --- 6. PARSING OUTPUT KE JSON ---
# Filter baris tahun target
target_predictions = current_df[current_df['Tahun'] == target_year]

bulan_nama_ind = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']

predictions_list = []
for _, row in target_predictions.iterrows():
    predictions_list.append({
        'bulan': int(row['Bulan']),
        'bulan_nama': bulan_nama_ind[int(row['Bulan'])-1],
        'volume': float(row['Volume_Sampah']),
        'penduduk': int(row['Jumlah_Penduduk']),
        'kepadatan': float(row['Kepadatan_Penduduk']),
        'wisatawan': int(row['Jumlah_Wisatawan']),
        'curah_hujan': float(row['Curah_Hujan'])
    })

total_annual = float(target_predictions['Volume_Sampah'].sum())
average_monthly = float(target_predictions['Volume_Sampah'].mean())

# Menghitung perbandingan pertumbuhan volume sampah terhadap baseline aktual tahun 2025
total_2025_actual = float(df[df['Tahun'] == 2025]['Volume_Sampah'].sum())
growth_rate = ((total_annual - total_2025_actual) / total_2025_actual) * 100

# Menghitung bobot pentingnya fitur (MDI Feature Importance)
mdi = model.feature_importances_
feature_importance = []
for name, label, imp in zip(FITUR_COLS, FITUR_LABEL, mdi):
    feature_importance.append({
        'name': name,
        'label': label,
        'importance': float(imp)
    })
feature_importance = sorted(feature_importance, key=lambda x: x['importance'], reverse=True)

# Merakit object JSON respon akhir untuk PHP/Web
output_data = {
    'status': 'success',
    'target_year': target_year,
    'total_annual': round(total_annual, 2),
    'average_monthly': round(average_monthly, 2),
    'growth_rate_vs_2025': round(growth_rate, 2),
    'total_2025_actual': round(total_2025_actual, 2),
    'predictions': predictions_list,
    'feature_importance': feature_importance,
    'metrics': {
        'train': {
            'mae': round(train_mae, 4),
            'rmse': round(train_rmse, 4),
            'r2': round(train_r2, 4)
        },
        'test': {
            'mae': round(test_mae, 4),
            'rmse': round(test_rmse, 4),
            'r2': round(test_r2_score, 4)
        }
    }
}

# Print JSON string ke stdout agar ditangkap oleh PHP shell_exec
print(json.dumps(output_data))
