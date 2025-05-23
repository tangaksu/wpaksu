<?php
// aksu defense - URI规则拦截模块
if (!defined('ABSPATH')) exit;

// 防止误拦后台插件管理

function aksu_uri_defend() {


    if (!get_option('wpss_fw_uri_status', 1)) return;
    $uri = $_SERVER['REQUEST_URI'] ?? '';

     // 放行所有后台且有插件管理权限的已登录管理员
    if (is_admin() && is_user_logged_in() && current_user_can('activate_plugins')) {
        return;
    }

    // 路径穿越、敏感字符拦截
    if (preg_match('/(\.\.\/|\.\.\\\\|%2e%2e|%2f|%5c)/i', $uri) || strpos($uri, "\0") !== false) {
        if (function_exists('wpss_log')) wpss_log('uri', "路径穿越/敏感字符拦截: $uri");
        aksu_defense_die('Forbidden URI', 403);
    }

    // 自定义URI规则
    if (get_option('wpss_fw_uri_custom_status', 0)) {
        $rules = get_option('wpss_uri_custom_rules', '');
        if (!empty($rules)) {
            $lines = explode("\n", $rules);
            foreach ($lines as $rule) {
                $rule = trim($rule);
                if ($rule === '') continue;
                // 判断是否正则（以/开头结尾），否则为字符串
                if ($rule[0] === '/' && substr($rule, -1) === '/') {
                    if (@preg_match($rule, $uri)) {
                        if (function_exists('wpss_log')) wpss_log('uri', "自定义URI规则拦截(正则): $uri, 规则: $rule");
                        aksu_defense_die('Forbidden URI', 403);
                    }
                } else {
                    if (stripos($uri, $rule) !== false) {
                        if (function_exists('wpss_log')) wpss_log('uri', "自定义URI规则拦截: $uri, 规则: $rule");
                        aksu_defense_die('Forbidden URI', 403);
                    }
                }
            }
        }
    }
}
add_action('init', 'aksu_uri_defend', 7);