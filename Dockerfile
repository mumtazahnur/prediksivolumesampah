FROM php:8.2-apache

# 1. Matikan mpm_event dan hidupkan mpm_prefork di awal untuk mencegah error AH00534
RUN a2dismod mpm_event || true && a2enmod mpm_prefork || true

# 2. Install python3, venv, dan pip
RUN apt-get update && apt-get install -y \
    python3 \
    python3-pip \
    python3-venv \
    && rm -rf /var/lib/apt/lists/*

# 3. Copy seluruh file project ke direktori web Apache
COPY . /var/www/html/

# 4. Buat virtual environment Python dan install requirements
RUN python3 -m venv /opt/venv
ENV PATH="/opt/venv/bin:$PATH"
RUN pip install --no-cache-dir -r /var/www/html/requirements.txt

# 5. Konfigurasi Apache agar mendengarkan Port dinamis dari Railway secara aman
RUN sed -i 's/Listen 80/Listen ${PORT}/g' /etc/apache2/ports.conf
RUN sed -i 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/g' /etc/apache2/sites-available/000-default.conf

# 6. Jalankan Apache
CMD ["apache2-foreground"]