<?php
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Reward View
 *
 * @since 1.2
 * @return void
 */
function reward_view( $reward_user ) {

	reward_admin_css();
	reward_admin_script();

	if( $reward_user == 'server' )
		reward_server_page();
	else if( $reward_user == 'server' )
		reward_manager_page();

}



/**
 * Server Page
 *
 * @since 1.2
 * @return void
 */
function reward_server_page() {	
	include( RWDIR . 'pages/server/reward_server.php');
}



/**
 * Server Page Components
 *
 * @since 1.2
 * @return void
 */
function reward_server_comp( $comp ) {	
	include( RWDIR . "pages/server/reward_{$comp}.php");
}



/**
 * Admin CSS
 *
 * @since 1.2
 * @return void
 */
function reward_admin_css() {
	wp_enqueue_style(
		'reward_font_web', 
		'//fonts.googleapis.com/css?family=Karla:400,400i,700,700i'
	);
	wp_enqueue_style(
		'reward_admin_css', 
		plugins_url( '../css/rw-admin.css', __FILE__ ) 
	);
}



/**
 * Admin Scripts
 *
 * @since 1.2
 * @return void
 */
function reward_admin_script() {
	
	wp_enqueue_script(
		'reward_customer_script', 
		plugins_url( '../scripts/rw-customer.js', __FILE__ ),
		false,
		array(),
		true
	);

	wp_enqueue_script(
		'reward_menu_script', 
		plugins_url( '../scripts/rw-menu.js', __FILE__ ),
		false,
		array(),
		true
	);

	wp_enqueue_script(
		'reward_import_script_lib', 
		plugins_url( '../scripts/rw-upload-lib.js', __FILE__ ),
		false,
		array(),
		true
	);

	wp_enqueue_script(
		'reward_file_lib', 
		plugins_url( '../scripts/rw-file.js', __FILE__ ),
		false,
		array(),
		true
	);

	wp_enqueue_script(
		'reward_import_script', 
		plugins_url( '../scripts/rw-import.js', __FILE__ ),
		false,
		array(),
		true
	);

	wp_enqueue_script(
		'reward_export_script', 
		plugins_url( '../scripts/rw-export.js', __FILE__ ),
		false,
		array(),
		true
	);

	wp_enqueue_script(
		'reward_gc_script', 
		plugins_url( '../scripts/rw-gc.js', __FILE__ ),
		false,
		array(),
		true
	);


	wp_localize_script( 'reward_customer_script', 'ajax_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );

	wp_localize_script( 'reward_import_script', 'ajax_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );

	wp_localize_script( 'reward_export_script', 'ajax_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );

	wp_localize_script( 'reward_gc_script', 'ajax_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );
}