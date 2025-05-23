<?php
// aksu defense - 敏感路径/扫描行为拦截
if (!defined('ABSPATH')) exit;

function aksu_scan_defend() {
    if (!get_option('wpss_fw_scan_status', 1)) return;
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $bad_paths = [
        '/phpmyadmin','/adminer','/wp-config','/wp-adminer','/www.zip','/backup.zip','/.env',
        '/shell.php','/webshell.php','/cmd.php','/info.php','/test.php','/dbadmin','/pma','/mysql'
    ];
    foreach ($bad_paths as $path) {
        if (stripos($request_uri, $path) !== false) {
            if (function_exists('wpss_log')) wpss_log('scan', "敏感路径扫描拦截: $request_uri");
            aksu_defense_die('Path forbidden', 403);
        }
    }
}
add_action('init', 'aksu_scan_defend', 5);