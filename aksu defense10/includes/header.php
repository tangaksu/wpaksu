<?php
// aksu defense - 头部安全防护（可扩展 CSP/HSTS/X-Frame 等安全头）
if (!defined('ABSPATH')) exit;

function aksu_set_security_headers() {
    // 推荐安全头部
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: no-referrer-when-downgrade');
    header('X-XSS-Protection: 1; mode=block');
    // 可选CSP
    // header("Content-Security-Policy: default-src 'self';");
}
add_action('send_headers', 'aksu_set_security_headers', 20);