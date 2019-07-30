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
     echo '<div class="wrap">';
     echo '<p><a href="?page=simple-zazzle-sub">new</a></p>';
     echo '</div>';
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