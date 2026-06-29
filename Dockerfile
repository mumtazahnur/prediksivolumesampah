FROM php:8.2-apache

# 1. Install python3, venv, dan pip
RUN apt-get update && apt-get install -y \
    python3 \
    python3-pip \
    python3-venv \
    && rm -rf /var/lib/apt/lists/*

# 2. Copy seluruh file project ke direktori web Apache
COPY . /var/www/html/

# 3. Buat virtual environment Python dan install requirements
RUN python3 -m venv /opt/venv
ENV PATH="/opt/venv/bin:$PATH"
RUN pip install --no-cache-dir -r /var/www/html/requirements.txt

# 4. Jalankan Apache di port default 80
EXPOSE 80

# 5. Bersihkan modul MPM (nonaktifkan mpm_event/mpm_worker, aktifkan mpm_prefork) di runtime, lalu jalankan Apache
CMD a2dismod mpm_event mpm_worker || true && a2enmod mpm_prefork || true && apache2-foreground