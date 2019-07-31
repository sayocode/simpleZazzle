<?php
mt_options_page();

function mt_options_page()
{
    global $wpdb;
    $newFlg = isset($_GET['scid']);

    $feedSetting;
    if($newFlg){
        $scid = $_GET['scid'];
        // 初期表示に使うフラグを設定
        $table_name = $wpdb->prefix . "sc_simple_zazzle_table";
        $feedSettings = $wpdb->get_results("SELECT * FROM ".$table_name." WHERE `scid` = '".$scid."'");
        $feedSetting = $feedSettings[0];
        if(empty($feedSettings)){
            $newFlg = false;
        }
    }

    // POSTデータがあれば設定を更新
    if (isset($_POST['feed_name'])) {

        // テーブルの接頭辞と名前を指定
        $table_name = $wpdb->prefix . "sc_simple_zazzle_table";

        $maxId = $wpdb->get_var("SELECT max(`scid`) FROM wp_sc_simple_zazzle_table;");

        $scid = $maxId + 1;
        $feed_title = "test";
        $feed_type = wp_unslash($_POST['type']);
        $feed_name = wp_unslash($_POST['feed_name']);
        $feed_default_flg = isset($_POST['default']) ? 1 : 0;
        $feed_custom = wp_unslash($_POST['feed_custom']);

        $wpdb->insert($table_name, array(
            'scid' => $scid,
            'title' => $feed_title,
            'feed_type' => $feed_type,
            'feed_name' => $feed_name,
            'feed_default_flg' => $feed_default_flg,
            'feed_custom' => $feed_custom
        ), array(
            '%d',
            '%s',
            '%s',
            '%s',
            '%d',
            '%s'
        ));
    }
    ?>
<div class="wrap">
	<h2>ショートコード設定</h2>
<?php
    // 更新完了を通知
    if (isset($_POST['feed_name'])) {
        echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
            <p><strong>設定を保存しました。</strong></p></div>';
    }
    ?>

<form method="post" action="">
		<table class="form-table">
			<tr>
				<th scope="row"><label for="typeSelect">種別</label></th>
				<td><select name="type" id="typeSelect">
						<option value="store"
							<?php
                                $selectFlg = $feedSetting->feed_type == 'store' ? ' selected' : '';
                                echo $selectFlg; ?>>ストア</option>
						<option value="collections"
							<?php
                                $selectFlg = $feedSetting->feed_type == 'collections' ? ' selected' : '';
                                echo $selectFlg; ?>>コレクション</option>
						<option value="market"
							<?php
                                $selectFlg = $feedSetting->feed_type == 'market' ? ' selected' : '';
                                echo $selectFlg; ?>>マーケットプレイス</option>
				</select></td>
			</tr>
			<tr id="hideMarket">
				<th scope="row"><label for="feedName" id="typeText">ストア名</label></th>
				<td><input name="feed_name" type="text" id="feedName"
						value="<?php form_option('feed_name'); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="defaultChk">Zazzleデフォルトの表示機能を利用する</label></th>
				<td><label><input name="default" type="checkbox" id="defaultChk"
							value="1" <?php checked( 1, get_option('default')); ?> /> チェック</label></td>
			</tr>
			<tr>
				<th scope="row"><label for="feedCustom">カスタムHTML</label></th>
				<td><textarea name="feed_custom" id="feedCustom"
						class="large-text code" rows="5"><?php echo esc_textarea(get_option('feed_custom')); ?></textarea></td>
			</tr>
		</table>
<?php submit_button(); ?>
</form>
</div>
<?php
}
