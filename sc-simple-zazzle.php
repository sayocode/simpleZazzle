<?php
/*
Plugin Name: SC Simple Zazzle
Plugin URI: https://wordpress.org/plugins/sc-simple-zazzle
Description: ZazzleのRSSフィードを取得し、HTMLとして出力します。マーケットプレイスのみならず、ストアやコレクションのフィードを設定することができます。アフィリエイトにももちろん対応しているので、紹介料の取りこぼしも防げます！
Author: sayoko
Version: 0.1
Author URI: https://sayoko-ct.com/
*/

/*  Copyright 2019 sayoko (email : communicate@sayoko-ct.com) */

add_action( 'admin_menu', 'add_plugin_admin_menu' );
 
function add_plugin_admin_menu() {
	 add_menu_page(
		'Simple-Zazzle', // page_title
		'Simple Zazzle', // menu_title
		'administrator', // capability
		'simple-zazzle', // menu_slug
		'display_plugin_admin_page', // function
		'dashicons-store',
		81 // position
	 );
 
	 add_submenu_page(
		'simple-zazzle', // parent_slug
		'Simple Zazzle', // page_title
		'新規', // menu_title
		'administrator', // capability
		'simple-zazzle-edit', // menu_slug
		'display_plugin_sub_page' // function
	 );
}
 
function display_plugin_admin_page() {
	// POSTデータがあれば設定を更新
	if (isset($_POST['affiliate'])) {
		$affiliate_agree = isset($_POST['affiliate_agree']) ? 1 : 0;
		update_option('affiliate_agree', $affiliate_agree);
	}
?>
<div class="wrap">
	<?php
	// 更新完了を通知
	if (isset($_POST['affiliate_agree'])) {
		echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>設定を保存しました。</strong></p></div>';
	}
	$agreeFlg = get_option('affiliate_agree');

	global $wpdb;
	$table_name = $wpdb->prefix . "sc_simple_zazzle_table";
	$allFeedSettings = $wpdb->get_results("SELECT * FROM ".$table_name);

	?>
	<h2>設定一覧</h2>
	<div class="main-table">
		<table id="feedSettingList">
			<thead>
				<tr>
					<th><a class="button" href="?page=simple-zazzle-edit">新規</a></th>
					<th>ID</th>
					<th>ショートコード</th>
					<th>タイトル</th>
					<th>種別</th>
					<th>ストア名 / コレクション名</th>
					<th>Zazzleデフォルト表示</th>
					<th>カスタムHTML</th>
				</tr>
			</thead>
			<tbody>
				<?php 
		foreach($allFeedSettings as $feedSetting){
		$scid = $feedSetting -> scid;
		$feedType = $feedSetting -> feed_type;
		$defaultFlg = $feedSetting -> feed_default_flg ? 'ON' : 'OFF';
		$feedCustom = htmlentities($feedSetting -> feed_custom);
		?>
				<tr>
					<td><a class="edit-button button"
							href="?page=simple-zazzle-edit&scid=<?php echo $scid; ?>">編集</a></td>
					<td>
						<?php echo $scid; ?>
					</td>
					<td>
						<?php echo '[simple_zazzle id='.$scid.']'; ?>
					</td>
					<td>
						<?php echo ($feedSetting -> title); ?>
					</td>
					<td>
						<?php echo $feedType; ?>
					</td>
					<td>
						<?php if($feedType != "market"){echo ($feedSetting -> feed_name);} ?>
					</td>
					<td>
						<?php echo $defaultFlg; ?>
					</td>
					<td class="feed-custom">
						<?php echo $feedCustom; ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<form method="post" action="">
		<table class="form-table">
			<tr id="agreeAffiliate">
				<td colspan="2"><label>アフィリエイトを利用します&emsp; <input
							name="affiliate_agree" type="checkbox" id="affiliate_agree"
							value="1"<?php checked( 1, $agreeFlg); ?> />
				</label></td>
			</tr>

		</table>
		<?php submit_button(); ?>
	</form>
</div>
<?php
}

function display_plugin_sub_page() {
	include('edit-short-code.php');
}

include('output-item-list.php');
include('uninstall.php');
include('list-table.php');
include('file-read.php');
