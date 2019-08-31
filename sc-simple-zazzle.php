<?php
/*
Plugin Name: SC Simple Zazzle
Plugin URI: https://sayoko-ct.com/sc-simple-zazzle/
Description: This plugin gets Zazzle's RSS feed and outputs it as HTML. You can set up a feed for the store as well as the marketplace. Affiliate setting is also possible.
Author: sayocode
Version: 1.0.5
Author URI: https://sayoko-ct.com/
Text Domain: sc-simple-zazzle
Domain Path: /languages

*/
//このプラグインはZazzleのRSSフィードを取得し、HTMLとして出力します。マーケットプレイスのみならず、ストアのフィードを設定することができます。アフィリエイト設定も可能です。

/*  Copyright 2019 sayoko (email : communicate@sayoko-ct.com) */

add_action( 'admin_menu', 'add_plugin_admin_menu' );
load_plugin_textdomain('sc-simple-zazzle', false, basename( dirname( __FILE__ ) ).'/languages' );

function add_plugin_admin_menu() {
	add_menu_page(
		'Simple-Zazzle', // page_title
		'Simple Zazzle', // menu_title
		'administrator', // capability
		'simple-zazzle', // menu_slug
		'scsz_display_plugin_admin_page', // function
		'dashicons-store',
		81 // position
	 );
 
	 add_submenu_page(
		'simple-zazzle', // parent_slug
		'Simple Zazzle', // page_title
		__('Add New', 'sc-simple-zazzle'), // menu_title
		'administrator', // capability
		'simple-zazzle-edit', // menu_slug
		'scsz_display_plugin_sub_page' // function
	 );
}

include('src/view-table.php');

function scsz_display_plugin_sub_page() {
	include('src/edit-short-code.php');
}

include('src/output-item-list.php');
include('src/uninstall.php');
include('src/list-table.php');
include('src/file-read.php');
