<?php
function szFiles(){//スタイルシート
	wp_enqueue_style('szFiles', plugins_url('files/szStyle.css', __FILE__));
	wp_enqueue_script( 'kakakujs', plugins_url('files/szScript.js', __FILE__));
}
add_action('admin_head','szFiles');
