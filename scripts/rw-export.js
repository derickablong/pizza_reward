/**
 * Export
 *
 * @since 1.2
 * @todo export csv file
 */

(function(rw) {


	/**
	 * Upload CSV File
	 * @param  {form} formdata
	 * @return json
	 */
	function reward_export_request( request, callback ){
		reward_wait( rw('.rw-upload-box'), true );
	    rw.ajax(request).done(function( response ) {
	    	callback(rw.parseJSON(response));	    	 		 	
	    }); 
	}


	/**
     * Clean Zip
     * @toda export customer csv file
     * @return csv file
     */
    function reward_export_clean() {
    	reward_export_request({
    		url: ajax_object.ajax_url,
	        type: 'POST',
	        data: {
	    		action: 'reward_export_handler',
	    		request: 'clean'  		
	    	}
    	}, function(response) {
    		reward_wait( null, false );
    		console.log('Done export...');
    	});
    }


	/**
     * Export Records
     * @toda export customer csv file
     * @return csv file
     */
	function reward_export_records( request ) {
		reward_export_request({
    		url: ajax_object.ajax_url,
	        type: 'POST',
	        data: {
	    		action: 'reward_export_handler',
	    		request: request  		
	    	}
    	}, function( response ) {
    		if( typeof response.file !== 'undefined' ) {
    			console.log(response.file);
    			setTimeout(function() {    				    				    				
    				$.fileDownload(response.file);    				
    				reward_export_clean();
    			}, 1000);
    		}
    	});
	}


	/**
     * Export CSV: Customers
     * @toda export customer csv file
     * @return csv file
     */
    doc.on('click', '.rw-export-data', function(e) {
    	e.stopPropagation();
    	e.preventDefault();
    	reward_export_records('customers');
    });


	/**
     * Export CSV: All
     * @toda export customer csv file
     * @return csv file
     */
    doc.on('click', '.rw-export-data-all', function(e) {
    	e.stopPropagation();
    	e.preventDefault();
    	reward_export_records('all');
    });


})(jQuery);