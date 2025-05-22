<?php
// aksu defense - SQL/XSS注入拦截模块
if (!defined('ABSPATH')) exit;

function aksu_injection_defend() {
    if (!get_option('wpss_fw_injection_status', 1)) return;
    $fields = array_merge($_GET, $_POST);
    foreach ($fields as $k => $v) {
        if (is_array($v)) $v = join(',', $v);
        if (preg_match('/(\b(select|union|insert|update|delete|drop|sleep|benchmark|base64_decode|eval|system|load_file|outfile)\b|<script|onerror\s*=|onload\s*=|javascript:)/i', $v)) {
            if (function_exists('wpss_log')) wpss_log('injection', "SQL/XSS注入拦截: $k=$v");
            aksu_defense_die('Bad Request', 403);
        }
    }
}
add_action('init', 'aksu_injection_defend', 2);