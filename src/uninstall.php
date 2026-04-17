<?php

register_uninstall_hook(__FILE__, 'scsz_uninstall');

function scsz_uninstall()
{
    scsz_uninstall_db();
}

function scsz_uninstall_db()
{
    global $wpdb;

    $scsz_table_name = $wpdb->prefix . 'sc_simple_zazzle_table';

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange, -- safe: removing plugin-specific table during uninstall, table name is static and not user input
    $wpdb->query("DROP TABLE IF EXISTS " . $scsz_table_name);

    delete_option("sc_simple_zazzle_db_version");
}
