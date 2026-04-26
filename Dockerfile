FROM wordpress:latest

# Veritabanı import işlemi için gerekli istemciyi kur
RUN apt-get update && apt-get install -y mariadb-client && rm -rf /var/lib/apt/lists/*

# Başlangıç scriptimizi, DB yedeğini ve wp-content klasörünü kopyala
COPY entrypoint.sh /usr/local/bin/custom-entrypoint.sh
COPY db_backup.sql /var/www/html/db_backup.sql
COPY wp-content /var/www/html/wp-content

# İzinleri ayarla
RUN chmod +x /usr/local/bin/custom-entrypoint.sh && \
    chmod 644 /var/www/html/db_backup.sql && \
    chown -R www-data:www-data /var/www/html/wp-content

# Scripti varsayılan başlangıç komutu yap
ENTRYPOINT ["custom-entrypoint.sh"]
