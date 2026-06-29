FROM php:8.2-apache

# Install python3, venv, and pip
RUN apt-get update && apt-get install -y \
    python3 \
    python3-pip \
    python3-venv \
    && rm -rf /var/lib/apt/lists/*

# Copy project files
COPY . /var/www/html/

# Create a virtual environment and install python dependencies
RUN python3 -m venv /opt/venv
ENV PATH="/opt/venv/bin:$PATH"

RUN /opt/venv/bin/pip install --no-cache-dir -r /var/www/html/requirements.txt
RUN a2dismod mpm_event && a2enmod mpm_prefork
# Configure Apache to listen to Railway's dynamic PORT
# We replace default port 80 with the env variable $PORT during container startup
CMD sed -i "s/80/\${PORT}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && apache2-foreground
