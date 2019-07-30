<?php
/*
Plugin Name: SC Simple Zazzle
Plugin URI: https://sayoko-ct.com/
Description: ZazzleのRSSから商品一覧を出力
Author: sayoko
Version: 0.1
Author URI: https://sayoko-ct.com/
*/
add_action( 'admin_menu', 'add_plugin_admin_menu' );
 
function add_plugin_admin_menu() {
	 add_menu_page(
		'Simple-Zazzle', // page_title
		'Simple Zazzle', // menu_title
		'administrator', // capability
		'simple-zazzle', // menu_slug
		'display_plugin_admin_page', // function
		'', // icon_url
		81 // position
	 );
 
	 add_submenu_page(
		'simple-zazzle', // parent_slug
		'Simple Zazzle', // page_title
		'新規', // menu_title
		'administrator', // capability
		'simple-zazzle-sub', // menu_slug
		'display_plugin_sub_page' // function
	 );
}
 
function display_plugin_admin_page() {
    // POSTデータがあれば設定を更新
    if (isset($_POST['affiliate'])) {
        // POSTデータの'"などがエスケープされるのでwp_unslashで戻して保存
        update_option('affiliate', wp_unslash($_POST['affiliate']));
        // チェックボックスはチェックされないとキーも受け取れないので、ない時は0にする
        $affiliate_agree = isset($_POST['affiliate_agree']) ? 1 : 0;
        update_option('affiliate_agree', $affiliate_agree);
    }
?>
<div class="wrap">
<a href="?page=simple-zazzle-sub">新規</a>
<?php
    // 更新完了を通知
    if (isset($_POST['affiliate'])) {
        echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
            <p><strong>設定を保存しました。</strong></p></div>';
    }
    $agreeFlg = get_option('affiliate_agree');
?>
<h2></h2>
<form method="post" action="">
<table class="form-table">
    <tr id="agreeAffiliate">
        <td colspan="2"><label>アフィリエイトを利用します&emsp;<input name="affiliate_agree" type="checkbox" id="affiliate_agree" value="1" <?php checked( 1, $agreeFlg); ?> /> </label></td>
    </tr>
    <tr class="after-agreeing" <?php if($agreeFlg == 0) { echo 'style="display:none;"'; } ?>>
        <th scope="row"><label for="affiliate">アフィリエイトコード</label></th>
        <td><input name="affiliate" type="text" id="affiliate" value="<?php form_option('affiliate'); ?>" class="regular-text" /></td>
    </tr>
    
</table>
<?php submit_button(); ?>
</form>
</div>
<?php
}


 
function display_plugin_sub_page() {
	// 設定変更画面を表示する
?>
	<div class="wrap">
		<h2>Settings</h2>
		<form method="post" action="options.php">
			<?php wp_nonce_field('update-options'); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">ストア名</th>
					<td><input type="text" name="storename" value="<?php echo get_option('storename'); ?>" /></td>
				</tr>
			</table>
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="storename" />
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
<?php
}
function echoItemList() {
if(!is_admin()){

	$storename_value = get_option('storename');
	$affiliate_value = get_option('affiliate');
	if ( $storename_value ) {
	$rss = simplexml_load_file('https://www.zazzle.co.jp/store/'.$storename_value.'/rss');
	echo '<ul>';
	if(empty($affiliate_value) || empty(get_option('affiliate_agree'))){
		$affiliate_value = "238522058487844682";
	}

	foreach($rss->channel->item as $item){
		$title = $item->title;
		$link = $item->link.'?rf='.$affiliate_value;
		$price = $item->price;
		$image = $item->children('media', true)->thumbnail->attributes()->url;
	?>

	<li>
		<img src="<?php echo $image; ?>" >
		<a href="<?php echo $link; ?>" target="_blank">
		<span class="title"><?php echo $title; ?></span>
		<span class="price"><?php echo $price; ?></span>
		</a>
	</li>
	<?php } echo '</ul>'; 
	}
}
}
add_shortcode('simple_zazzle', 'echoItemList');

// スタイルとスクリプト
include('fileRead.php');