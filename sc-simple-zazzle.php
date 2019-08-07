<?php
/*
Plugin Name: SC Simple Zazzle
Plugin URI: https://sayoko-ct.com/sc-simple-zazzle/
Description: このプラグインはZazzleのRSSフィードを取得し、HTMLとして出力します。マーケットプレイスのみならず、ストアのフィードを設定することができます。アフィリエイト設定も可能です。
Author: sayocode
Version: 1.0.0
Author URI: https://sayoko-ct.com/
*/

/*  Copyright 2019 sayoko (email : communicate@sayoko-ct.com) */

add_action( 'admin_menu', 'add_plugin_admin_menu' );
 
function add_plugin_admin_menu() {
	 add_menu_page(
		'Simple-Zazzle', // page_title
		'Simple Zazzle', // menu_title
		'administrator', // capability
		'simple-zazzle', // menu_slug
		'display_plugin_admin_page', // function
		'dashicons-store',
		81 // position
	 );
 
	 add_submenu_page(
		'simple-zazzle', // parent_slug
		'Simple Zazzle', // page_title
		'新規', // menu_title
		'administrator', // capability
		'simple-zazzle-edit', // menu_slug
		'display_plugin_sub_page' // function
	 );
}

include('view-table.php');

function display_plugin_sub_page() {
	include('edit-short-code.php');
}

include('output-item-list.php');
include('uninstall.php');
include('list-table.php');
include('file-read.php');
