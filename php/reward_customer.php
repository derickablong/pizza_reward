<?php
if ( ! defined( 'ABSPATH' ) ) exit;



/**
 * Reward Feed
 *
 * @since 1.2
 * @return void
 */
function reward_feed() {
	extract($_POST);

	switch ( $request ) {

		/**
		 * Customers
		 */
		case 'all_customers':
			$results = reward_get_all_customers();
			reward_return($results);
			break;

		case 'search_customer':
			$results = reward_query_search_customer( $filter );
			reward_return($results);
			break;	

		case 'customer_info':
			reward_query_customer_info( $customer_id, $group_id );
			break;
		
		case 'add_customer':
			reward_query_add_customer( $data );
			break;		

		case 'update_customer':
			reward_query_update_customer( (object)$data );
			break;

		case 'delete_customer':
			reward_query_delete_customer( $customer_id );
			break;


		/**
		 * Points
		 */
		case 'delete_points':

			$record = (object)$data;
			$group_id = $record->group_id;

			reward_query_delete_points( $record->row_id );
			reward_query_update_customer_points( $record );

			reward_return(array(
				'status' => 'success',
				'msg' => 'Row successfully deleted.',
				'points' => reward_get_points( $record->group_id ),
				'group_id' => $group_id
			));

			break;


		case 'add_points':

			$record = (object)$data;
			$group_id = reward_query_add_points( $record );

			$record->group_id = $group_id;

			reward_query_update_customer_points( $record );

			reward_return(array(
				'status' => 'success',
				'msg' => 'Amount successfully added.',
				'points' => reward_get_points( $record->group_id ),
				'group_id' => $group_id
			));

			break;
				

		case 'update_points':

			$record = (object)$data;
			$group_id = reward_query_update_points( $record );
			reward_query_update_customer_points( $record );

			reward_return(array(
				'status' => 'success',
				'msg' => 'Row successfully updated.',
				'points' => reward_get_points( $record->group_id ),
				'group_id' => $group_id
			));

			break;



		/**
		 * Certificates
		 */
		case 'unclaimed_certificates':
			reward_unclaimed_certificates();
			break;


		case 'customer_certificates':
			reward_customer_get_certificates( $customer_id );
			break;

		case 'customer_certificates_submit':
			reward_customer_claim_certificates( $certificates );
			break;

		case 'gc_available':
			reward_return(array( 'results' => reward_gc_available( $total_gc ) ));
			break;

		case 'validate_certificate_number':
			reward_validate_assigned_certificate($gc_number);
			break;
		case 'assign-certificate-row':
			reward_assign_certificate( (object)$data );
			break;



	}
}







/**
 * Get All Customers
 *
 * @since 1.2
 * @return void
 */
function reward_get_all_customers() {
	global $wpdb;

	$table = $wpdb->prefix . 'rw_customer';
	$results = reward_results("SELECT * FROM {$table} ORDER BY lname, fname LIMIT 10");
	
	return $results;
}






/**
 * Get Customer By ID
 *
 * @since 1.2
 * @return void
 */
function reward_get_customer( $customer_id = 0 ) {
	global $wpdb;

	$table = $wpdb->prefix . 'rw_customer';
	$results = reward_results("SELECT * FROM {$table} WHERE id = {$customer_id}");
	
	return $results;
}






/**
 * Get All Points By Groupd ID
 *
 * @since 1.2
 * @return void
 */
function reward_get_points( $group_id = 0 ) {
	global $wpdb;

	$table = $wpdb->prefix . 'rw_point';
	$results = reward_results("SELECT * FROM {$table} WHERE group_id = {$group_id} ORDER BY id DESC");
	
	return $results;
}






/**
 * Get Customer Info
 *
 * @since 1.2
 * @return void
 */
function reward_query_customer_info( $customer_id = 0, $group_id = 0 ) {
	reward_return(array(
		'info' => reward_get_customer( $customer_id ),
		'points' => reward_get_points( $group_id )
	));
}






/**
 * Add New Customer
 *
 * @since 1.2
 * @return void
 */
function reward_query_add_customer( $data ) {	

	$customer_data = $data['points'];

	$data = (object)$data;
	$card_number = preg_replace("/[^0-9,.]/", "", $data->card_number );	


	$is_exists = reward_record_exist( 
		'rw_customer',
		array( 'email' => $data->email )
	);

	if( $is_exists === false ) {
		
		$customer_id = reward_insert(
			'rw_customer',
			array(
				'card_number' => $card_number,
				'fname' => trim($data->fname),
				'lname' => trim($data->lname),
				'email' => trim($data->email),
				'created_at' => date('Y-m-d H:i:s')
			)
		);		
		
		$customer_data['data']['customer_id'] = $customer_id;

		if ( $customer_data['has_points'] ) {
			
			$record = (object)$customer_data['data'];
			$group_id = reward_query_add_points( $record );

			$record->group_id = $group_id;

			reward_query_update_customer_points( $record );
		}



		/**
		 * Add Constant Contact
		 *
		 * @author Derick
		 * @since  2018
		 */
		// include( RWDIR . 'autoresponder/constant-contact/cc.php');  
  //   	$reward_cc = new RewardCCIntegration();

		// $args = array(
		// 	'fname' => trim($data->fname),
		// 	'lname' => trim($data->lname),
		// 	'email' => trim($data->email)
		// );
		// $reward_cc->add_contacts($args);
		


		reward_return(array(
			'status' => 'success',
			'msg' => "Customer {$data->fname} {$data->lname} successfully added.",
			'card_number' => $card_number
		));
	} else {
		reward_return(array(
			'status' => 'error',
			'msg' => "Email {$data->email} was already exists."			
		));
	}
}






/**
 * Update Customer
 *
 * @since 1.2
 * @return void
 */
function reward_query_update_customer( $data ) {
	reward_update(
		'rw_customer',
		array(
			$data->column => $data->value,			
			'updated_at' => date('Y-m-d H:i:s')
		),
		array( 'id' => $data->customer_id )
	);

	reward_return(array(
		'status' => 'success',
		'msg' => 'Customer account successfully updated.'
	));
}






/**
 * Search Customer
 *
 * @since 1.2
 * @return void
 */
function reward_query_search_customer( $filter ) {
	global $wpdb;
	

	$search = $filter['search'];
	$search = ltrim($search, 'o');
	$search = rtrim($search, '?');

	$letter = $filter['filter'];

	$table = $wpdb->prefix . 'rw_customer';

	if(! empty( $search )) {
		$query = "SELECT * FROM {$table}
				  WHERE card_number LIKE '%$search%'
				  OR CONCAT(fname, ' ', lname) LIKE '%$search%'
				  ORDER BY lname, fname";
	} else {
		$query = "SELECT * FROM {$table}
				  WHERE fname LIKE '{$letter}%'
				  OR lname LIKE '{$letter}%'
				  ORDER BY lname, fname";
	}
	$results = reward_results($query);
	
	return array(
		'search' => $search,
		'results' => $results
	);
}






/**
 * Delete Customer
 *
 * @since 1.2
 * @return void
 */
function reward_query_delete_customer( $customer_id ) {
	reward_delete(
		'rw_customer',
		array( 'id' => $customer_id )
	);

	reward_return(array(
		'status' => 'success',
		'msg' => 'Customer account successfully deleted.'
	));
}






/**
 * Update Customer Total Points
 *
 * @since 1.2
 * @return void
 */
function reward_query_update_customer_points( $data, $group_id = 0 ) {
		

	if( $data->reward == 1 ) {
		
		$group_id = $data->group_id + ($data->total_gc + 1);
		$data->gc += $data->total_gc;	
		$data->points = $data->remaining_points;	

		reward_customer_gift_certificates($data);
	}
	
	$insert = array(		
		'points' => $data->points,
		'updated_at' => date('Y-m-d H:i:s')
	);

	if( $data->gc > 0 )
		$insert['gc'] = $data->gc;

	if( $data->group_id > 0 )
		$insert['gc_g'] = $data->group_id;

	reward_update(
		'rw_customer',
		$insert,
		array( 'id' => $data->customer_id )
	);
	
}





/**
 * Delete Points
 *
 * @since 1.2
 * @return void
 */
function reward_query_delete_points( $row_id ) {
	reward_delete(
		'rw_point',
		array( 'id' => $row_id )
	);	
}





/**
 * Add Points
 *
 * @since 1.2
 * @return void
 */
function reward_query_add_points( $data, $group_id = 0 ) {
	

	if( $data->total_gc > 0 ) {
		for( $i = 1; $i <= $data->total_gc; $i++ ) {
			
			if( (int)$data->group_id < 1 )
				$data->group_id = "{$data->customer_id}1";
			else
				$data->group_id += 1;

			
			$points_data = array(
				'customer_id' => $data->customer_id,
				'group_id' => $data->group_id,
				'amount' => TARGETPOINTS,
				'points' => TARGETPOINTS,
				'created_at' => date('Y-m-d H:i:s')
			);


			reward_insert(
				'rw_point',
				$points_data
			);			

		}

		$data->group_id += 1;
		$data->points = $data->remaining_points;

	}

	if( $data->points > 0 ) {
		if( (int)$data->group_id < 1 )
			$data->group_id = "{$data->customer_id}1";
		
		$points_data = array(
			'customer_id' => $data->customer_id,
			'group_id' => $data->group_id,
			'amount' => $data->points,
			'points' => $data->points,
			'created_at' => date('Y-m-d H:i:s')
		);


		reward_insert(
			'rw_point',
			$points_data
		);
		
	}

	return $data->group_id;
	
}




/**
 * Update Points
 *
 * @since 1.2
 * @return void
 */
function reward_query_update_points( $data ) {	


	if( $data->total_gc > 0 ) {
		for( $i = 1; $i <= $data->total_gc; $i++ ) {
			
			if( (int)$data->group_id < 1 )
				$data->group_id = "{$data->customer_id}1";
			else
				$data->group_id += 1;

			
			$points_data = array(
				'customer_id' => $data->customer_id,
				'group_id' => $data->group_id,
				'amount' => TARGETPOINTS,
				'points' => TARGETPOINTS,
				'created_at' => date('Y-m-d H:i:s')
			);


			reward_insert(
				'rw_point',
				$points_data
			);			

		}

		$data->group_id += 1;
		$data->points = $data->remaining_points;

	}

	if( $data->points > 0 && $data->total_gc > 0 ) {
		if( (int)$data->group_id < 1 )
			$data->group_id = "{$data->customer_id}1";
		
		$points_data = array(
			'customer_id' => $data->customer_id,
			'group_id' => $data->group_id,
			'amount' => $data->points,
			'points' => $data->points,
			'created_at' => date('Y-m-d H:i:s')
		);


		reward_insert(
			'rw_point',
			$points_data
		);
		
	} else {

		reward_update(
			'rw_point',
			array(
				'amount' => $data->points,
				'points' => $data->points,
				'updated_at' => date('Y-m-d H:i:s')
			),
			array( 'id' => $data->row_id )
		);	
	}


	return $data->group_id;
	
}





/**
 * Unclaimed Certificates
 *
 * @since 1.2
 * @return unclaimed certificates
 */
function reward_unclaimed_certificates() {
	global $wpdb;

	$query = "SELECT cus.id, cus.card_number, cus.fname, cus.lname, cus.gc_g, COUNT(gc.id) total
			  FROM {$wpdb->prefix}rw_customer cus, {$wpdb->prefix}rw_customer_gift_certificate gc
			  WHERE cus.id = gc.customer_id
			  AND gc.claimed = 0
			  GROUP BY gc.customer_id
			  ORDER BY gc.id DESC";
	$results = reward_results($query);

	reward_return(
		array(
			'count' => count($results),
			'results' => $results
		)
	);
}





/**
 * Get Customer Certificates
 *
 * @since 1.2
 * @return customer certificates
 */
function reward_customer_get_certificates( $customer_id ) {
	global $wpdb;

	$query = "SELECT *
			  FROM {$wpdb->prefix}rw_customer_gift_certificate
			  WHERE customer_id = {$customer_id}
			  ORDER BY claimed";
	$results = reward_results($query);

	reward_return(array(			
		'results' => $results
	));
}





/**
 * Submit Customer Certificates
 *
 * @since 1.2
 * @return submit certificates
 */
function reward_customer_claim_certificates( $certificates, $gc = array() ) {

	foreach( $certificates as $gc_number ) {
		$gc[] = $gc_number;
		reward_gc_claim_certificate( $gc_number, false );
	}
	

	reward_return(array(			
		'status' => 'success',
		'msg' => 'Certificates '. implode(', ', $gc) .' are now claimed.'
	));
}



/**
 * Available
 *
 * @since 1.2
 * @return available number
 */
function reward_gc_available( $limit = 5 ) {
	global $wpdb;

	$query = "SELECT gc_number
			  FROM {$wpdb->prefix}rw_gift_certificate 
			  WHERE gc_number NOT IN(
			  	SELECT gc_number 
			  	FROM {$wpdb->prefix}rw_customer_gift_certificate 
			  	WHERE claimed = 0
			  )			  
			  LIMIT {$limit}";

	$results = reward_results($query);
	return $results;
}



/**
 * Validate Assigned Certificate Number
 *
 * @since 1.2
 * @return void
 */
function reward_validate_assigned_certificate( $gc_numbers, $valid = false, $status = '', $msg = '' ) {
	global $wpdb;

	if( is_array( $gc_numbers ) ) {
		foreach( $gc_numbers as $gc_number ) {


			$query = "SELECT gc_number
			  FROM {$wpdb->prefix}rw_gift_certificate
			  WHERE gc_number NOT IN(SELECT gc_number FROM {$wpdb->prefix}rw_customer_gift_certificate)
			  AND gc_number = {$gc_number}
			  AND claimed = 0";

			$gc_result = reward_results($query);


			if(count($gc_result)) {
				
				reward_insert(
					'rw_gift_certificate',
					array(
						'gc_number' => $gc_number
					)
				);

			}		


		}
	}
	

	reward_return(array(		
		'valid' => true,
		'status' => 'success',
		'msg' => 'Certificate is valid!'
	));
}



/**
 * Assign Certificate to Row
 *
 * @since 1.2
 * @return void
 */
function reward_assign_certificate( $data ) {


	reward_update(
		'rw_customer_gift_certificate',
		array(
			'gc_number' => $data->certificate,
			'updated_at' => date('Y-m-d H:i:s')
		),
		array( 'id' => $data->row_id )
	);


	reward_return(array(				
		'status' => 'success',
		'msg' => "Certificate: <strong>{$data->certificate}</strong> was assigned."
	));


}