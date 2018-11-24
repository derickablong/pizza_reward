<?php
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * DB Delta
 * @todo  library to create database table
 */
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );


/**
 * Migration
 *
 * @since 1.2
 * @return void
 */
function reward_migration() {
	reward_migration_customer();
	reward_migration_points();
	reward_migration_customer_certificates();
	reward_migration_gift_certificates();
}




/**
 * Customers
 *
 * @since 1.2
 * @return void
 */
function reward_migration_customer() {
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();

	$table_name = $wpdb->prefix . "rw_customer";

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  card_number VARCHAR(55),
	  fname VARCHAR(100),
	  lname VARCHAR(100),
	  email VARCHAR(100),
	  points FLOAT(10,2) DEFAULT '0.00',
	  gc mediumint(9) DEFAULT 0,
	  gc_g mediumint(9) DEFAULT 0,
	  created_at VARCHAR(100) DEFAULT '0000-00-00 00:00:00',
	  updated_at VARCHAR(100) DEFAULT '0000-00-00 00:00:00',
	  PRIMARY KEY (id)
	) $charset_collate;";

	dbDelta( $sql );
}




/**
 * Points
 *
 * @since 1.2
 * @return void
 */
function reward_migration_points() {
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();

	$table_name = $wpdb->prefix . "rw_point";

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  customer_id mediumint(9) DEFAULT 0,
	  group_id mediumint(9) DEFAULT 0,
	  amount FLOAT(10,2) NOT NULL DEFAULT '0.00',	  
	  points FLOAT(10,2) NOT NULL DEFAULT '0.00',	  
	  created_at VARCHAR(100) DEFAULT '0000-00-00 00:00:00',
	  updated_at VARCHAR(100) DEFAULT '0000-00-00 00:00:00',
	  PRIMARY KEY (id),	  
	  FOREIGN KEY (customer_id) REFERENCES {$wpdb->prefix}rw_customer (id) ON DELETE CASCADE ON UPDATE CASCADE
	) $charset_collate;";

	dbDelta( $sql );
}




/**
 * Customer Gift Certificates
 *
 * @since 1.2
 * @return void
 */
function reward_migration_customer_certificates() {
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();

	$table_name = $wpdb->prefix . "rw_customer_gift_certificate";

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  customer_id mediumint(9) DEFAULT 0,
	  group_id mediumint(9) DEFAULT 0,
	  gc_number BIGINT DEFAULT 0,	  
	  claimed int(1) DEFAULT 0,
	  expired_at VARCHAR(100) DEFAULT '0000-00-00 00:00:00',
	  created_at VARCHAR(100) DEFAULT '0000-00-00 00:00:00',
	  updated_at VARCHAR(100) DEFAULT '0000-00-00 00:00:00',
	  PRIMARY KEY (id),	  
	  FOREIGN KEY (customer_id) REFERENCES {$wpdb->prefix}rw_customer (id) ON DELETE CASCADE ON UPDATE CASCADE	  
	) $charset_collate;";

	dbDelta( $sql );
}




/**
 * Gift Certificates
 *
 * @since 1.2
 * @return void
 */
function reward_migration_gift_certificates() {
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();

	$table_name = $wpdb->prefix . "rw_gift_certificate";

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,	  
	  gc_number BIGINT DEFAULT 0,
	  claimed INT(1) DEFAULT 0,
	  PRIMARY KEY (id)	  
	) $charset_collate;";

	dbDelta( $sql );
}