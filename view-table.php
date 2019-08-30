<?php
if (! class_exists('WP_List_Table')) {
	require_once (ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * 管理ページに表示するテーブルのクラス
 */
class scsz_My_List_Table extends WP_List_Table
{

	// カラムの設定
	function get_columns()
	{
		$columns = array(
			'title' => 'Title', //タイトル
			'short_code' => 'Short code', //ショートコード
			'feed_type' => 'Type', //種別
			'feed_name' => 'Store name', // ストア名
			'feed_default_flg' => 'Custom HTML' //HTMLカスタム
		);
		return $columns;
	}

	// テーブルに表示するデータの設定
	function prepare_items()
	{

		// sc_simple_zazzle_tableからデータを取得
		global $wpdb;
		$scsz_table_name = $wpdb->prefix . "sc_simple_zazzle_table";
		$scsz_all_feed_settings = $wpdb->get_results("SELECT * FROM " . $scsz_table_name);

		// 種別の日本語表記
		$scsz_feed_type_mapping = array(
			'store' => 'Store', // ストア
			'collections' => 'Collections', //コレクション
			'market' => 'Market place' //マーケットプレイス
		);
		foreach ($scsz_all_feed_settings as &$scsz_obj_to_arr) {
			$scsz_obj_to_arr = json_decode(json_encode($scsz_obj_to_arr), true);
			$scsz_obj_to_arr['short_code'] = '[simple_zazzle id=' . $scsz_obj_to_arr['scid'] . ']';
			$scsz_obj_to_arr['feed_type'] = $scsz_feed_type_mapping[$scsz_obj_to_arr['feed_type']];
			$scsz_obj_to_arr['feed_default_flg'] = $scsz_obj_to_arr['feed_default_flg'] ? 'OFF' : 'ON';
		}

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = array();
		$this->_column_headers = array(
			$columns,
			$hidden,
			$sortable
		);
		$this->items = $scsz_all_feed_settings;
	}

	// デフォルトのカラム表示
	function column_default($item, $column_name)
	{
		switch ($column_name) {
			case 'title':
			case 'short_code':
			case 'feed_type':
			case 'feed_name':
			case 'feed_default_flg':
				return $item[$column_name];
			default:
				return print_r($item, true);
		}
	}

	// タイトル列のカラム表示
	function column_title($item)
	{
		$actions = array(
			'edit' => sprintf('<a href="?page=simple-zazzle-edit&scid=%s">'.'Edit'.'</a>', $item['scid'])
		);
		return sprintf('%1$s %2$s', $item['title'], $this->row_actions($actions));
	}

	// ショートコード列のカラム表示
	function column_short_code($item)
	{
		$actions = array(
			'edit' => sprintf('<a class="text-copy" data-short-code=%s>'.'Copy'.'</a>', "'" . $item['short_code'] . "'")
		);
		return sprintf('%1$s %2$s', $item['short_code'], $this->row_actions($actions));
	}
}

function scsz_display_plugin_admin_page()
{

	// POSTデータがあれば設定を更新
	if (isset($_POST['affiliate_update'])) {
		$scsz_affiliate_agree = isset($_POST['scsz_affiliate_agree']) ? 1 : 0;
		update_option('scsz_affiliate_agree', $scsz_affiliate_agree);
        $scsz_result_msg = 'The settings was saved.'; // 設定を保存しました。
		echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>'.$scsz_result_msg.'</strong></p></div>';
	}
	$scsz_agree_flg = get_option('scsz_affiliate_agree');

	$myListTable = new scsz_My_List_Table();
	echo '<div class="wrap aioseop_options_wrapper"><h1 class="wp-heading-inline">SC Simple Zazzle</h1><a class="page-title-action" href="?page=simple-zazzle-edit">新規追加</a>';
	echo '<div class="main-table"><h2>設定一覧</h2>';
	$myListTable->prepare_items();
	$myListTable->display();
	?>
<input type="hidden" id="outputCode">
<!-- ショートコードをクリップボードにコピーするためのボックス -->
<div class="postbox">
	<h2>
    <span><?php echo 'About This Plugin.'; //このプラグインについて ?></span>
	</h2>
	<div class="inside">
    <p><?php echo 'Plugin author: ' // プラグインの作者 ?><a href="https://sayoko-ct.com/">sayoko</a><br>
        <a href="https://sayoko-ct.com/sc-simple-zazzle/" target="_blank"><?php echo 'This plugin page.'; ?></a></p>
		<h3><?php echo 'Report bugs' ?></h3>
        <p><?php echo 'Please send bug reports and feature improvements suggestions on GitHub.'; ?><a href="https://github.com/sayocode/simpleZazzle/issues/new" target="_blank">GitHub</a></p>
	</div>
</div>
</div>
</div>
<form method="post" action="">
	<table class="form-table">
		<tr id="agreeAffiliate">
            <td colspan="2"><label><?php echo 'Use an affiliate'; ?>&emsp; <input
						name="scsz_affiliate_agree" type="checkbox"
						id="scsz_affiliate_agree" value="1"
						<?php checked( 1, $scsz_agree_flg); ?> />
			</label></td>
		</tr>
	</table>
	<input type="hidden" name="affiliate_update" value="1" />
		<?php submit_button(); ?>
	</form>
<?php
}
