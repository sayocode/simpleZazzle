<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function scsz_file_read()
{
	$version = '2.0.0'; // プラグインバージョンに合わせる

	// スタイル
	wp_enqueue_style(
		'scsz-style',
		plugins_url('../files/szStyle.css', __FILE__),
		array(),
		$version
	);

	// スクリプト（共通）
	wp_enqueue_script(
		'scsz-script',
		plugins_url('../files/szScript.js', __FILE__),
		array('jquery'),
		$version,
		true // フッター読み込み
	);

	// sack（必要なら）
	wp_enqueue_script('sack');

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- safe admin page check
	$page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';

	if ($page === 'simple-zazzle-edit') {
		wp_enqueue_script(
			'scsz-edit-script',
			plugins_url('../files/scEditPageScript.js', __FILE__),
			array('jquery'),
			$version,
			true
		);
	}
}
add_action('admin_enqueue_scripts', 'scsz_file_read');
