<?php

function scsz_delete_shortcode($scid){

	global $wpdb;
	$scsz_table_name = $wpdb->prefix . "sc_simple_zazzle_table";
	$scsz_query_data = scsz_create_query_data($scid, $scsz_table_name);

	$wpdb->delete( $scsz_table_name, $scsz_query_data, array( '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%s' ) );

}

function scsz_duplication_shortcode($scid){

	global $wpdb;
	$scsz_table_name = $wpdb->prefix . "sc_simple_zazzle_table";
	$scsz_max_id = $wpdb->get_var("SELECT max(`scid`) FROM wp_sc_simple_zazzle_table;");

	$scsz_new_data = scsz_create_query_data($scid, $scsz_table_name);
	$scsz_new_data['scid'] = $scsz_max_id + 1;
	$scsz_new_data['title'] .= '_copy';

	$wpdb->insert($scsz_table_name, $scsz_new_data, array(
		'%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%s'
	));

	echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>'
	.__('The selected short code setting was successfully duplicated.', 'sc-simple-zazzle').'</strong></p></div>';
}

function scsz_create_query_data($scid, $scsz_table_name){
	$scid = sanitize_text_field($scid);
	global $wpdb;
	$scsz_feed_settings = (array)($wpdb->get_results("SELECT * FROM " . $scsz_table_name . " WHERE `scid` = '" . $scid . "'"))[0];

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
