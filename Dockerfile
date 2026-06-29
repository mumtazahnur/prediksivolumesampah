FROM php:8.2-apache

# 1. Matikan mpm_event dan mpm_worker, lalu hidupkan mpm_prefork secara aman untuk mencegah konflik MPM
RUN a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork || true

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

# 5. Jalankan port substitution secara dinamis di runtime menggunakan port Railway, kemudian jalankan Apache
RUN sed -i 's/Listen 80/Listen ${PORT}/g' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/g' /etc/apache2/sites-available/000-default.conf

CMD ["apache2-foreground"]