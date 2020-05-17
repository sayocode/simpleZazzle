let $typeSelectDom;
jQuery(document).ready(function($) {
	if ($("body").hasClass("simple-zazzle_page_simple-zazzle-edit")) {
		$typeSelectDom = $(typeSelectDom).clone();
	}

	// 編集画面
	const $typeSelect = $("#typeSelect");
	const $countrySelect = $("#countrySelect");
	const $feedName = $("#feedName");
	countrySetting($, $countrySelect, $typeSelect);
	typeSetting($, $typeSelect);

	$countrySelect.on("change", function() {
		countrySetting($, $countrySelect, $typeSelect);
	});
	$typeSelect.on("change", function() {
		typeSetting($, $typeSelect);
		changeFeedNameLink($, $typeSelect.val(), $feedName.val());
	});

	// デフォルトの出力機能を使わない場合はテキストエリアを不活性にする。
	const $defaultChk = $("#defaultChk");
	const $feedCustom = $("#feedCustom");
	const $feedCustomBefore = $("#feedCustomBefore");
	const $feedCustomAfter = $("#feedCustomAfter");
	const $feedCustomStyle = $("#feedCustomStyle");
	const $assistButtons = $("#assistButtonsWrap").find("button");
	disabledFeedCustom($defaultChk, $feedCustom, $feedCustomBefore, $feedCustomAfter, $feedCustomStyle, $assistButtons);
	$defaultChk.on("change", function() {
		disabledFeedCustom($defaultChk, $feedCustom, $feedCustomBefore, $feedCustomAfter, $feedCustomStyle, $assistButtons);
	});

	/** 要素の入力アシスト */
	// エンターキーで発火させない
	$( 'input' ).keypress( function ( e ) {
		if ( e.which == 13 ) {
			e.preventDefault();
			return false;
		}
	} );

	// クリックのみで発火
	$(".assist-button").on("click", function(e) {
		// 通信をさせない
		e.preventDefault();

		// 挿入する文字列
		const obj = "%" + $(this).data("object") + "%";

		// テキストエリアの文字取得とカーソル位置を取得
		const feedCustomDom = $feedCustom.get(0);
		let sentence = feedCustomDom.value;
		const len = sentence.length;
		const pos = feedCustomDom.selectionStart;

		// カーソル位置で文字をぶった切る
		const before = sentence.substr(0, pos);
		const after = sentence.substr(pos, len);

		// 文字列を挿入し、カーソルを返す
		sentence = before + obj + after;
		feedCustomDom.value = sentence;
		feedCustomDom.selectionStart = pos + obj.length;
	});

	changeFeedNameLink($, $typeSelect.val(), $feedName.val());
	$feedName.on("change", function(){
		changeFeedNameLink($, $typeSelect.val(), $feedName.val());
	});

	/** タブ制御 */
	$('.tab li').click(function() {

		var index = $('.tab li').index(this);
		$('.tab li').removeClass('active');
		$(this).addClass('active');

		// コンテンツを一旦非表示にし、クリックされた順番のコンテンツのみを表示
		$('.tab-area').removeClass('show').eq(index).addClass('show');
	});

	// バリデーション
	$(".sc-edit-short-code #submit").on("click", function(e) {
		const $validError = $("#validError");
		if (!isHalfUnderBar($feedName.val())) {
			$validError.text(validStoreOrCollectionsHalf);
			e.preventDefault();
		}
		if($("#maxNum").val() > 100 || $("#maxNum").val() < 0){
			$validError.text(validMaximumNumberOfAcquisitions);
			e.preventDefault();
		}

		const bcColor = $("#backgroundColor").val().replace("#", "");
		if(!isFinite(parseInt("0x" + bcColor , 16)) || bcColor.length != 6){
			$validError.text(validimageBgColor);
			e.preventDefault();
		}

		if(!isHalf($("#affiliateCode").val())){
			$validError.text(validAffiliateHalf);
			e.preventDefault();
		}
		if(!isHalf($("#trackingCode").val())){
			$validError.text(validTrackingHalf);
			e.preventDefault();
		}
	});
});


/** アメリカの場合のみコレクションを利用できるようにする処理 */
function countrySetting($, $countrySelect, $typeSelect){
	let useCollectinoFeed = false;
	const typeSelectVal = $typeSelect.val();

	// コレクションの利用が可能かどうかの判定、OKの場合種別にコレクションを追加
	$.each(collectionUsables, function(i, elm){
		if($countrySelect.val() == elm){
			$typeSelect.html($typeSelectDom.clone().html());
			useCollectinoFeed = true;
			$typeSelect.val(typeSelectVal);
		}
	});

	// NGの場合コレクションを除いた種別のセレクトボックスをつくる
	if(!useCollectinoFeed){
		const $typeSelectDomOptions = $typeSelectDom.find("option");
		$typeSelect.html("");
		$typeSelectDomOptions.each(function(i, elm){
			const $elm = $(elm).clone();
			if($elm.val() != "collections"){
				$typeSelect.append($elm.prop("outerHTML"));
				if($elm.val() == typeSelectVal){
					$typeSelect.val(typeSelectVal);
				}
			}
		});
	}
}

/** 種別チェック */
function typeSetting($, $typeSelect) {
	const $typeText = $("#typeText");
	const $feedName = $("#feedName");
	const $hideMarket = $("#hideMarket");
	const val = $typeSelect.val();
	const $feedNameVal = $feedName.val();

	if (val == "market") {
		$hideMarket.addClass("hide");
		$typeText.removeClass("required");
		$feedName.prop("required", "");
	} else if (val == "store") {
		$typeText.text(storeName).addClass("required");
		$hideMarket.removeClass("hide");
		$feedName.prop("required", "required");

		changeFeedNameLink($, val, $feedNameVal);
	} else {
		$typeText.text(correctionsName).addClass("required");
		$hideMarket.removeClass("hide");
		$feedName.prop("required", "required");

		changeFeedNameLink($, val, $feedNameVal);
	}
}

/** デフォルトの出力機能を使わない場合はテキストエリアを不活性にする。 */
function disabledFeedCustom($defaultChk, $feedCustom, $feedCustomBefore, $feedCustomAfter, $feedCustomStyle, $assistButtons){
	if ($defaultChk.prop("checked")) {
		$feedCustom.prop("readonly", true);
		$feedCustomBefore.prop("readonly", true);
		$feedCustomAfter.prop("readonly", true);
		$feedCustomStyle.prop("readonly", true);
		$assistButtons.prop("disabled", true);
	} else {
		$feedCustom.prop("readonly", false);
		$feedCustomBefore.prop("readonly", false);
		$feedCustomAfter.prop("readonly", false);
		$feedCustomStyle.prop("readonly", false);
		$assistButtons.prop("disabled", false);
	}
}


/** ストア名・コレクション名のリンクを生成し確認できるようにする */
function changeFeedNameLink($, val, $feedNameVal){
	const $feedNameLink = $('<a target="_blank">' + linkCheck + '</a>');
	if($feedNameVal != null && $feedNameVal != ""){
		$feedNameLink.prop("href", "https://www.zazzle.com/" + val + "/" + $feedNameVal + "?rf=238522058487844682&tc=scadmin");
		$("#feedNameLinkWrap").html($feedNameLink);
	} else {
		$("#feedNameLinkWrap").html("");
	}
}

/** 半角英数チェック */
function isHalf(str) {
	str = (str == null) ? "" : str;
	if (str.match(/^[A-Za-z0-9]*$/)) {
		return true;
	} else {
		return false;
	}
}

/** 半角英数アンダーバーチェック */
function isHalfUnderBar(str) {
	str = (str == null) ? "" : str;
	if (str.match(/^[A-Za-z0-9\_]*$/)) {
		return true;
	} else {
		return false;
	}
}