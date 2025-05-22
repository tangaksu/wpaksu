<?php
// aksu defense - 日志模块
if (!defined('ABSPATH')) exit;

function wpss_log($type, $msg) {
    global $wpdb;
    $table = $wpdb->prefix . 'wpss_logs';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $url = $_SERVER['REQUEST_URI'] ?? '';
    $wpdb->insert($table, [
        'ip'   => $ip,
        'type' => $type,
        'msg'  => $msg,
        'ua'   => $ua,
        'url'  => $url,
        'time' => current_time('mysql'),
    ]);
}