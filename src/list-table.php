<?php
scsz_create_tables();

function scsz_create_tables()
{
	global $wpdb;

	$sql = "";
	$charset_collate = "";

	// 接頭辞の追加（socal_count_cache）
	$scsz_table_name = $wpdb->prefix . 'sc_simple_zazzle_table';

	// charsetを指定する
	if (! empty($wpdb->charset))
		$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} ";

	// 照合順序を指定する（ある場合。通常デフォルトのutf8_general_ci）
	if (! empty($wpdb->collate))
		$charset_collate .= "COLLATE {$wpdb->collate}";

	// SQL文でテーブルを作る
	$sql = "
			CREATE TABLE {$scsz_table_name} (
				scid bigint(20) NOT NULL,
				title varchar(50) NOT NULL,
				country varchar(50) NOT NULL DEFAULT 'unitedStates',
				feed_type varchar(50) NOT NULL,
				feed_name varchar(50) NOT NULL,
				feed_default_flg boolean NOT NULL DEFAULT false,
				feed_custom varchar(65535),
				feed_custom_before varchar(65535),
				feed_custom_after varchar(65535),
				feed_custom_style varchar(65535),
				phrase varchar(50),
				department varchar(50),
				popular_flg boolean NOT NULL DEFAULT false,
				popular_days int(4),
				background_color varchar(6),
				max_num int(3),
				page int(3),
				affiliate_code varchar(30),
				tracking_code varchar(30),
				create_date timestamp NOT NULL DEFAULT NOW(),
				update_date timestamp NOT NULL,
				delete_affiliate_code_flg boolean NOT NULL DEFAULT false,
				PRIMARY KEY (scid)
			) {$charset_collate};";

	require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}
