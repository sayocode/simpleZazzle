<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function scsz_delete_shortcode($scid){

	global $wpdb;
	$scsz_table_name = $wpdb->prefix . "sc_simple_zazzle_table";
	$scsz_query_data = scsz_create_query_data($scid, $scsz_table_name);

	// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- nonce verified before calling this function and direct delete is intended here
	$wpdb->delete(
		$scsz_table_name,
		$scsz_query_data,
		array('%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%s')
	);
}

function scsz_duplication_shortcode($scid){

	global $wpdb;
	$scsz_table_name = $wpdb->prefix . "sc_simple_zazzle_table";
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- simple admin query, no caching needed
	$scsz_max_id = $wpdb->get_var("SELECT max(`scid`) FROM wp_sc_simple_zazzle_table;");

	$scsz_new_data = scsz_create_query_data($scid, $scsz_table_name);
	$scsz_new_data['scid'] = $scsz_max_id + 1;
	$scsz_new_data['title'] .= '_copy';

	// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.DB.DirectDatabaseQuery.DirectQuery -- nonce verified before calling this function, direct insert is intended here
	$wpdb->insert($scsz_table_name, $scsz_new_data, array(
		'%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%s'
	));

	echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>'
	.esc_html__('The selected short code setting was successfully duplicated.', 'sc-simple-zazzle').'</strong></p></div>';
}

function scsz_create_query_data($scid, $scsz_table_name){

	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- no direct user input, sanitized param
	global $wpdb;

	$scid = intval($scid);

	// テーブル名をエスケープ
	$table = esc_sql($scsz_table_name);

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- admin screen, small dataset, no caching needed
	$scsz_feed_settings = (array) $wpdb->get_row(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is not user input
			"SELECT * FROM {$table} WHERE scid = %d",
			$scid
		),
		ARRAY_A
	);

	$scsz_new_data = array(
		'scid' => $scid,
		'country' => $scsz_feed_settings['country'],
		'title' => $scsz_feed_settings['title'],
		'feed_type' => $scsz_feed_settings['feed_type'],
		'feed_name' => $scsz_feed_settings['feed_name'],
		'feed_default_flg' => $scsz_feed_settings['feed_default_flg'],
		'feed_custom' => $scsz_feed_settings['feed_custom'],
		'feed_custom_before' => $scsz_feed_settings['feed_custom_before'],
		'feed_custom_after' => $scsz_feed_settings['feed_custom_after'],
		'feed_custom_style' => $scsz_feed_settings['feed_custom_style'],
		'phrase' => $scsz_feed_settings['phrase'],
		'department' => $scsz_feed_settings['department'],
		'popular_flg' => $scsz_feed_settings['popular_flg'],
		'popular_days' => $scsz_feed_settings['popular_days'],
		'max_num' => $scsz_feed_settings['max_num'],
		'page' => $scsz_feed_settings['page'],
		'background_color' => $scsz_feed_settings['background_color'],
		'affiliate_code' => $scsz_feed_settings['affiliate_code'],
		'tracking_code' => $scsz_feed_settings['tracking_code']
	);

	return $scsz_new_data;
}
