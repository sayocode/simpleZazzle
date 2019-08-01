<?php
mt_options_page();
    $updateFlg;

function mt_options_page()
{
    global $wpdb;
    // テーブルの接頭辞と名前を指定
    $table_name = $wpdb->prefix . "sc_simple_zazzle_table";

    
    $feedSetting;
    // POSTデータがあれば設定を更新
    if (isset($_POST['feed_name'])) {

        $feed_title = wp_unslash($_POST['title']);
        $feed_type = wp_unslash($_POST['type']);
        $feed_name = wp_unslash($_POST['feed_name']);
        $feed_default_flg = isset($_POST['default']) ? 1 : 0;
        $feed_custom = wp_unslash($_POST['feed_custom']);

        if($updateFlg){ // 更新
            $oldDataResults = $wpdb->get_results("SELECT * FROM ".$table_name." WHERE `scid` = '".$scid."'");
            var_dump($oldDataResults);
            if(empty($oldDataResults)){
                // 存在しないか、すでに削除されたデータのため、更新できません。
                echo '<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible"><p><strong>存在しないか、すでに削除されたデータのため、更新できません。</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">この通知を非表示にする</span></button></div>';
            }
            
            // 最終更新日時チェック
            $oldData = $oldDataResults[0];
            $oldUpdateDate = $oldData -> update_date;
            $nowUpdateDate = wp_unslash($_POST['update_date']);
            if(strcmp($oldUpdateDate, $nowUpdateDate) != 0){
                // 更新が既にされているようです。画面を更新して再度内容を入力し、更新し直してください。
                
                echo '<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible"><p><strong>更新が既にされているようです。画面を更新して再度内容を入力し、更新し直してください。</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">この通知を非表示にする</span></button></div>';
            }
            
            echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>設定を更新しました。</strong></p></div>';
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
                                             'feed_custom' => $feed_custom
                                             ), array(
                                                      '%d', '%s', '%s', '%s', '%d', '%s'
                                                      ));
            
            echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>設定を保存しました。</strong></p></div>';
            $updateFlg = true;
        }
    }
    $updateFlg = isset($_GET['scid']);
    if($updateFlg){
        $scid = $_GET['scid'];
        // 初期表示に使うフラグを設定
        $feedSettings = $wpdb->get_results("SELECT * FROM ".$table_name." WHERE `scid` = '".$scid."'");
        $feedSetting = $feedSettings[0];
        if(empty($feedSettings)){
            $updateFlg = false;
        }
    }
    var_dump($updateFlg);
    ?>
<div class="wrap">
    <h2>ショートコード設定</h2>

<form method="post" action="">
        <table class="form-table">

            <tr id="hideMarket">
                <th scope="row"><label for="title">タイトル</label></th>
                <td><input name="title" type="text" id="title"
                        value="<?php if($updateFlg){echo $feedSetting->title;} ?>" class="regular-text" /></td>
            </tr>
            <tr><th>ショートコード</th><td><?php if($updateFlg){echo '[simple_zazzle id='.$scid.']';} ?></td></tr>
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
                        value="<?php if($updateFlg){echo $feedSetting->feed_name;} ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th scope="row"><label for="defaultChk">Zazzleデフォルトの表示機能を利用する</label></th>
                <td><label><input name="default" type="checkbox" id="defaultChk"
                            value="1" <?php checked( 1, $feedSetting -> feed_default_flg); ?> /> チェック</label></td>
            </tr>
            <tr>
                <th scope="row"><label for="feedCustom">カスタムHTML</label></th>
                <td><textarea name="feed_custom" id="feedCustom"
                        class="large-text code" rows="5"><?php if($updateFlg){echo $feedSetting->feed_custom;} ?></textarea></td>
            </tr>
        </table>
<input type="text" name="update_date" value="<?php if($updateFlg){echo $feedSetting->update_date;} ?>">
<?php submit_button(); ?>
</form>
</div>
<?php
}