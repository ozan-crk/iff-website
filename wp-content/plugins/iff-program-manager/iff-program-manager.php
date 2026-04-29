<?php
/**
 * Plugin Name: IFF Gösterim Programı Yöneticisi
 * Description: İşçi Filmleri Festivali için Excel'den program aktarma ve düzenleme aracı.
 * Version: 1.2
 * Author: IFF
 */

if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/SimpleXLSX.php';

class IFF_Program_Manager {
    
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'iff_programs';

        register_activation_hook(__FILE__, [$this, 'create_table']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_post_iff_upload_excel', [$this, 'handle_excel_upload']);
        add_action('admin_post_iff_delete_program', [$this, 'handle_delete']);
        add_action('admin_post_iff_save_program', [$this, 'handle_save']);
        
        // Eklenti güncellendiğinde tabloyu kontrol et
        add_action('admin_init', [$this, 'create_table']);
    }

    public function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $this->table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            sehir varchar(100) NOT NULL,
            mekan varchar(255) NOT NULL,
            tarih varchar(50) NOT NULL,
            saat varchar(20) NOT NULL,
            film_adi varchar(255) NOT NULL,
            sure varchar(50) DEFAULT '',
            etkinlik varchar(255) DEFAULT '',
            is_special tinyint(1) DEFAULT 0,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function add_admin_menu() {
        add_menu_page(
            'Gösterim Programı',
            'Program Yönetimi',
            'manage_options',
            'iff-program-manager',
            [$this, 'admin_page_html'],
            'dashicons-calendar-alt',
            25
        );
    }

    private function normalize_text($text) {
        if (empty($text)) return '';

        // UTF-8 Normalizasyonu (Form C)
        if (class_exists('Normalizer')) {
            $text = Normalizer::normalize($text, Normalizer::FORM_C);
        }

        // Bazı Excel/Mac kaynaklı dosyalardan gelen "Combining Marks" (Birleştirici İşaretler) temizliği
        // Özellikle i̇ (i + dot) gibi durumları düzeltir
        $text = preg_replace('/\p{M}/u', '', $text);

        // Türkçe karakterleri de kapsayan Title Case (İlk Harf Büyük) dönüşümü
        // Not: MB_CASE_TITLE bazen i/İ dönüşümlerinde bu karakterleri tekrar tetikleyebilir, 
        // o yüzden önce case conversion yapıp sonra temizlik yapmak daha garanti olabilir.
        $text = mb_convert_case(trim($text), MB_CASE_TITLE, "UTF-8");
        
        // Tekrar bir temizlik geçelim (Title case sonrası oluşabilecek durumlar için)
        $text = preg_replace('/\p{M}/u', '', $text);

        return $text;
    }

    public function admin_page_html() {
        global $wpdb;
        
        $edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
        $edit_row = null;
        if ($edit_id) {
            $edit_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->table_name WHERE id = %d", $edit_id));
        }

        $results = $wpdb->get_results("SELECT * FROM $this->table_name ORDER BY tarih ASC, saat ASC");

        echo '<div class="wrap">';
        echo '<h1>IFF Gösterim Programı Yönetimi</h1>';

        if (isset($_GET['msg'])) {
            if ($_GET['msg'] == 'success') echo '<div class="notice notice-success is-dismissible"><p>İşlem başarılı.</p></div>';
            if ($_GET['msg'] == 'error') echo '<div class="notice notice-error is-dismissible"><p>Bir hata oluştu.</p></div>';
        }

        // Excel Yükleme Formu
        echo '<div class="card" style="max-width:100%; margin-top:20px; padding:20px;">';
        echo '<h2>Excel\'den Toplu İçe Aktar (.xlsx)</h2>';
        echo '<p>Sütun sırası: <strong>Şehir | Mekan | Tarih | Saat | Film Adı | Süre | Etkinlik | Özel Gösterim(1/0)</strong>.</p>';
        echo '<form action="' . esc_url(admin_url('admin-post.php')) . '" method="post" enctype="multipart/form-data">';
        echo '<input type="hidden" name="action" value="iff_upload_excel">';
        wp_nonce_field('iff_upload_excel_nonce');
        echo '<input type="file" name="excel_file" accept=".xlsx" required>';
        echo '<label style="margin-left:15px;"><input type="checkbox" name="clear_old" value="1"> Eski kayıtları sil (Üzerine yaz)</label>';
        echo '<input type="submit" class="button button-primary" value="Yükle" style="margin-left:15px;">';
        echo '</form>';
        echo '</div>';

        // Manuel Ekleme / Düzenleme Formu
        echo '<div class="card" style="max-width:100%; margin-top:20px; padding:20px;">';
        echo '<h2>' . ($edit_row ? 'Kaydı Düzenle' : 'Yeni Kayıt Ekle') . '</h2>';
        echo '<form action="' . esc_url(admin_url('admin-post.php')) . '" method="post">';
        echo '<input type="hidden" name="action" value="iff_save_program">';
        if ($edit_row) echo '<input type="hidden" name="id" value="' . $edit_row->id . '">';
        wp_nonce_field('iff_save_program_nonce');
        
        echo '<table class="form-table">';
        echo '<tr><th>Şehir</th><td><input type="text" name="sehir" value="' . ($edit_row ? esc_attr($edit_row->sehir) : '') . '" placeholder="Örn: İstanbul" required></td></tr>';
        echo '<tr><th>Mekan</th><td><input type="text" name="mekan" value="' . ($edit_row ? esc_attr($edit_row->mekan) : '') . '" placeholder="Örn: Şişli NHRKM" required></td></tr>';
        echo '<tr><th>Tarih</th><td><input type="date" name="tarih" value="' . ($edit_row ? esc_attr($edit_row->tarih) : '') . '" required></td></tr>';
        echo '<tr><th>Saat</th><td><input type="time" name="saat" value="' . ($edit_row ? esc_attr($edit_row->saat) : '') . '" required></td></tr>';
        echo '<tr><th>Film Adı</th><td><input type="text" name="film_adi" value="' . ($edit_row ? esc_attr($edit_row->film_adi) : '') . '" required></td></tr>';
        echo '<tr><th>Süre</th><td><input type="text" name="sure" value="' . ($edit_row ? esc_attr($edit_row->sure) : '') . '" placeholder="Örn: 90 dk"></td></tr>';
        echo '<tr><th>Etkinlik</th><td><input type="text" name="etkinlik" value="' . ($edit_row ? esc_attr($edit_row->etkinlik) : '') . '" placeholder="Örn: Yönetmen Söyleşisi"></td></tr>';
        echo '<tr><th>Özel Gösterim</th><td><label><input type="checkbox" name="is_special" value="1" ' . ($edit_row && $edit_row->is_special ? 'checked' : '') . '> Bu özel bir gösterimdir</label></td></tr>';
        echo '</table>';
        
        echo '<p class="submit"><input type="submit" class="button button-primary" value="Kaydet">';
        if ($edit_row) echo ' <a href="?page=iff-program-manager" class="button">İptal</a>';
        echo '</p>';
        echo '</form>';
        echo '</div>';

        // Tablo
        echo '<h2 style="margin-top:40px;">Kayıtlı Programlar</h2>';
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>Şehir</th><th>Mekan</th><th>Tarih</th><th>Saat</th><th>Film Adı</th><th>Süre</th><th>Etkinlik</th><th>Özel</th><th>İşlemler</th></tr></thead>';
        echo '<tbody>';
        if ($results) {
            foreach ($results as $row) {
                $delete_url = wp_nonce_url(admin_url('admin-post.php?action=iff_delete_program&id=' . $row->id), 'iff_delete_program_nonce');
                echo '<tr>';
                echo '<td><strong>' . esc_html($row->sehir) . '</strong></td>';
                echo '<td>' . esc_html($row->mekan) . '</td>';
                echo '<td>' . esc_html($row->tarih) . '</td>';
                echo '<td>' . esc_html($row->saat) . '</td>';
                echo '<td>' . esc_html($row->film_adi) . '</td>';
                echo '<td>' . esc_html($row->sure) . '</td>';
                echo '<td>' . esc_html($row->etkinlik) . '</td>';
                echo '<td>' . ($row->is_special ? '<span class="dashicons dashicons-star-filled" style="color:orange;"></span>' : '') . '</td>';
                echo '<td>
                        <a href="?page=iff-program-manager&edit=' . $row->id . '">Düzenle</a> | 
                        <a href="' . esc_url($delete_url) . '" style="color:red;" onclick="return confirm(\'Silmek istediğinize emin misiniz?\')">Sil</a>
                      </td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="9">Henüz kayıt yok.</td></tr>';
        }
        echo '</tbody></table>';

        echo '</div>';
    }

    public function handle_excel_upload() {
        if (!current_user_can('manage_options')) wp_die('Yetkisiz erişim.');
        check_admin_referer('iff_upload_excel_nonce');

        global $wpdb;

        if (isset($_POST['clear_old']) && $_POST['clear_old'] == '1') {
            $wpdb->query("TRUNCATE TABLE $this->table_name");
        }

        if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] == UPLOAD_ERR_OK) {
            if ($xlsx = \Shuchkin\SimpleXLSX::parse($_FILES['excel_file']['tmp_name'])) {
                $rows = $xlsx->rows();
                for ($i = 1; $i < count($rows); $i++) {
                    $row = $rows[$i];
                    if (count($row) >= 5 && !empty($row[0])) {
                        
                        // Tarih Temizleme (Excel'den 2026-05-03 00:00:00 gelirse)
                        $tarih = sanitize_text_field($row[2]);
                        if (strpos($tarih, ' ') !== false) {
                            $tarih = explode(' ', $tarih)[0];
                        }

                        // Saat Temizleme (Excel'den 1970-01-01 15:00:00 gelirse)
                        $saat = sanitize_text_field($row[3]);
                        if (strpos($saat, '1970-01-01') !== false) {
                            $saat = trim(str_replace('1970-01-01', '', $saat));
                        }
                        $saat = substr($saat, 0, 5); // HH:MM al

                        // Süre Temizleme (Excel'den 1970-01-01 01:24:00 gelirse)
                        $sure = isset($row[5]) ? sanitize_text_field($row[5]) : '';
                        if (strpos($sure, '1970-01-01') !== false) {
                            $time_part = trim(str_replace('1970-01-01', '', $sure));
                            $parts = explode(':', $time_part);
                            if (count($parts) >= 2) {
                                $hours = intval($parts[0]);
                                $minutes = intval($parts[1]);
                                if ($hours > 0) {
                                    $sure = ($hours * 60 + $minutes) . ' dk';
                                } else {
                                    $sure = $minutes . ' dk';
                                }
                            }
                        }

                        $wpdb->insert($this->table_name, [
                            'sehir'      => $this->normalize_text($row[0]),
                            'mekan'      => $this->normalize_text($row[1]),
                            'tarih'      => $tarih,
                            'saat'       => $saat,
                            'film_adi'   => sanitize_text_field($row[4]),
                            'sure'       => $sure,
                            'etkinlik'   => isset($row[6]) ? sanitize_text_field($row[6]) : '',
                            'is_special' => (isset($row[7]) && $row[7] == '1') ? 1 : 0,
                        ]);
                    }
                }
                wp_redirect(admin_url('admin.php?page=iff-program-manager&msg=success'));
                exit;
            }
        }
        wp_redirect(admin_url('admin.php?page=iff-program-manager&msg=error'));
        exit;
    }

    public function handle_save() {
        if (!current_user_can('manage_options')) wp_die('Yetkisiz erişim.');
        check_admin_referer('iff_save_program_nonce');

        global $wpdb;

        $data = [
            'sehir'      => $this->normalize_text($_POST['sehir']),
            'mekan'      => $this->normalize_text($_POST['mekan']),
            'tarih'      => sanitize_text_field($_POST['tarih']),
            'saat'       => sanitize_text_field($_POST['saat']),
            'film_adi'   => sanitize_text_field($_POST['film_adi']),
            'sure'       => sanitize_text_field($_POST['sure']),
            'etkinlik'   => sanitize_text_field($_POST['etkinlik']),
            'is_special' => isset($_POST['is_special']) ? 1 : 0,
        ];

        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $wpdb->update($this->table_name, $data, ['id' => intval($_POST['id'])]);
        } else {
            $wpdb->insert($this->table_name, $data);
        }

        wp_redirect(admin_url('admin.php?page=iff-program-manager&msg=success'));
        exit;
    }

    public function handle_delete() {
        if (!current_user_can('manage_options')) wp_die('Yetkisiz erişim.');
        check_admin_referer('iff_delete_program_nonce');

        if (isset($_GET['id'])) {
            global $wpdb;
            $wpdb->delete($this->table_name, ['id' => intval($_GET['id'])]);
            wp_redirect(admin_url('admin.php?page=iff-program-manager&msg=success'));
            exit;
        }
    }

    public static function get_programs($limit = 10, $tarih = '') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'iff_programs';
        
        if (!empty($tarih)) {
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table_name WHERE tarih = %s ORDER BY saat ASC LIMIT %d", 
                $tarih, 
                $limit
            ));
        }

        return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY tarih ASC, saat ASC LIMIT %d", $limit));
    }
}

new IFF_Program_Manager();
