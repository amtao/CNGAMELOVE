<?php
$list = array(
    'find /data/logs/epzjfh_log/ -type f -mtime +3 -exec rm -f {} \;',
    'find /data/logs/memcache_error_log* -type f -mtime +3 -exec rm -f {} \;',
    'find /data/logs/mysql_error_log* -type f -mtime +3 -exec rm -f {} \;',
    'find /data/logs/php_error_* -type f -mtime +3 -exec rm -f {} \;',
    'find /data/logs/server_list_empty_* -type f -mtime +3 -exec rm -f {} \;',
    'find /data/logs/mysql_slow_log* -type f -mtime +3 -exec rm -f {} \;'
);
foreach ($list as $str) {
    @system($str);
}
echo 'ok.';