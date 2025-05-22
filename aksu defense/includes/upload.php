<?php
// aksu defense - 文件上传拦截模块
if (!defined('ABSPATH')) exit;

function aksu_upload_defend() {
    if (!get_option('wpss_fw_upload_status', 1)) return;
    // 阻止上传PHP脚本
    $block_php = get_option('wpss_fw_php_script_status', 1);
    foreach ($_FILES as $file) {
        if (empty($file['name'])) continue;
        $filename = strtolower($file['name']);
        // 检查扩展名
        if ($block_php && preg_match('/\.(php[0-9]?|phtml)$/i', $filename)) {
            if (function_exists('wpss_log')) wpss_log('upload', "文件上传拦截: $filename");
            aksu_defense_die('Blocked file upload', 403);
        }
        // 检查文件内容是否包含php代码
        if ($block_php && is_uploaded_file($file['tmp_name'])) {
            $content = @file_get_contents($file['tmp_name'], false, null, 0, 512);
            if (stripos($content, '<?php') !== false) {
                if (function_exists('wpss_log')) wpss_log('upload', "文件上传拦截（含php代码）: $filename");
                aksu_defense_die('Blocked file upload', 403);
            }
        }
    }
}
add_action('init', 'aksu_upload_defend', 8);