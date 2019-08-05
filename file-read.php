<?php

function sc_file_read()
{ // スタイルシート
	wp_enqueue_style('sc_file_read', plugins_url('files/szStyle.css', __FILE__));
	wp_enqueue_script('kakakujs', plugins_url('files/szScript.js', __FILE__));
}
add_action('admin_head', 'sc_file_read');
