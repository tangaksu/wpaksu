<?php
// aksu defense - 日志模块

if (!defined('ABSPATH')) exit;

function wpss_log($type, $msg, $url = '', $ua = '') {
    global $wpdb;
    $table = $wpdb->prefix . 'wpss_logs';
    // 检查表是否存在，防止报错
    if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) return;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $ua = $ua ?: ($_SERVER['HTTP_USER_AGENT'] ?? '');
    $url = $url ?: ($_SERVER['REQUEST_URI'] ?? '');
    $msg = wp_strip_all_tags($msg);
    $wpdb->insert($table, [
        'time' => current_time('mysql'),
        'ip'   => $ip,
        'type' => $type,
        'msg'  => $msg,
        'ua'   => $ua,
        'url'  => $url
    ]);
}