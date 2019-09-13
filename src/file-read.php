<?php

function scsz_file_read()
{ // スタイルシート
	wp_enqueue_style('sc_file_read', plugins_url('../files/szStyle.css', __FILE__));
    wp_enqueue_script( array( 'sack' ));
	wp_enqueue_script('szScriptjs', plugins_url('../files/szScript.js', __FILE__));

    if(isset($_GET['page']) && $_GET['page'] == 'simple-zazzle-edit'){
        wp_enqueue_script('szEditPageScriptjs', plugins_url('../files/scEditPageScript.js', __FILE__));
    }
}
add_action('admin_head', 'scsz_file_read');
