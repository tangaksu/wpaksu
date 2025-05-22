<?php
if (!defined('ABSPATH')) exit;

// 仪表盘页面
function aksu_dashboard_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'wpss_logs';

    $firewall_types = [
        'CC攻击防御'      => 'cc',
        'SQL/XSS注入拦截' => 'injection',
        '恶意User-Agent拦截' => 'useragent',
        'UA黑名单拦截'       => 'useragent_blacklist',
        '敏感路径扫描拦截'   => 'scan',
        'Cookie注入拦截'    => 'cookie',
        '文件上传拦截'      => 'upload',
        'URI规则拦截'      => 'uri',
    ];
    $counts = [];
    foreach ($firewall_types as $label => $type) {
        $counts[$type] = (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE type=%s", $type));
    }

    $total_logs = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table");
    $blocked_today = (int)$wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE DATE(time) = %s AND type IN ('cc','injection','useragent','useragent_blacklist','scan','cookie','upload','uri')",
        date('Y-m-d')
    ));
    $ip_blocked = (int)$wpdb->get_var("SELECT COUNT(DISTINCT ip) FROM $table WHERE type = 'ipblock'");

    $display_count = intval(get_option('wpss_log_display_count', 30));
    if ($display_count < 1) $display_count = 30;
    ?>
    <div class="wrap">
        <h1>安全防御仪表盘</h1>
        <div style="display:flex;gap:30px;margin:25px 0;flex-wrap:wrap;">
            <div class="wpss-card">
                <h2><?php echo esc_html($total_logs); ?></h2>
                <p>拦截总数</p>
            </div>
            <div class="wpss-card">
                <h2><?php echo esc_html($blocked_today); ?></h2>
                <p>今日拦截</p>
            </div>
            <div class="wpss-card">
                <h2><?php echo esc_html($ip_blocked); ?></h2>
                <p>累计封禁IP</p>
            </div>
        </div>

        <h2 style="margin-top:30px;">各防火墙分项拦截统计</h2>
        <div style="display:flex;gap:22px;flex-wrap:wrap;margin-bottom:30px;">
            <?php foreach ($firewall_types as $label => $type): ?>
                <div class="wpss-card" style="min-width:180px;">
                    <h2><?php echo esc_html($counts[$type]); ?></h2>
                    <p><?php echo esc_html($label); ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <style>
        .wpss-card { background:#fff; border:1px solid #eee; border-radius:6px; box-shadow:0 1px 3px #ddd; display:inline-block; min-width:150px; text-align:center; padding:20px 30px;}
        .wpss-card h2 { margin:0 0 10px 0; font-size:2rem; }
        </style>

        <h2>最近<?php echo esc_html($display_count); ?>条安全日志</h2>
        <table class="widefat fixed">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>时间</th>
                    <th>IP</th>
                    <th>类型</th>
                    <th>信息</th>
                    <th>请求</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $logs = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table ORDER BY id DESC LIMIT %d", $display_count));
            if ($logs) foreach ($logs as $log) {
                echo '<tr>';
                echo '<td>' . esc_html($log->id) . '</td>';
                echo '<td>' . esc_html($log->time) . '</td>';
                echo '<td>' . esc_html($log->ip) . '</td>';
                echo '<td>' . esc_html($log->type) . '</td>';
                echo '<td>' . esc_html($log->msg) . '</td>';
                echo '<td>' . esc_html($log->url) . '</td>';
                echo '</tr>';
            } else {
                echo '<tr><td colspan="6">暂无日志</td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php
}