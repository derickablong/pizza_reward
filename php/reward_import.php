<?php
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Reward Import
 *
 * @since 1.2
 * @return void
 */
function reward_import_handler() {
	switch ( $_POST['handler'] ) {
		case 'upload_file':
			reward_import_file();
			break;

		case 'cancel_upload':
			reward_delete_zip( $_POST['zip_file'] );
			break;		

		case 'submit_zip_data':
			reward_submit_import_zip( $_POST['zip_type'], $_POST['zip_file'] );
			break;
	}
}


/**
 * Reward Import
 *
 * @since 1.2
 * @return void
 */
function reward_import_file( $zip_file = '' ) {

	$filename = $_FILES['file']['name'];	
	$filesize = $_FILES['file']['size'];	
	$zip_file = RWDIR . 'import/' . $filename;
	
	
	if( move_uploaded_file($_FILES['file']['tmp_name'], $zip_file) )
		$valid = true;

	reward_populate_zip( $zip_file, $filename );				

}


/**
 * Populate ZIP
 *
 * @since 1.2
 * @return void
 */
function reward_populate_zip( $zip_file, $filename, $data = array(), $valid = false, $status = 'error' ) {

	$msg = 'Imported file was invalid. Please check your file if is correct!.';

	$zip_type = explode('_', $filename);
	$zip = zip_open( $zip_file );

	if( $zip ) {
		while ($zip_entry = zip_read($zip)) {
			if (zip_entry_open($zip, $zip_entry) && $valid !== TRUE) {				
				$valid = true;
				$msg = 'Imported file was verified and ready to import. Click the "SUBMIT DATA" to continue.';
				$status = 'valid';

				zip_entry_close($zip_entry);
				break;
			}

		}
	}

	reward_return(array(		
		'msg' => $msg,
		'status' => $status,
		'zip' => $zip_file,
		'type' => $zip_type[0]
	));

}


/**
 * Delete ZIP
 *
 * @since 1.2
 * @return void
 */
function reward_delete_zip( $zip ) {
	unlink($zip);	
	reward_return(array(
		'status' => 'success',
		'handler' => 'cancel'
	));	
}


/**
 * Excess Points
 *
 * @since 1.2
 * @return void
 */
function reward_excess_points_insert( $zip_data ) {

	$customer_id = $zip_data['points']['customer_id'];
	$points = $zip_data['customer']['points'];
	$certificates = $zip_data['customer']['gc'];


	$excess = $points - TARGETPOINTS;
	$entered_points = $points - $excess;


	$group_id = (($customer_id * 10) + 2);



	reward_update(
		'rw_customer',
		array(
			'points' => $excess,
			'gc' => $certificates + 1,
			'gc_g' => $group_id,
		),
		array( 'id' => $customer_id )
	);



	/**
	 * Old Points
	 */
	$zip_data['points']['group_id'] = $group_id - 1;
	$zip_data['points']['amount'] = $entered_points;
	$zip_data['points']['points'] = $entered_points;

	reward_insert(
		'rw_point', 
		$zip_data['points']
	);



	/**
	 * New Points
	 */
	$zip_data['points']['group_id'] = $group_id;
	$zip_data['points']['amount'] = $excess;
	$zip_data['points']['points'] = $excess;

	reward_insert(
		'rw_point', 
		$zip_data['points']
	);


	/**
	 * Generate Gift Certificates
	 */
	reward_customer_gift_certificates(
		(object)array(
			'customer_id' => $customer_id,
			'group_id' => $group_id - 1,
			'gc_number' => 0
		)
	);

}



/**
 * Insert Record From ZIP
 *
 * @since 1.2
 * @return void
 */
function reward_points_insert( $zip_data, $points = 0, $is_award = false ) {

	$points = $zip_data['customer']['points'];
	$customer_id = $zip_data['points']['customer_id'];


	if( $customer_id <= 0 ) {		

		$customer_id = reward_insert(
			'rw_customer', 
			$zip_data['customer']
		);

		$zip_data['points']['customer_id'] = $customer_id;
		$zip_data['points']['group_id'] = "{$customer_id}1";

	}	
	


	if( $points >= TARGETPOINTS ) {

		$is_award = true;
		reward_excess_points_insert( $zip_data );

	} else {
		

		reward_insert(
			'rw_point', 
			$zip_data['points']
		);

		
		reward_update(
			'rw_customer',
			array(
				'points' => $zip_data['customer']['points'],
				'gc_g' => $zip_data['points']['group_id'] 
			),
			array( 'id' => $customer_id )
		);
		

	}

	
	return $is_award;

}


/**
 * Submit ZIP
 *
 * @since 1.2
 * @return void
 */
function reward_submit_import_zip( $type, $zip_file, $is_award = false ) {	
	
	$customers = array();
	$points = array();
	$cus_certificates = array();
	$gift_certificates = array();

	$zip = zip_open( $zip_file );	

	if( $zip ) {
		while ($zip_entry = zip_read($zip)) {
			if (zip_entry_open($zip, $zip_entry) && $valid !== TRUE) {

				$zip_name = explode('_', zip_entry_name($zip_entry));
				
				$data_entry = explode(PHP_EOL, zip_entry_read($zip_entry));
				foreach( $data_entry as $data_row ) {

					$data_record = explode(',', $data_row);

					if( $zip_name[0] === 'customers' )
						$customers[] = $data_record;

					if( $zip_name[0] === 'points' )
						$points[] = $data_record;

					if( $zip_name[0] === 'customercertificates' )
						$cus_certificates[] = $data_record;

					if( $zip_name[0] === 'giftcertificates' )
						$gift_certificates[] = $data_record;

				}
				
				zip_entry_close($zip_entry);				
			}

		}
	}

	//Customers Only
	if( $type === 'customers' )
		reward_import_customers( $customers );

	//All Records
	if( $type === 'all' ) {
		reward_import_all(
			array(
				'customers' => $customers,
				'points' => $points,
				'cus_certificates' => $cus_certificates,
				'gift_certificates' => $gift_certificates
			)
		);
	}


	reward_return(array(
		'status' => 'success',
		'msg' =>  'Zip file was successfully migrated!',
		'popup' => $is_award
	));
	
}


/**
 * Import All
 *
 * @since 1.2
 * @return void
 */
function reward_import_all( $zip_data, $response = array() ) {


	//Customer Table
	$records = $zip_data['customers'];
	foreach( $records as $record ) {

		$data = array(
			'id' => $record[0],
			'card_number' => $record[1],
			'fname' => $record[2],
			'lname' => $record[3],
			'email' => $record[4],
			'points' => $record[5],
			'gc' => $record[6],
			'gc_g' => $record[7],
			'created_at' => $record[8],
			'updated_at' => $record[9]
		);
		
		if( array_key_exists(1, $record) && $record[1] !== '' )
			reward_insert('rw_customer', $data);
	}	


	//Points Table
	$records = $zip_data['points'];
	foreach( $records as $record ) {		
		$data = array(
			'id' => $record[0],
			'customer_id' => $record[1],
			'group_id' => $record[2],
			'amount' => $record[3],
			'points' => $record[4],			
			'created_at' => $record[5],
			'updated_at' => $record[6]
		);
		
		if( array_key_exists(1, $record) && $record[1] !== '' )
			reward_insert('rw_point', $data);
	}	


	//Customer Certificates Table
	$records = $zip_data['cus_certificates'];
	foreach( $records as $record ) {

		$expired_at = $record[5];
		$created_at = $record[6];
		$updated_at = $record[7];

		$data = array(
			'id' => $record[0],
			'customer_id' => $record[1],
			'group_id' => $record[2],
			'gc_number' => $record[3],
			'claimed' => $record[4],			
			'expired_at' => $expired_at,
			'created_at' => $created_at,
			'updated_at' => $updated_at
		);
		
		if( array_key_exists(1, $record) && $record[1] !== '' )
			reward_insert('rw_customer_gift_certificate', $data);
	}	


	//Gift Certificates Table
	$records = $zip_data['gift_certificates'];
	foreach( $records as $record ) {

		$data = array(
			'id' => $record[0],
			'gc_number' => $record[1],
			'claimed' => $record[2]
		);
		
		if( array_key_exists(1, $record) && $record[1] !== '' )
			reward_insert('rw_gift_certificate', $data);
	}


}


/**
 * Import Customers
 *
 * @since 1.2
 * @return void
 */
function reward_import_customers( $customers ) {
	foreach ($customers as $data) {

		if(array_key_exists(1, $data) && $data[1] !== '') {
			$zip_data = array(

				'customer' => array(
					'card_number' => trim($data[0]),
					'fname' => trim($data[1]),
					'lname' => trim($data[2]),
					'email' => trim($data[3]),
					'points' => trim($data[4]),		
					'gc' => 0,	
					'created_at' => date('Y-m-d H:i:s')
				),


				'points' => array(
					'customer_id' => 0,
					'group_id' => 0,
					'amount' => $data[4],
					'points' => $data[4],
					'created_at' => date('Y-m-d H:i:s')
				)

			);
			


			$customer = reward_record_exist( 
				'rw_customer',
				array( 'card_number' => $data[0] )
			);


			
			if( $customer !== false ) {

				$zip_data['customer']['points'] = $customer->points + $data[4];
				$zip_data['customer']['gc'] = $customer->gc;
				$zip_data['customer']['gc_g'] = $customer->gc_g;

				$zip_data['points']['customer_id'] = $customer->id;
				$zip_data['points']['group_id'] = $customer->gc_g;

			}	
			

			$excess = reward_points_insert( $zip_data );

			if( $excess )
				$is_award = true;
		}

	}		
}