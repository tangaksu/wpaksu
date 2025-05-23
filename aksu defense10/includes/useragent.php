<?php
// aksu defense - 恶意User-Agent防御
if (!defined('ABSPATH')) exit;

function aksu_useragent_defend() {
    if (!get_option('wpss_fw_useragent_status', 1)) return;
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

    // 固定黑名单
    $bad_ua_patterns = [
        '/sqlmap/i','/acunetix/i','/nmap/i','/Nikto/i','/w3af/i','/python-requests/i','/curl\//i',
        '/HTTrack/i','/ZmEu/i','/nessus/i','/masscan/i','/dirbuster/i','/fuzz/i','/zmeu/i',
        '/hydra/i','/sqlninja/i','/webshag/i','/webtrends/i','/AppScan/i','/WinHttpRequest/i','/Indy Library/i'
    ];
    foreach ($bad_ua_patterns as $pattern) {
        if (preg_match($pattern, $ua)) {
            if (function_exists('wpss_log')) wpss_log('useragent', "恶意User-Agent: $ua");
            aksu_defense_die('Bad UserAgent', 403);
        }
    }

    // 2. 自定义User-Agent黑名单
    $custom = trim(get_option('wpss_ua_blacklist', ''));
    if ($custom) {
        $rules = explode('|', $custom);
        foreach ($rules as $rule) {
            $pattern = trim($rule);
            if ($pattern === '') continue;
            $pattern_regex = '/^' . str_replace(['*', '/'], ['.*', '\/'], preg_quote($pattern, '/')) . '$/i';
            if (preg_match($pattern_regex, $ua)) {
                if (function_exists('wpss_log')) wpss_log('useragent_blacklist', "自定义User-Agent黑名单拦截: $ua, 规则: $pattern");
                aksu_defense_die('Bad UserAgent', 403);
            }
        }
    }
}
add_action('init', 'aksu_useragent_defend', 4);