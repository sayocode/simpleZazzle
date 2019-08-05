<?php

add_shortcode('simple_zazzle', 'echoItemList');
function echoItemList($atts) {
    if(!is_admin()){
        $affiliate_value = '238522058487844682&tc=wpscplugin';

        // idの指定がない場合はマーケットプレイスの出力を行う。
        if(empty($atts)){
            return defaultMarketPlace($affiliate_value);
        }
        $scid = $atts['id'];
        if(empty($scid)){
            return defaultMarketPlace($affiliate_value);
        }

        // idに対応するデータが存在しない場合にもマーケットプレイスの出力を行う
        global $wpdb;
        $table_name = $wpdb->prefix . "sc_simple_zazzle_table";
        $feedSettings = $wpdb->get_results("SELECT * FROM ".$table_name." WHERE `scid` = '".$scid."'");
        if(empty($feedSettings)){
            return defaultMarketPlace($affiliate_value);
        }

        // フィードを取得
        $feedSetting = $feedSettings[0];
        $rss = readFeed($feedSetting);

        // アフィリエイトコードの設定
        if(!(empty($feedSetting -> affiliate_code) && strcmp(get_option('affiliate_agree'), '1'))){
            $affiliate_value = $feedSetting -> affiliate_code;
            if(!empty($feedSetting -> tracking_code)){
                $affiliate_value = $affiliate_value . '&tc=' . $feedSetting -> tracking_code;
            }
        }

        // デフォルトフラグが付いているか、feed_customが空の場合はデフォルト形式で出力
        $feed_custom = $feedSetting -> feed_custom;
        if($feedSetting -> feed_default_flg == "1" || empty($feed_custom)){
            return defaultView($rss, $affiliate_value);
        }

        // カスタムHTMLの出力
        $return = '';
        foreach($rss->channel->item as $item){
            $fullTitle = $item->title;
            $category = str_replace(' ', '', str_replace($item->children('media', true)->title, '', $fullTitle));
            $title = str_replace(' ', '', str_replace($category, '', $fullTitle));
            $link = $item->link.'?rf='.$affiliate_value;
            $price = $item->price;
            $author = $item->author;
            $image = $item->children('media', true)->content->attributes()->url;
            $thumbnail = $item->children('media', true)->thumbnail->attributes()->url;
            $description = nl2br($item->children('media', true)->description);
            $keywords = '["'. str_replace( ', ', '", "',$item->children('media', true)->keywords) . '"]';

            $itemDom = str_replace('%fullTitle%', esc_html($fullTitle), $feed_custom);
            $itemDom = str_replace('%category%', esc_html($category), $itemDom);
            $itemDom = str_replace('%title%', esc_html($title), $itemDom);
            $itemDom = str_replace('%link%', esc_html($link), $itemDom);
            $itemDom = str_replace('%price%', esc_html($price), $itemDom);
            $itemDom = str_replace('%author%', esc_html($author), $itemDom);
            $itemDom = str_replace('%image%', esc_html($image), $itemDom);
            $itemDom = str_replace('%thumbnail%', $thumbnail, $itemDom);
            $itemDom = str_replace('%description%', $description, $itemDom);
            $itemDom = str_replace('%descriptionJs%', str_replace(array("\r\n", "\r", "\n"), '', esc_html($description, ENT_QUOTES|ENT_HTML5)), $itemDom);
            $itemDom = str_replace('%tags%', $keywords, $itemDom);
            $return = $return.$itemDom;
        }
        return $return;
    }
}

 /** フィードを読み込む */
function readFeed($feedSetting){
    $rss = "";
    $feed_name = $feedSetting -> feed_name;

    // 取得上限数の設定
    $optionParams = "?ps=";
    if(!empty($feedSetting -> max_num)){
        $optionParams = $optionParams.$feedSetting -> max_num;
    } else {
        $optionParams = $optionParams."100";
    }

    // ページ設定
    if(!empty($feedSetting -> page)){
        $optionParams = $optionParams."&pg=".$feedSetting -> page;
    }

    // フレーズ
    if(!empty($feedSetting -> phrase)){
        $optionParams = $optionParams."&qs=".$feedSetting -> phrase;
    }

    // 部門ID
    if(!empty($feedSetting -> department)){
        $optionParams = $optionParams."&dp=".$feedSetting -> department;
    }

    // 人気順
    if($feedSetting -> popular_flg == 1){
        $optionParams = $optionParams."&st=popularity";
        if(!empty($feedSetting -> popular_days)){
            $optionParams = $optionParams."&sp=".$feedSetting -> popular_days;
        }
    }

    // 画像の背景色の設定
    $bg_color = $feedSetting -> background_color;
    if(!empty($bg_color)){
        $optionParams = $optionParams."&bg=".$bg_color;
    }

    // フィードの取得
    if(strcmp($feedSetting -> feed_type, 'market') == 0){
        $rss = simplexml_load_file('https://www.zazzle.co.jp/rss'.$optionParams);
    } else {
        $feed_name = $feedSetting -> feed_type.'/'.$feed_name.'/';
        $feedUrl = 'https://www.zazzle.co.jp/'.$feed_name.'rss'.$optionParams;
        $get_contents = @file_get_contents($feedUrl);
        if($get_contents){
            $rss = simplexml_load_file($feedUrl);
        } else {
            $rss = simplexml_load_file('https://www.zazzle.co.jp/rss'.$optionParams);
        }
    }
    return $rss;
}

/** Zazzleのデフォルト形式での出力 */
function defaultView($rss, $affiliate_value){
    $return = '';
    foreach($rss->channel->item as $item){
        $description = $item->description;
        $link = $item->link;
        $author = $item->author;
        $afLink = $link.'?rf='.$affiliate_value;

        // なぜかRSSに作者のリンクが書かれていないので、こちらで変換する。（Zazzle側のバグ）
        $pattern = '/<span class="ZazzleCollectionItemCellProduct-byLine">作者：(.*)<\/span>/u';
        $replace = '<span class="ZazzleCollectionItemCellProduct-byLine">作者：'.$author.'</span>';
        $description = preg_replace($pattern, $replace, str_replace($link, $afLink, $description));

        $return = $return.$description;
    }

    return $return;
}

/** Zazzleのデフォルト形式でのマーケットプレイスの出力 */
function defaultMarketPlace($affiliate_value){
    $rss = simplexml_load_file('https://www.zazzle.co.jp/rss');
    return defaultView($rss, $affiliate_value);
}