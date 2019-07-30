<?php
/*
Plugin Name: Simple Zazzle
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
		'Simple-Zazzle', // menu_title
		'administrator', // capability
		'simple-zazzle', // menu_slug
		'display_plugin_admin_page', // function
		'', // icon_url
		81 // position
	 );
 
	 add_submenu_page(
		'simple-zazzle', // parent_slug
		'Simple Zazzle', // page_title
		'Simple-Zazzle_Sub', // menu_title
		'administrator', // capability
		'simple-zazzle-sub', // menu_slug
		'display_plugin_sub_page' // function
	 );
}
 
function display_plugin_admin_page() {
	 ?>
	<div class="wrap">
		<h2>基本設定</h2>
		<form method="post" action="options.php">
			<?php wp_nonce_field('update-options'); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">アフィリエイトコード</th>
					<td><input type="text" name="affiliate" value="<?php echo get_option('affiliate'); ?>" /></td>
				</tr>
			</table>
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="affiliate" />
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
		<p><a href="?page=simple-zazzle-sub">new</a></p>
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
	if(empty($affiliate_value)){
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