<?php
class WP_Architizer_API_Manager {
    private $api_keys;
    
    public function __construct() {
        $this->api_keys = get_option('wp_architizer_api_keys', array());
        
        add_action('admin_menu', array($this, 'add_api_menu'));
        add_action('admin_init', array($this, 'register_api_settings'));
        add_action('rest_api_init', array($this, 'register_custom_endpoints'));
    }

    public function add_api_menu() {
        add_submenu_page(
            'options-general.php',
            'API设置',
            'API设置',
            'manage_options',
            'wp-architizer-api',
            array($this, 'render_api_page')
        );
    }

    public function register_api_settings() {
        register_setting('wp_architizer_api_options', 'wp_architizer_api_keys');
        
        // 添加设置字段
        add_settings_section(
            'wp_architizer_api_section',
            'API密钥设置',
            null,
            'wp-architizer-api'
        );
        
        $this->add_api_field('google_maps', 'Google Maps API密钥');
        $this->add_api_field('google_translate', 'Google Translate API密钥');
        $this->add_api_field('baidu_maps', '百度地图API密钥');
        $this->add_api_field('amap', '高德地图API密钥');
    }

    private function add_api_field($key, $label) {
        add_settings_field(
            'api_key_' . $key,
            $label,
            array($this, 'render_api_field'),
            'wp-architizer-api',
            'wp_architizer_api_section',
            array('key' => $key)
        );
    }

    public function render_api_field($args) {
        $key = $args['key'];
        $value = isset($this->api_keys[$key]) ? $this->api_keys[$key] : '';
        echo '<input type="text" name="wp_architizer_api_keys[' . $key . ']" value="' . esc_attr($value) . '" class="regular-text">';
    }

    public function render_api_page() {
        ?>
        <div class="wrap">
            <h1>API设置</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('wp_architizer_api_options');
                do_settings_sections('wp-architizer-api');
                submit_button();
                ?>
            </form>
            
            <div class="api-status">
                <h2>API状态检查</h2>
                <?php $this->check_api_status(); ?>
            </div>
        </div>
        <?php
    }

    private function check_api_status() {
        foreach ($this->api_keys as $key => $value) {
            if ($value) {
                $status = $this->test_api_connection($key);
                echo '<div class="api-status-item">';
                echo '<strong>' . esc_html($key) . ':</strong> ';
                echo $status ? '<span class="status-ok">正常</span>' : '<span class="status-error">异常</span>';
                echo '</div>';
            }
        }
    }

    private function test_api_connection($api_type) {
        switch ($api_type) {
            case 'google_maps':
                return $this->test_google_maps_api();
            case 'google_translate':
                return $this->test_google_translate_api();
            case 'baidu_maps':
                return $this->test_baidu_maps_api();
            case 'amap':
                return $this->test_amap_api();
            default:
                return false;
        }
    }

    public function register_custom_endpoints() {
        // 注册地图API端点
        register_rest_route('wp-architizer/v1', '/map/geocode', array(
            'methods' => 'GET',
            'callback' => array($this, 'handle_geocode_request'),
            'permission_callback' => '__return_true',
            'args' => array(
                'address' => array(
                    'required' => true,
                    'type' => 'string'
                )
            )
        ));
        
        // 注册翻译API端点
        register_rest_route('wp-architizer/v1', '/translate', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_translate_request'),
            'permission_callback' => '__return_true',
            'args' => array(
                'text' => array(
                    'required' => true,
                    'type' => 'string'
                ),
                'target_language' => array(
                    'required' => true,
                    'type' => 'string'
                )
            )
        ));
    }

    public function handle_geocode_request($request) {
        $address = $request->get_param('address');
        $provider = $request->get_param('provider') ?: 'google';
        
        switch ($provider) {
            case 'google':
                return $this->geocode_google($address);
            case 'baidu':
                return $this->geocode_baidu($address);
            case 'amap':
                return $this->geocode_amap($address);
            default:
                return new WP_Error('invalid_provider', '不支持的地图提供商');
        }
    }

    public function handle_translate_request($request) {
        $text = $request->get_param('text');
        $target_language = $request->get_param('target_language');
        
        try {
            $translated = $this->translate_text($text, $target_language);
            return rest_ensure_response(array(
                'success' => true,
                'translated_text' => $translated
            ));
        } catch (Exception $e) {
            return new WP_Error('translation_failed', $e->getMessage());
        }
    }

    private function geocode_google($address) {
        $api_key = $this->api_keys['google_maps'];
        if (!$api_key) {
            return new WP_Error('missing_api_key', 'Google Maps API密钥未配置');
        }
        
        $url = add_query_arg(array(
            'address' => urlencode($address),
            'key' => $api_key
        ), 'https://maps.googleapis.com/maps/api/geocode/json');
        
        $response = wp_remote_get($url);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if ($body['status'] === 'OK') {
            return array(
                'lat' => $body['results'][0]['geometry']['location']['lat'],
                'lng' => $body['results'][0]['geometry']['location']['lng'],
                'formatted_address' => $body['results'][0]['formatted_address']
            );
        }
        
        return new WP_Error('geocoding_failed', '地理编码失败');
    }

    private function translate_text($text, $target_language) {
        $api_key = $this->api_keys['google_translate'];
        if (!$api_key) {
            throw new Exception('Google Translate API密钥未配置');
        }
        
        $url = 'https://translation.googleapis.com/language/translate/v2';
        $body = array(
            'q' => $text,
            'target' => $target_language,
            'key' => $api_key
        );
        
        $response = wp_remote_post($url, array(
            'body' => $body
        ));
        
        if (is_wp_error($response)) {
            throw new Exception($response->get_error_message());
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['data']['translations'][0]['translatedText'])) {
            return $body['data']['translations'][0]['translatedText'];
        }
        
        throw new Exception('翻译失败');
    }
}

// 初始化API管理器
new WP_Architizer_API_Manager(); 