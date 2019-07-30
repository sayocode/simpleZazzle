jQuery(document).ready(
		function($) {
			const $affiliateAgree = $("#affiliate_agree");
			const $afterAgreeing = $(".after-agreeing");
			$affiliateAgree.on("change", function(){
				if($(this).prop("checked")){
					$afterAgreeing.prop("style", "display: table-row;");
				} else {
					$afterAgreeing.prop("style", "display: none;");
				}
			});
		});