<?php
/**
 * Plugin Name: IFF Submission Manager
 * Description: İletişim ve Gönüllü başvurularını yönetir, DB'ye kaydeder ve Webhook'a gönderir.
 * Version: 1.0.0
 * Author: Antigravity
 */

if (!defined('ABSPATH')) exit;

class IFF_Submission_Manager {
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'iff_submissions';

        register_activation_hook(__FILE__, array($this, 'create_table'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_iff_submit_form', array($this, 'handle_submission'));
        add_action('wp_ajax_nopriv_iff_submit_form', array($this, 'handle_submission'));
    }

    public function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $this->table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            form_type varchar(50) NOT NULL,
            data longtext NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function add_admin_menu() {
        add_menu_page(
            'Başvurular',
            'Başvurular',
            'manage_options',
            'iff-submissions',
            array($this, 'render_admin_page'),
            'dashicons-feedback',
            26
        );
    }

    public function handle_submission() {
        check_ajax_referer('iff_form_nonce', 'nonce');

        $form_type = sanitize_text_field($_POST['form_type']);
        $raw_data = $_POST;
        
        // Hassas verileri temizle
        unset($raw_data['action']);
        unset($raw_data['nonce']);
        
        $clean_data = array_map('sanitize_text_field', $raw_data);
        
        // Array olan alanları (checkbox vb.) virgülle ayrılmış metne çevir
        foreach ($_POST as $key => $value) {
            if (is_array($value)) {
                $clean_data[$key] = implode(', ', array_map('sanitize_text_field', $value));
            }
        }

        // 1. Veri Tabanına Kaydet
        global $wpdb;
        $wpdb->insert($this->table_name, array(
            'form_type' => $form_type,
            'data'      => json_encode($clean_data, JSON_UNESCAPED_UNICODE)
        ));

        // 2. Webhook'a Gönder (Placeholder URL)
        $this->send_to_webhook($clean_data, $form_type);

        wp_send_json_success(array('message' => 'Başarıyla kaydedildi.'));
    }

    private function send_to_webhook($data, $type) {
        $webhook_url = 'https://webhook.site/placeholder'; // Buraya gerçek webhook URL'si gelecek
        
        wp_remote_post($webhook_url, array(
            'method'    => 'POST',
            'body'      => json_encode(array(
                'source' => 'IFF Website',
                'type'   => $type,
                'timestamp' => current_time('mysql'),
                'data'   => $data
            )),
            'headers'   => array('Content-Type' => 'application/json'),
        ));
    }

    public function render_admin_page() {
        global $wpdb;
        $results = $wpdb->get_results("SELECT * FROM $this->table_name ORDER BY created_at DESC LIMIT 100");
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Form Başvuruları</h1>
            <hr class="wp-header-end">

            <div class="card" style="max-width: 100%; margin-top: 20px; border-left: 4px solid #f97316;">
                <p><strong>Bilgi:</strong> Tüm başvurular hem buraya kaydedilir hem de tanımlı webhook adresine iletilir.</p>
            </div>

            <table class="wp-list-table widefat fixed striped table-view-list" style="margin-top: 20px;">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th style="width: 120px;">Tür</th>
                        <th>İçerik Özeti</th>
                        <th style="width: 180px;">Tarih</th>
                        <th style="width: 100px;">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($results): foreach ($results as $row): 
                        $data = json_decode($row->data, true);
                        $summary = isset($data['name']) ? $data['name'] : (isset($data['first_name']) ? $data['first_name'] . ' ' . $data['last_name'] : 'İsimsiz');
                        $email = isset($data['email']) ? $data['email'] : '-';
                        ?>
                        <tr>
                            <td><?php echo $row->id; ?></td>
                            <td>
                                <span class="badge <?php echo $row->form_type; ?>">
                                    <?php echo ($row->form_type == 'volunteer') ? 'Gönüllü' : 'İletişim'; ?>
                                </span>
                            </td>
                            <td>
                                <strong><?php echo esc_html($summary); ?></strong> (<?php echo esc_html($email); ?>)<br>
                                <small style="color: #666;"><?php echo mb_strimwidth(isset($data['message']) ? $data['message'] : (isset($data['notes']) ? $data['notes'] : ''), 0, 80, '...'); ?></small>
                            </td>
                            <td><?php echo $row->created_at; ?></td>
                            <td>
                                <button type="button" class="button view-details" data-json='<?php echo esc_attr($row->data); ?>'>Detaylar</button>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="5">Henüz başvuru yok.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <style>
            .badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
            .badge.volunteer { background: #ffedd5; color: #9a3412; border: 1px solid #fed7aa; }
            .badge.contact { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        </style>

        <script>
        jQuery(document).ready(function($) {
            $('.view-details').on('click', function() {
                const data = $(this).data('json');
                let details = '';
                for (const [key, value] of Object.entries(data)) {
                    details += `<strong>${key.toUpperCase()}:</strong> ${value}\n\n`;
                }
                alert(details);
            });
        });
        </script>
        <?php
    }
}

new IFF_Submission_Manager();
