<?php
// aksu defense - 防火墙核心函数
if (!defined('ABSPATH')) exit;

function aksu_defense_die($msg = 'Access Denied', $code = 403) {
    status_header($code);
    header('Content-Type: text/plain; charset=utf-8');
    echo $msg;
    exit;
}