import streamlit as st
import pandas as pd
import numpy as np
import plotly.express as px
import plotly.graph_objects as go

from prediksi_sampah_surakarta import (
    data_raw, preprocess, split_and_scale, tune, train,
    hitung_metrik, importance, forecast_2026, SPLIT_IDX
)

# Config optimasi memory
st.set_page_config(
    page_title="Prediksi Volume Sampah di Kota Surakarta Menggunakan Random Forest Regression Berbasis Faktor Kependudukan",
    page_icon="",
    layout="wide",
)

# Kurangi cache memory
st.session_state.setdefault("cache_cleared", False)

bulan_nama = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agt","Sep","Okt","Nov","Des"]

BULAN_MAP = {
    "januari": 1, "februari": 2, "maret": 3, "april": 4,
    "mei": 5, "juni": 6, "juli": 7, "agustus": 8,
    "september": 9, "oktober": 10, "november": 11, "desember": 12,
}

@st.cache_data(ttl=3600)
def load_data():
    df = pd.read_csv("dataset_sampah - DATASET FIX.csv")
    df.columns = [c.replace("\n", " ").strip() for c in df.columns]
    df['Tahun'] = df['Tahun'].ffill()

    # Kolom "Bulan" di CSV berisi nama bulan dalam teks (mis. "Januari"),
    # bukan angka. Konversi ke angka 1-12 supaya heatmap & groupby musiman benar.
    df['Bulan'] = df['Bulan'].str.strip().str.lower().map(BULAN_MAP)

    return df

@st.cache_resource(ttl=3600)
def load_model():
    df = preprocess(data_raw)
    out = split_and_scale(df)
    X_train_s, X_test_s, y_train_s, y_test_s, y_train, y_test, _, _, scaler_X, scaler_y = out
    best_params = tune(X_train_s, y_train_s)
    model, cv_r2 = train(X_train_s, y_train_s, best_params)
    pred_train = scaler_y.inverse_transform(model.predict(X_train_s).reshape(-1,1)).ravel()
    pred_test  = scaler_y.inverse_transform(model.predict(X_test_s).reshape(-1,1)).ravel()
    m_train = hitung_metrik(y_train, pred_train, "Train")
    m_test  = hitung_metrik(y_test, pred_test, "Test")
    imp_df  = importance(model, X_test_s, scaler_y.transform(y_test.reshape(-1,1)).ravel())
    pred_df = forecast_2026(df, model, scaler_X, scaler_y)
    return df, model, cv_r2, y_train, y_test, pred_train, pred_test, m_train, m_test, imp_df, pred_df, scaler_X, scaler_y

st.title("Prediksi Sampah Surakarta")
st.markdown(
    "Aplikasi ini memprediksi volume sampah bulanan yang masuk ke TPA Putri Cempo "
    "menggunakan metode **Random Forest Regression**. Scroll ke bawah untuk melihat dataset, analisis, dan input prediksi."
)

st.divider()

# ── Dataset ──
with st.expander("DATASET", expanded=True):
    df = load_data()
    st.subheader("Dataset volume sampah")
    st.caption("Data volume sampah dan variabel pendukung Kota Surakarta, 2016–2025.")

    tahun_list = sorted(df["Tahun"].dropna().unique().astype(int).tolist())
    col1, col2 = st.columns([2, 1])
    with col1:
        tahun_range = st.slider(
            "Filter tahun", 
            min_value=tahun_list[0], 
            max_value=tahun_list[-1],
            value=(tahun_list[0], tahun_list[-1]),
            step=1,
            key="tahun_filter_slider"
        )
    tahun_filter = [t for t in tahun_list if tahun_range[0] <= t <= tahun_range[1]]
    df_filtered = df[df["Tahun"].isin(tahun_filter)]

    st.subheader("Statistik")
    col1, col2, col3, col4 = st.columns(4)
    vol_col = [c for c in df.columns if "Volume" in c][0]
    col1.metric("Rata-rata", f"{df_filtered[vol_col].mean():,.0f} ton")
    col2.metric("Maksimum", f"{df_filtered[vol_col].max():,.0f} ton")
    col3.metric("Minimum", f"{df_filtered[vol_col].min():,.0f} ton")
    col4.metric("Std. deviasi", f"{df_filtered[vol_col].std():,.0f} ton")

    st.divider()
    st.subheader("Tabel data")
    st.dataframe(df_filtered.head(100), width='stretch', height=300)

    st.divider()
    st.subheader("Heatmap volume sampah per tahun & bulan")
    
    try:
        
        # Gunakan df langsung, pastikan Bulan & Tahun numeric
        df_heat = df.copy()
        df_heat["Bulan"] = pd.to_numeric(df_heat["Bulan"], errors='coerce')
        df_heat["Tahun"] = pd.to_numeric(df_heat["Tahun"], errors='coerce')
        df_heat = df_heat.dropna(subset=["Bulan", "Tahun", vol_col])
        
        if len(df_heat) > 0:
            pivot = df_heat.pivot_table(
                values=vol_col, 
                index="Tahun", 
                columns="Bulan", 
                aggfunc="mean"
            )
            
            if not pivot.empty:
                # Reindex kolom 1-12
                pivot = pivot[[col for col in range(1, 13) if col in pivot.columns]]
                
                fig_heat = px.imshow(
                    pivot,
                    color_continuous_scale="YlOrRd",
                    labels={"color": "Ton"},
                    text_auto=".0f",
                    aspect="auto",
                )
                fig_heat.update_yaxes(dtick=1)
                fig_heat.update_xaxes(ticktext=bulan_nama, tickvals=list(range(1, 13)))
                fig_heat.update_layout(height=400)
                st.plotly_chart(fig_heat, use_container_width=True)
            else:
                st.error("Pivot table kosong - cek data Bulan dan Tahun")
        else:
            st.error(f"Data tidak valid: {len(df_heat)} rows setelah cleaning")
    except Exception as e:
        st.error(f"Error heatmap: {str(e)}")

    st.divider()
    st.subheader("Pola musiman volume sampah")
    st.caption("Rata-rata ± 1 std per bulan, 2016–2025.")

    monthly_mean = df.groupby("Bulan")[vol_col].mean()
    monthly_std  = df.groupby("Bulan")[vol_col].std()

    fig_seasonal = go.Figure([
        go.Scatter(
            x=bulan_nama, y=monthly_mean + monthly_std,
            fill=None, mode="lines", line_color="rgba(0,0,0,0)", showlegend=False
        ),
        go.Scatter(
            x=bulan_nama, y=monthly_mean - monthly_std,
            fill="tonexty", mode="lines", line_color="rgba(0,0,0,0)",
            fillcolor="rgba(55,138,221,0.15)", name="±1 Std Dev"
        ),
        go.Scatter(
            x=bulan_nama, y=monthly_mean,
            mode="lines+markers", name="Rata-rata",
            line=dict(color="#185FA5", width=2),
            marker=dict(size=7)
        ),
    ])
    fig_seasonal.update_layout(
        yaxis_title="Volume sampah (ton)",
        legend=dict(orientation="h", y=1.1),
        margin=dict(l=0, r=0, t=10, b=0),
    )
    st.plotly_chart(fig_seasonal, use_container_width=True)

st.divider()

# ── Model & Prediksi Utama ──
with st.expander("Hasil Analisis dan Prediksi", expanded=True):
    df_model, model, cv_r2, y_train, y_test, pred_train, pred_test, m_train, m_test, imp_df, pred_df, scaler_X, scaler_y = load_model()

    st.subheader("Performa model")
    c1, c2, c3, c4, c5 = st.columns(5)
    c1.metric("R² (test)", f"{m_test['R2']:.4f}")
    c2.metric("MAPE (test)", f"{m_test['MAPE']:.2f}%")
    c3.metric("MAE (test)", f"{m_test['MAE']:,.0f} ton")
    c4.metric("RMSE (test)", f"{m_test['RMSE']:,.0f} ton")
    c5.metric("CV R² rata-rata", f"{cv_r2.mean():.4f}")

    st.divider()
    st.subheader("Volume sampah aktual vs prediksi (2017–2025)")
    st.caption("Data train: Jan 2017–Des 2023. Garis merah = prediksi test 2024–2025.")

    train_dates = df_model["Tanggal"].iloc[:SPLIT_IDX]
    test_dates  = df_model["Tanggal"].iloc[SPLIT_IDX:]
    fig_main = go.Figure()
    fig_main.add_trace(go.Scatter(x=df_model["Tanggal"], y=df_model["Volume_Sampah"],
        name="Aktual", line=dict(color="#185FA5", width=1.8)))
    fig_main.add_trace(go.Scatter(x=train_dates, y=pred_train,
        name="Prediksi (train)", line=dict(color="#3B6D11", width=1.2, dash="dash")))
    fig_main.add_trace(go.Scatter(x=test_dates, y=pred_test,
        name="Prediksi (test)", line=dict(color="#A32D2D", width=2.2)))
    fig_main.add_vline(x="2024-01-01", line_dash="dot", line_color="gray",
        annotation_text="Batas train/test", annotation_position="top right")
    fig_main.add_vline(x="2022-01-01", line_dash="dash", line_color="#BA7517",
        annotation_text="Lonjakan 2022+", annotation_position="top left")
    fig_main.update_layout(
        yaxis_title="Volume sampah (ton)",
        legend=dict(orientation="h", y=1.1),
        margin=dict(l=0, r=0, t=10, b=0),
        xaxis=dict(dtick="M12", tickformat="%Y"),
    )
    st.plotly_chart(fig_main, use_container_width=True)

    st.divider()
    col1, col2 = st.columns(2)
    with col1:
        st.subheader("Detail test set (2024–2025)")
        fig_test = go.Figure()
        fig_test.add_trace(go.Scatter(x=test_dates, y=y_test,
            mode="lines+markers", name="Aktual", line=dict(color="#185FA5")))
        fig_test.add_trace(go.Scatter(x=test_dates, y=pred_test,
            mode="lines+markers", name="Prediksi", line=dict(color="#A32D2D", dash="dash")))
        fig_test.update_layout(yaxis_title="Ton", margin=dict(l=0, r=0, t=10, b=0))
        st.plotly_chart(fig_test, use_container_width=True)

    with col2:
        st.subheader("Scatter aktual vs prediksi")
        lim = [min(y_test.min(), pred_test.min())-200, max(y_test.max(), pred_test.max())+200]
        fig_scatter = go.Figure()
        fig_scatter.add_trace(go.Scatter(x=y_test, y=pred_test,
            mode="markers", name="Test set",
            marker=dict(color="#7F77DD", size=9, opacity=0.8)))
        fig_scatter.add_trace(go.Scatter(x=lim, y=lim,
            mode="lines", name="Ideal (y=x)", line=dict(color="#A32D2D", dash="dash")))
        fig_scatter.update_layout(xaxis_title="Aktual (ton)", yaxis_title="Prediksi (ton)",
            margin=dict(l=0, r=0, t=10, b=0))
        st.plotly_chart(fig_scatter, use_container_width=True)

    st.divider()
    col1, col2 = st.columns(2)
    with col1:
        st.subheader("Feature importance (MDI)")
        fig_fi = go.Figure(go.Bar(
            x=imp_df["MDI"], y=imp_df["Fitur"],
            orientation="h",
            marker_color=["#EF9F27" if k in ["Bulan_Sin","Bulan_Cos","lag_12"] else "#185FA5"
                          for k in imp_df["Kode"]],
        ))
        fig_fi.update_layout(yaxis=dict(autorange="reversed"),
            xaxis_title="Importance score", margin=dict(l=0, r=0, t=10, b=0))
        st.plotly_chart(fig_fi, use_container_width=True)

    with col2:
        st.subheader("Residual plot (test set)")
        residuals = y_test - pred_test
        fig_res = go.Figure()
        fig_res.add_hrect(y0=-500, y1=500, fillcolor="#3B6D11", opacity=0.08, line_width=0)
        fig_res.add_hline(y=0, line_dash="dash", line_color="#A32D2D")
        fig_res.add_trace(go.Scatter(x=pred_test, y=residuals,
            mode="markers", marker=dict(color="#7F77DD", size=9, opacity=0.8)))
        fig_res.update_layout(xaxis_title="Nilai prediksi (ton)", yaxis_title="Residual (ton)",
            margin=dict(l=0, r=0, t=10, b=0))
        st.plotly_chart(fig_res, use_container_width=True)

    st.divider()
    st.subheader("Prediksi volume sampah 2026")
    st.caption("Prediksi menggunakan ekstrapolasi linear variabel tahunan dan rata-rata historis variabel bulanan.")
    hist_tail = df_model.tail(24)
    fig_2026 = go.Figure()
    fig_2026.add_trace(go.Scatter(x=hist_tail["Tanggal"], y=hist_tail["Volume_Sampah"],
        mode="lines+markers", name="Historis (2024–2025)", line=dict(color="#185FA5")))
    fig_2026.add_trace(go.Scatter(x=pred_df["Tanggal"], y=pred_df["Prediksi_Volume"],
        mode="lines+markers", name="Prediksi 2026",
        line=dict(color="#3B6D11", dash="dash")))
    fig_2026.add_vline(x="2026-01-01", line_dash="dot", line_color="gray")
    fig_2026.update_layout(yaxis_title="Volume sampah (ton)",
        legend=dict(orientation="h", y=1.1), margin=dict(l=0, r=0, t=10, b=0))
    st.plotly_chart(fig_2026, use_container_width=True)

    pred_df["Bulan_Nama"] = pred_df["Bulan"].apply(lambda x: bulan_nama[x-1])
    st.dataframe(
        pred_df[["Bulan_Nama","Prediksi_Volume"]].rename(
            columns={"Bulan_Nama":"Bulan","Prediksi_Volume":"Prediksi 2026 (ton)"}
        ).style.format({"Prediksi 2026 (ton)": "{:,.2f}"}),
        width='stretch', hide_index=True
    )

st.divider()

# ── Input Prediksi ──
with st.expander("Input tahun prediksi", expanded=True):
    st.subheader("Input prediksi tahunan")
    st.caption(
        "Pilih tahun yang ingin diprediksi. Model akan mengekstrapolasi variabel tahunan "
        "berdasarkan tren data 2016–2025, lalu menghasilkan prediksi 12 bulan."
    )

    df_input = df_model
    FITUR_COLS = [
        "Jumlah_Penduduk","Kepadatan_Penduduk","Jumlah_Rumah_Tangga","Jumlah_Rumah_Makan",
        "Tingkat_Hunian_Hotel","Jumlah_Wisatawan","Curah_Hujan","Jumlah_Hari_Besar",
        "Bulan_Sin","Bulan_Cos","lag_12",
    ]

    col1, col2 = st.columns([1, 2])
    with col1:
        tahun_pred = st.number_input(
            "Tahun prediksi",
            min_value=2026, max_value=2035,
            value=2026, step=1,
        )
        st.caption(
            f"Data train: **Jan 2017 – Des 2023**\n\n"
            f"Makin jauh dari 2025, makin besar ketidakpastian prediksi "
            f"karena ekstrapolasi variabel tahunan semakin panjang."
        )
        run_btn = st.button("Jalankan prediksi", type="primary", use_container_width=True)

    with col2:
        if tahun_pred > 2028:
            st.warning(
                f"Prediksi untuk tahun {tahun_pred} mengekstrapolasi data lebih dari "
                f"3 tahun ke depan dari data terakhir (2025). Hasilnya bersifat indikatif."
            )
        else:
            st.info(
                f"Model akan menggunakan ekstrapolasi linear dari data 2016–2025 "
                f"untuk memperkirakan variabel di tahun {tahun_pred}."
            )

    if run_btn:
        def extrap(kolom, tahun_target):
            vals = df_input.groupby("Tahun")[kolom].first().values
            yr   = np.arange(df_input["Tahun"].min(), df_input["Tahun"].max()+1)
            return np.polyval(np.polyfit(yr, vals, 1), tahun_target)

        monthly_hotel = df_input.groupby("Bulan")["Tingkat_Hunian_Hotel"].mean()
        monthly_hujan = df_input.groupby("Bulan")["Curah_Hujan"].mean()
        monthly_hari  = df_input.groupby("Bulan")["Jumlah_Hari_Besar"].mean()
        vol_ref = df_input[df_input["Tahun"] == df_input["Tahun"].max()].set_index("Bulan")["Volume_Sampah"]

        records = []
        for bulan in range(1, 13):
            sem_col = "Jumlah_Rumah_Tangga"
            s1_vals = df_input[df_input["Bulan"]==1].groupby("Tahun")[sem_col].first().values
            s2_vals = df_input[df_input["Bulan"]==7].groupby("Tahun")[sem_col].first().values
            yr = np.arange(df_input["Tahun"].min(), df_input["Tahun"].max()+1)
            rt = np.polyval(np.polyfit(yr, s1_vals if bulan<=6 else s2_vals, 1), tahun_pred)

            records.append({
                "Tahun": tahun_pred, "Bulan": bulan,
                "Jumlah_Penduduk":     extrap("Jumlah_Penduduk", tahun_pred),
                "Kepadatan_Penduduk":  extrap("Kepadatan_Penduduk", tahun_pred),
                "Jumlah_Rumah_Tangga": rt,
                "Jumlah_Rumah_Makan":  extrap("Jumlah_Rumah_Makan", tahun_pred),
                "Tingkat_Hunian_Hotel":monthly_hotel[bulan],
                "Jumlah_Wisatawan":    extrap("Jumlah_Wisatawan", tahun_pred),
                "Curah_Hujan":         monthly_hujan[bulan],
                "Jumlah_Hari_Besar":   monthly_hari[bulan],
                "Bulan_Sin": np.sin(2*np.pi*bulan/12),
                "Bulan_Cos": np.cos(2*np.pi*bulan/12),
                "lag_12":    vol_ref[bulan],
            })

        pred_df = pd.DataFrame(records)
        X = scaler_X.transform(pred_df[FITUR_COLS].values)
        y = scaler_y.inverse_transform(model.predict(X).reshape(-1,1)).ravel()
        pred_df["Prediksi_Volume"] = np.round(y, 2)

        st.divider()
        col1, col2, col3 = st.columns(3)
        col1.metric("Total tahunan", f"{pred_df['Prediksi_Volume'].sum():,.0f} ton")
        col2.metric("Rata-rata bulanan", f"{pred_df['Prediksi_Volume'].mean():,.0f} ton")
        col3.metric("Bulan tertinggi",
            bulan_nama[pred_df['Prediksi_Volume'].idxmax()] +
            f" ({pred_df['Prediksi_Volume'].max():,.0f} ton)")

        hist = df_input[df_input["Tahun"] >= 2023]
        fig = go.Figure()
        fig.add_trace(go.Scatter(
            x=hist["Tanggal"], y=hist["Volume_Sampah"],
            mode="lines+markers", name=f"Historis (2023–2025)",
            line=dict(color="#185FA5")
        ))
        pred_dates = pd.to_datetime(
            pred_df["Tahun"].astype(str)+"-"+pred_df["Bulan"].astype(str)+"-01"
        )
        fig.add_trace(go.Scatter(
            x=pred_dates, y=pred_df["Prediksi_Volume"],
            mode="lines+markers", name=f"Prediksi {tahun_pred}",
            line=dict(color="#3B6D11", dash="dash")
        ))
        fig.add_vline(x=f"{tahun_pred}-01-01", line_dash="dot", line_color="gray",
            annotation_text=f"Mulai {tahun_pred}")
        fig.update_layout(
            yaxis_title="Volume sampah (ton)",
            legend=dict(orientation="h", y=1.1),
            margin=dict(l=0, r=0, t=10, b=0)
        )
        st.plotly_chart(fig, use_container_width=True)

        pred_df["Bulan_Nama"] = pred_df["Bulan"].apply(lambda x: bulan_nama[x-1])
        st.dataframe(
            pred_df[["Bulan_Nama","Prediksi_Volume"]].rename(
                columns={"Bulan_Nama":"Bulan","Prediksi_Volume":f"Prediksi {tahun_pred} (ton)"}
            ).style.format({f"Prediksi {tahun_pred} (ton)": "{:,.2f}"}),
            width='stretch', hide_index=True
        )

        csv = pred_df[["Bulan_Nama","Prediksi_Volume"]].to_csv(index=False).encode()
        st.download_button(
            f"Download prediksi {tahun_pred} (.csv)",
            csv, f"prediksi_{tahun_pred}.csv", "text/csv"
        )