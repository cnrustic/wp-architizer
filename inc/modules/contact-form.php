<?php
class WP_Architizer_Contact_Form {
    private $db;
    
    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        
        add_action('init', array($this, 'create_tables'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_form_scripts'));
        add_action('wp_ajax_submit_contact_form', array($this, 'handle_form_submission'));
        add_action('wp_ajax_nopriv_submit_contact_form', array($this, 'handle_form_submission'));
        
        add_shortcode('contact_form', array($this, 'render_contact_form'));
        add_shortcode('contact_info', array($this, 'render_contact_info'));
    }

    public function create_tables() {
        $charset_collate = $this->db->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$this->db->prefix}contact_submissions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            form_id varchar(50) NOT NULL,
            name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(50),
            subject varchar(200),
            message text NOT NULL,
            attachment_url varchar(255),
            ip_address varchar(45),
            user_agent text,
            status varchar(20) DEFAULT 'new',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function add_admin_menu() {
        add_menu_page(
            '联系表单',
            '联系表单',
            'manage_options',
            'contact-forms',
            array($this, 'render_admin_page'),
            'dashicons-email',
            30
        );
        
        add_submenu_page(
            'contact-forms',
            '所有提交',
            '所有提交',
            'manage_options',
            'contact-submissions',
            array($this, 'render_submissions_page')
        );
        
        add_submenu_page(
            'contact-forms',
            '表单设置',
            '表单设置',
            'manage_options',
            'contact-form-settings',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings() {
        register_setting('contact_form_settings', 'contact_form_options');
        
        add_settings_section(
            'contact_form_general',
            '常规设置',
            null,
            'contact-form-settings'
        );
        
        add_settings_field(
            'email_recipient',
            '收件邮箱',
            array($this, 'render_email_recipient_field'),
            'contact-form-settings',
            'contact_form_general'
        );
        
        add_settings_field(
            'email_template',
            '邮件模板',
            array($this, 'render_email_template_field'),
            'contact-form-settings',
            'contact_form_general'
        );
        
        add_settings_field(
            'recaptcha_settings',
            'reCAPTCHA设置',
            array($this, 'render_recaptcha_fields'),
            'contact-form-settings',
            'contact_form_general'
        );
    }

    public function render_email_recipient_field() {
        $options = get_option('contact_form_options');
        ?>
        <input type="email" name="contact_form_options[email_recipient]" 
               value="<?php echo esc_attr($options['email_recipient'] ?? ''); ?>" class="regular-text">
        <p class="description">接收表单提交通知的邮箱地址</p>
        <?php
    }

    public function render_email_template_field() {
        $options = get_option('contact_form_options');
        ?>
        <textarea name="contact_form_options[email_template]" rows="10" class="large-text"><?php 
            echo esc_textarea($options['email_template'] ?? ''); 
        ?></textarea>
        <p class="description">
            可用变量: {name}, {email}, {phone}, {subject}, {message}<br>
            HTML格式支持
        </p>
        <?php
    }

    public function render_recaptcha_fields() {
        $options = get_option('contact_form_options');
        ?>
        <p>
            <label>Site Key:</label><br>
            <input type="text" name="contact_form_options[recaptcha_site_key]" 
                   value="<?php echo esc_attr($options['recaptcha_site_key'] ?? ''); ?>" class="regular-text">
        </p>
        <p>
            <label>Secret Key:</label><br>
            <input type="text" name="contact_form_options[recaptcha_secret_key]" 
                   value="<?php echo esc_attr($options['recaptcha_secret_key'] ?? ''); ?>" class="regular-text">
        </p>
        <p class="description">
            从 <a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA</a> 获取密钥
        </p>
        <?php
    }

    public function handle_form_submission() {
        check_ajax_referer('contact_form_nonce', 'nonce');
        
        $form_data = array(
            'form_id' => sanitize_text_field($_POST['form_id']),
            'name' => sanitize_text_field($_POST['name']),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'subject' => sanitize_text_field($_POST['subject']),
            'message' => sanitize_textarea_field($_POST['message']),
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT']
        );
        
        // 验证reCAPTCHA
        $options = get_option('contact_form_options');
        if (!empty($options['recaptcha_secret_key'])) {
            $recaptcha_response = $_POST['g-recaptcha-response'];
            $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
            $verify_data = array(
                'secret' => $options['recaptcha_secret_key'],
                'response' => $recaptcha_response,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            );
            
            $response = wp_remote_post($verify_url, array('body' => $verify_data));
            
            if (is_wp_error($response)) {
                wp_send_json_error('reCAPTCHA验证失败');
                return;
            }
            
            $result = json_decode(wp_remote_retrieve_body($response), true);
            if (!$result['success']) {
                wp_send_json_error('请完成人机验证');
                return;
            }
        }
        
        // 处理文件上传
        if (!empty($_FILES['attachment'])) {
            if (!function_exists('wp_handle_upload')) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
            }
            
            $uploaded_file = wp_handle_upload($_FILES['attachment'], array('test_form' => false));
            
            if (!empty($uploaded_file['url'])) {
                $form_data['attachment_url'] = $uploaded_file['url'];
            }
        }
        
        // 保存到数据库
        $inserted = $this->db->insert(
            $this->db->prefix . 'contact_submissions',
            $form_data,
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );
        
        if (!$inserted) {
            wp_send_json_error('提交失败，请稍后重试');
            return;
        }
        
        // 发送邮件通知
        $this->send_notification_email($form_data);
        
        wp_send_json_success('表单提交成功！我们会尽快与您联系。');
    }

    private function send_notification_email($form_data) {
        $options = get_option('contact_form_options');
        $recipient = $options['email_recipient'] ?? get_option('admin_email');
        $template = $options['email_template'] ?? $this->get_default_email_template();
        
        // 替换模板变量
        $message = str_replace(
            array('{name}', '{email}', '{phone}', '{subject}', '{message}'),
            array(
                $form_data['name'],
                $form_data['email'],
                $form_data['phone'],
                $form_data['subject'],
                $form_data['message']
            ),
            $template
        );
        
        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        wp_mail(
            $recipient,
            '新的联系表单提交 - ' . $form_data['subject'],
            $message,
            $headers
        );
    }

    private function get_default_email_template() {
        return '
            <h2>新的联系表单提交</h2>
            <p><strong>姓名:</strong> {name}</p>
            <p><strong>邮箱:</strong> {email}</p>
            <p><strong>电话:</strong> {phone}</p>
            <p><strong>主题:</strong> {subject}</p>
            <p><strong>消息:</strong><br>{message}</p>
        ';
    }

    public function render_contact_form($atts) {
        $atts = shortcode_atts(array(
            'id' => 'default',
            'title' => '联系我们',
            'description' => '请填写以下表单，我们会尽快回复您。',
            'submit_text' => '提交',
            'success_message' => '表单提交成功！我们会尽快与您联系。',
            'error_message' => '提交失败，请稍后重试。'
        ), $atts);
        
        $options = get_option('contact_form_options');
        
        ob_start();
        ?>
        <div class="contact-form-wrapper" id="contact-form-<?php echo esc_attr($atts['id']); ?>">
            <h2 class="form-title"><?php echo esc_html($atts['title']); ?></h2>
            <p class="form-description"><?php echo esc_html($atts['description']); ?></p>
            
            <form class="contact-form" method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('contact_form_nonce', 'contact_form_nonce'); ?>
                <input type="hidden" name="form_id" value="<?php echo esc_attr($atts['id']); ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">姓名 <span class="required">*</span></label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">邮箱 <span class="required">*</span></label>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">电话</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">主题 <span class="required">*</span></label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="message">消息 <span class="required">*</span></label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="attachment">附件</label>
                    <input type="file" id="attachment" name="attachment">
                    <p class="description">支持的文件类型: PDF, DOC, DOCX, JPG, PNG (最大2MB)</p>
                </div>
                
                <?php if (!empty($options['recaptcha_site_key'])) : ?>
                    <div class="form-group">
                        <div class="g-recaptcha" 
                             data-sitekey="<?php echo esc_attr($options['recaptcha_site_key']); ?>">
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <button type="submit" class="submit-button">
                        <?php echo esc_html($atts['submit_text']); ?>
                    </button>
                </div>
            </form>
            
            <div class="form-messages">
                <div class="success-message" style="display: none;">
                    <?php echo esc_html($atts['success_message']); ?>
                </div>
                <div class="error-message" style="display: none;">
                    <?php echo esc_html($atts['error_message']); ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_contact_info($atts) {
        $atts = shortcode_atts(array(
            'title' => '联系方式',
            'address' => '',
            'phone' => '',
            'email' => '',
            'hours' => '',
            'show_map' => 'yes',
            'map_latitude' => '',
            'map_longitude' => ''
        ), $atts);
        
        ob_start();
        ?>
        <div class="contact-info">
            <h2 class="info-title"><?php echo esc_html($atts['title']); ?></h2>
            
            <?php if ($atts['address']) : ?>
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div class="info-content">
                        <h3>地址</h3>
                        <p><?php echo esc_html($atts['address']); ?></p>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($atts['phone']) : ?>
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <div class="info-content">
                        <h3>电话</h3>
                        <p><a href="tel:<?php echo esc_attr($atts['phone']); ?>">
                            <?php echo esc_html($atts['phone']); ?>
                        </a></p>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($atts['email']) : ?>
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <div class="info-content">
                        <h3>邮箱</h3>
                        <p><a href="mailto:<?php echo esc_attr($atts['email']); ?>">
                            <?php echo esc_html($atts['email']); ?>
                        </a></p>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($atts['hours']) : ?>
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <div class="info-content">
                        <h3>营业时间</h3>
                        <p><?php echo nl2br(esc_html($atts['hours'])); ?></p>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($atts['show_map'] === 'yes' && $atts['map_latitude'] && $atts['map_longitude']) : ?>
                <div class="contact-map" 
                     data-latitude="<?php echo esc_attr($atts['map_latitude']); ?>"
                     data-longitude="<?php echo esc_attr($atts['map_longitude']); ?>">
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}

// 初始化联系表单模块
new WP_Architizer_Contact_Form(); 