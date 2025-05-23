<?php
// aksu defense - 文件监控模块（快照）
if (!defined('ABSPATH')) exit;

function wpss_filemonitor_save_snapshot() {
    // 快照路径与目录
    $dir = ABSPATH;
    $ignore = ['wp-content/cache', 'wp-content/uploads', 'wp-content/backup', '.git', '.svn', 'node_modules'];
    $snapshot = [];
    aksu_filemonitor_walk($dir, $snapshot, $ignore);
    update_option('wpss_file_snapshot', json_encode($snapshot));
}
function aksu_filemonitor_walk($dir, &$snapshot, $ignore) {
    foreach (scandir($dir) as $f) {
        if ($f == '.' || $f == '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $f;
        foreach ($ignore as $ig) {
            if (stripos($path, $ig) !== false) continue 2;
        }
        if (is_dir($path)) {
            aksu_filemonitor_walk($path, $snapshot, $ignore);
        } else {
            $snapshot[$path] = @md5_file($path);
        }
    }
}