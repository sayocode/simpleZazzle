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
		$scsz_feed_type = sanitize_text_field($_POST['type']);
		$scsz_feed_name = sanitize_text_field(preg_replace('/( |　)/', '', $_POST['feed_name']));
		$scsz_feed_default_flg = sanitize_text_field(isset($_POST['default']) ? 1 : 0);
		$scsz_feed_custom = substr_replace(esc_url(urlencode(wp_unslash($_POST['feed_custom']))), '' , 0, 7 );
		$scsz_feed_custom_before = substr_replace(esc_url(urlencode(wp_unslash($_POST['feed_custom_before']))), '' , 0, 7 );
		$scsz_feed_custom_after = substr_replace(esc_url(urlencode(wp_unslash($_POST['feed_custom_after']))), '' , 0, 7 );
		$scsz_phrase = sanitize_text_field($_POST['phrase']);
		$scsz_department = sanitize_text_field($_POST['department']);
		$scsz_popular_flg = sanitize_text_field(isset($_POST['popular_flg']) ? 1 : 0);
		$scsz_popular_days = sanitize_text_field(empty($_POST['popular_days']) ? 0 : $_POST['popular_days']);
		$scsz_max_num = sanitize_text_field(empty($_POST['max_num']) ? 100 : $_POST['max_num']);
		$scsz_page = sanitize_text_field(empty($_POST['page']) ? 0 : $_POST['page']);
		$scsz_background_color = sanitize_text_field(str_replace('#', '', sanitize_text_field($_POST['background_color'])));
		$scsz_affiliate_code = preg_replace('/( |　)/', '', sanitize_text_field($_POST['affiliate_code']));
		$scsz_tracking_code = preg_replace('/( |　)/', '', sanitize_text_field($_POST['tracking_code']));
		$scsz_update_flag = sanitize_text_field(($_POST['update_flg'] == 'true') ? true : false);
		$scsz_now_update_date = sanitize_text_field($_POST['update_date']);

		if ($scsz_update_flag) { // 更新
			$scsz_old_data_results = $wpdb->get_results("SELECT * FROM " . $scsz_table_name . " WHERE `scid` = '" . $scid . "'");

			if (empty($scsz_old_data_results)) {
                // 存在しないか、すでに削除されたデータのため、更新できません。
				echo '<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible"><p><strong>'.'Cannot be updated because it does not exist or has already been deleted.'.'</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">'.'Hide this notification.'.'</span></button></div>';
			} else {
				// 最終更新日時チェック
				$oldData = $scsz_old_data_results[0];
				$scsz_old_update_date = $oldData->update_date;
				if (strcmp($scsz_old_update_date, $scsz_now_update_date) != 0) {

					echo '<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible"><p><strong>'.'It seems that the update has already been done. Please update the screen, enter the content again, and update again.'.'</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">'.'Hide this notification.'.'</span></button></div>';
				} else {
					$wpdb->update($scsz_table_name, array(
						'title' => $scsz_feed_title,
						'feed_type' => $scsz_feed_type,
						'feed_name' => $scsz_feed_name,
						'feed_default_flg' => $scsz_feed_default_flg,
						'feed_custom' => $scsz_feed_custom,
						'feed_custom_before' => $scsz_feed_custom_before,
						'feed_custom_after' => $scsz_feed_custom_after,
						'phrase' => $scsz_phrase,
						'department' => $scsz_department,
						'popular_flg' => $scsz_popular_flg,
						'popular_days' => $scsz_popular_days,
						'max_num' => $scsz_max_num,
						'page' => $scsz_page,
						'background_color' => $scsz_background_color,
						'affiliate_code' => $scsz_affiliate_code,
						'tracking_code' => $scsz_tracking_code,
						'update_date' => date('Y-m-d H:i:s')
					), array(
						'scid' => $scid
					), array(
						'%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s'
					), array(
						'%d'
					));
					echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>'.'The configuration update was successful.'.'</strong></p></div>';
					$scsz_feed_setting = sc_set_feed_info($wpdb, $scsz_table_name, $scid);
				}
			}
		} else { // 新規

			// 新しいscidを発行
			$scsz_max_id = $wpdb->get_var("SELECT max(`scid`) FROM wp_sc_simple_zazzle_table;");
			$scid = $scsz_max_id + 1;
			$wpdb->insert($scsz_table_name, array(
				'scid' => $scid,
				'title' => $scsz_feed_title,
				'feed_type' => $scsz_feed_type,
				'feed_name' => $scsz_feed_name,
				'feed_default_flg' => $scsz_feed_default_flg,
				'feed_custom' => $scsz_feed_custom,
				'feed_custom_before' => $scsz_feed_custom_before,
				'feed_custom_after' => $scsz_feed_custom_after,
				'phrase' => $scsz_phrase,
				'department' => $scsz_department,
				'popular_flg' => $scsz_popular_flg,
				'popular_days' => $scsz_popular_days,
				'max_num' => $scsz_max_num,
				'page' => $scsz_page,
				'background_color' => $scsz_background_color,
				'affiliate_code' => $scsz_affiliate_code,
				'tracking_code' => $scsz_tracking_code,
				'update_date' => date('Y-m-d H:i:s')
			), array(
				'%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s'
			));
			echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>'.'The settings have been saved successfully.'.'</strong></p></div>';
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
<a href="?page=simple-zazzle"><?php echo 'Back'; ?></a>
<h2><?php echo 'Short code setting'; //ショートコード 設定 ?></h2>

	<form class="sc-edit-short-code" method="post" action="">

		<ul class="tab clearfix">
			<li class="active"><?php echo 'Default'; //基本設定 ?></li>
			<li><?php echo 'Options'; //オプション ?></li>
		</ul>
		<div class="area">
        <span style="color:#ef6340;">*</span>&ensp;<?php echo 'is a required field.'; //は必須項目です。 ?>
		<div>
            <?php echo 'Please check the official website for a detailed explanation of each parameter.'; // 各パラメータの詳しい説明は公式サイトをご確認ください。 ?>&ensp;<a href="https://www.zazzle.com/sell/affiliates/promotionaltools/rss" target="_blank">RSS feeds</a><br>
            <?php echo 'If the settings are incomplete, the marketplace feed is displayed.'; // 設定に不備があった場合、マーケットプレイスのFeedが表示されます。 ?></div>
			<div class="tab-area show">
				<table class="form-table">

					<tr>
                        <th scope="row"><label class="required" for="title"><?php echo 'Title'; // タイトル ?></label></th>
						<td><input name="title" type="text" id="title" required
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->title);} else { echo 'New items';} ?>"
								class="regular-text" maxlength="50" /></td>
					</tr>
					<tr>
						<th><?php echo 'Short code'; // ショートコード ?></th>
						<td><?php if($scsz_update_flag){$scode = '[simple_zazzle id='.$scid.']'; echo $scode.'&emsp;<a class="text-copy" data-short-code="'.$scode.'">'.'Copy'.'</a><input type="hidden" id="outputCode">';} ?></td>
					</tr>
					<tr>
						<th scope="row"><label class="required" for="typeSelect"><?php echo 'Type'; // 種別 ?></label></th>
						<td><select name="type" id="typeSelect">
								<option value="store"
									<?php

									if ($scsz_update_flag) {
										$selectFlg = $scsz_feed_setting->feed_type == 'store' ? ' selected' : '';
										echo $selectFlg;
									}
									?>><?php echo 'Store'; ?></option>
								<!-- <option value="collections"
									<?php
									if ($scsz_update_flag) {
										$selectFlg = $scsz_feed_setting->feed_type == 'collections' ? ' selected' : '';
										echo $selectFlg;
									}
									?>><?php echo 'Collections';?></option> -->
								<option value="market"
									<?php

									if ($scsz_update_flag) {
										$selectFlg = $scsz_feed_setting->feed_type == 'market' ? ' selected' : '';
										echo $selectFlg;
									}
									?>><?php echo 'Market place';?></option>
                            </select><br><?php echo '* Since the RSS of the collection cannot be acquired normally, the provision of the function has been suspended.'; // ※コレクションのRSSが現在正常に取得できないため、機能の提供を一時停止しております。 ?></td>
					</tr>
					<tr id="hideMarket">
						<th scope="row"><label for="feedName" id="typeText"><?php echo 'Store Name'; //ストア名 ?></label></th>
						<td><input name="feed_name" type="text" id="feedName"
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->feed_name);} ?>"
								class="regular-text" maxlength="50" />
								<span id="feedNameLinkWrap"></span></td>
					</tr>
					<tr>
                        <th scope="row"><label for="defaultChk"><?php echo 'Use HTML provided by Zazzle'; //Zazzleが提供しているHTMLを利用する ?></label></th>
						<td><input name="default" type="checkbox" id="defaultChk"
									value="1"
									<?php  if($scsz_update_flag){checked( 1, $scsz_feed_setting -> feed_default_flg);} ?> /></td>
					</tr>
					<tr id="customHtmlWrap">
                        <th scope="row"><?php echo 'Custom HTML'; //カスタムHTML ?></th>
                        <td><label for="feedCustomBefore"><?php echo 'HTML to be output just before'; //直前に出力するHTML ?></label>
							<textarea name="feed_custom_before" id="feedCustomBefore" maxlength="65535"
								class="large-text code" rows="3"><?php
								if($scsz_update_flag){
									echo urldecode(esc_textarea($scsz_feed_setting->feed_custom_before));
								} else {
									echo '<ul>';
								}
								?></textarea>
							<label for="feedCustom"><?php echo 'HTML for each product'; //商品ごとのHTML?></label>
							<div>
							<button class="button assist-button" data-object="title"><?php echo 'Product name'; //商品名?></button>
							<button class="button assist-button" data-object="fullTitle"><?php echo 'Product name in Zazzle'; //Zazzleでの商品名?></button>
							<button class="button assist-button" data-object="category"><?php echo 'Category'; //カテゴリー?></button>
							<button class="button assist-button" data-object="link"><?php echo 'Product URL'; //商品URL?></button>
							<button class="button assist-button" data-object="price"><?php echo 'Price'; //値段?></button>
							<button class="button assist-button" data-object="author"><?php echo 'Author'; //作者?></button>
							<button class="button assist-button" data-object="image"><?php echo 'Image URL'; //画像URL?></button>
							<button class="button assist-button" data-object="thumbnail"><?php echo 'Thumbnail URL'; //サムネイルURL?></button>
							<button class="button assist-button" data-object="description"><?php echo 'Description'; //商品説明?></button>
							<button class="button assist-button" data-object="descriptionJs"><?php echo 'Product description (HTML escape)'; //商品説明（HTMLエスケープ）?></button>
							<button class="button assist-button" data-object="tags"><?php echo 'Tag (JavaScript array)'; //タグ（JavaScript配列）?></button>
							<button class="button assist-button" data-object="roopIndex"><?php echo 'Roop index'; //ループインデックス?></button>
							</div>
							<textarea name="feed_custom" id="feedCustom" maxlength="65535"
								class="large-text code" rows="5"><?php
								if($scsz_update_flag){
									echo urldecode(esc_textarea($scsz_feed_setting->feed_custom));
								} else {
									echo '<li id="scSimpleZazzle-%roopIndex%">&#13;<a href="%link%"><img src="%thumbnail%" alt="%fullTitle%"><br>&#13;%title% %price%</a><br>&#13;%description%&#13;</li>';
								}
								?></textarea>
							<label for="feedCustomAfter"><?php echo 'HTML to be output just after'; //直後に出力するHTML ?></label>
							<textarea name="feed_custom_after" id="feedCustomAfter" maxlength="65535"
								class="large-text code" rows="3"><?php
								if($scsz_update_flag){
									echo urldecode(esc_textarea($scsz_feed_setting->feed_custom_after));
								} else {
									echo '</ul>';
								}
								?></textarea>
						</td>
					</tr>
				</table>
			</div>
			<div class="tab-area">
				<table class="form-table">
					<tr>
						<th scope="row"><label for="phrase"><?php echo 'Product name search phrase'; //商品名の検索フレーズ ?></label></th>
						<td><input name="phrase" type="text" id="phrase" maxlength="50"
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->phrase);} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="department"><?php echo 'Department ID'; //部門ID ?></label>&ensp;
                        <span style="font-size:0.8em;">(<a href="https://www.zazzle.com/sell/affiliates/promotionaltools/rss#page_departmentId" target="_blank"><?php echo 'Find'; //検索 ?></a>)</span></th>
						<td><input name="department" type="text" id="department" maxlength="50"
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->department);} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="popularChk"><?php echo 'View by popularity'; //人気順に表示する ?></label></th>
						<td><input name="popular_flg" type="checkbox" id="popularChk"
									value="1"
									<?php if($scsz_update_flag){checked( 1, $scsz_feed_setting -> popular_flg);} ?> /></td>
					</tr>
					<tr>
						<th scope="row"><label for="popularDays"><?php echo 'Aggregation period by popularity'; //人気順の集計期間（日） ?></label></th>
						<td><input name="popular_days" type="number" id="popularDays"
									value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->popular_days);} ?>" 
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="maxNum"><?php echo 'Maximum number of acquisitions'; //取得数上限 ?></label></th>
						<td><input name="max_num" type="number" id="maxNum"
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->max_num);} else { echo "100";} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="page"><?php echo 'Page'; //ページ ?></label></th>
						<td><input name="page" type="number" id="page"
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->page);} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="backgroundColor"><?php echo 'Image background color'; //画像の背景色 ?></label></th>
						<td><input name="background_color" type="color" id="backgroundColor"
								value="<?php if($scsz_update_flag){echo '#'.esc_html($scsz_feed_setting->background_color);} else { echo "#ffffff";} ?>"
								class="regular-text" /></td>
					</tr>
					<?php if(!strcmp(get_option('scsz_affiliate_agree'), '1')) {?>
					<tr>
						<th scope="row"><label for="affiliateCode"><?php echo 'Affiliate ID'; // アフィリエイトコード ?></label></th>
						<td><input name="affiliate_code" type="text" id="affiliateCode" maxlength="30"
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->affiliate_code);} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="trackingCode"><?php echo 'Tracking ID'; // トラッキングコード ?></label></th>
						<td><input name="tracking_code" type="text" id="trackingCode" maxlength="30"
								value="<?php if($scsz_update_flag){echo esc_html($scsz_feed_setting->tracking_code);} ?>"
								class="regular-text" /></td>
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
            const storeName = "<?php echo 'Store Name'; //ストア名 ?>";
            const correctionsName = "<?php echo 'Corrections Name'; //コレクション名 ?>";
            const linkCheck = "<?php echo 'Check'; //確認 ?>";
            const copyMsg = "<?php echo 'Copied.'; //コピーしました。 ?>";
            const validStoreOrCollectionsHalf = "<?php echo 'The store name / collection name can only be entered in single-byte alphanumeric characters.'; //「ストア名」 / 「コレクション名」は半角英数で入力してください。 ?>";
            const validMaximumNumberOfAcquisitions = "<?php echo 'A numerical value from 0 to 100 can be entered in the “Maximum number of acquisitions”.'; //「取得数上限」は0から100までの数値を入力できます。 ?>";
            const validimageBgColor = "<?php echo '“Image background color” can only be specified with a 6-digit hexadecimal color code.'; //「画像の背景色」は16進数6桁のカラーコードでのみ指定できます。 ?>";
            const validAffiliateHalf = "<?php echo 'Only one-byte alphanumeric characters can be entered for the “Affiliate code”.'; //「アフィリエイトコード」は半角英数で入力してください。 ?>";
            const validTrackingHalf = "<?php echo 'Only one-byte alphanumeric characters can be entered for the “Tracking ID”.'; //「トラッキングコード」は半角英数で入力してください。 ?>";
        </script>
<?php submit_button(); ?>
<div id="validError"></div>
</form>
</div>
<?php
}
