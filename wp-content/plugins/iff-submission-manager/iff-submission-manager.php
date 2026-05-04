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
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_ajax_iff_submit_form', array($this, 'handle_submission'));
        add_action('wp_ajax_nopriv_iff_submit_form', array($this, 'handle_submission'));
        
        // SMTP Ayarları varsa PHPMailer'ı yapılandır
        add_action('phpmailer_init', array($this, 'configure_smtp'));
    }

    public function register_settings() {
        register_setting('iff_submission_settings', 'iff_webhook_enabled');
        register_setting('iff_submission_settings', 'iff_webhook_url');
        register_setting('iff_submission_settings', 'iff_email_enabled');
        register_setting('iff_submission_settings', 'iff_notification_email');
        register_setting('iff_submission_settings', 'iff_smtp_host');
        register_setting('iff_submission_settings', 'iff_smtp_port');
        register_setting('iff_submission_settings', 'iff_smtp_user');
        register_setting('iff_submission_settings', 'iff_smtp_pass');
        register_setting('iff_submission_settings', 'iff_smtp_secure');
    }

    public function configure_smtp($phpmailer) {
        $enabled = get_option('iff_email_enabled');
        if (!$enabled) return;

        $host = get_option('iff_smtp_host');
        if (!$host) return;

        $phpmailer->isSMTP();
        $phpmailer->Host       = $host;
        $phpmailer->SMTPAuth   = true;
        $phpmailer->Port       = get_option('iff_smtp_port', 587);
        $phpmailer->Username   = get_option('iff_smtp_user');
        $phpmailer->Password   = get_option('iff_smtp_pass');
        $phpmailer->SMTPSecure = get_option('iff_smtp_secure', 'tls');
        $phpmailer->From       = get_option('iff_smtp_user');
        $phpmailer->FromName   = 'IFF Website';
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

        add_submenu_page(
            'iff-submissions',
            'Ayarlar',
            'Ayarlar',
            'manage_options',
            'iff-submission-settings',
            array($this, 'render_settings_page')
        );
    }

    public function handle_submission() {
        try {
            check_ajax_referer('iff_form_nonce', 'nonce');

            $form_type = sanitize_text_field($_POST['form_type']);
            $raw_data = $_POST;
            
            unset($raw_data['action']);
            unset($raw_data['nonce']);
            
            $clean_data = array_map('sanitize_text_field', $raw_data);
            
            foreach ($_POST as $key => $value) {
                if (is_array($value)) {
                    $clean_data[$key] = implode(', ', array_map('sanitize_text_field', $value));
                }
            }

            global $wpdb;
            $inserted = $wpdb->insert($this->table_name, array(
                'form_type' => $form_type,
                'data'      => json_encode($clean_data, JSON_UNESCAPED_UNICODE)
            ));

            if ($inserted === false) {
                error_log('IFF Submission DB Insert Error: ' . $wpdb->last_error);
                throw new Exception('Veritabanı kaydı başarısız oldu.');
            }

            // 2. Webhook ve E-posta Bildirimleri (Sadece aktifse çalışır)
            if (get_option('iff_webhook_enabled')) {
                $this->send_to_webhook($clean_data, $form_type);
            }

            if (get_option('iff_email_enabled')) {
                $this->send_email_notification($clean_data, $form_type);
            }

            wp_send_json_success(array('message' => 'Başarıyla kaydedildi.'));

        } catch (Exception $e) {
            error_log('IFF Submission Error: ' . $e->getMessage());
            wp_send_json_error(array('message' => $e->getMessage()));
        }
    }

    private function send_to_webhook($data, $type) {
        $webhook_url = get_option('iff_webhook_url');
        if (!$webhook_url) return;
        
        $response = wp_remote_post($webhook_url, array(
            'method'    => 'POST',
            'timeout'   => 15,
            'body'      => json_encode(array(
                'source' => 'IFF Website',
                'type'   => $type,
                'timestamp' => current_time('mysql'),
                'data'   => $data
            )),
            'headers'   => array('Content-Type' => 'application/json'),
        ));

        if (is_wp_error($response)) {
            error_log('IFF Webhook Error: ' . $response->get_error_message());
        } else {
            $code = wp_remote_retrieve_response_code($response);
            if ($code >= 400) {
                error_log('IFF Webhook HTTP Error: ' . $code . ' - ' . wp_remote_retrieve_body($response));
            }
        }
    }

    private function send_email_notification($data, $type) {
        $to = get_option('iff_notification_email');
        if (!$to) return;

        $subject = 'Yeni Başvuru: ' . (($type == 'volunteer') ? 'Gönüllü' : 'İletişim');
        
        $message = "Yeni bir form başvurusu alındı:\n\n";
        foreach ($data as $key => $value) {
            $message .= strtoupper(str_replace('_', ' ', $key)) . ": " . $value . "\n";
        }
        $message .= "\nDetaylar için yönetim paneline bakabilirsiniz: " . admin_url('admin.php?page=iff-submissions');

        $sent = wp_mail($to, $subject, $message);
        if (!$sent) {
            error_log('IFF Email Notification Failed to: ' . $to . '. SMTP ayarlarnızı veya wp_mail yapılandırmanızı kontrol edin.');
        }
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>Başvuru Ayarları</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('iff_submission_settings');
                do_settings_sections('iff_submission_settings');
                ?>
                <div class="card" style="max-width: 800px; padding: 20px; margin-top: 20px;">
                    <h2>Genel ve Webhook Ayarları</h2>
                    <table class="form-table">
                        <tr>
                            <th>Webhook Durumu</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="iff_webhook_enabled" value="1" <?php checked(get_option('iff_webhook_enabled'), '1'); ?>>
                                    Webhook gönderimini aktif et
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>Webhook URL</th>
                            <td><input type="url" name="iff_webhook_url" value="<?php echo esc_attr(get_option('iff_webhook_url')); ?>" class="regular-text" placeholder="https://webhook.site/..."></td>
                        </tr>
                        <tr style="border-top: 1px solid #eee;">
                            <th>E-posta Bildirimi Durumu</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="iff_email_enabled" value="1" <?php checked(get_option('iff_email_enabled'), '1'); ?>>
                                    E-posta bildirimlerini aktif et
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>Bildirim E-posta Adresi</th>
                            <td><input type="email" name="iff_notification_email" value="<?php echo esc_attr(get_option('iff_notification_email')); ?>" class="regular-text" placeholder="admin@site.com"></td>
                        </tr>
                    </table>

                    <h2 style="margin-top: 40px; border-top: 1px solid #eee; pt: 20px;">SMTP Ayarları</h2>
                    <p class="description">E-posta bildirimleri aktifse ve bu ayarlar boş bırakılırsa varsayılan WordPress mail sistemi kullanılır.</p>
                    <table class="form-table">
                        <tr>
                            <th>SMTP Host</th>
                            <td><input type="text" name="iff_smtp_host" value="<?php echo esc_attr(get_option('iff_smtp_host')); ?>" class="regular-text" placeholder="smtp.gmail.com"></td>
                        </tr>
                        <tr>
                            <th>SMTP Port</th>
                            <td><input type="number" name="iff_smtp_port" value="<?php echo esc_attr(get_option('iff_smtp_port', 587)); ?>" class="small-text"></td>
                        </tr>
                        <tr>
                            <th>SMTP Kullanıcı Adı</th>
                            <td><input type="text" name="iff_smtp_user" value="<?php echo esc_attr(get_option('iff_smtp_user')); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th>SMTP Şifre</th>
                            <td><input type="password" name="iff_smtp_pass" value="<?php echo esc_attr(get_option('iff_smtp_pass')); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th>Güvenlik</th>
                            <td>
                                <select name="iff_smtp_secure">
                                    <option value="tls" <?php selected(get_option('iff_smtp_secure'), 'tls'); ?>>TLS</option>
                                    <option value="ssl" <?php selected(get_option('iff_smtp_secure'), 'ssl'); ?>>SSL</option>
                                    <option value="" <?php selected(get_option('iff_smtp_secure'), ''); ?>>Yok</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function render_admin_page() {
        global $wpdb;
        $results = $wpdb->get_results("SELECT * FROM $this->table_name ORDER BY created_at DESC LIMIT 100");
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Form Başvuruları</h1>
            <hr class="wp-header-end">

            <div class="card" style="max-width: 100%; margin-top: 20px; border-left: 4px solid #f97316;">
                <p><strong>Bilgi:</strong> Tüm başvurular hem buraya kaydedilir hem de <a href="<?php echo admin_url('admin.php?page=iff-submission-settings'); ?>">ayarlarda</a> tanımlı webhook ve e-posta adresine iletilir.</p>
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

        <!-- Detay Modal -->
        <div id="submission-modal" style="display:none; position:fixed; z-index:99999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6); overflow:auto;">
            <div style="background:#fff; margin:5% auto; padding:30px; border-radius:8px; width:90%; max-width:800px; box-shadow:0 10px 25px rgba(0,0,0,0.2); position:relative;">
                <span id="close-modal" style="position:absolute; right:20px; top:15px; font-size:28px; cursor:pointer; color:#999; line-height:1;">&times;</span>
                <h2 style="border-bottom:2px solid #f97316; padding-bottom:10px; margin-bottom:20px; font-size:20px;">Başvuru Detayları</h2>
                <div id="modal-content-inner" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap:15px; font-size:13px;">
                    <!-- Dinamik içerik -->
                </div>
                <div style="margin-top:30px; text-align:right;">
                    <button type="button" class="button button-secondary" id="close-modal-btn">Kapat</button>
                </div>
            </div>
        </div>

        <style>
            .badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
            .badge.volunteer { background: #ffedd5; color: #9a3412; border: 1px solid #fed7aa; }
            .badge.contact { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
            #modal-content-inner div { background: #f9fafb; padding: 12px; border-radius: 6px; border: 1px solid #e5e7eb; }
            #modal-content-inner strong { display: block; font-size: 10px; color: #f97316; text-transform: uppercase; margin-bottom: 5px; letter-spacing: 0.05em; }
        </style>

        <script>
        jQuery(document).ready(function($) {
            $('.view-details').on('click', function() {
                const data = $(this).data('json');
                let html = '';
                for (const [key, value] of Object.entries(data)) {
                    // Anahtar isimlerini daha okunaklı yapalım (alt çizgileri boşluk yap, baş harfleri büyük yap)
                    const label = key.replace(/_/g, ' ');
                    html += `<div>
                                <strong>${label}</strong>
                                <div style="color:#1f2937; line-height:1.5;">${value || '-'}</div>
                            </div>`;
                }
                $('#modal-content-inner').html(html);
                $('#submission-modal').fadeIn(200);
            });

            $('#close-modal, #close-modal-btn, #submission-modal').on('click', function(e) {
                if (e.target === this || e.target.id === 'close-modal' || e.target.id === 'close-modal-btn') {
                    $('#submission-modal').fadeOut(200);
                }
            });
        });
        </script>
        <?php
    }
}

new IFF_Submission_Manager();
