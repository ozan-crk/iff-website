#!/bin/bash
set -e

echo "----------------------------------------------------"
echo "VERİTABANI KURULUM SİHİRBAZI BAŞLATILDI"
echo "----------------------------------------------------"

# 1. Veritabanı Yedeğini İçe Aktar (Eğer dosya varsa)
# Not: Buradaki yolu /init-data/db_backup.sql olarak docker-compose'da bağladık
if [ -f "/init-data/db_backup.sql" ]; then
    echo ">> db_backup.sql dosyası bulundu, içe aktarılıyor..."
    mysql -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE" < /init-data/db_backup.sql
    echo ">> İçe aktarma tamamlandı."
else
    echo ">> UYARI: /init-data/db_backup.sql dosyası bulunamadı! Atlanıyor."
fi

# 2. URL'leri Güncelle
echo ">> Hedef URL güncelleniyor: $SITE_URL"
mysql -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE" <<EOF
UPDATE wp_options SET option_value = '$SITE_URL' WHERE option_name = 'siteurl';
UPDATE wp_options SET option_value = '$SITE_URL' WHERE option_name = 'home';
EOF

echo "----------------------------------------------------"
echo "TÜM İŞLEMLER BAŞARIYLA TAMAMLANDI"
echo "----------------------------------------------------"
