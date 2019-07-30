jQuery(document).ready(
		function($) {
			const $affiliateAgree = $("#affiliate_agree");
			const $afterAgreeing = $(".after-agreeing");
			$affiliateAgree.on("change", function(){
				if($affiliateAgree.prop("checked")){
					$afterAgreeing.prop("style", "display: table-row;");
				} else {
					$afterAgreeing.prop("style", "display: none;");
				}
			});

			// 編集画面
			const $typeSelect = $("#typeSelect");
			const $typeText = $("#typeText");
			const $feedName = $("#feedName");
			const $hideMarket = $("#hideMarket");
			$typeSelect.on("change", function(){
				const val = $typeSelect.val();
				if(val == "market"){
					$hideMarket.addClass("hide");
				} else if(val == "store"){
					$typeText.text("ストア名");
					$hideMarket.removeClass("hide");
				} else {
					$typeText.text("コレクション名");
					$hideMarket.removeClass("hide");
				}
			});

			const $defaultChk = $("#defaultChk");
			const $feedCustom = $("#feedCustom");
			$defaultChk.on("change", function(){
				if($defaultChk.prop("checked")){
					$feedCustom.prop("disabled", true);
				} else {
					$feedCustom.prop("disabled", false);
				}
			});
		});