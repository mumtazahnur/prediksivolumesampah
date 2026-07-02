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
OUTPUT_DIR = 'C:\\vscode\\D_AI_Kelompok1_VolumeSampah' #ganti sesuai dengan path masing-masing
path  = 'C:\\vscode\\D_AI_Kelompok1_VolumeSampah\\dataset_sampah_-_DATASET_FIX.csv'  #ganti sesuai lokasi file CSV masing-masing

SPLIT_IDX   = 84         
RANDOM_SEED = 42
N_SPLITS_CV = 5           


# Mapping nama bulan (Indonesia, seperti di file CSV) -> angka 1-12
BULAN_KE_ANGKA = {
    'januari': 1, 'februari': 2, 'maret': 3, 'april': 4,
    'mei': 5, 'juni': 6, 'juli': 7, 'agustus': 8,
    'september': 9, 'oktober': 10, 'november': 11, 'desember': 12,
}

# Kolom CSV asli -> nama kolom yang dipakai di seluruh script
RENAME_KOLOM = {
    'Tahun':                                  'Tahun',
    'Bulan':                                  'Bulan',
    'Volume Sampah\n(Ton)':                   'Volume_Sampah',
    'Jumlah Penduduk\n(Jiwa) (Tahun)':        'Jumlah_Penduduk',
    'Kepadatan Penduduk \n(Jiwa/km2) (Tahun)':'Kepadatan_Penduduk',
    'Jumlah Rumah Tangga\n(Jumlah KK)':       'Jumlah_Rumah_Tangga',
    'Jumlah Rumah Makan \n(Buah)':            'Jumlah_Rumah_Makan',
    'Tingkat Hunian Hotel\n(%)':              'Tingkat_Hunian_Hotel',
    'Jumlah Wisatawan\n (Daya Tarik) (Jiwa)': 'Jumlah_Wisatawan',
    'Curah Hujan \n(mm)':                     'Curah_Hujan',
    'Jumlah Hari \nBesar (Hari)':             'Jumlah_Hari_Besar',
}


def muat_data(path):
    """Baca dataset langsung dari CSV (bukan hardcode manual) supaya
    tidak ada risiko salah ketik/transkripsi seperti pada versi sebelumnya."""
    df = pd.read_csv(path)
    df.columns = [c.strip() for c in df.columns]

    # Samakan key RENAME_KOLOM (yang sudah di-strip juga) dengan kolom aktual
    rename_map = {k.strip(): v for k, v in RENAME_KOLOM.items()}
    df = df.rename(columns=rename_map)

    # Tahun: hanya terisi di baris Januari (merged cell dari Excel) -> forward-fill
    df['Tahun'] = df['Tahun'].ffill().astype(int)

    # Bulan: nama bulan Indonesia -> angka 1-12
    df['Bulan'] = (
        df['Bulan'].astype(str).str.strip().str.lower().map(BULAN_KE_ANGKA)
    )

    # Curah_Hujan: '-' berarti data tidak tercatat -> jadi NaN, lalu numeric
    df['Curah_Hujan'] = df['Curah_Hujan'].replace('-', np.nan)
    df['Curah_Hujan'] = pd.to_numeric(df['Curah_Hujan'], errors='coerce')

    return df


def preprocess(df):
    df = df.copy()
    df['Tanggal'] = pd.to_datetime(
        {'year': df['Tahun'], 'month': df['Bulan'], 'day': 1}
    )
    df = df.sort_values('Tanggal').reset_index(drop=True)

    n_missing = df['Curah_Hujan'].isna().sum()
    df['Curah_Hujan'] = df['Curah_Hujan'].fillna(df['Curah_Hujan'].mean())
    print(f"    Missing value Curah_Hujan : {n_missing} nilai → mean imputation "
          f"({df['Curah_Hujan'].mean():.2f} mm)")

    df['Bulan_Sin'] = np.sin(2 * np.pi * df['Bulan'] / 12)
    df['Bulan_Cos'] = np.cos(2 * np.pi * df['Bulan'] / 12)
    df['lag_12'] = df['Volume_Sampah'].shift(12)

    df = df.dropna(subset=['lag_12']).reset_index(drop=True)
    print(f"    Dataset efektif: {len(df)} observasi "
          f"(Jan {df['Tahun'].min()} – Des {df['Tahun'].max()}) "
          f"setelah drop 2016 akibat lag_12")

    return df


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

    X_train, X_test = X[:SPLIT_IDX], X[SPLIT_IDX:]
    y_train, y_test = y[:SPLIT_IDX], y[SPLIT_IDX:]

    scaler_X = MinMaxScaler()
    scaler_y = MinMaxScaler()

    X_train_s = scaler_X.fit_transform(X_train)
    X_test_s  = scaler_X.transform(X_test)

    y_train_s = scaler_y.fit_transform(y_train.reshape(-1, 1)).ravel()
    y_test_s  = scaler_y.transform(y_test.reshape(-1, 1)).ravel()

    return (X_train_s, X_test_s, y_train_s, y_test_s,
            y_train, y_test, X, y, scaler_X, scaler_y)


def tune(X_train_s, y_train_s):
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


def train(X_train_s, y_train_s, best_params):
    model = RandomForestRegressor(
        **best_params, random_state=RANDOM_SEED, n_jobs=-1
    )
    model.fit(X_train_s, y_train_s)

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

def hitung_metrik(y_true, y_pred, label):
    mae  = mean_absolute_error(y_true, y_pred)
    rmse = np.sqrt(mean_squared_error(y_true, y_pred))
    mape = np.mean(np.abs((y_true - y_pred) / y_true)) * 100
    r2   = r2_score(y_true, y_pred)
    print(f"  [{label}]  MAE={mae:,.2f}  RMSE={rmse:,.2f}  "
          f"MAPE={mape:.2f}%  R²={r2:.4f}")
    return {'label': label, 'MAE': mae, 'RMSE': rmse,
            'MAPE': mape, 'R2': r2}


def importance(model, X_test_s, y_test_s):
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


def forecast_2026(df, model, scaler_X, scaler_y):
    years = np.arange(2016, 2026)

    def extrap_tahunan(kolom):
        vals = df.groupby('Tahun')[kolom].first().values
        yr   = np.arange(df['Tahun'].min(), df['Tahun'].max() + 1)
        slope = np.polyfit(yr, vals, 1)
        return np.polyval(slope, 2026)

    pop_2026 = extrap_tahunan('Jumlah_Penduduk')
    kpd_2026 = extrap_tahunan('Kepadatan_Penduduk')
    rm_2026  = extrap_tahunan('Jumlah_Rumah_Makan')
    wis_2026 = extrap_tahunan('Jumlah_Wisatawan')

    sem_years = np.arange(df['Tahun'].min(), df['Tahun'].max() + 1)
    s1_vals = df[df['Bulan'] == 1].groupby('Tahun')['Jumlah_Rumah_Tangga'].first().values
    s2_vals = df[df['Bulan'] == 7].groupby('Tahun')['Jumlah_Rumah_Tangga'].first().values
    rt_s1_2026 = np.polyval(np.polyfit(sem_years, s1_vals, 1), 2026)
    rt_s2_2026 = np.polyval(np.polyfit(sem_years, s2_vals, 1), 2026)

    monthly_hotel = df.groupby('Bulan')['Tingkat_Hunian_Hotel'].mean()
    monthly_hujan = df.groupby('Bulan')['Curah_Hujan'].mean()
    monthly_hari  = df.groupby('Bulan')['Jumlah_Hari_Besar'].mean()

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
            'lag_12':    vol_2025[bulan], 
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

def visualisasi(df, y_train, y_test, pred_train, pred_test,
                m_train, m_test, imp_df, pred_df, cv_r2):
    
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

    fig2, axes2 = plt.subplots(1, 2, figsize=(16, 6))
    fig2.suptitle('Analisis Tambahan — Volume Sampah Kota Surakarta',
                  fontsize=14, fontweight='bold')

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


def simpan_csv(df, y_train, y_test, pred_train, pred_test,
               m_train, m_test, imp_df, pred_df, cv_r2):

    df_train_out = df.iloc[:SPLIT_IDX][['Tanggal','Tahun','Bulan', TARGET_COL]].copy()
    df_train_out['Prediksi'] = np.round(pred_train, 2)
    df_train_out['Error_Ton'] = np.round(y_train - pred_train, 2)
    df_train_out['Error_%']   = np.round(
        np.abs((y_train - pred_train) / y_train) * 100, 2
    )
    df_train_out.to_csv(f'{OUTPUT_DIR}/rf_aktual_prediksi_train.csv', index=False)

    df_test_out = df.iloc[SPLIT_IDX:][['Tanggal','Tahun','Bulan', TARGET_COL]].copy()
    df_test_out['Prediksi'] = np.round(pred_test, 2)
    df_test_out['Error_Ton'] = np.round(y_test - pred_test, 2)
    df_test_out['Error_%']   = np.round(
        np.abs((y_test - pred_test) / y_test) * 100, 2
    )
    df_test_out.to_csv(f'{OUTPUT_DIR}/rf_aktual_prediksi_test.csv', index=False)

    pred_df[['Tanggal','Tahun','Bulan','Prediksi_Volume']].to_csv(
        f'{OUTPUT_DIR}/rf_prediksi_2026.csv', index=False
    )

    imp_df.to_csv(f'{OUTPUT_DIR}/rf_feature_importance.csv', index=False)

    pd.DataFrame([
        {'Set': 'Train (Hold-out)', 'R2': m_train['R2'], 'MAE': m_train['MAE'],
         'RMSE': m_train['RMSE'], 'MAPE_%': m_train['MAPE']},
        {'Set': 'Test (Hold-out)',  'R2': m_test['R2'],  'MAE': m_test['MAE'],
         'RMSE': m_test['RMSE'],  'MAPE_%': m_test['MAPE']},
    ]).to_csv(f'{OUTPUT_DIR}/rf_ringkasan_metrik.csv', index=False)

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

if __name__ == '__main__':
    SEP = "═" * 60

    print(SEP)
    print("  RANDOM FOREST REGRESSION — PREDIKSI SAMPAH SURAKARTA")
    print("  Kelompok 1 | Informatika UNS")
    print(SEP)

    print("\n[1] MUAT DATA")
    df_raw = muat_data(path)
    print(f"    Sumber     : {path}")
    print(f"    Baris baca : {len(df_raw)}")

    print("\n[2] PREPROCESSING")
    df = preprocess(df_raw)
    print(f"    Dataset    : {len(df)} observasi efektif "
          f"(Jan {df['Tahun'].min()} – Des {df['Tahun'].max()})")
    print(f"    Fitur      : {len(FITUR_COLS)} kolom")
    print(f"    Split      : {SPLIT_IDX} train / {len(df)-SPLIT_IDX} test")

    print("\n[3] SPLIT & NORMALISASI")
    (X_train_s, X_test_s, y_train_s, y_test_s,
     y_train, y_test, X_all, y_all,
     scaler_X, scaler_y) = split_and_scale(df)
    print(f"    Scaler     : MinMaxScaler, fit pada train saja")

    print("\n[4] HYPERPARAMETER TUNING (GridSearchCV + TimeSeriesSplit)")
    best_params = tune(X_train_s, y_train_s)

    print("\n[5] TRAINING MODEL FINAL")
    model, cv_r2 = train(X_train_s, y_train_s, best_params)

    pred_train_s = model.predict(X_train_s)
    pred_test_s  = model.predict(X_test_s)
    pred_train   = scaler_y.inverse_transform(
        pred_train_s.reshape(-1, 1)).ravel()
    pred_test    = scaler_y.inverse_transform(
        pred_test_s.reshape(-1, 1)).ravel()

    print("\n[6] EVALUASI METRIK")
    print(f"  {'Metrik':<8} {'Train':>14} {'Test':>14}")
    print(f"  {'-'*38}")
    m_train = hitung_metrik(y_train, pred_train, 'Train')
    m_test  = hitung_metrik(y_test,  pred_test,  'Test ')
    print(f"\n  CV R² (5-fold TimeSeriesSplit, train set):")
    for i, s in enumerate(cv_r2):
        print(f"    Fold {i+1}: {s:.4f}")
    print(f"  Rata-rata CV R² : {cv_r2.mean():.4f} ± {cv_r2.std():.4f}")

    print("\n[7] FEATURE IMPORTANCE")
    imp_df = importance(model, X_test_s, y_test_s)
    print(f"\n  {'No':<4} {'Fitur':<22} {'MDI':>8} {'Perm':>8}")
    print(f"  {'-'*46}")
    for i, row in imp_df.iterrows():
        print(f"  {i+1:<4} {row['Fitur']:<22} "
              f"{row['MDI']:>8.4f} {row['Perm_Mean']:>8.4f}")

    print("\n[8] FORECASTING 2026")
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

    print("\n[9] VISUALISASI")
    visualisasi(df, y_train, y_test, pred_train, pred_test,
                m_train, m_test, imp_df, pred_df, cv_r2)

    print("\n[10] SIMPAN CSV")
    simpan_csv(df, y_train, y_test, pred_train, pred_test,
               m_train, m_test, imp_df, pred_df, cv_r2)

    print(f"\n{SEP}")
    print("  SELESAI — semua output tersimpan di outputs/")
    print(SEP)
