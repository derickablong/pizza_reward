<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Reward Export
 *
 * @since 1.2
 * @return void
 */
function reward_export_handler( $file = array() ) {
	switch( $_POST['request'] ) {

		case 'customers':
			$file = reward_export_archive(
				'customers',
				array( reward_export_all_customers() )
			);
			break;

		case 'all':
			$file = reward_export_all_records();
			break;

		case 'clean':
			reward_export_clean();
			break;

	}
	
	reward_return(array(
		'file' => $file
	));
}




/**
 * Reward Export Create Archive
 *
 * @since 1.2
 * @return void
 */
function reward_export_archive( $request, $files, $zip_file = '' ) {

	$zip = new ZipArchive();	
	$time = microtime(true);

	$file_path = get_bloginfo('siteurl') . '/wp-content/plugins/reward-1.2/export/';
	$file_name = $request . '_reward_export_'. $time .'.zip';
	$zip_file = $file_path . $file_name;
	$path = RWDIR . "export/{$file_name}";


	touch($path);  //<--- this line creates the file
	$res = $zip->open($path, ZipArchive::CREATE);

	if ( $res !== TRUE ) {
	    $zip_file = "Could not open archive";
	}
	
	foreach( $files as $file )
		$zip->addFile( $file['path'], $file['name'] );	

	$zip->close(); 

	return $zip_file;
}


/**
 * Reward Export All Customers
 *
 * @since 1.2
 * @return void
 */
function reward_export_all_customers( $all = false ) {
	global $wpdb;


	$file_path = get_bloginfo('siteurl') . '/wp-content/plugins/reward-1.2/export/';
	$file_name = 'customers_reward_export_'. date('Y_m_d_H_i_s') .'.csv';
	$path = RWDIR . "export/{$file_name}";
	$csv_file = (file_exists($path)) ? fopen($path, "at") : fopen($path, "wt");	

	
	$records = reward_results(
		"SELECT * 
		FROM {$wpdb->prefix}rw_customer"
	);

	foreach( $records as $record ) {		
		$points = number_format($record->points, 2, '.', '');

		$data = array(
			"{$record->card_number}", 
			"{$record->fname}", 
			"{$record->lname}", 
			"{$record->email}", 
			"{$points}"					
		);

		if( $all === TRUE ) {

			$created_at = $record->created_at;
			$updated_at = $record->updated_at;

			$data = array(
				"{$record->id}",
				"{$record->card_number}", 
				"{$record->fname}", 
				"{$record->lname}", 
				"{$record->email}", 
				"{$points}",
				"{$record->gc}", 
				"{$record->gc_g}", 
				"{$created_at}", 
				"{$updated_at}" 
			);
		}

		fputcsv( $csv_file, $data);
	}

	fclose( $csv_file );

	return  array(
		'path' => $path,
		'name' => $file_name		
	);

}




/**
 * Reward Export: Points
 *
 * @since 1.2
 * @return void
 */
function reward_export_points() {
	global $wpdb;

	$file_path = get_bloginfo('siteurl') . '/wp-content/plugins/reward-1.2/export/';
	$file_name = 'points_reward_export_'. date('Y_m_d_H_i_s') .'.csv';
	$path = RWDIR . "export/{$file_name}";
	$csv_file = (file_exists($path)) ? fopen($path, "at") : fopen($path, "wt");	

	
	$records = reward_results(
		"SELECT * 
		FROM {$wpdb->prefix}rw_point"
	);

	foreach( $records as $record ) {		
		
		$points = number_format($record->points, 2, '.', '');
		$created_at = $record->created_at;
		$updated_at = $record->updated_at;

		fputcsv( $csv_file, array(
			"{$record->id}",
			"{$record->customer_id}", 
			"{$record->group_id}", 
			"{$record->amount}", 
			"{$points}", 
			"{$created_at}",					
			"{$updated_at}"
		));
	}

	fclose( $csv_file );

	return  array(
		'path' => $path,
		'name' => $file_name		
	);	
}




/**
 * Reward Export: Gift Certificates
 *
 * @since 1.2
 * @return void
 */
function reward_export_certificates() {
	global $wpdb;

	$file_path = get_bloginfo('siteurl') . '/wp-content/plugins/reward-1.2/export/';
	$file_name = 'giftcertificates_reward_export_'. date('Y_m_d_H_i_s') .'.csv';
	$path = RWDIR . "export/{$file_name}";
	$csv_file = (file_exists($path)) ? fopen($path, "at") : fopen($path, "wt");	

	
	$records = reward_results(
		"SELECT * 
		FROM {$wpdb->prefix}rw_gift_certificate"
	);

	foreach( $records as $record ) {				
		fputcsv( $csv_file, array(	
			"{$record->id}",					
			"{$record->gc_number}", 
			"{$record->claimed}" 						
		));
	}

	fclose( $csv_file );

	return  array(
		'path' => $path,
		'name' => $file_name		
	);	
}




/**
 * Reward Export: Gift Certificates
 *
 * @since 1.2
 * @return void
 */
function reward_export_customer_certificates() {
	global $wpdb;

	$file_path = get_bloginfo('siteurl') . '/wp-content/plugins/reward-1.2/export/';
	$file_name = 'customercertificates_reward_export_'. date('Y_m_d_H_i_s') .'.csv';
	$path = RWDIR . "export/{$file_name}";
	$csv_file = (file_exists($path)) ? fopen($path, "at") : fopen($path, "wt");	

	
	$records = reward_results(
		"SELECT * 
		FROM {$wpdb->prefix}rw_customer_gift_certificate"
	);

	foreach( $records as $record ) {

		$created_at = $record->created_at;
		$updated_at = $record->updated_at;
		$expired_at = $record->expired_at;

		fputcsv( $csv_file, array(
			"{$record->id}",
			"{$record->customer_id}", 
			"{$record->group_id}", 
			"{$record->gc_number}", 
			"{$record->claimed}", 
			"{$expired_at}", 
			"{$created_at}",					
			"{$updated_at}"
		));
	}

	fclose( $csv_file );

	return  array(
		'path' => $path,
		'name' => $file_name		
	);	
}



/**
 * Reward Export All Customers
 *
 * @since 1.2
 * @return void
 */
function reward_export_all_records() {
	return  reward_export_archive(
		'all',
		array(
			reward_export_all_customers(true),
			reward_export_points(),
			reward_export_customer_certificates(),
			reward_export_certificates()
		)
	);
}



/**
 * Reward Export: Clean
 *
 * @since 1.2
 * @return void
 */
function reward_export_clean() {
	$files = glob( RWDIR . 'export/*' );
	foreach($files as $file) { 
		if(is_file($file))
			unlink($file);
	}
}