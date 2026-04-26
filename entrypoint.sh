#!/bin/bash
set -e

echo "----------------------------------------------------"
echo "IFF WORDPRESS BAŞLATILIYOR..."
echo "----------------------------------------------------"

# SITE_URL içindeki olası çift kolon (::) hatalarını düzelt
SITE_URL=$(echo $SITE_URL | sed 's/::/:/g')

# Veritabanı bilgilerini ayıkla (host:port formatını desteklemek için)
DB_HOST=$(echo $WORDPRESS_DB_HOST | cut -d: -f1)
DB_HOST=${DB_HOST:-db}
DB_PORT=$(echo $WORDPRESS_DB_HOST | cut -s -d: -f2)
DB_PORT=${DB_PORT:-3306}

# Veritabanı sunucusunun hazır olmasını bekle
echo ">> Veritabanı bağlantısı bekleniyor ($DB_HOST:$DB_PORT)..."
until printf "" 2>>/dev/null >/dev/tcp/$DB_HOST/$DB_PORT; do 
    sleep 2
    echo ">> Bekleniyor..."
done

echo ">> Veritabanı hazır!"

# wp-content Senkronizasyonu (Git'ten gelen güncel dosyaları kalıcı volume'a aktar)
echo ">> wp-content dosyaları senkronize ediliyor..."
mkdir -p /var/www/html/wp-content
cp -ru /usr/src/wp-content-source/. /var/www/html/wp-content/
chown -R www-data:www-data /var/www/html/wp-content

# Veritabanı boş mu kontrol et (Eğer wp_options yoksa sıfır kurulumdur)
DB_EXISTS=$(mysql --ssl=FALSE -h "$DB_HOST" -P "$DB_PORT" -u "$WORDPRESS_DB_USER" -p"$WORDPRESS_DB_PASSWORD" "$WORDPRESS_DB_NAME" -e "SHOW TABLES LIKE 'wp_options';" | grep wp_options || true)

if [ -z "$DB_EXISTS" ]; then
    if [ -f "/var/www/html/db_backup.sql" ]; then
        echo ">> Boş veritabanı tespit edildi. db_backup.sql içe aktarılıyor..."
        mysql --ssl=FALSE -h "$DB_HOST" -P "$DB_PORT" -u "$WORDPRESS_DB_USER" -p"$WORDPRESS_DB_PASSWORD" "$WORDPRESS_DB_NAME" < /var/www/html/db_backup.sql
        
        echo ">> URL'ler güncelleniyor: $SITE_URL"
        mysql --ssl=FALSE -h "$DB_HOST" -P "$DB_PORT" -u "$WORDPRESS_DB_USER" -p"$WORDPRESS_DB_PASSWORD" "$WORDPRESS_DB_NAME" <<EOF
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
