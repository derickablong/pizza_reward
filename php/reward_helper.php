<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Alpabhet
 *
 * @since 1.2
 * @return void
 */
function reward_alphabet_filter( $html = '' ) {
	$letters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];

	$html = "<span class='rw-filter-cta active' data-role='all'>All</span>";
	foreach($letters as $letter)
		$html .= "<span class='rw-filter-cta' data-role='{$letter}'>{$letter}</span>";
	echo $html;
}


/**
 * Query: Results
 *
 * @since 1.2
 * @return void
 */
function reward_results( $query ) {
	global $wpdb;
	return $wpdb->get_results($query, OBJECT);
}



/**
 * Query: Insert
 *
 * @since 1.2
 * @return void
 */
function reward_insert( $table, $data ) {
	global $wpdb;
	$wpdb->insert(
		$wpdb->prefix . $table, 
		$data
	);
	return $wpdb->insert_id;
}



/**
 * Query: Update
 *
 * @since 1.2
 * @return void
 */
function reward_update( $table, $data, $where ) {
	global $wpdb;
	$wpdb->update(
		$wpdb->prefix . $table, 
		$data,
		$where
	);
}



/**
 * Query: Query
 *
 * @since 1.2
 * @return void
 */
function reward_query( $query ) {
	global $wpdb;
	$wpdb->query( $query );
}



/**
 * Query: Delete
 *
 * @since 1.2
 * @return void
 */
function reward_delete( $table, $where ) {
	global $wpdb;
	$wpdb->delete(
		$wpdb->prefix . $table,
		$where
	);
}



/**
 * Record Existing
 *
 * @since 1.2
 * @return result
 */
function reward_record_exist( $table, $column, $con = 'AND', $query = null, $where = 'WHERE ' ) {
	global $wpdb;

	if( is_array($column) && count($column) > 0) {
		foreach( $column as $name => $value ) {
			if (! is_numeric($value))
				$value = "'{$value}'";

			$where .= "{$name} = {$value} {$con} ";
		}
		$where = substr( $where, 0, -((strlen($con) + 2)) );
	}

	$query = "SELECT * FROM {$wpdb->prefix}{$table} {$where}";
	$result = reward_results($query);
	
	if( count( $result ) )
		return $result[0];
	return false;
}



/**
 * Customer Gift Certificates
 *
 * @since 1.2
 * @return void
 */
function reward_customer_gift_certificates( $data ) {

	$group_id = ( $data->group_id - $data->total_gc );

	foreach( $data->gc_number as $gc_number ) {

		reward_insert(
			'rw_customer_gift_certificate',
			array(
				'customer_id' => $data->customer_id,
				'group_id' => $group_id,		
				'gc_number' => $gc_number,
				'created_at' => date('Y-m-d H:i:s')
			)
		);

		$group_id++;
	}
}



/**
 * Echo JSON
 *
 * @since 1.2
 * @return json
 */
function reward_return( $data ) {
	echo json_encode($data);
	die;
}



/**
 * Get First Unclaimed Certificate Number
 *
 * @since 1.2
 * @return first unclaimed number
 */
function reward_gc_unclaimed_number( $gc_number = 0 ) {
	global $wpdb;

	$result = reward_results(
		"SELECT gc_number
		FROM {$wpdb->prefix}rw_gift_certificate		
		WHERE gc_number NOT IN ( SELECT gc_number FROM {$wpdb->prefix}rw_customer_gift_certificate )
		LIMIT 1"
	);

	if(count( $result )) {
		$gc_number = $result[0]->gc_number;
	} else {


		$result = reward_results(
			"SELECT gc_number
			FROM {$wpdb->prefix}rw_gift_certificate		
			ORDER BY id DESC
			LIMIT 1"
		);

		if( count($result) )
			$gc_number = ($result[0]->gc_number + 1);
		else
			$gc_number = 1000000000;

		reward_insert(
			'rw_gift_certificate',
			array('gc_number' => $gc_number)
		);

	}

	return $gc_number;
}