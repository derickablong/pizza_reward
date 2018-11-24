<?php
/**
 * Clean up the options 
 * and database tables
 * @package  reward-1.2
 */

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

 
// drop a custom database table
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->prefix}rw_customer WHERE id > 0");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}rw_customer_gift_certificate");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}rw_gift_certificate");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}rw_point");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}rw_customer");