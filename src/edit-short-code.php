<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (!function_exists('scsz_get_post_text')) {
    function scsz_get_post_text($key, $default = '') {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- sanitized helper, nonce verified before use
        if (!isset($_POST[$key])) {
            return $default;
        }

				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- sanitized helper, nonce verified before use
        return sanitize_text_field(wp_unslash($_POST[$key]));
    }
}

if (!function_exists('scsz_get_post_int')) {
    function scsz_get_post_int($key, $default = 0) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- sanitized helper, nonce verified before use
        if (!isset($_POST[$key]) || $_POST[$key] === '') {
            return $default;
        }

				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- sanitized helper, nonce verified before use
        return intval(wp_unslash($_POST[$key]));
    }
}

if (!function_exists('scsz_get_post_raw')) {
    /**
     * Get raw POST value (unsanitized).
     *
     * IMPORTANT:
     * This function returns unsanitized input.
     * Sanitization must be performed at the output stage.
     */
    function scsz_get_post_raw($key, $default = '') {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- sanitized helper, nonce verified before use
        if (!isset($_POST[$key])) {
            return $default;
        }

        // HTML許可
				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- sanitized helper, nonce verified before use
    		return wp_kses_post(wp_unslash($_POST[$key]));
    }
}

if (!function_exists('scsz_get_post_bool')) {
    function scsz_get_post_bool($key) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- sanitized helper, nonce verified before use
        return isset($_POST[$key]) ? 1 : 0;
    }
}

scsz_mt_options_page();

function scsz_mt_options_page()
{
	global $wpdb;
	$scsz_update_flag;
	$scsz_feed_setting = '';
	$scid = '';
	// テーブルの接頭辞と名前を指定
	$scsz_table_name = esc_sql($wpdb->prefix . "scsz_table");
	$scsz_update_flag = isset($_GET['scid']);
	if ($scsz_update_flag) {
    $scid = 0;

	if (isset($_GET['scid'])) {
    	$scid = absint( sanitize_text_field( wp_unslash( $_GET['scid'] ) ) );
	}

$scsz_update_flag = ! empty($scid);

    $scsz_feed_setting = scsz_set_feed_info($wpdb, $scsz_table_name, $scid);
    $scsz_update_flag = ! empty($scsz_feed_setting);
	}

	// POSTデータがあれば設定を更新
	if (isset($_POST['scid'])) {

    $nonce = scsz_get_post_text('scsz_nonce');

    if (!wp_verify_nonce($nonce, 'scsz_save_action')) {
        wp_die('Invalid request');
    }

    $scid = scsz_get_post_int('scid');

    $scsz_feed_title = scsz_get_post_text('title');
    $scsz_country = scsz_get_post_text('country');
    $scsz_feed_type = scsz_get_post_text('type');

    // ★ feed_name（個別処理）
    $scsz_feed_name = scsz_get_post_raw('feed_name');
    $scsz_feed_name = preg_replace('/( |　)/', '', $scsz_feed_name);
    $scsz_feed_name = sanitize_text_field($scsz_feed_name);

    $scsz_feed_default_flg = scsz_get_post_bool('default');

    $scsz_feed_custom = scsz_get_post_raw('feed_custom');
    $scsz_feed_custom_before = scsz_get_post_raw('feed_custom_before');
    $scsz_feed_custom_after = scsz_get_post_raw('feed_custom_after');
    $scsz_feed_custom_style = scsz_get_post_raw('feed_custom_style');

    $scsz_phrase = scsz_get_post_text('phrase');
    $scsz_department = scsz_get_post_text('department');

    $scsz_popular_flg = scsz_get_post_bool('popular_flg');

    $scsz_popular_days = scsz_get_post_int('popular_days', 0);
    $scsz_max_num = scsz_get_post_int('max_num', 100);
    $scsz_page = scsz_get_post_int('page', 0);

    // 色コード
    $scsz_background_color = scsz_get_post_text('background_color');
    $scsz_background_color = str_replace('#', '', $scsz_background_color);

    // affiliate系
    $scsz_affiliate_code = scsz_get_post_text('affiliate_code');
    $scsz_affiliate_code = preg_replace('/( |　)/', '', $scsz_affiliate_code);

    $scsz_tracking_code = scsz_get_post_text('tracking_code');
    $scsz_tracking_code = preg_replace('/( |　)/', '', $scsz_tracking_code);

    $scsz_delete_affiliate_code_flg = scsz_get_post_bool('delete_affiliate_code_flg');

    $scsz_update_flag = (scsz_get_post_text('update_flg') === 'true');

    $scsz_now_update_date = scsz_get_post_text('update_date');

		if ($scsz_update_flag) { // 更新
			$scsz_table_name = esc_sql($wpdb->prefix . 'sc_simple_zazzle_table');

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- and simple admin query, no caching needed
			$scsz_old_data_results = $wpdb->get_results(
    		$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is not user input
        	"SELECT * FROM {$scsz_table_name} WHERE scid = %d",
        	$scid
    		)
			);

			if (empty($scsz_old_data_results)) {
				echo '<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible"><p><strong>'.esc_html__('Cannot be updated because it does not exist or has already been deleted.', 'sc-simple-zazzle').'</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">'.'Hide this notification.'.'</span></button></div>';
			} else {
				// 最終更新日時チェック
				$oldData = $scsz_old_data_results[0];
				$scsz_old_update_date = $oldData->update_date;
				if (strcmp($scsz_old_update_date, $scsz_now_update_date) != 0) {

					echo '<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible"><p><strong>'.esc_html__('It seems that the update has already been done. Please update the screen, enter the content again, and update again.', 'sc-simple-zazzle').'</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">'.'Hide this notification.'.'</span></button></div>';
				} else {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- table name is not user input and simple admin query, no caching needed
					$wpdb->update($scsz_table_name, array(
						'title' => $scsz_feed_title,
						'country' => $scsz_country,
						'feed_type' => $scsz_feed_type,
						'feed_name' => $scsz_feed_name,
						'feed_default_flg' => $scsz_feed_default_flg,
						'feed_custom' => $scsz_feed_custom,
						'feed_custom_before' => $scsz_feed_custom_before,
						'feed_custom_after' => $scsz_feed_custom_after,
						'feed_custom_style' => $scsz_feed_custom_style,
						'phrase' => $scsz_phrase,
						'department' => $scsz_department,
						'popular_flg' => $scsz_popular_flg,
						'popular_days' => $scsz_popular_days,
						'max_num' => $scsz_max_num,
						'page' => $scsz_page,
						'background_color' => $scsz_background_color,
						'affiliate_code' => $scsz_affiliate_code,
						'delete_affiliate_code_flg' => $scsz_delete_affiliate_code_flg,
						'tracking_code' => $scsz_tracking_code,
						'update_date' => current_time('mysql')
					), array(
						'scid' => $scid
					), array(
						'%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s'
					), array(
						'%d'
					));
					echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>'.esc_html__('The configuration update was successful.', 'sc-simple-zazzle').'</strong></p></div>';
					$scsz_feed_setting = scsz_set_feed_info($wpdb, $scsz_table_name, $scid);
				}
			}
		} else { // 新規

			// 新しいscidを発行
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- table name is not user input and simple admin query, no caching needed
			$scsz_max_id = $wpdb->get_var("SELECT max(`scid`) FROM wp_scsz_table;");
			$scid = $scsz_max_id + 1;
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- table name is not user input and simple admin query, no caching needed
			$wpdb->insert($scsz_table_name, array(
				'scid' => $scid,
				'country' => $scsz_country,
				'title' => $scsz_feed_title,
				'feed_type' => $scsz_feed_type,
				'feed_name' => $scsz_feed_name,
				'feed_default_flg' => $scsz_feed_default_flg,
				'feed_custom' => $scsz_feed_custom,
				'feed_custom_before' => $scsz_feed_custom_before,
				'feed_custom_after' => $scsz_feed_custom_after,
				'feed_custom_style' => $scsz_feed_custom_style,
				'phrase' => $scsz_phrase,
				'department' => $scsz_department,
				'popular_flg' => $scsz_popular_flg,
				'popular_days' => $scsz_popular_days,
				'max_num' => $scsz_max_num,
				'page' => $scsz_page,
				'background_color' => $scsz_background_color,
				'affiliate_code' => $scsz_affiliate_code,
				'tracking_code' => $scsz_tracking_code,
				'delete_affiliate_code_flg' => $scsz_delete_affiliate_code_flg,
				'update_date' => current_time('mysql')
			), array(
				'%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s'
			));
			echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>'.esc_html__('The settings have been saved successfully.', 'sc-simple-zazzle').'</strong></p></div>';
			$scsz_update_flag = true;
			$scsz_feed_setting = scsz_set_feed_info($wpdb, $scsz_table_name, $scid);
		}
	}
	scsz_editHtml($scid, $scsz_update_flag, $scsz_feed_setting);
}

function scsz_set_feed_info($wpdb, $scsz_table_name, $scid){
	$scsz_table_name = esc_sql($wpdb->prefix . 'sc_simple_zazzle_table');

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- table name is not user input and simple admin query, no caching needed
	$scsz_feed_settings = $wpdb->get_results(
    $wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is not user input
        "SELECT * FROM {$scsz_table_name} WHERE scid = %d",
        $scid
    )
	);
	return $scsz_feed_settings[0];
}

function scsz_editHtml($scid, $scsz_update_flag, $scsz_feed_setting){
	?>
<div class="wrap">
<a href="?page=simple-zazzle"><?php esc_html_e('Back', 'sc-simple-zazzle'); ?></a>
<h2><?php esc_html_e('Short code setting', 'sc-simple-zazzle'); ?></h2>

	<form class="sc-edit-short-code" method="post" action="">
		<?php wp_nonce_field('scsz_save_action', 'scsz_nonce'); ?>

		<ul class="tab clearfix">
			<li class="active"><?php esc_html_e('Default', 'sc-simple-zazzle'); ?></li>
			<li><?php esc_html_e('Options', 'sc-simple-zazzle'); ?></li>
		</ul>
		<div class="area">
		<?php 
		/* translators: %s: required field mark */
		printf( esc_html__( '%s is a required field.', 'sc-simple-zazzle' ), '<span style="color:#ef6340;">*</span>' ); ?>
		<div>
			<?php esc_html_e('Please check the official website for a detailed explanation of each parameter.', 'sc-simple-zazzle'); ?>&ensp;<a href="https://www.zazzle.com/sell/affiliates/promotionaltools/rss" target="_blank">RSS feeds</a><br>
			<?php esc_html_e('If the settings are incomplete, the marketplace feed is displayed.', 'sc-simple-zazzle'); ?></div>
			<div class="tab-area show">
				<table class="form-table">

					<tr>
						<th scope="row"><label class="required" for="title"><?php esc_html_e('Title', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="title" type="text" id="title" required
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->title);} else { echo esc_html__( 'New item', 'sc-simple-zazzle' ); } ?>"
								class="regular-text" maxlength="50" /></td>
					</tr>
					<tr>
						<th><?php esc_html_e('Short code', 'sc-simple-zazzle'); ?></th>
						<td><?php if($scsz_update_flag){$scode = '[simple_zazzle id='.$scid.']'; echo esc_html($scode).'&emsp;<a class="text-copy" data-short-code="'.esc_attr($scode).'">'.esc_html__('Copy', 'sc-simple-zazzle').'</a><input type="hidden" id="outputCode">';} ?></td>
					</tr>
					<tr>
						<th scope="row"><label class="required" for="countrySelect"><?php esc_html_e('Country', 'sc-simple-zazzle'); ?></label></th>
						<td><select name="country" id="countrySelect">
							<?php 
							include('country-list.php');
							$collectionUsables = array();
							foreach ($scsz_country_list as $scsz_country_key => $scsz_country_val) {
								$scsz_select_flg = '';
								if ($scsz_update_flag) {
									$scsz_select_flg = $scsz_feed_setting->country == $scsz_country_key ? ' selected' : '';
								} else {
									$scsz_select_flg = $scsz_country_val['location'] == get_locale() ? ' selected' : '';
								}
								echo '<option value="'.esc_attr($scsz_country_key).'" '.esc_attr($scsz_select_flg).' >'.esc_html($scsz_country_val['countryName']).'</option>';
								if($scsz_country_val['collectionFeed']){
									$collectionUsables[] = $scsz_country_key;
								}
							}
							?>
						</select>
						<script type="text/javascript">const collectionUsables = [<?php foreach ($collectionUsables as $collectionUsable){echo '"'.esc_html($collectionUsable).'",';}?>];</script></td>
					</tr>
					<tr>
						<th scope="row"><label class="required" for="typeSelect"><?php esc_html_e('Type', 'sc-simple-zazzle'); ?></label></th>
						<td><select name="type" id="typeSelect">
								<option value="store"
									<?php
									if ($scsz_update_flag) {
										$scsz_select_flg = $scsz_feed_setting->feed_type == 'store' ? ' selected' : '';
										echo esc_html($scsz_select_flg);
									}
									?>><?php esc_html_e('Store', 'sc-simple-zazzle'); ?></option>
								<option value="collections"
									<?php
									if ($scsz_update_flag) {
										$scsz_select_flg = $scsz_feed_setting->feed_type == 'collections' ? ' selected' : '';
										echo esc_html($scsz_select_flg);
									}
									?>><?php esc_html_e('Collections', 'sc-simple-zazzle');?></option>
								<option value="market"
									<?php

									if ($scsz_update_flag) {
										$scsz_select_flg = $scsz_feed_setting->feed_type == 'market' ? ' selected' : '';
										echo esc_html($scsz_select_flg);
									}
									?>><?php esc_html_e('Market place', 'sc-simple-zazzle');?></option>
							</select><br><?php esc_html_e('* You cannot use the collection outside of the United States.', 'sc-simple-zazzle'); ?>
							<script type="text/javascript">const typeSelectDom = document.getElementById("typeSelect");</script></td>
					</tr>
					<tr id="hideMarket">
						<th scope="row"><label for="feedName" id="typeText"><?php esc_html_e('Store Name', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="feed_name" type="text" id="feedName"
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->feed_name);} ?>"
								class="regular-text" maxlength="50" />
								<span id="feedNameLinkWrap"></span></td>
					</tr>
					<tr>
						<th scope="row"><label for="defaultChk"><?php esc_html_e('Use HTML provided by Zazzle', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="default" type="checkbox" id="defaultChk"
									value="1"
									<?php  if($scsz_update_flag){checked( 1, $scsz_feed_setting -> feed_default_flg);} ?> /></td>
					</tr>
					<tr id="customHtmlWrap">
						<th scope="row"><?php esc_html_e('Custom HTML', 'sc-simple-zazzle'); ?><br></th>
						<td><label for="feedCustomBefore"><?php esc_html_e('HTML to be output just before', 'sc-simple-zazzle'); ?></label>
							<textarea name="feed_custom_before" id="feedCustomBefore" maxlength="65535"
								class="large-text code" rows="3"><?php 
								if($scsz_update_flag){
									echo esc_textarea(urldecode($scsz_feed_setting->feed_custom_before));
								} else {
									echo '<ul class="scsz-item-list">';
								}
								?></textarea>
							<label for="feedCustom"><?php esc_html_e('HTML for each product', 'sc-simple-zazzle'); ?></label>
							<div id="assistButtonsWrap">
							<button class="button assist-button" data-object="title"><?php esc_html_e('Product name', 'sc-simple-zazzle'); ?></button>
							<button class="button assist-button" data-object="fullTitle"><?php esc_html_e('Product name in Zazzle', 'sc-simple-zazzle'); ?></button>
							<button class="button assist-button" data-object="category"><?php esc_html_e('Category', 'sc-simple-zazzle'); ?></button>
							<button class="button assist-button" data-object="link"><?php esc_html_e('Product URL', 'sc-simple-zazzle'); ?></button>
							<button class="button assist-button" data-object="price"><?php esc_html_e('Price', 'sc-simple-zazzle'); ?></button>
							<button class="button assist-button" data-object="author"><?php esc_html_e('Author', 'sc-simple-zazzle'); ?></button>
							<button class="button assist-button" data-object="image"><?php esc_html_e('Image URL', 'sc-simple-zazzle'); ?></button>
							<button class="button assist-button" data-object="thumbnail"><?php esc_html_e('Thumbnail URL', 'sc-simple-zazzle'); ?></button>
							<button class="button assist-button" data-object="description"><?php esc_html_e('Description', 'sc-simple-zazzle'); ?></button>
							<button class="button assist-button" data-object="descriptionJs"><?php esc_html_e('Product description (HTML escape)', 'sc-simple-zazzle'); ?></button>
							<button class="button assist-button" data-object="tags"><?php esc_html_e('Tag (JavaScript array)', 'sc-simple-zazzle'); ?></button>
							<button class="button assist-button" data-object="roopIndex"><?php esc_html_e('Roop index', 'sc-simple-zazzle'); ?></button>
							</div>
							<textarea name="feed_custom" id="feedCustom" maxlength="65535"
								class="large-text code" rows="5"><?php
								if($scsz_update_flag){
									echo esc_textarea($scsz_feed_setting->feed_custom);
								} else {
									echo '<li id="scSimpleZazzle-%roopIndex%">&#13;<a href="%link%"><img src="%thumbnail%" alt="%fullTitle%"><br>&#13;%title% %price%</a><br>&#13;%description%&#13;</li>';
								}
								?></textarea>
							<label for="feedCustomAfter"><?php esc_html_e('HTML to be output just after', 'sc-simple-zazzle'); ?></label>
							<textarea name="feed_custom_after" id="feedCustomAfter" maxlength="65535"
								class="large-text code" rows="3"><?php
								if($scsz_update_flag){
									echo esc_textarea(urldecode($scsz_feed_setting->feed_custom_after));
								} else {
									echo '</ul>';
								}
								?></textarea>
							<label for="feedCustomStyle">CSS</label>
							<textarea name="feed_custom_style" id="feedCustomStyle" maxlength="65535"
								class="large-text code" rows="3"><?php
								if($scsz_update_flag){
									echo esc_textarea(urldecode($scsz_feed_setting->feed_custom_style));
								} else {
									echo 'ul.scsz-item-list{list-style: none;}&#13;ul.scsz-item-list li{list-style: none;margin: 5px;}';
								}
								?></textarea>
						</td>
					</tr>
				</table>
			</div>
			<div class="tab-area">
				<table class="form-table">
					<tr>
						<th scope="row"><label for="phrase"><?php esc_html_e('Product name search phrase', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="phrase" type="text" id="phrase" maxlength="50"
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->phrase);} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="department"><?php esc_html_e('Department ID', 'sc-simple-zazzle'); ?></label>&ensp;
						<span style="font-size:0.8em;">(<a href="https://www.zazzle.com/sell/affiliates/promotionaltools/rss#page_departmentId" target="_blank"><?php esc_html_e('Find', 'sc-simple-zazzle'); ?></a>)</span></th>
						<td><input name="department" type="text" id="department" maxlength="50"
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->department);} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="popularChk"><?php esc_html_e('View by popularity', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="popular_flg" type="checkbox" id="popularChk"
									value="1"
									<?php if($scsz_update_flag){checked( 1, $scsz_feed_setting -> popular_flg);} ?> /></td>
					</tr>
					<tr>
						<th scope="row"><label for="popularDays"><?php esc_html_e('Aggregation period by popularity', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="popular_days" type="number" id="popularDays"
									value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->popular_days);} ?>" 
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="maxNum"><?php esc_html_e('Maximum number of acquisitions', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="max_num" type="number" id="maxNum"
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->max_num);} else { echo "100";} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="page"><?php esc_html_e('Page', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="page" type="number" id="page"
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->page);} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="backgroundColor"><?php esc_html_e('Image background color', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="background_color" type="color" id="backgroundColor"
								value="<?php if($scsz_update_flag){echo '#'.esc_html($scsz_feed_setting->background_color);} else { echo "#ffffff";} ?>"
								class="regular-text" /></td>
					</tr>
					<?php if(!strcmp(get_option('scsz_affiliate_agree'), '1')) {?>
					<tr>
						<th scope="row"><label for="affiliateCode"><?php esc_html_e('Affiliate ID', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="affiliate_code" type="text" id="affiliateCode" maxlength="30"
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->affiliate_code);} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="trackingCode"><?php esc_html_e('Tracking ID', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="tracking_code" type="text" id="trackingCode" maxlength="30"
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->tracking_code);} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="deleteAffiliateCodeFlg"><?php esc_html_e('Do not use affiliate queries', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="delete_affiliate_code_flg" type="checkbox" id="deleteAffiliateCodeFlg"
								value="1" <?php if($scsz_update_flag){checked( 1, $scsz_feed_setting -> delete_affiliate_code_flg);} ?> /></td>
					</tr>
					<?php } ?>
				</table>
			</div>
			<input type="hidden" name="update_date"
				value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->update_date);} ?>">
			<input type="hidden" name="scid"
				value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->scid);} ?>">
			<input type="hidden" name="update_flg"
				value="<?php if($scsz_update_flag){echo 'true';} else {echo 'false';} ?>">
		</div>
		<script type="text/javascript">
			const storeName = "<?php echo esc_html__('Store name', 'sc-simple-zazzle'); ?>";
			const correctionsName = "<?php echo esc_html__('Correction ID', 'sc-simple-zazzle'); ?>";
			const linkCheck = "<?php echo esc_html__('Check', 'sc-simple-zazzle'); ?>";
			const copyMsg = "<?php echo esc_html__('Copied to clipboard.', 'sc-simple-zazzle'); ?>";
			const validStoreOrCollectionsHalf = "<?php echo esc_html__('The store name / collection ID can only be entered in single-byte alphanumeric characters.', 'sc-simple-zazzle'); ?>";
			const validMaximumNumberOfAcquisitions = "<?php echo esc_html__('A numerical value from 0 to 100 can be entered in the “Maximum number of acquisitions”.', 'sc-simple-zazzle'); ?>";
			const validimageBgColor = "<?php echo esc_html__('“Image background color” can only be specified with a 6-digit hexadecimal color code.', 'sc-simple-zazzle'); ?>";
			const validAffiliateHalf = "<?php echo esc_html__('Only one-byte alphanumeric characters can be entered for the “Affiliate code”.', 'sc-simple-zazzle'); ?>";
			const validTrackingHalf = "<?php echo esc_html__('Only one-byte alphanumeric characters can be entered for the “Tracking ID”.', 'sc-simple-zazzle'); ?>";
		</script>
<?php submit_button(); ?>
<div id="validError"></div>
</form>
</div>
<?php
}
