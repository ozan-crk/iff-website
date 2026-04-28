FROM wordpress:latest



# Başlangıç scriptimizi, DB yedeğini ve wp-content kaynaklarını kopyala
COPY entrypoint.sh /usr/local/bin/custom-entrypoint.sh

COPY wp-content /usr/src/wp-content-source
COPY uploads.ini /usr/local/etc/php/conf.d/uploads.ini

# İzinleri ayarla
RUN chmod +x /usr/local/bin/custom-entrypoint.sh && \
    chown -R www-data:www-data /usr/src/wp-content-source

# Scripti varsayılan başlangıç komutu yap
ENTRYPOINT ["custom-entrypoint.sh"]
