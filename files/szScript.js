jQuery(document).ready(function($) {

	// トースト通知クラス
	const Toast = (function(){
		var timer;
		var speed;
		function Toast() {
			this.speed = 3000;
		}
		// メッセージを表示。表示時間(speed)はデフォルトで3秒
		Toast.prototype.show = function(message, speed) {
			if (speed === undefined) speed = this.speed;
			$('.toast').remove();
			clearTimeout(this.timer);
			$('body').append('<div class="toast">' + message + '</div>');
			var leftpos = $('body').width()/2 - $('.toast').outerWidth()/2;
			$('.toast').css('left', leftpos).hide().fadeIn('fast');

			this.timer = setTimeout(function() {
				$('.toast').fadeOut('slow',function(){
					$(this).remove();
				});
			}, speed);
		};
		// 明示的にメッセージを消したい場合は使う
		Toast.prototype.hide = function() {
			$('.toast').fadeOut('slow',function() {
				$(this).remove();
			});
		}
		return Toast;
	})();
	const toast = new Toast();

	// コピー押下でクリップボードにコピー
	$(".text-copy").on("click", function(){
		// ショートコードを専用inputに記載する
		const $outputCode = $("#outputCode");
		$outputCode.val($(this).data("shortCode"));

		const hideInput = $outputCode[0];
		hideCopy(hideInput);
		toast.show(copyMsg);
	});

});
/** 見えないinput要素の内容をクリップボードにコピーする */
function hideCopy(hideInput){

	// その場限りのinput要素を作る
	const newInput = document.createElement("input");
	newInput.type = "text";

	// 処理は一瞬で見えないけど、一応画面外に追い出す…
	newInput.style.position = "absolute";
	newInput.style.marginLeft = "200vw";
	hideInput.parentNode.insertBefore(newInput, hideInput.nextSibling);
	newInput.value = hideInput.value;

	newInput.focus();
	newInput.setSelectionRange(0, newInput.value.length);

	// 選択範囲をコピー
	document.execCommand('copy');
	window.getSelection().collapse(document.body, 0);

	// 選択を解除
	const active_element = document.activeElement;
	if(active_element){
	active_element.blur();
	}

	// 作ったinput要素を消す
	newInput.parentNode.removeChild(newInput);
}

/** テーブル中のアクションリンクをクリックした際の処理 */
function scsz_table_submit(action, scid){
	const $ = jQuery;
	const form = document.getElementById("scsz_table_submit");
	$("#scszSendTableAction").val(action);
	$("#scszSendTableScid").val(scid);
	form.submit();
	return false;
}