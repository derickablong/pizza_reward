<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Reward Gift Certificate
 *
 * @since 1.2
 * @return void
 */
function reward_gc_handler() {
	switch ($_POST['handler']) {

		case 'gc_validate':
			reward_gc_validate( $_POST['gc_number'] );
			break;

		case 'gc_check_start_number':
			reward_gc_check_start_number( $_POST['starting_number'] );
			break;

		case 'gc_generate':
			reward_gc_generate( $_POST['starting_number'] );
			break;

		case 'gc_all':
			reward_gc_feed( $_POST['filter'] );
			break;

		case 'gc_last_number':
			reward_gc_last_number();
			break;

		case 'gc_claim_certificate':
			reward_gc_claim_certificate( $_POST['gc_number'] );
			break;

	}
}



/**
 * Reward Gift Certificate Validate
 *
 * @since 1.2
 * @return void
 */
function reward_gc_validate( $gc_number ) {
	global $wpdb;

	$query = "SELECT cgc.gc_number, CONCAT(cus.fname,' ', cus.lname) as name
			  FROM {$wpdb->prefix}rw_customer_gift_certificate cgc,
			       {$wpdb->prefix}rw_customer cus
			  WHERE cgc.gc_number = {$gc_number}
			  AND cus.id = cgc.customer_id
			  AND cgc.claimed = 0";
	$gc_result = reward_results($query);


	reward_return(array(		
		'result' => $gc_result[0],
		'valid' => (count($gc_result) > 0 )
	));
}



/**
 * Generate Cirtificates
 *
 * @since 1.2
 * @return void
 */
function reward_gc_generate( $starting_number, $total_number = 10 ) {	

	for( $total_number; $total_number > 0; $total_number-- ) {

		$gc_number = $starting_number;		
		reward_insert('rw_gift_certificate', array(
			'gc_number' => $gc_number
		));
		$starting_number++;
	}


	reward_return(array(		
		'status' => 'success',
		'msg' => 'Certificate setup successfully save!'
	));
}



/**
 * Check Starting Number
 *
 * @since 1.2
 * @return void
 */
function reward_gc_check_start_number( $starting_number ) {	
	global $wpdb;

	$result = reward_results(
		"SELECT *
		FROM {$wpdb->prefix}rw_gift_certificate
		WHERE gc_number = {$starting_number}"
	);


	$found = (count($result) > 0);

	reward_return(array(		
		'status' => ($found)? 'error' : 'success',
		'msg' => ($found)? 'Starting number was already generated.' : '',
		'excess' => $found
	));
}




/**
 * Redeemed
 *
 * @since 1.2
 * @return claimed
 */
function reward_gc_feed_status( $status = 0 ) {
	global $wpdb;

	$results = reward_results(
		"SELECT *
		FROM {$wpdb->prefix}rw_gift_certificate
		WHERE claimed = {$status}
		ORDER BY gc_number ASC"
	);

	return $results;
}



/**
 * Feed
 *
 * @since 1.2
 * @return void
 */
function reward_gc_feed( $filter = '' ) {	
	global $wpdb;

	$query = "SELECT *
			 FROM {$wpdb->prefix}rw_gift_certificate
			 {$filter}";
	$results = reward_results($query);

	$claimed = count(reward_gc_feed_status(1));
	$unclaimed = count(reward_gc_feed_status());

	reward_return(array(				
		'results' => $results,	
		'claimed' => sprintf("%02d", $claimed),
		'unclaimed' => sprintf("%02d", $unclaimed),
		'total' => sprintf("%02d", ($unclaimed + $claimed))		
	));
}



/**
 * Last Number
 *
 * @since 1.2
 * @return last number
 */
function reward_gc_last_number() {
	global $wpdb;

	$results = reward_results(
		"SELECT gc_number
		FROM {$wpdb->prefix}rw_gift_certificate
		ORDER BY id DESC
		LIMIT 1"
	);

	reward_return(array(
		'last_number' => $results[0]->gc_number
	));
}



/**
 * Redeem Certificate Number
 *
 * @since 1.2
 * @return void
 */
function reward_gc_claim_certificate( $gc_number, $return = true ) {
	
	reward_update(
		'rw_customer_gift_certificate',
		array('claimed' => 1),
		array('gc_number' => $gc_number)
	);

	reward_update(
		'rw_gift_certificate',
		array('claimed' => 1),
		array('gc_number' => $gc_number)
	);

	if( $return ) {
		reward_return(array(
			'status' => 'success',
			'msg' => "Card number <strong>{$gc_number}</strong> was now claimed."
		));
	}
}