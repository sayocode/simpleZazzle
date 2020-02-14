<?php

add_shortcode('simple_zazzle', 'sc_echo_item_list');
function sc_echo_item_list($atts) {
	if(!is_admin()){
		$scsz_affiliate_value = '238522058487844682&tc=wpscplugin';

		// idの指定がない場合はマーケットプレイスの出力を行う。
		if(empty($atts)){
			return sc_default_market_place($scsz_affiliate_value);
		}
		$scid = $atts['id'];
		if(empty($scid)){
			return sc_default_market_place($scsz_affiliate_value);
		}

		// idに対応するデータが存在しない場合にもマーケットプレイスの出力を行う
		global $wpdb;
		$scsz_table_name = $wpdb->prefix . "sc_simple_zazzle_table";
		$scsz_feed_settings = $wpdb->get_results("SELECT * FROM ".$scsz_table_name." WHERE `scid` = '".$scid."'");
		if(empty($scsz_feed_settings)){
			return sc_default_market_place($scsz_affiliate_value);
		}

		// フィードを取得
		$scsz_feed_setting = $scsz_feed_settings[0];
		$scsz_rss = sc_reed_feed($scsz_feed_setting);

		// アフィリエイトコードの設定
		if(get_option('scsz_affiliate_agree') == '1') {
			if($scsz_feed_setting -> delete_affiliate_code_flg == "1"){
				$scsz_affiliate_value = "";
			} else if(!empty($scsz_feed_setting -> affiliate_code)){
				$scsz_affiliate_value = $scsz_feed_setting -> affiliate_code;
				if(!empty($scsz_feed_setting -> tracking_code)){
					$scsz_affiliate_value = $scsz_affiliate_value . '&tc=' . $scsz_feed_setting -> tracking_code;
				}
			}
		}

		// デフォルトフラグが付いているか、feed_customが空の場合はデフォルト形式で出力
		$scsz_feed_custom = urldecode($scsz_feed_setting -> feed_custom);
		if($scsz_feed_setting -> feed_default_flg == "1" || empty($scsz_feed_custom)){
			return sc_default_view($scsz_rss, $scsz_affiliate_value);
		}

		// カスタムHTMLの出力
		if($scsz_affiliate_value != ""){
			$scsz_affiliate_value = 'rf='.$scsz_affiliate_value;
		}
		$return = '<div class="sc-simple-zazzle"><!-- Output By "SC Simple Zazzle". --><!-- https://wordpress.org/plugins/sc-simple-zazzle/ --><style type="text/css">'.urldecode($scsz_feed_setting -> feed_custom_style).'</style>'.urldecode($scsz_feed_setting -> feed_custom_before);
		$roopIndex = 0;
		foreach($scsz_rss->channel->item as $item){
			$scsz_full_title = $item->title;
			$scsz_category = str_replace(' ', '', str_replace($item->children('media', true)->title, '', $scsz_full_title));
			$scsz_title = trim(str_replace($scsz_category, '', $scsz_full_title));
			$scsz_link_param = strpos($item->link, '?')!==false ? '&' : '?';
			$scsz_link = $item->link.$scsz_link_param.$scsz_affiliate_value;
			$scsz_link = rtrim(rtrim($scsz_link, '?'), '&');
			$scsz_price = $item->price;
			$scsz_author = $item->author;
			$scsz_image = $item->children('media', true)->content->attributes()->url;
			$scsz_thumbnail = $item->children('media', true)->thumbnail->attributes()->url;
			$scsz_description = nl2br($item->children('media', true)->description);
			$scsz_keywords = '["'. str_replace( ', ', '", "',$item->children('media', true)->keywords) . '"]';

			$itemDom = str_replace('%fullTitle%', esc_html($scsz_full_title), $scsz_feed_custom);
			$itemDom = str_replace('%category%', esc_html($scsz_category), $itemDom);
			$itemDom = str_replace('%title%', esc_html($scsz_title), $itemDom);
			$itemDom = str_replace('%link%', esc_html($scsz_link), $itemDom);
			$itemDom = str_replace('%price%', esc_html($scsz_price), $itemDom);
			$itemDom = str_replace('%author%', esc_html($scsz_author), $itemDom);
			$itemDom = str_replace('%image%', esc_html($scsz_image), $itemDom);
			$itemDom = str_replace('%thumbnail%', $scsz_thumbnail, $itemDom);
			$itemDom = str_replace('%description%', $scsz_description, $itemDom);
			$itemDom = str_replace('%descriptionJs%', str_replace(array("\r\n", "\r", "\n"), '', esc_html($scsz_description, ENT_QUOTES|ENT_HTML5)), $itemDom);
			$itemDom = str_replace('%tags%', $scsz_keywords, $itemDom);
			$itemDom = str_replace('%roopIndex%', $roopIndex, $itemDom);
			$roopIndex++;
			$return = $return.$itemDom;
		}

		$return = $return.urldecode($scsz_feed_setting -> feed_custom_after).'</div>';
		return $return;
	}
}

 /** フィードを読み込む */
function sc_reed_feed($scsz_feed_setting){
	$scsz_rss = "";
	$scsz_feed_name = $scsz_feed_setting -> feed_name;

	// 取得上限数の設定
	$scsz_option_params = "?ps=";
	if(!empty($scsz_feed_setting -> max_num)){
		$scsz_option_params = $scsz_option_params.urlencode($scsz_feed_setting -> max_num);
	} else {
		$scsz_option_params = $scsz_option_params."100";
	}

	// ページ設定
	if(!empty($scsz_feed_setting -> page)){
		$scsz_option_params = $scsz_option_params."&pg=".urlencode($scsz_feed_setting -> page);
	}

	// フレーズ
	if(!empty($scsz_feed_setting -> phrase)){
		$scsz_option_params = $scsz_option_params."&qs=".urlencode($scsz_feed_setting -> phrase);
	}

	// 部門ID
	if(!empty($scsz_feed_setting -> department)){
		$scsz_option_params = $scsz_option_params."&dp=".urlencode($scsz_feed_setting -> department);
	}

	// 人気順
	if($scsz_feed_setting -> popular_flg == 1){
		$scsz_option_params = $scsz_option_params."&st=popularity";
		if(!empty($scsz_feed_setting -> popular_days)){
			$scsz_option_params = $scsz_option_params."&sp=".urlencode($scsz_feed_setting -> popular_days);
		}
	}

	// 画像の背景色の設定
	$bg_color = $scsz_feed_setting -> background_color;
	if(!empty($bg_color)){
		$scsz_option_params = $scsz_option_params."&bg=".urlencode($bg_color);
	}

	// 言語設定がある場合は言語のfeedを取得する
	include('country-list.php');
	$scsz_country = $scsz_country_list[$scsz_feed_setting -> country];
	$scsz_country_url = $scsz_country['url'];
	$scsz_country_lang = $scsz_country['lang'];
	if(!empty($scsz_country_lang)){
		$scsz_option_params = $scsz_option_params.'&'.$scsz_country_lang;
	}

	// フィードの取得
	$scsz_feed_type = $scsz_feed_setting -> feed_type;
	if(strcmp($scsz_feed_type, 'market') == 0){
		return simplexml_load_file($scsz_country_url.'rss'.$scsz_option_params);
	} else {
		if(strcmp($scsz_feed_type, 'collections') == 0){
			if(!$scsz_country['collectionFeed']){
				return simplexml_load_file($scsz_country_url.'rss'.$scsz_option_params);
			}
		}
		$scsz_feed_name = $scsz_feed_type.'/'.$scsz_feed_name.'/';
		$scsz_feed_url = $scsz_country_url.$scsz_feed_name.'rss'.$scsz_option_params;
		$response = wp_remote_get($scsz_feed_url);
		if($response['response']['code'] == 200){
			$scsz_rss = simplexml_load_file($scsz_feed_url);
		} else {
			$scsz_rss = simplexml_load_file($scsz_country_url.'rss'.$scsz_option_params);
		}
	}
	return $scsz_rss;
}

/** Zazzleのデフォルト形式での出力 */
function sc_default_view($scsz_rss, $scsz_affiliate_value){
	$return = '';
	if($scsz_affiliate_value != ""){
		$scsz_affiliate_value = '?rf='.$scsz_affiliate_value;
	}
	foreach($scsz_rss->channel->item as $item){
		$scsz_description = $item->description;
		$scsz_link = $item->link;
		$scsz_author = $item->author;
		$scsz_affiliate_link = $scsz_link.$scsz_affiliate_value;

		// なぜかRSSに作者のリンクが書かれていないので、こちらで変換する。（Zazzle側のバグ）
		$pattern = '/<span class="ZazzleCollectionItemCellProduct-byLine">'.__('Author', 'sc-simple-zazzle').'：(.*)<\/span>/u';
		$replace = '<span class="ZazzleCollectionItemCellProduct-byLine">'.__('Author', 'sc-simple-zazzle').'：'.$scsz_author.'</span>';
		$scsz_description = preg_replace($pattern, $replace, str_replace($scsz_link, $scsz_affiliate_link, $scsz_description));

		$return = $return.$scsz_description;
	}

	return $return;
}

/** Zazzleのデフォルト形式でのマーケットプレイスの出力 */
function sc_default_market_place($scsz_affiliate_value){
	include('country-list.php');
	$scsz_country = $scsz_country_list;
	$scsz_country_url = 'https://www.zazzle.com/';
	$scsz_country_lang = '';
	foreach($scsz_country_list as $scsz_country_key => $scsz_country_val){
		if(get_locale() == $scsz_country_val['location']){
			$scsz_country_url = $scsz_country_val['url'];
			$scsz_country_lang = empty($scsz_country_val['lang']) ? '' : '?'.$scsz_country_val['lang'];
		}
	}

	$scsz_rss = simplexml_load_file($scsz_country_url.'rss');
	return sc_default_view($scsz_rss, $scsz_affiliate_value);
}