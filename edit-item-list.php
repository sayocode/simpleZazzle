<?php

add_shortcode('simple_zazzle', 'echoItemList');
function echoItemList($atts) {
    if(!is_admin()){
        $affiliate_value = '238522058487844682';

        // idの指定がない場合はマーケットプレイスの出力を行う。
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

        if(!(empty($affiliate_value) && get_option('affiliate_agree') == '1')){
            $affiliate_value = get_option('affiliate');
        }

        $feedSetting = $feedSettings[0];
        $feed_name = $feedSetting -> feed_name;

        $rss = '';
        if($feed_name == 'market'){
            $rss = simplexml_load_file('https://www.zazzle.co.jp/rss');
        } else {
            $feed_name = $feedSetting -> feed_type.'/'.$feed_name.'/';
            $rss = simplexml_load_file('https://www.zazzle.co.jp/'.$feed_name.'rss');
            if(empty($rss)){
                $rss = simplexml_load_file('https://www.zazzle.co.jp/rss');
            }
        }

        // デフォルトフラグが付いているか、feed_customが空の場合はデフォルト形式で出力
        $feed_custom = $feedSetting -> feed_custom;
        if($feedSetting -> feed_default_flg == "1" || empty($feed_custom)){
            return defaultView($rss, $affiliate_value);
        }

        $return = '';
        foreach($rss->channel->item as $item){
            $fullTitle = $item->title;
            $category = str_replace(' ', '', str_replace($item->children('media', true)->title, '', $fullTitle));
            $title = str_replace(' ', '', str_replace($category, '', $fullTitle));
            $link = $item->link.'?rf='.$affiliate_value;
            $price = $item->price;
            $image = $item->children('media', true)->content->attributes()->url;
            $thumbnail = $item->children('media', true)->thumbnail->attributes()->url;
            $description = nl2br($item->children('media', true)->description);

            $itemDom = str_replace('%fullTitle%', $fullTitle, $feed_custom);
            $itemDom = str_replace('%category%', $category, $itemDom);
            $itemDom = str_replace('%title%', $title, $itemDom);
            $itemDom = str_replace('%link%', $link, $itemDom);
            $itemDom = str_replace('%price%', $price, $itemDom);
            $itemDom = str_replace('%image%', $image, $itemDom);
            $itemDom = str_replace('%thumbnail%', $thumbnail, $itemDom);
            $itemDom = str_replace('%description%', $description, $itemDom);
            $return = $return.$itemDom;
        }
        return $return;
    }
}

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

function defaultMarketPlace($affiliate_value){
    $rss = simplexml_load_file('https://www.zazzle.co.jp/rss');
    return defaultView($rss, $affiliate_value);
}