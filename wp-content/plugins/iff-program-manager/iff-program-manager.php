<?php
/**
 * Plugin Name: IFF Gösterim Programı Yöneticisi
 * Description: İşçi Filmleri Festivali için Excel'den program aktarma ve düzenleme aracı.
 * Version: 1.2
 * Author: IFF
 */

if (!defined('ABSPATH'))
    exit;

require_once __DIR__ . '/SimpleXLSX.php';
require_once __DIR__ . '/SimpleXLSXGen.php';

class IFF_Program_Manager
{

    private $table_name;

    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'iff_programs';

        register_activation_hook(__FILE__, [$this, 'create_table']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_post_iff_upload_excel', [$this, 'handle_excel_upload']);
        add_action('admin_post_iff_export_excel', [$this, 'handle_export']);
        add_action('admin_post_iff_delete_program', [$this, 'handle_delete']);
        add_action('admin_post_iff_save_program', [$this, 'handle_save']);
        add_action('admin_post_iff_update_mekan', [$this, 'handle_update_mekan']);

        // Eklenti güncellendiğinde tabloyu kontrol et
        add_action('admin_init', [$this, 'create_table']);
    }

    public function create_table()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $this->table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            sehir varchar(100) NOT NULL,
            mekan varchar(255) NOT NULL,
            tarih varchar(50) NOT NULL,
            saat varchar(20) NOT NULL,
            tur varchar(20) DEFAULT 'film',
            film_adi varchar(255) NOT NULL,
            sure varchar(50) DEFAULT '',
            etkinlik varchar(255) DEFAULT '',
            is_special tinyint(1) DEFAULT 0,
            is_gala tinyint(1) DEFAULT 0,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function add_admin_menu()
    {
        add_menu_page(
            'Gösterim Programı',
            'Program Yönetimi',
            'manage_options',
            'iff-program-manager',
            [$this, 'admin_page_html'],
            'dashicons-calendar-alt',
            25
        );

        add_submenu_page(
            'iff-program-manager',
            'Mekan Yönetimi',
            'Mekan Yönetimi',
            'manage_options',
            'iff-mekan-manager',
            [$this, 'mekan_page_html']
        );
    }

    private function normalize_time($time)
    {
        if (empty($time))
            return '';

        $time = str_replace('1970-01-01', '', $time);
        $time = trim($time);

        // nokta yerine iki nokta (18.00 -> 18:00)
        $time = str_replace('.', ':', $time);

        $parts = explode(':', $time);
        $h = isset($parts[0]) ? intval($parts[0]) : 0;
        $m = isset($parts[1]) ? intval($parts[1]) : 0;

        return sprintf('%02d:%02d', $h, $m);
    }

    private function normalize_text($text)
    {
        if (empty($text))
            return '';

        // Kaçış karakterlerini temizle (Excel'den gelen ters slash veya özel kodlar)
        $text = stripslashes($text);
        $text = str_replace(['\\', '_x000D_'], '', $text);

        // UTF-8 Normalizasyonu (Form C)
        if (class_exists('Normalizer')) {
            $text = Normalizer::normalize($text, Normalizer::FORM_C);
        }


        // Özellikle i̇ (i + dot) gibi durumları düzeltir
        $text = preg_replace('/\p{M}/u', '', $text);

        // Gereksiz boşlukları temizle ama casing'e dokunma
        return trim($text);
    }

    public function admin_page_html()
    {
        global $wpdb;

        $where_clauses = [];
        $query_args = [];

        $filter_sehir = isset($_GET['filter_sehir']) ? sanitize_text_field($_GET['filter_sehir']) : '';
        $filter_mekan = isset($_GET['filter_mekan']) ? sanitize_text_field($_GET['filter_mekan']) : '';
        $filter_tarih = isset($_GET['filter_tarih']) ? sanitize_text_field($_GET['filter_tarih']) : '';
        $filter_tur = isset($_GET['filter_tur']) ? sanitize_text_field($_GET['filter_tur']) : '';

        if ($filter_sehir) {
            $where_clauses[] = "sehir = %s";
            $query_args[] = $filter_sehir;
        }
        if ($filter_mekan) {
            $where_clauses[] = "mekan = %s";
            $query_args[] = $filter_mekan;
        }
        if ($filter_tarih) {
            $where_clauses[] = "tarih = %s";
            $query_args[] = $filter_tarih;
        }
        if ($filter_tur) {
            $where_clauses[] = "tur = %s";
            $query_args[] = $filter_tur;
        }

        $where_sql = count($where_clauses) > 0 ? "WHERE " . implode(" AND ", $where_clauses) : "";
        $query = "SELECT * FROM $this->table_name $where_sql ORDER BY tarih ASC, saat ASC";

        if (!empty($query_args)) {
            $results = $wpdb->get_results($wpdb->prepare($query, ...$query_args));
        } else {
            $results = $wpdb->get_results($query);
        }

        $sehirler = $wpdb->get_col("SELECT DISTINCT sehir FROM $this->table_name ORDER BY sehir ASC");
        $mekanlar = $wpdb->get_col("SELECT DISTINCT mekan FROM $this->table_name ORDER BY mekan ASC");
        $tarihler = $wpdb->get_col("SELECT DISTINCT tarih FROM $this->table_name ORDER BY tarih ASC");

        echo '<div class="wrap">';
        echo '<h1>IFF Gösterim Programı Yönetimi</h1>';

        if (isset($_GET['msg'])) {
            if ($_GET['msg'] == 'success')
                echo '<div class="notice notice-success is-dismissible"><p>İşlem başarılı.</p></div>';
            if ($_GET['msg'] == 'error')
                echo '<div class="notice notice-error is-dismissible"><p>Bir hata oluştu.</p></div>';
        }

        // Excel Yükleme Formu
        echo '<div class="card" style="max-width:100%; margin-top:20px; padding:20px;">';
        echo '<h2>Excel İşlemleri</h2>';
        echo '<p>İçe aktarma için sütun sırası: <strong>Şehir | Mekan | Tarih | Saat | Tür | Başlık | Süre | Detay | Özel | Gala</strong>.</p>';

        echo '<div style="display:flex; gap:20px; align-items:center;">';

        // İçe Aktar
        echo '<form action="' . esc_url(admin_url('admin-post.php')) . '" method="post" enctype="multipart/form-data" style="flex:1;">';
        echo '<input type="hidden" name="action" value="iff_upload_excel">';
        wp_nonce_field('iff_upload_excel_nonce');
        echo '<input type="file" name="excel_file" accept=".xlsx" required>';
        echo '<label style="margin-left:15px;"><input type="checkbox" name="clear_old" value="1"> Eski kayıtları sil (Üzerine yaz)</label>';
        echo '<input type="submit" class="button button-primary" value="İçe Aktar (.xlsx)" style="margin-left:15px;">';
        echo '</form>';

        // Dışa Aktar
        echo '<form action="' . esc_url(admin_url('admin-post.php')) . '" method="post">';
        echo '<input type="hidden" name="action" value="iff_export_excel">';
        wp_nonce_field('iff_export_excel_nonce');
        echo '<input type="submit" class="button" value="Mevcut Veriyi İndir (.xlsx)">';
        echo '</form>';

        echo '</div>';
        echo '</div>';

        // Manuel Ekleme Formu
        echo '<div class="card" style="max-width:100%; margin-top:20px; padding:20px;">';
        echo '<h2>Yeni Kayıt Ekle</h2>';
        echo '<form action="' . esc_url(admin_url('admin-post.php')) . '" method="post">';
        echo '<input type="hidden" name="action" value="iff_save_program">';
        wp_nonce_field('iff_save_program_nonce');

        echo '<table class="form-table">';
        echo '<tr><th>Şehir</th><td><input type="text" name="sehir" value="" placeholder="Örn: İstanbul" required></td></tr>';
        echo '<tr><th>Mekan</th><td><input type="text" name="mekan" value="" placeholder="Örn: Şişli NHRKM" required></td></tr>';
        echo '<tr><th>Tarih</th><td><input type="date" name="tarih" value="" required></td></tr>';
        echo '<tr><th>Saat</th><td><input type="time" name="saat" value="" required></td></tr>';
        echo '<tr><th>Tür</th><td><select name="tur"><option value="film">Film</option><option value="etkinlik">Etkinlik</option></select></td></tr>';
        echo '<tr><th>Başlık (Film/Etkinlik Adı)</th><td><textarea name="film_adi" required rows="3" style="width:100%;"></textarea></td></tr>';
        echo '<tr><th>Süre</th><td><input type="text" name="sure" value="" placeholder="Örn: 90 dk"></td></tr>';
        echo '<tr><th>Detay</th><td><textarea name="etkinlik" placeholder="Örn: Yönetmen Söyleşisi" rows="3" style="width:100%;"></textarea></td></tr>';
        echo '<tr><th>Özel</th><td><label><input type="checkbox" name="is_special" value="1"> Bu özel bir gösterimdir</label></td></tr>';
        echo '<tr><th>Gala</th><td><label><input type="checkbox" name="is_gala" value="1"> Bu bir Gala seansıdır</label></td></tr>';
        echo '</table>';

        echo '<p class="submit"><input type="submit" class="button button-primary" value="Kaydet">';
        echo '</p>';
        echo '</form>';
        echo '</div>';

        // Tablo
        echo '<h2 style="margin-top:40px;">Kayıtlı Programlar</h2>';

        // Filtre Formu
        echo '<div class="tablenav top">';
        echo '<form method="get" action="">';
        echo '<input type="hidden" name="page" value="iff-program-manager">';

        echo '<select name="filter_sehir">';
        echo '<option value="">Tüm Şehirler</option>';
        foreach ($sehirler as $s) {
            echo '<option value="' . esc_attr($s) . '" ' . selected($filter_sehir, $s, false) . '>' . esc_html($s) . '</option>';
        }
        echo '</select> ';

        echo '<select name="filter_mekan">';
        echo '<option value="">Tüm Mekanlar</option>';
        foreach ($mekanlar as $m) {
            echo '<option value="' . esc_attr($m) . '" ' . selected($filter_mekan, $m, false) . '>' . esc_html($m) . '</option>';
        }
        echo '</select> ';

        echo '<select name="filter_tarih">';
        echo '<option value="">Tüm Tarihler</option>';
        foreach ($tarihler as $t) {
            echo '<option value="' . esc_attr($t) . '" ' . selected($filter_tarih, $t, false) . '>' . esc_html($t) . '</option>';
        }
        echo '</select> ';

        echo '<select name="filter_tur">';
        echo '<option value="">Tüm Türler</option>';
        echo '<option value="film" ' . selected($filter_tur, 'film', false) . '>Film</option>';
        echo '<option value="etkinlik" ' . selected($filter_tur, 'etkinlik', false) . '>Etkinlik</option>';
        echo '</select> ';

        echo '<input type="submit" class="button" value="Filtrele">';
        echo ' <a href="?page=iff-program-manager" class="button">Temizle</a>';

        echo '</form>';
        echo '</div>';

        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>Şehir</th><th>Mekan</th><th>Tarih</th><th>Saat</th><th>Tür</th><th>Başlık</th><th>Süre</th><th>Detay</th><th>Özel</th><th>Gala</th><th>İşlemler</th></tr></thead>';
        echo '<tbody>';
        if ($results) {
            foreach ($results as $row) {
                $delete_url = wp_nonce_url(admin_url('admin-post.php?action=iff_delete_program&id=' . $row->id), 'iff_delete_program_nonce');
                echo '<tr>';
                echo '<td><strong>' . esc_html($row->sehir) . '</strong></td>';
                echo '<td>' . esc_html($row->mekan) . '</td>';
                echo '<td>' . esc_html($row->tarih) . '</td>';
                echo '<td>' . esc_html($row->saat) . '</td>';
                echo '<td><span class="tag" style="background:' . ($row->tur == 'etkinlik' ? '#red' : '#eee') . '; color:' . ($row->tur == 'etkinlik' ? 'white' : 'inherit') . '; padding:2px 6px; border-radius:3px; font-size:10px; text-transform:uppercase;">' . esc_html($row->tur) . '</span></td>';
                echo '<td>' . esc_html($row->film_adi) . '</td>';
                echo '<td>' . esc_html($row->sure) . '</td>';
                echo '<td>' . esc_html($row->etkinlik) . '</td>';
                echo '<td>' . ($row->is_special ? '<span class="dashicons dashicons-star-filled" style="color:orange;"></span>' : '') . '</td>';
                echo '<td>' . ($row->is_gala ? '<span class="dashicons dashicons-awards" style="color:red;"></span>' : '') . '</td>';
                echo '<td>
                        <a href="#" class="iff-edit-btn" data-program=\'' . esc_attr(wp_json_encode($row)) . '\'>Düzenle</a> | 
                        <a href="' . esc_url($delete_url) . '" style="color:red;" onclick="return confirm(\'Silmek istediğinize emin misiniz?\')">Sil</a>
                      </td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="10">Henüz kayıt yok.</td></tr>';
        }
        echo '</tbody></table>';

        echo '</div>';

        // Modal HTML & CSS & JS
        $this->render_edit_modal();
    }

    private function render_edit_modal()
    {
        ?>
        <style>
            #iff-edit-modal {
                display: none;
                position: fixed;
                z-index: 99999;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
                background-color: rgba(0, 0, 0, 0.6);
            }

            #iff-edit-modal .modal-content {
                background-color: #fefefe;
                margin: 5% auto;
                padding: 20px;
                border: 1px solid #888;
                width: 80%;
                max-width: 600px;
                border-radius: 8px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            }

            #iff-edit-modal .close {
                color: #aaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
                cursor: pointer;
            }

            #iff-edit-modal .close:hover,
            #iff-edit-modal .close:focus {
                color: black;
                text-decoration: none;
                cursor: pointer;
            }
        </style>

        <div id="iff-edit-modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Kaydı Düzenle</h2>
                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" id="iff-edit-form">
                    <input type="hidden" name="action" value="iff_save_program">
                    <input type="hidden" name="id" id="edit_id" value="">
                    <?php wp_nonce_field('iff_save_program_nonce'); ?>
                    <table class="form-table">
                        <tr>
                            <th>Şehir</th>
                            <td><input type="text" name="sehir" id="edit_sehir" required></td>
                        </tr>
                        <tr>
                            <th>Mekan</th>
                            <td><input type="text" name="mekan" id="edit_mekan" required></td>
                        </tr>
                        <tr>
                            <th>Tarih</th>
                            <td><input type="date" name="tarih" id="edit_tarih" required></td>
                        </tr>
                        <tr>
                            <th>Saat</th>
                            <td><input type="time" name="saat" id="edit_saat" required></td>
                        </tr>
                        <tr>
                            <th>Tür</th>
                            <td><select name="tur" id="edit_tur">
                                    <option value="film">Film</option>
                                    <option value="etkinlik">Etkinlik</option>
                                </select></td>
                        </tr>
                        <tr>
                            <th>Başlık (Film/Etkinlik Adı)</th>
                            <td><textarea name="film_adi" id="edit_film_adi" required rows="3" style="width:100%;"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>Süre</th>
                            <td><input type="text" name="sure" id="edit_sure"></td>
                        </tr>
                        <tr>
                            <th>Detay</th>
                            <td><textarea name="etkinlik" id="edit_etkinlik" rows="3" style="width:100%;"></textarea></td>
                        </tr>
                        <tr>
                            <th>Özel</th>
                            <td><label><input type="checkbox" name="is_special" id="edit_is_special" value="1"> Bu özel bir
                                    gösterimdir</label></td>
                        </tr>
                        <tr>
                            <th>Gala</th>
                            <td><label><input type="checkbox" name="is_gala" id="edit_is_gala" value="1"> Bu bir Gala
                                    seansıdır</label></td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" class="button button-primary" value="Güncelle">
                        <button type="button" class="button iff-modal-cancel">İptal</button>
                    </p>
                </form>
            </div>
        </div>

        <script>
            jQuery(document).ready(function ($) {
                var modal = $('#iff-edit-modal');

                $('.iff-edit-btn').on('click', function (e) {
                    e.preventDefault();
                    var data = $(this).data('program');

                    $('#edit_id').val(data.id);
                    $('#edit_sehir').val(data.sehir);
                    $('#edit_mekan').val(data.mekan);
                    $('#edit_tarih').val(data.tarih);
                    $('#edit_saat').val(data.saat);
                    $('#edit_tur').val(data.tur);
                    $('#edit_film_adi').val(data.film_adi);
                    $('#edit_sure').val(data.sure);
                    $('#edit_etkinlik').val(data.etkinlik);
                    $('#edit_is_special').prop('checked', data.is_special == '1');
                    $('#edit_is_gala').prop('checked', data.is_gala == '1');

                    modal.show();
                });

                $('.close, .iff-modal-cancel').on('click', function () {
                    modal.hide();
                });

                $(window).on('click', function (e) {
                    if ($(e.target).is(modal)) {
                        modal.hide();
                    }
                });
            });
        </script>
        <?php
    }

    public function handle_export()
    {
        if (!current_user_can('manage_options'))
            wp_die('Yetkisiz erişim.');
        check_admin_referer('iff_export_excel_nonce');

        global $wpdb;
        $results = $wpdb->get_results("SELECT * FROM $this->table_name ORDER BY tarih ASC, saat ASC", ARRAY_A);

        $data = [
            ['Şehir', 'Mekan', 'Tarih', 'Saat', 'Tür', 'Başlık', 'Süre', 'Detay', 'Özel', 'Gala']
        ];

        foreach ($results as $row) {
            $data[] = [
                $row['sehir'],
                $row['mekan'],
                $row['tarih'],
                $row['saat'],
                $row['tur'],
                $row['film_adi'],
                $row['sure'],
                $row['etkinlik'],
                $row['is_special'] ? '1' : '0',
                $row['is_gala'] ? '1' : '0'
            ];
        }

        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($data);
        $xlsx->downloadAs('iff_program_' . date('Y_m_d') . '.xlsx');
        exit;
    }

    public function handle_excel_upload()
    {
        if (!current_user_can('manage_options'))
            wp_die('Yetkisiz erişim.');
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

                        // Saat Temizleme ve Formatlama
                        $saat = $this->normalize_time(sanitize_text_field($row[3]));

                        // Süre Temizleme (Excel'in tarih formatına çevirdiği durumları düzelt)
                        $sure = isset($row[6]) ? sanitize_text_field($row[6]) : '';
                        if (preg_match('/1970-01-01\s+(\d{2}):(\d{2})/', $sure, $matches)) {
                            $hours = intval($matches[1]);
                            $minutes = intval($matches[2]);
                            $total_minutes = ($hours * 60) + $minutes;
                            $sure = $total_minutes . ' dk';
                        }

                        $wpdb->insert($this->table_name, [
                            'sehir' => $this->normalize_text($row[0]),
                            'mekan' => $this->normalize_text($row[1]),
                            'tarih' => $tarih,
                            'saat' => $saat,
                            'tur' => isset($row[4]) ? strtolower(sanitize_text_field($row[4])) : 'film',
                            'film_adi' => isset($row[5]) ? sanitize_text_field($row[5]) : '',
                            'sure' => $sure,
                            'etkinlik' => isset($row[7]) ? sanitize_text_field($row[7]) : '',
                            'is_special' => (isset($row[8]) && $row[8] == '1') ? 1 : 0,
                            'is_gala' => (isset($row[9]) && $row[9] == '1') ? 1 : 0,
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

    public function handle_save()
    {
        if (!current_user_can('manage_options'))
            wp_die('Yetkisiz erişim.');
        check_admin_referer('iff_save_program_nonce');

        global $wpdb;

        $data = [
            'sehir' => $this->normalize_text($_POST['sehir']),
            'mekan' => $this->normalize_text($_POST['mekan']),
            'tarih' => sanitize_text_field(wp_unslash($_POST['tarih'])),
            'saat' => $this->normalize_time(sanitize_text_field(wp_unslash($_POST['saat']))),
            'tur' => sanitize_text_field(wp_unslash($_POST['tur'])),
            'film_adi' => sanitize_textarea_field(wp_unslash($_POST['film_adi'])),
            'sure' => sanitize_text_field(wp_unslash($_POST['sure'])),
            'etkinlik' => sanitize_textarea_field(wp_unslash($_POST['etkinlik'])),
            'is_special' => isset($_POST['is_special']) ? 1 : 0,
            'is_gala' => isset($_POST['is_gala']) ? 1 : 0,
        ];

        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $wpdb->update($this->table_name, $data, ['id' => intval($_POST['id'])]);
        } else {
            $wpdb->insert($this->table_name, $data);
        }

        wp_redirect(admin_url('admin.php?page=iff-program-manager&msg=success'));
        exit;
    }

    public function handle_delete()
    {
        if (!current_user_can('manage_options'))
            wp_die('Yetkisiz erişim.');
        check_admin_referer('iff_delete_program_nonce');

        if (isset($_GET['id'])) {
            global $wpdb;
            $wpdb->delete($this->table_name, ['id' => intval($_GET['id'])]);
            wp_redirect(admin_url('admin.php?page=iff-program-manager&msg=success'));
            exit;
        }
    }

    public function mekan_page_html()
    {
        global $wpdb;

        $mekanlar = $wpdb->get_col("SELECT DISTINCT mekan FROM $this->table_name WHERE mekan != '' ORDER BY mekan ASC");

        echo '<div class="wrap">';
        echo '<h1>Mekan Yönetimi</h1>';

        if (isset($_GET['msg'])) {
            if ($_GET['msg'] == 'success')
                echo '<div class="notice notice-success is-dismissible"><p>İşlem başarılı.</p></div>';
            if ($_GET['msg'] == 'error')
                echo '<div class="notice notice-error is-dismissible"><p>Bir hata oluştu.</p></div>';
        }

        echo '<div class="card" style="max-width:100%; margin-top:20px; padding:20px;">';
        echo '<p>Bu alanda mevcut mekanların isimlerini toplu olarak değiştirebilirsiniz. Bir mekan ismini güncellediğinizde, <strong>ilgili tüm gösterim kayıtları</strong> yeni mekan ismiyle güncellenecektir.</p>';

        echo '<table class="wp-list-table widefat fixed striped" style="max-width:800px;">';
        echo '<thead><tr><th style="width:50%;">Mevcut Mekan Adı</th><th>Yeni Adı Belirle</th></tr></thead>';
        echo '<tbody>';

        if ($mekanlar) {
            foreach ($mekanlar as $mekan) {
                echo '<tr>';
                echo '<td><strong>' . esc_html($mekan) . '</strong></td>';

                echo '<td>';
                echo '<form action="' . esc_url(admin_url('admin-post.php')) . '" method="post" style="display:flex; gap:10px; margin:0;">';
                echo '<input type="hidden" name="action" value="iff_update_mekan">';
                echo '<input type="hidden" name="old_mekan" value="' . esc_attr($mekan) . '">';
                wp_nonce_field('iff_update_mekan_nonce');

                echo '<input type="text" name="new_mekan" value="' . esc_attr($mekan) . '" required style="flex:1;">';
                echo '<input type="submit" class="button button-primary" value="Güncelle">';
                echo '</form>';
                echo '</td>';

                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="2">Kayıtlı mekan bulunamadı.</td></tr>';
        }

        echo '</tbody></table>';
        echo '</div>';
        echo '</div>';
    }

    public function handle_update_mekan()
    {
        if (!current_user_can('manage_options'))
            wp_die('Yetkisiz erişim.');
        check_admin_referer('iff_update_mekan_nonce');

        global $wpdb;

        $old_mekan = stripslashes($_POST['old_mekan'] ?? '');
        $new_mekan = $this->normalize_text(wp_unslash($_POST['new_mekan'] ?? ''));

        if (!empty($old_mekan) && !empty($new_mekan) && $old_mekan !== $new_mekan) {
            $wpdb->update(
                $this->table_name,
                ['mekan' => $new_mekan],
                ['mekan' => $old_mekan]
            );
        }

        wp_redirect(admin_url('admin.php?page=iff-mekan-manager&msg=success'));
        exit;
    }

    public static function get_active_cities()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'iff_programs';
        return $wpdb->get_col("SELECT DISTINCT sehir FROM $table_name WHERE sehir != '' ORDER BY sehir ASC");
    }

    public static function get_programs($limit = 10, $tarih = '', $sehir = '', $hide_past = false)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'iff_programs';

        $where = ["1=1"];
        $params = [];

        if (!empty($tarih)) {
            $where[] = "tarih = %s";
            $params[] = $tarih;
        }

        if (!empty($sehir)) {
            $where[] = "sehir = %s";
            $params[] = $sehir;
        }

        if ($hide_past && !empty($tarih) && $tarih === current_time('Y-m-d')) {
            $where[] = "saat >= %s";
            $params[] = current_time('H:i');
        }

        $where_clause = implode(" AND ", $where);

        $query = $wpdb->prepare(
            "SELECT * FROM $table_name WHERE $where_clause ORDER BY saat ASC LIMIT %d",
            array_merge($params, [(int) $limit])
        );

        return $wpdb->get_results($query);
    }
}

new IFF_Program_Manager();
