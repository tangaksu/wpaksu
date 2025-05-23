<?php
// aksu defense - CC攻击防护模块
if (!defined('ABSPATH')) exit;

function aksu_cc_defend() {
    if (!get_option('wpss_fw_cc_status', 1)) return;

    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $limit = intval(get_option('wpss_cc_limit', 60));
    $period = intval(get_option('wpss_cc_period', 60));
    $blocktime = intval(get_option('wpss_cc_blocktime', 1800));
    $key = 'aksu_cc_' . md5($ip . $_SERVER['HTTP_HOST']);

    $now = time();
    $cc_data = get_transient($key);
    if (!$cc_data) {
        $cc_data = ['start' => $now, 'count' => 1, 'blocked' => 0];
    } else {
        if (!empty($cc_data['blocked']) && $now < $cc_data['blocked']) {
            if (function_exists('wpss_log')) wpss_log('cc', "CC攻击防护，已封锁: $ip");
            aksu_defense_die('CC Blocked', 403);
        }
        if ($now - $cc_data['start'] > $period) {
            $cc_data = ['start' => $now, 'count' => 1, 'blocked' => 0];
        } else {
            $cc_data['count']++;
            if ($cc_data['count'] > $limit) {
                $cc_data['blocked'] = $now + $blocktime;
                set_transient($key, $cc_data, $blocktime);
                if (function_exists('wpss_log')) wpss_log('cc', "CC攻击检测，自动封锁: $ip");
                aksu_defense_die('CC Blocked', 403);
            }
        }
    }
    set_transient($key, $cc_data, $period);
}
add_action('init', 'aksu_cc_defend', 3);