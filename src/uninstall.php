<?php

function myplugin_activate()
{
	// プラグインが有効となったときにアンインストール処理をフックする
	register_uninstall_hook(__FILE__, 'myplugin_uninstall');
}
register_activation_hook(__FILE__, 'myplugin_activate');

function myplugin_uninstall()
{
	sc_uninstall_db();
}

function sc_uninstall_db()
{
	global $wpdb;
	$scsz_table_name = $wpdb->prefix . 'sc_simple_zazzle_table';
	$wpdb->query("DROP TABLE IF EXISTS ".$scsz_table_name);
	delete_option("sc_simple_zazzle_db_version");
}
