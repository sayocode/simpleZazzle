<?php
/*
Plugin Name: SC Simple Zazzle
Plugin URI: https://sayoko-ct.com/sc-simple-zazzle/
Description: This is a plug-in that displays products sold on Zazzle.Products listed in the store and marketplace can be easily output with short codes.Affiliate settings are also possible.
Author: sayocode
Version: 1.1.6
Author URI: https://sayoko-ct.com/
Text Domain: sc-simple-zazzle
Domain Path: /languages

*/

/*  Copyright 2019 sayoko (email : communicate@sayoko-ct.com) */

add_action( 'admin_menu', 'scsz_add_plugin_admin_menu' );
load_plugin_textdomain('sc-simple-zazzle', false, basename( dirname( __FILE__ ) ).'/languages' );

function scsz_add_plugin_admin_menu() {
	add_menu_page(
		'Simple-Zazzle', // page_title
		'Simple Zazzle', // menu_title
		'administrator', // capability
		'simple-zazzle', // menu_slug
		'scsz_display_plugin_admin_page', // function
		'dashicons-store',
		51 // position
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
include('src/table_actions.php');
include('src/widget.php');
