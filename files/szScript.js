jQuery(document).ready(function($) {

	// 編集画面
	const $typeSelect = $("#typeSelect");
	typeSetting($, $typeSelect);
	$typeSelect.on("change", function() {
		typeSetting($, $typeSelect);
	});

	const $defaultChk = $("#defaultChk");
	const $feedCustom = $("#feedCustom");
	$defaultChk.on("change", function() {
		if ($defaultChk.prop("checked")) {
			$feedCustom.prop("disabled", true);
		} else {
			$feedCustom.prop("disabled", false);
		}
	});

	/** 要素の入力アシスト */
	$(".assist-button").on("click", function(e) {
		e.preventDefault();
		const obj = "%" + $(this).data("object") + "%";

		const feedCustomDom = $feedCustom.get(0);
		let sentence = feedCustomDom.value;
		const len = sentence.length;
		const pos = feedCustomDom.selectionStart;

		const before = sentence.substr(0, pos);
		const after = sentence.substr(pos, len);

		sentence = before + obj + after;
		feedCustomDom.value = sentence;
		feedCustomDom.selectionStart = pos + obj.length;
	});

	const $feedName = $("#feedName");
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
		if (!isHalf($feedName.val())) {
			$validError.text("ストア名 / コレクション名は半角英数で入力してください。");
			e.preventDefault();
		}
		if($("#maxNum").val() > 100){
			$validError.text("取得数上限は100までの数値で入力してください。");
			e.preventDefault();
		}

		const bcColor = $("#backgroundColor").val().replace("#", "");
		console.log(isFinite(bcColor));
		console.log(bcColor.length != 6);
		if(!isFinite(parseInt("0x" + bcColor , 16)) || bcColor.length != 6){
			$validError.text("画像の背景色は16進数6桁のカラーコードで指定してください。");
			e.preventDefault();
		}

		if(!isHalf($("#affiliateCode").val())){
			$validError.text("アフィリエイトコードは半角英数で入力してください。");
			e.preventDefault();
		}
		if(!isHalf($("#trackingCode").val())){
			$validError.text("トラッキングコードは半角英数で入力してください。");
			e.preventDefault();
		}
	});
});

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
		$typeText.text("ストア名").addClass("required");
		$hideMarket.removeClass("hide");
		$feedName.prop("required", "required");

		changeFeedNameLink($, val, $feedNameVal);
	} else {
		$typeText.text("コレクション名").addClass("required");
		$hideMarket.removeClass("hide");
		$feedName.prop("required", "required");

		changeFeedNameLink($, val, $feedNameVal);
	}
}

function changeFeedNameLink($, val, $feedNameVal){
	const $feedNameLink = $('<a target="_blank">確認</a>');
	if($feedNameVal != null && $feedNameVal != ""){
		$feedNameLink.prop("href", "https://www.zazzle.co.jp/" + val + "/" + $feedNameVal + "?rf=238522058487844682&tc=scadmin");
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