FROM wordpress:latest

# Veritabanı import işlemi için gerekli istemciyi kur
RUN apt-get update && apt-get install -y mariadb-client && rm -rf /var/lib/apt/lists/*

# Başlangıç scriptimizi kopyala ve izinlerini ver
COPY entrypoint.sh /usr/local/bin/custom-entrypoint.sh
RUN chmod +x /usr/local/bin/custom-entrypoint.sh

# Scripti varsayılan başlangıç komutu yap
ENTRYPOINT ["custom-entrypoint.sh"]
