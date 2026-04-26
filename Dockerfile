FROM wordpress:latest

# Veritabanı import işlemi için gerekli istemciyi kur
RUN apt-get update && apt-get install -y mariadb-client && rm -rf /var/lib/apt/lists/*

# Başlangıç scriptimizi ve DB yedeğini kopyala, izinlerini ver
COPY entrypoint.sh /usr/local/bin/custom-entrypoint.sh
COPY db_backup.sql /var/www/html/db_backup.sql
RUN chmod +x /usr/local/bin/custom-entrypoint.sh && chmod 644 /var/www/html/db_backup.sql

# Scripti varsayılan başlangıç komutu yap
ENTRYPOINT ["custom-entrypoint.sh"]
