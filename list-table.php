<?php
create_tables();

function create_tables()
{
    global $wpdb;

    $sql = "";
    $charset_collate = "";

    // 接頭辞の追加（socal_count_cache）
    $table_name = $wpdb->prefix . 'sc_simple_zazzle_table';

    // charsetを指定する
    if (! empty($wpdb->charset))
        $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} ";

    // 照合順序を指定する（ある場合。通常デフォルトのutf8_general_ci）
    if (! empty($wpdb->collate))
        $charset_collate .= "COLLATE {$wpdb->collate}";

    // SQL文でテーブルを作る
    $sql = "
          CREATE TABLE {$table_name} (
               scid bigint(20) NOT NULL,
               title varchar(50) NOT NULL,
               feed_type varchar(50) NOT NULL,
               feed_name varchar(50) NOT NULL,
               feed_default_flg boolean NOT NULL,
               feed_custom varchar(65535),
               PRIMARY KEY  (scid)
          ) {$charset_collate};";

    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
