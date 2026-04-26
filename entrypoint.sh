#!/bin/bash
set -e

echo "----------------------------------------------------"
echo "IFF WORDPRESS BAŞLATILIYOR..."
echo "----------------------------------------------------"

# Veritabanı sunucusunun hazır olmasını bekle
echo ">> Veritabanı bağlantısı bekleniyor (db:3306)..."
until printf "" 2>>/dev/null >/dev/tcp/db/3306; do 
    sleep 2
    echo ">> Bekleniyor..."
done

echo ">> Veritabanı hazır!"

# Veritabanı boş mu kontrol et (Eğer wp_options yoksa sıfır kurulumdur)
DB_EXISTS=$(mysql -h db -u "$WORDPRESS_DB_USER" -p"$WORDPRESS_DB_PASSWORD" "$WORDPRESS_DB_NAME" -e "SHOW TABLES LIKE 'wp_options';" | grep wp_options || true)

if [ -z "$DB_EXISTS" ]; then
    if [ -f "/var/www/html/db_backup.sql" ]; then
        echo ">> Boş veritabanı tespit edildi. db_backup.sql içe aktarılıyor..."
        mysql -h db -u "$WORDPRESS_DB_USER" -p"$WORDPRESS_DB_PASSWORD" "$WORDPRESS_DB_NAME" < /var/www/html/db_backup.sql
        
        echo ">> URL'ler güncelleniyor: $SITE_URL"
        mysql -h db -u "$WORDPRESS_DB_USER" -p"$WORDPRESS_DB_PASSWORD" "$WORDPRESS_DB_NAME" <<EOF
UPDATE wp_options SET option_value = '$SITE_URL' WHERE option_name = 'siteurl';
UPDATE wp_options SET option_value = '$SITE_URL' WHERE option_name = 'home';
EOF
        echo ">> Veritabanı kurulumu tamamlandı."
    else
        echo ">> UYARI: db_backup.sql bulunamadı, sıfır WordPress kuruluyor."
    fi
else
    echo ">> Veritabanı zaten dolu, aktarma atlanıyor."
fi

echo "----------------------------------------------------"
echo "APACHE BAŞLATILIYOR..."
exec docker-entrypoint.sh apache2-foreground
