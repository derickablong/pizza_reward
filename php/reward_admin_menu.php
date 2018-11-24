<?php
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Register admin menu
 *
 * @since 1.2
 * @return void
 */
function reward_admin_menu() {
    global $reward_plugin;

	add_menu_page(
        'Rewards',
        'Rewards',
        'manage_options',
        'reward_main_page',
        'reward_main_page',
        plugins_url( 'reward-1.2/images/icon.png' ),
        6
    );


    include( RWDIR . 'autoresponder/constant-contact/cc.php');  
    $reward_cc = new RewardCCIntegration();

    add_submenu_page( 
        'reward_main_page', 
        'Constant Contact',
        'Constant Contact',
        'moderate_comments', 
        'reward_constant_contact', 
        array($reward_cc, 'create_tokens')
    );
}



/**
 * Load main page when visited
 * by the user
 *
 * @since 1.2
 * @return void
 */
function reward_main_page()
{
	$user = 'server';
	reward_view( $user );
}