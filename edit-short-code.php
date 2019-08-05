<?php
mt_options_page();

function mt_options_page()
{
	global $wpdb;
	$updateFlg;
	$feedSetting = '';
	$scid = '';
	// テーブルの接頭辞と名前を指定
	$table_name = $wpdb->prefix . "sc_simple_zazzle_table";
	$updateFlg = isset($_GET['scid']);
	if ($updateFlg) {
		$scid = $_GET['scid'];
		// 初期表示に使うフラグを設定
		$feedSetting = setFeedInfo($wpdb, $table_name, $scid);
		$updateFlg = ! empty($feedSetting);
	}

	// POSTデータがあれば設定を更新
	if (isset($_POST['scid'])) {

		$scid = wp_unslash($_POST['scid']);
		$feed_title = wp_unslash($_POST['title']);
		$feed_type = wp_unslash($_POST['type']);
		$feed_name = wp_unslash($_POST['feed_name']);
		$feed_default_flg = isset($_POST['default']) ? 1 : 0;
		$feed_custom = wp_unslash($_POST['feed_custom']);
		$phrase = wp_unslash($_POST['phrase']);
		$department = wp_unslash($_POST['department']);
		$popular_flg = isset($_POST['popular_flg']) ? 1 : 0;
		$popular_days = empty($_POST['popular_days']) ? 0 : $_POST['popular_days'];
		$max_num = empty($_POST['max_num']) ? 100 : $_POST['max_num'];
		$page = empty($_POST['page']) ? 0 : $_POST['page'];
		$background_color = str_replace('#', '', wp_unslash($_POST['background_color']));
		$affiliate_code = wp_unslash($_POST['affiliate_code']);
		$tracking_code = wp_unslash($_POST['tracking_code']);
		$updateFlg = (($_POST['update_flg'] == 'true') ? true : false);

		if ($updateFlg) { // 更新
			$oldDataResults = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE `scid` = '" . $scid . "'");

			if (empty($oldDataResults)) {
				echo '<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible"><p><strong>存在しないか、すでに削除されたデータのため、更新できません。</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">この通知を非表示にする</span></button></div>';
			} else {
				// 最終更新日時チェック
				$oldData = $oldDataResults[0];
				$oldUpdateDate = $oldData->update_date;
				$nowUpdateDate = wp_unslash($_POST['update_date']);
				if (strcmp($oldUpdateDate, $nowUpdateDate) != 0) {

					echo '<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible"><p><strong>更新が既にされているようです。画面を更新して再度内容を入力し、更新し直してください。</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">この通知を非表示にする</span></button></div>';
				} else {
					$wpdb->update($table_name, array(
						'title' => $feed_title,
						'feed_type' => $feed_type,
						'feed_name' => $feed_name,
						'feed_default_flg' => $feed_default_flg,
						'feed_custom' => $feed_custom,
						'phrase' => $phrase,
						'department' => $department,
						'popular_flg' => $popular_flg,
						'popular_days' => $popular_days,
						'max_num' => $max_num,
						'page' => $page,
						'background_color' => $background_color,
						'affiliate_code' => $affiliate_code,
						'tracking_code' => $tracking_code,
						'update_date' => date('Y-m-d H:i:s')
					), array(
						'scid' => $scid
					), array(
						'%s',
						'%s',
						'%s',
						'%d',
						'%s',
						'%s',
						'%s',
						'%d',
						'%d',
						'%d',
						'%d',
						'%s',
						'%s',
						'%s',
						'%s'
					), array(
						'%d'
					));
					echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>設定を更新しました。</strong></p></div>';
					$feedSetting = setFeedInfo($wpdb, $table_name, $scid);
				}
			}
		} else { // 新規

			// 新しいscidを発行
			$maxId = $wpdb->get_var("SELECT max(`scid`) FROM wp_sc_simple_zazzle_table;");
			$scid = $maxId + 1;
			$wpdb->insert($table_name, array(
				'scid' => $scid,
				'title' => $feed_title,
				'feed_type' => $feed_type,
				'feed_name' => $feed_name,
				'feed_default_flg' => $feed_default_flg,
				'feed_custom' => $feed_custom,
				'phrase' => $phrase,
				'department' => $department,
				'popular_flg' => $popular_flg,
				'popular_days' => $popular_days,
				'max_num' => $max_num,
				'page' => $page,
				'background_color' => $background_color,
				'affiliate_code' => $affiliate_code,
				'tracking_code' => $tracking_code
			), array(
				'%d',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%d',
				'%d',
				'%d',
				'%d',
				'%s',
				'%s',
				'%s'
			));
			echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>設定を保存しました。</strong></p></div>';
			$updateFlg = true;
			$feedSetting = setFeedInfo($wpdb, $table_name, $scid);
		}
	}
	editHtml($scid, $updateFlg, $feedSetting);
}

function setFeedInfo($wpdb, $table_name, $scid)
{
	$feedSettings = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE `scid` = '" . $scid . "'");
	return $feedSettings[0];
}

function editHtml($scid, $updateFlg, $feedSetting){
	?>
<div class="wrap">
	<a href="?page=simple-zazzle">戻る</a>
	<h2>ショートコード設定</h2>

	<form class="sc-edit-short-code" method="post" action="">

		<ul class="tab clearfix">
			<li class="active">基本設定</li>
			<li>その他</li>
		</ul>
		<div class="area">
		<span style="color:#ef6340;">*</span>は必須項目
		<div>各パラメータの詳しい説明は&ensp;<a href="https://www.zazzle.com/sell/affiliates/promotionaltools/rss" target="_blank">RSS feeds (Zazzle公式ページ・英語ページのみ)</a>&ensp;をご確認ください。<br>
		設定に不備があった場合、マーケットプレイスのFeedが読み込まれます。</div>
			<div class="tab-area show">
				<table class="form-table">

					<tr>
						<th scope="row"><label class="required" for="title">タイトル</label></th>
						<td><input name="title" type="text" id="title" required
								value="<?php if($updateFlg){echo esc_html($feedSetting->title);} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th>ショートコード</th>
						<td><?php if($updateFlg){echo '[simple_zazzle id='.$scid.']';} ?></td>
					</tr>
					<tr>
						<th scope="row"><label class="required" for="typeSelect">種別</label></th>
						<td><select name="type" id="typeSelect">
								<option value="store"
									<?php

									if ($updateFlg) {
										$selectFlg = $feedSetting->feed_type == 'store' ? ' selected' : '';
										echo $selectFlg;
									}
									?>>ストア</option>
								<!-- <option value="collections"
									<?php
									if ($updateFlg) {
										$selectFlg = $feedSetting->feed_type == 'collections' ? ' selected' : '';
										echo $selectFlg;
									}
									?>>コレクション</option> -->
								<option value="market"
									<?php

									if ($updateFlg) {
										$selectFlg = $feedSetting->feed_type == 'market' ? ' selected' : '';
										echo $selectFlg;
									}
									?>>マーケットプレイス</option>
							</select> ※コレクションのRSSが現在正常に取得できないため、機能の提供を一時停止しております。</td>
					</tr>
					<tr id="hideMarket">
						<th scope="row"><label for="feedName" id="typeText">ストア名</label></th>
						<td><input name="feed_name" type="text" id="feedName"
								value="<?php if($updateFlg){echo esc_html($feedSetting->feed_name);} ?>"
								class="regular-text" />
								<span id="feedNameLinkWrap"></span></td>
					</tr>
					<tr>
						<th scope="row"><label for="defaultChk">Zazzleデフォルトの表示機能を利用する</label></th>
						<td><input name="default" type="checkbox" id="defaultChk"
									value="1"
									<?php  if($updateFlg){checked( 1, $feedSetting -> feed_default_flg);} ?> /></td>
					</tr>
					<tr>
						<th scope="row"><label for="feedCustom">カスタムHTML</label></th>
						<td>
							<button class="button assist-button" data-object="title">商品名</button>
							<button class="button assist-button" data-object="fullTitle">商品名（フル）</button>
							<button class="button assist-button" data-object="category">カテゴリー</button>
							<button class="button assist-button" data-object="link">商品URL</button>
							<button class="button assist-button" data-object="price">値段</button>
							<button class="button assist-button" data-object="author">作者</button>
							<button class="button assist-button" data-object="image">商品画像URL</button>
							<button class="button assist-button" data-object="thumbnail">商品画像URL（サムネイル）</button>
							<button class="button assist-button" data-object="description">商品説明</button>
							<button class="button assist-button" data-object="descriptionJs">商品説明（HTMLエスケープ）</button>
							<button class="button assist-button" data-object="tags">タグ（JavaScript配列）</button>
							<textarea name="feed_custom" id="feedCustom"
								class="large-text code" rows="5"><?php if($updateFlg){echo $feedSetting->feed_custom;} ?></textarea>
						</td>
					</tr>
				</table>
			</div>
			<div class="tab-area">
				<table class="form-table">
					<tr>
						<th scope="row"><label for="phrase">商品名の検索フレーズ</label></th>
						<td><input name="phrase" type="text" id="phrase"
								value="<?php if($updateFlg){echo esc_html($feedSetting->phrase);} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="department">部門ID</label>
						<span style="font-size:0.8em;">（<a href="https://www.zazzle.com/sell/affiliates/promotionaltools/rss#page_departmentId" target="_blank">検索</a>）</span></th>
						<td><input name="department" type="text" id="department"
								value="<?php if($updateFlg){echo esc_html($feedSetting->department);} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="popularChk">人気順に表示する</label></th>
						<td><input name="popular_flg" type="checkbox" id="popularChk"
									value="1"
									<?php if($updateFlg){checked( 1, $feedSetting -> popular_flg);} ?> /></td>
					</tr>
					<tr>
						<th scope="row"><label for="popularDays">人気順の集計期間（日）</label></th>
						<td><input name="popular_days" type="number" id="popularDays"
									value="<?php if($updateFlg){echo esc_html($feedSetting->popular_days);} ?>" 
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="maxNum">取得数上限</label></th>
						<td><input name="max_num" type="number" id="maxNum"
								value="<?php if($updateFlg){echo esc_html($feedSetting->max_num);} else { echo "100";} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="page">ページ</label></th>
						<td><input name="page" type="number" id="page"
								value="<?php if($updateFlg){echo esc_html($feedSetting->page);} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="backgroundColor">画像の背景色</label></th>
						<td><input name="background_color" type="color" id="backgroundColor"
								value="<?php if($updateFlg){echo '#'.esc_html($feedSetting->background_color);} else { echo "#ffffff";} ?>"
								class="regular-text" /></td>
					</tr>
					<?php if(!strcmp(get_option('affiliate_agree'), '1')) {?>
					<tr>
						<th scope="row"><label for="affiliateCode">アフィリエイトコード</label></th>
						<td><input name="affiliate_code" type="text" id="affiliateCode"
								value="<?php if($updateFlg){echo esc_html($feedSetting->affiliate_code);} ?>"
								class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="trackingCode">トラッキングコード</label></th>
						<td><input name="tracking_code" type="text" id="trackingCode"
								value="<?php if($updateFlg){echo esc_html($feedSetting->tracking_code);} ?>"
								class="regular-text" /></td>
					</tr>
					<?php } ?>
				</table>
			</div>
			<input type="hidden" name="update_date"
				value="<?php if($updateFlg){echo esc_html($feedSetting->update_date);} ?>">
			<input type="hidden" name="scid"
				value="<?php if($updateFlg){echo esc_html($feedSetting->scid);} ?>">
			<input type="hidden" name="update_flg"
				value="<?php if($updateFlg){echo 'true';} else {echo 'false';} ?>">
		</div>
<?php submit_button(); ?>
<div id="validError"></div>
</form>
</div>
<?php
}
