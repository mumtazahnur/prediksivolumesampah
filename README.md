# Prediksi Volume Sampah di Kota Surakarta Menggunakan Random Forest Regression Berbasis Faktor Kependudukan
Surakarta Waste Volume Predictor is a web-based forecasting tool for monthly waste volume entering TPA Putri Cempo, Surakarta, Indonesia. Using a Random Forest Regression model trained on demographic, contextual, and calendar-based features, the application helps local governments anticipate waste generation trends to support landfill capacity planning and waste management budgeting.

Kelompok 1 - Kelas D
Department of Informatics, Faculty of Information Technology and Data Science, Sebelas Maret University

## About
This project builds a prediction model for the monthly waste volume entering TPA Putri Cempo, Surakarta, using Random Forest Regression with eleven input features, which is eight demographic and contextual variables, two Fourier-encoding calendar features, and one lag-12 feature representing the waste volume of the same month in the previous year. The model is trained on 2017–2023 data and tested on 2024–2025, with hyperparameters tuned via GridSearchCV using 5-fold TimeSeriesSplit. It is then used to forecast monthly waste volume for 2026 as a reference for waste management planning in Surakarta.

## Links
- Streamlit: https://prediksivolumesampah-d-ai-kelompok1.streamlit.app/
- Paper: https://docs.google.com/document/d/1Fwhc5OS6u6lLDQ1Y8w7Fsemil0WoUQOxdzyotyS8wtI/edit?usp=sharing

## Stack
Python (Pandas, NumPy, Scikit-learn), Streamlit
