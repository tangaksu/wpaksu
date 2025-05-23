<?php
// aksu defense - Cookie注入防护模块
if (!defined('ABSPATH')) exit;

function aksu_cookie_defend() {
    if (!get_option('wpss_fw_cookie_status', 1)) return;
    foreach ($_COOKIE as $k => $v) {
        if (is_array($v)) $v = join(',', $v);
        if (preg_match('/(<script|select\s+|insert\s+|update\s+|union\s+|eval\s*\(|base64_decode\s*\()/i', $v)) {
            if (function_exists('wpss_log')) wpss_log('cookie', "Cookie注入拦截: $k=$v");
            aksu_defense_die('Bad Cookie', 403);
        }
    }
}
add_action('init', 'aksu_cookie_defend', 4);