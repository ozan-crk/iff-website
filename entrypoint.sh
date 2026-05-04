#!/bin/bash
set -e

echo "----------------------------------------------------"
echo "IFF WORDPRESS BAŞLATILIYOR..."
echo "----------------------------------------------------"





# wp-content Senkronizasyonu (Git'ten gelen güncel dosyaları kalıcı volume'a aktar)
echo ">> wp-content dosyaları senkronize ediliyor..."
mkdir -p /var/www/html/wp-content/sessions
cp -ru /usr/src/wp-content-source/. /var/www/html/wp-content/
chown -R www-data:www-data /var/www/html/wp-content
chmod 777 /var/www/html/wp-content/sessions


echo "----------------------------------------------------"
echo "APACHE BAŞLATILIYOR..."
exec docker-entrypoint.sh apache2-foreground
