<?php
   /*
   Plugin Name: Rewards
   Plugin URI: http://joshuaalexander.net/
   description: Customer loyalty reward
   Version: 1.2
   Author: Joshua Alexander
   Author http://joshuaalexander.net/
   License: GPL2
   */
  
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'RWDIR' , plugin_dir_path( __FILE__ ) );
define( 'TARGETPOINTS', 250 );

/**
  * This class is the main class of the plugin  
  * It loads the included plugin files and add functions to hooks or filters. 
  * The class also handles the admin menu  *
  * @since 1.2
  */
class Reward_Plugin
{

	public $reward_cc;

	/**
	 * Main Construct Function
	 * Default function to call when instantiated
	 */
	function __construct()
	{
		$this->load_dependencies();
		$this->add_hooks();
	}



	/**
  	  * Load File Dependencies
  	  * @todo  include files
  	  */
	public function load_dependencies()
	{
		
		include( RWDIR . 'php/reward_migration.php' );
		include( RWDIR . 'php/reward_admin_menu.php' );
		include( RWDIR . 'php/reward_helper.php' );		
		include( RWDIR . 'php/reward_customer.php' );
		include( RWDIR . 'php/reward_gift_certificates.php' );
		include( RWDIR . 'php/reward_import.php' );		
		include( RWDIR . 'php/reward_export.php' );		
		include( RWDIR . 'php/reward_view.php' );		
	}



	/**
  	  * Add Hooks
  	  * @todo  Adds functions to relavent 
  	  * hooks and filters
  	  */
	public function add_hooks()
	{
		add_action('admin_menu', 'reward_admin_menu');
		add_action('admin_init', 'reward_migration');
			
		add_action('wp_ajax_reward_feed', 'reward_feed');
		add_action('wp_ajax_nopriv_reward_feed', 'reward_feed');

		add_action('wp_ajax_reward_import_handler', 'reward_import_handler');
		add_action('wp_ajax_nopriv_reward_import_handler', 'reward_import_handler');

		add_action('wp_ajax_reward_export_handler', 'reward_export_handler');
		add_action('wp_ajax_nopriv_reward_export_handler', 'reward_export_handler');

		add_action('wp_ajax_reward_gc_handler', 'reward_gc_handler');
		add_action('wp_ajax_nopriv_reward_gc_handler', 'reward_gc_handler');

	}

}



/**
 * Loads the addon if
 * plugin is installed and activated
 *
 * @since 1.2
 * @return void
 */
function reward_load_plugin() {		
	$reward_plugin = new Reward_Plugin();	
}
add_action( 'plugins_loaded', 'reward_load_plugin' );