<?php
sc_mt_options_page();

function sc_mt_options_page()
{
	global $wpdb;
	$scsz_update_flag;
	$scsz_feed_setting = '';
	$scid = '';
	// テーブルの接頭辞と名前を指定
	$scsz_table_name = $wpdb->prefix . "sc_simple_zazzle_table";
	$scsz_update_flag = isset($_GET['scid']);
	if ($scsz_update_flag) {
		$scid = $_GET['scid'];
		// 初期表示に使うフラグを設定
		$scsz_feed_setting = sc_set_feed_info($wpdb, $scsz_table_name, $scid);
		$scsz_update_flag = ! empty($scsz_feed_setting);
	}

	// POSTデータがあれば設定を更新
	if (isset($_POST['scid'])) {

		$scid = sanitize_text_field($_POST['scid']);
		$scsz_feed_title = sanitize_text_field($_POST['title']);
		$scsz_country = sanitize_text_field($_POST['country']);
		$scsz_feed_type = sanitize_text_field($_POST['type']);
		$scsz_feed_name = sanitize_text_field(preg_replace('/( |　)/', '', $_POST['feed_name']));
		$scsz_feed_default_flg = sanitize_text_field(isset($_POST['default']) ? 1 : 0);
		$scsz_feed_custom = urlencode(wp_unslash($_POST['feed_custom']));
		$scsz_feed_custom_before = urlencode(wp_unslash($_POST['feed_custom_before']));
		$scsz_feed_custom_after = urlencode(wp_unslash($_POST['feed_custom_after']));
		$scsz_feed_custom_style = urlencode(wp_unslash($_POST['feed_custom_style']));
		$scsz_phrase = sanitize_text_field($_POST['phrase']);
		$scsz_department = sanitize_text_field($_POST['department']);
		$scsz_popular_flg = sanitize_text_field(isset($_POST['popular_flg']) ? 1 : 0);
		$scsz_popular_days = sanitize_text_field(empty($_POST['popular_days']) ? 0 : $_POST['popular_days']);
		$scsz_max_num = sanitize_text_field(empty($_POST['max_num']) ? 100 : $_POST['max_num']);
		$scsz_page = sanitize_text_field(empty($_POST['page']) ? 0 : $_POST['page']);
		$scsz_background_color = sanitize_text_field(str_replace('#', '', sanitize_text_field($_POST['background_color'])));
		$scsz_affiliate_code = empty($_POST['affiliate_code']) ? "" : preg_replace('/( |　)/', '', sanitize_text_field($_POST['affiliate_code']));
		$scsz_tracking_code = empty($_POST['tracking_code']) ? "" : preg_replace('/( |　)/', '', sanitize_text_field($_POST['tracking_code']));
		$scsz_delete_affiliate_code_flg = sanitize_text_field(isset($_POST['delete_affiliate_code_flg']) ? 1 : 0);
		$scsz_update_flag = sanitize_text_field(($_POST['update_flg'] == 'true') ? true : false);
		$scsz_now_update_date = sanitize_text_field($_POST['update_date']);

		if ($scsz_update_flag) { // 更新
			$scsz_old_data_results = $wpdb->get_results("SELECT * FROM " . $scsz_table_name . " WHERE `scid` = '" . $scid . "'");

			if (empty($scsz_old_data_results)) {
				echo '<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible"><p><strong>'.__('Cannot be updated because it does not exist or has already been deleted.', 'sc-simple-zazzle').'</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">'.'Hide this notification.'.'</span></button></div>';
			} else {
				// 最終更新日時チェック
				$oldData = $scsz_old_data_results[0];
				$scsz_old_update_date = $oldData->update_date;
				if (strcmp($scsz_old_update_date, $scsz_now_update_date) != 0) {

					echo '<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible"><p><strong>'.__('It seems that the update has already been done. Please update the screen, enter the content again, and update again.', 'sc-simple-zazzle').'</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">'.'Hide this notification.'.'</span></button></div>';
				} else {
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
						'update_date' => date('Y-m-d H:i:s')
					), array(
						'scid' => $scid
					), array(
						'%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s'
					), array(
						'%d'
					));
					echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>'.__('The configuration update was successful.', 'sc-simple-zazzle').'</strong></p></div>';
					$scsz_feed_setting = sc_set_feed_info($wpdb, $scsz_table_name, $scid);
				}
			}
		} else { // 新規

			// 新しいscidを発行
			$scsz_max_id = $wpdb->get_var("SELECT max(`scid`) FROM wp_sc_simple_zazzle_table;");
			$scid = $scsz_max_id + 1;
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
				'update_date' => date('Y-m-d H:i:s')
			), array(
				'%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s'
			));
			echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>'.__('The settings have been saved successfully.', 'sc-simple-zazzle').'</strong></p></div>';
			$scsz_update_flag = true;
			$scsz_feed_setting = sc_set_feed_info($wpdb, $scsz_table_name, $scid);
		}
	}
	editHtml($scid, $scsz_update_flag, $scsz_feed_setting);
}

function sc_set_feed_info($wpdb, $scsz_table_name, $scid)
{
	$scsz_feed_settings = $wpdb->get_results("SELECT * FROM " . $scsz_table_name . " WHERE `scid` = '" . $scid . "'");
	return $scsz_feed_settings[0];
}

function editHtml($scid, $scsz_update_flag, $scsz_feed_setting){
	?>
<div class="wrap">
<a href="?page=simple-zazzle"><?php _e('Back'); ?></a>
<h2><?php _e('Short code setting', 'sc-simple-zazzle'); ?></h2>

	<form class="sc-edit-short-code" method="post" action="">

		<ul class="tab clearfix">
			<li class="active"><?php _e('Default', 'sc-simple-zazzle'); ?></li>
			<li><?php _e('Options', 'sc-simple-zazzle'); ?></li>
		</ul>
		<div class="area">
		<span style="color:#ef6340;">*</span>&ensp;<?php _e('is a required field.', 'sc-simple-zazzle'); ?>
		<div>
			<?php _e('Please check the official website for a detailed explanation of each parameter.', 'sc-simple-zazzle'); ?>&ensp;<a href="https://www.zazzle.com/sell/affiliates/promotionaltools/rss" target="_blank">RSS feeds</a><br>
			<?php _e('If the settings are incomplete, the marketplace feed is displayed.', 'sc-simple-zazzle'); ?></div>
			<div class="tab-area show">
				<table class="form-table">

					<tr>
						<th scope="row"><label class="required" for="title"><?php _e('Title', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="title" type="text" id="title" required
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->title);} else { echo 'New items';} ?>"
								class="regular-text" maxlength="50" /></td>
					</tr>
					<tr>
						<th><?php _e('Short code', 'sc-simple-zazzle'); ?></th>
						<td><?php if($scsz_update_flag){$scode = '[simple_zazzle id='.$scid.']'; echo $scode.'&emsp;<a class="text-copy" data-short-code="'.$scode.'">'.__('Copy').'</a><input type="hidden" id="outputCode">';} ?></td>
					</tr>
					<tr>
						<th scope="row"><label class="required" for="countrySelect"><?php _e('Country', 'sc-simple-zazzle'); ?></label></th>
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
								echo '<option value="'.$scsz_country_key.'" '.$scsz_select_flg.' >'.$scsz_country_val['countryName'].'</option>';
								if($scsz_country_val['collectionFeed']){
									$collectionUsables[] = $scsz_country_key;
								}
							}
							?>
						</select>
						<script type="text/javascript">const collectionUsables = [<?php foreach ($collectionUsables as $collectionUsable){echo '"'.$collectionUsable.'",';}?>];</script></td>
					</tr>
					<tr>
						<th scope="row"><label class="required" for="typeSelect"><?php _e('Type', 'sc-simple-zazzle'); ?></label></th>
						<td><select name="type" id="typeSelect">
								<option value="store"
									<?php
									if ($scsz_update_flag) {
										$scsz_select_flg = $scsz_feed_setting->feed_type == 'store' ? ' selected' : '';
										echo $scsz_select_flg;
									}
									?>><?php _e('Store', 'sc-simple-zazzle'); ?></option>
								<option value="collections"
									<?php
									if ($scsz_update_flag) {
										$scsz_select_flg = $scsz_feed_setting->feed_type == 'collections' ? ' selected' : '';
										echo $scsz_select_flg;
									}
									?>><?php _e('Collections', 'sc-simple-zazzle');?></option>
								<option value="market"
									<?php

									if ($scsz_update_flag) {
										$scsz_select_flg = $scsz_feed_setting->feed_type == 'market' ? ' selected' : '';
										echo $scsz_select_flg;
									}
									?>><?php _e('Market place', 'sc-simple-zazzle');?></option>
							</select><br><?php _e('* You cannot use the collection outside of the United States.', 'sc-simple-zazzle'); ?>
							<script type="text/javascript">const typeSelectDom = document.getElementById("typeSelect");</script></td>
					</tr>
					<tr id="hideMarket">
						<th scope="row"><label for="feedName" id="typeText"><?php _e('Store Name', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="feed_name" type="text" id="feedName"
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->feed_name);} ?>"
								class="regular-text" maxlength="50" />
								<span id="feedNameLinkWrap"></span></td>
					</tr>
					<tr>
						<th scope="row"><label for="defaultChk"><?php _e('Use HTML provided by Zazzle', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="default" type="checkbox" id="defaultChk"
									value="1"
									<?php  if($scsz_update_flag){checked( 1, $scsz_feed_setting -> feed_default_flg);} ?> /></td>
					</tr>
					<tr id="customHtmlWrap">
						<th scope="row"><?php _e('Custom HTML', 'sc-simple-zazzle') ?></th>
						<td><label for="feedCustomBefore"><?php _e('HTML to be output just before', 'sc-simple-zazzle'); ?></label>
							<textarea name="feed_custom_before" id="feedCustomBefore" maxlength="65535"
								class="large-text code" rows="3"><?php
								if($scsz_update_flag){
									echo urldecode(esc_textarea($scsz_feed_setting->feed_custom_before));
								} else {
									echo '<ul class="scsz-item-list">';
								}
								?></textarea>
							<label for="feedCustom"><?php _e('HTML for each product', 'sc-simple-zazzle'); ?></label>
							<div id="assistButtonsWrap">
							<button class="button assist-button" data-object="title"><?php _e('Product name', 'sc-simple-zazzle'); ?></button>
							<button class="button assist-button" data-object="fullTitle"><?php _e('Product name in Zazzle', 'sc-simple-zazzle'); ?></button>
							<button class="button assist-button" data-object="category"><?php _e('Category', 'sc-simple-zazzle'); ?></button>
							<button class="button assist-button" data-object="link"><?php _e('Product URL', 'sc-simple-zazzle'); ?></button>
							<button class="button assist-button" data-object="price"><?php _e('Price', 'sc-simple-zazzle'); ?></button>
							<button class="button assist-button" data-object="author"><?php _e('Author', 'sc-simple-zazzle'); ?></button>
							<button class="button assist-button" data-object="image"><?php _e('Image URL', 'sc-simple-zazzle'); ?></button>
							<button class="button assist-button" data-object="thumbnail"><?php _e('Thumbnail URL', 'sc-simple-zazzle'); ?></button>
							<button class="button assist-button" data-object="description"><?php _e('Description', 'sc-simple-zazzle'); ?></button>
							<button class="button assist-button" data-object="descriptionJs"><?php _e('Product description (HTML escape)', 'sc-simple-zazzle'); ?></button>
							<button class="button assist-button" data-object="tags"><?php _e('Tag (JavaScript array)', 'sc-simple-zazzle'); ?></button>
							<button class="button assist-button" data-object="roopIndex"><?php _e('Roop index', 'sc-simple-zazzle'); ?></button>
							</div>
							<textarea name="feed_custom" id="feedCustom" maxlength="65535"
								class="large-text code" rows="5"><?php
								if($scsz_update_flag){
									echo urldecode(esc_textarea($scsz_feed_setting->feed_custom));
								} else {
									echo '<li id="scSimpleZazzle-%roopIndex%">&#13;<a href="%link%"><img src="%thumbnail%" alt="%fullTitle%"><br>&#13;%title% %price%</a><br>&#13;%description%&#13;</li>';
								}
								?></textarea>
							<label for="feedCustomAfter"><?php _e('HTML to be output just after', 'sc-simple-zazzle'); ?></label>
							<textarea name="feed_custom_after" id="feedCustomAfter" maxlength="65535"
								class="large-text code" rows="3"><?php
								if($scsz_update_flag){
									echo urldecode(esc_textarea($scsz_feed_setting->feed_custom_after));
								} else {
									echo '</ul>';
								}
								?></textarea>
							<label for="feedCustomStyle">CSS</label>
							<textarea name="feed_custom_style" id="feedCustomStyle" maxlength="65535"
								class="large-text code" rows="3"><?php
								if($scsz_update_flag){
									echo urldecode(esc_textarea($scsz_feed_setting->feed_custom_style));
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
						<th scope="row"><label for="phrase"><?php _e('Product name search phrase', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="phrase" type="text" id="phrase" maxlength="50"
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->phrase);} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="department"><?php _e('Department ID', 'sc-simple-zazzle'); ?></label>&ensp;
						<span style="font-size:0.8em;">(<a href="https://www.zazzle.com/sell/affiliates/promotionaltools/rss#page_departmentId" target="_blank"><?php _e('Find', 'sc-simple-zazzle'); ?></a>)</span></th>
						<td><input name="department" type="text" id="department" maxlength="50"
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->department);} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="popularChk"><?php _e('View by popularity', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="popular_flg" type="checkbox" id="popularChk"
									value="1"
									<?php if($scsz_update_flag){checked( 1, $scsz_feed_setting -> popular_flg);} ?> /></td>
					</tr>
					<tr>
						<th scope="row"><label for="popularDays"><?php _e('Aggregation period by popularity', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="popular_days" type="number" id="popularDays"
									value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->popular_days);} ?>" 
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="maxNum"><?php _e('Maximum number of acquisitions', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="max_num" type="number" id="maxNum"
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->max_num);} else { echo "100";} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="page"><?php _e('Page', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="page" type="number" id="page"
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->page);} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="backgroundColor"><?php _e('Image background color', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="background_color" type="color" id="backgroundColor"
								value="<?php if($scsz_update_flag){echo '#'.esc_html($scsz_feed_setting->background_color);} else { echo "#ffffff";} ?>"
								class="regular-text" /></td>
					</tr>
					<?php if(!strcmp(get_option('scsz_affiliate_agree'), '1')) {?>
					<tr>
						<th scope="row"><label for="affiliateCode"><?php _e('Affiliate ID', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="affiliate_code" type="text" id="affiliateCode" maxlength="30"
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->affiliate_code);} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="trackingCode"><?php _e('Tracking ID', 'sc-simple-zazzle'); ?></label></th>
						<td><input name="tracking_code" type="text" id="trackingCode" maxlength="30"
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->tracking_code);} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="deleteAffiliateCodeFlg"><?php _e('Do not use affiliate queries', 'sc-simple-zazzle'); ?></label></th>
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
			const storeName = "<?php echo __('Store name', 'sc-simple-zazzle'); ?>";
			const correctionsName = "<?php echo __('Correction ID', 'sc-simple-zazzle'); ?>";
			const linkCheck = "<?php echo __('Check', 'sc-simple-zazzle'); ?>";
			const copyMsg = "<?php echo __('Copied to clipboard.', 'sc-simple-zazzle'); ?>";
			const validStoreOrCollectionsHalf = "<?php echo __('The store name / collection ID can only be entered in single-byte alphanumeric characters.', 'sc-simple-zazzle'); ?>";
			const validMaximumNumberOfAcquisitions = "<?php echo __('A numerical value from 0 to 100 can be entered in the “Maximum number of acquisitions”.', 'sc-simple-zazzle'); ?>";
			const validimageBgColor = "<?php echo __('“Image background color” can only be specified with a 6-digit hexadecimal color code.', 'sc-simple-zazzle'); ?>";
			const validAffiliateHalf = "<?php echo __('Only one-byte alphanumeric characters can be entered for the “Affiliate code”.', 'sc-simple-zazzle'); ?>";
			const validTrackingHalf = "<?php echo __('Only one-byte alphanumeric characters can be entered for the “Tracking ID”.', 'sc-simple-zazzle'); ?>";
		</script>
<?php submit_button(); ?>
<div id="validError"></div>
</form>
</div>
<?php
}
