<?php
// aksu defense - IP黑白名单模块
if (!defined('ABSPATH')) exit;

function aksu_iplist_defend() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $white = get_option('wpss_ip_whitelist', '');
    $black = get_option('wpss_ip_blacklist', '');
    $white_arr = array_filter(array_map('trim', explode("\n", $white)));
    $black_arr = array_filter(array_map('trim', explode("\n", $black)));
    
    // 白名单优先
    foreach ($white_arr as $w) {
        if (aksu_ip_match($ip, $w)) return;
    }
    foreach ($black_arr as $b) {
        if (aksu_ip_match($ip, $b)) {
            if (function_exists('wpss_log')) wpss_log('ipblock', 'IP黑名单拦截: '.$ip);
            aksu_defense_die('IP Blacklisted');
        }
    }
}
function aksu_ip_match($ip, $rule) {
    $rule = str_replace(['*', '.'], ['[0-9]{1,3}', '\.'], $rule);
    return preg_match('/^'.$rule.'$/', $ip);
}
// 挂载
add_action('init', 'aksu_iplist_defend', 1);