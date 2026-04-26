#!/bin/bash
set -e

echo "----------------------------------------------------"
echo "OTOMATİK URL GÜNCELLEME BAŞLATILDI"
echo "Hedef URL: $SITE_URL"
echo "----------------------------------------------------"

# Veritabanı bağlantısı için bir süre bekleyebiliriz (opsiyonel)
# MySQL initdb.d scriptleri veritabanı hazır olduğunda çalışır.

mysql -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE" <<EOF
UPDATE wp_options SET option_value = '$SITE_URL' WHERE option_name = 'siteurl';
UPDATE wp_options SET option_value = '$SITE_URL' WHERE option_name = 'home';
EOF

echo "URL güncelleme işlemi başarıyla tamamlandı."
echo "----------------------------------------------------"
