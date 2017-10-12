<?php
/**
 *  多媒体资料文件目录设置。
 */
$admin_root = str_replace('ueditor/server/config.php', '', str_replace('\\', '/', __FILE__));
$web_root = substr($admin_root,0, strrpos($admin_root, '/',-2) + 1 );
$media_root = str_replace('web', 'media', $web_root);