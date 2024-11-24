<?php
class WP_Architizer_Statistics {
    private $db;
    
    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        
        // 创建必要的数据表
        $this->create_tables();
        
        // 注册统计动作
        add_action('wp_ajax_log_statistics', array($this, 'log_statistics'));
        add_action('wp_ajax_nopriv_log_statistics', array($this, 'log_statistics'));
        
        // 添加管理菜单
        add_action('admin_menu', array($this, 'add_statistics_menu'));
        
        // 注册定时任务
        add_action('wp', array($this, 'schedule_tasks'));
        add_action('daily_statistics_calculation', array($this, 'calculate_daily_statistics'));
    }

    private function create_tables() {
        $charset_collate = $this->db->get_charset_collate();
        
        // 访问记录表
        $sql = "CREATE TABLE IF NOT EXISTS {$this->db->prefix}architizer_visits (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            page_id bigint(20) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            ip_address varchar(45) NOT NULL,
            user_agent varchar(255) NOT NULL,
            referrer varchar(255) DEFAULT NULL,
            visit_time datetime NOT NULL,
            session_id varchar(32) NOT NULL,
            device_type varchar(20) NOT NULL,
            country varchar(2) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY page_id (page_id),
            KEY visit_time (visit_time)
        ) $charset_collate;";
        
        // 互动记录表
        $sql .= "CREATE TABLE IF NOT EXISTS {$this->db->prefix}architizer_interactions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            page_id bigint(20) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            interaction_type varchar(50) NOT NULL,
            interaction_data text DEFAULT NULL,
            interaction_time datetime NOT NULL,
            session_id varchar(32) NOT NULL,
            PRIMARY KEY (id),
            KEY page_id (page_id),
            KEY interaction_time (interaction_time)
        ) $charset_collate;";
        
        // 统计汇总表
        $sql .= "CREATE TABLE IF NOT EXISTS {$this->db->prefix}architizer_statistics_summary (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            date date NOT NULL,
            page_id bigint(20) NOT NULL,
            visits int(11) NOT NULL DEFAULT 0,
            unique_visitors int(11) NOT NULL DEFAULT 0,
            avg_time_on_page float NOT NULL DEFAULT 0,
            bounce_rate float NOT NULL DEFAULT 0,
            interactions int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY date_page (date, page_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function log_statistics() {
        check_ajax_referer('statistics_nonce', 'nonce');
        
        $data = json_decode(stripslashes($_POST['data']), true);
        $type = sanitize_text_field($_POST['type']);
        
        switch ($type) {
            case 'pageview':
                $this->log_pageview($data);
                break;
            case 'interaction':
                $this->log_interaction($data);
                break;
        }
        
        wp_send_json_success();
    }

    private function log_pageview($data) {
        $this->db->insert(
            $this->db->prefix . 'architizer_visits',
            array(
                'page_id' => absint($data['page_id']),
                'user_id' => get_current_user_id(),
                'ip_address' => $this->get_client_ip(),
                'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT']),
                'referrer' => isset($_SERVER['HTTP_REFERER']) ? esc_url_raw($_SERVER['HTTP_REFERER']) : '',
                'visit_time' => current_time('mysql'),
                'session_id' => $this->get_session_id(),
                'device_type' => $this->get_device_type(),
                'country' => $this->get_visitor_country()
            )
        );
    }

    private function log_interaction($data) {
        $this->db->insert(
            $this->db->prefix . 'architizer_interactions',
            array(
                'page_id' => absint($data['page_id']),
                'user_id' => get_current_user_id(),
                'interaction_type' => sanitize_text_field($data['interaction_type']),
                'interaction_data' => json_encode($data['interaction_data']),
                'interaction_time' => current_time('mysql'),
                'session_id' => $this->get_session_id()
            )
        );
    }

    public function calculate_daily_statistics() {
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        
        // 获取所有页面
        $pages = get_posts(array(
            'post_type' => array('post', 'page', 'project', 'product'),
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        
        foreach ($pages as $page) {
            // 计算访问数据
            $visits = $this->db->get_var($this->db->prepare(
                "SELECT COUNT(*) FROM {$this->db->prefix}architizer_visits 
                WHERE page_id = %d AND DATE(visit_time) = %s",
                $page->ID, $yesterday
            ));
            
            $unique_visitors = $this->db->get_var($this->db->prepare(
                "SELECT COUNT(DISTINCT session_id) FROM {$this->db->prefix}architizer_visits 
                WHERE page_id = %d AND DATE(visit_time) = %s",
                $page->ID, $yesterday
            ));
            
            // 计算平均停留时间
            $avg_time = $this->calculate_avg_time_on_page($page->ID, $yesterday);
            
            // 计算跳出率
            $bounce_rate = $this->calculate_bounce_rate($page->ID, $yesterday);
            
            // 计算互动数
            $interactions = $this->db->get_var($this->db->prepare(
                "SELECT COUNT(*) FROM {$this->db->prefix}architizer_interactions 
                WHERE page_id = %d AND DATE(interaction_time) = %s",
                $page->ID, $yesterday
            ));
            
            // 更新或插入汇总数据
            $this->db->replace(
                $this->db->prefix . 'architizer_statistics_summary',
                array(
                    'date' => $yesterday,
                    'page_id' => $page->ID,
                    'visits' => $visits,
                    'unique_visitors' => $unique_visitors,
                    'avg_time_on_page' => $avg_time,
                    'bounce_rate' => $bounce_rate,
                    'interactions' => $interactions
                )
            );
        }
    }

    private function calculate_avg_time_on_page($page_id, $date) {
        // 计算逻辑...
        return 0;
    }

    private function calculate_bounce_rate($page_id, $date) {
        // 计算逻辑...
        return 0;
    }

    private function get_session_id() {
        if (!session_id()) {
            session_start();
        }
        return session_id();
    }

    private function get_client_ip() {
        $ip_keys = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );
        
        foreach ($ip_keys as $key) {
            if (isset($_SERVER[$key])) {
                return sanitize_text_field($_SERVER[$key]);
            }
        }
        
        return '0.0.0.0';
    }

    private function get_device_type() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', $user_agent)) {
            return 'tablet';
        }
        
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $user_agent)) {
            return 'mobile';
        }
        
        return 'desktop';
    }

    private function get_visitor_country() {
        // 使用 IP 地理位置服务
        // 这里可以集成第三方服务如 MaxMind GeoIP2
        return '';
    }

    public function add_statistics_menu() {
        add_menu_page(
            '网站统计',
            '网站统计',
            'manage_options',
            'wp-architizer-statistics',
            array($this, 'render_statistics_page'),
            'dashicons-chart-bar',
            30
        );
    }

    public function render_statistics_page() {
        // 加载统计页面视图
        include(get_template_directory() . '/inc/views/statistics.php');
    }

    public function schedule_tasks() {
        if (!wp_next_scheduled('daily_statistics_calculation')) {
            wp_schedule_event(strtotime('tomorrow 00:00:00'), 'daily', 'daily_statistics_calculation');
        }
    }
}

// 初始化统计系统
new WP_Architizer_Statistics(); 