/**
 * Import
 *
 * @since 1.2
 * @todo upload zip file 
 */

/**
 * ZIP File
 * @todo  variable for zip file     
 */
var zip_file;

/**
 * ZIP Type
 * @todo  variable for zip file     
 */
var zip_type;


/**
 * ZIP Previous File
 * @todo  variable for previous file
 */
var zip_prev_file;


/**
 * ZIP Data
 * @todo  variable for zip file     
 */
var zip_data;


/**
 * Upload Area Var
 * @todo  variable for drop box     
 */
var upload_area = rw('.rw-upload-area');


/**
 * Upload ZIP File
 * @param  {form} formdata
 * @return json
 */
function reward_import_request( request, callback ){
    reward_wait( rw('.rw-upload-box'), true );
	
    rw.ajax(request).done(function( response ) {        
    	callback(rw.parseJSON(response));	    
 		reward_wait( null, false );
    }); 
}


function reward_upload_zip_file( formdata ) {		
	reward_import_request({
        url: ajax_object.ajax_url,
        type: 'POST',
        data: formdata,
        contentType:false,
    	processData:false       
    }, reward_populate_zip_data);
}


/**
 * Populate ZIP Data
 * @todo  display zip data
 * @param  {json} zip
 * @return json
 */
function reward_populate_zip_data( req_data ){    
	if( rw.isEmptyObject(req_data) !== true ) {
		zip_file = req_data.zip;
        zip_type = req_data.type;

        if( req_data.status === 'valid' )
            rw('.rw-zip-submit').css('display', 'inline-block');            
        else
            rw('.rw-zip-submit').css('display', 'none');
    }
    
    rw('.rw-import-feed')
        .text(req_data.msg)
        .attr('data-status', req_data.status);
    
    rw('.rw-zip-display').show();
    rw('.rw-upload-area').hide();
}


/**
 * Cancel ZIP Upload
 * @todo cancel upload
 * @return void
 */
function reward_cancel_zip_upload(){
	rw('.rw-zip-display').css('display', 'none');
	rw('.rw-import-feed').html('');
	rw('.rw-import-total').text('0.00');
	rw('.rw-upload-area').show();
}


(function(rw) {



    /**
     * Drag Over
     * @todo  action drag over box
     * @return void
     */
    upload_area.on('dragover', function reward_(e) {
        e.stopPropagation();
        e.preventDefault();       
    });


    /**
     * Drop File
     * @todo  action drop file on the box
     * @return void
     */
    upload_area.on('drop', function reward_(e) {
        e.stopPropagation();
        e.preventDefault();
        var file = e.originalEvent.dataTransfer.files;
        var fd = new FormData();
        fd.append('file', file[0]);
        fd.append('action', 'reward_import_handler');
        fd.append('handler', 'upload_file');
        reward_upload_zip_file(fd);        
    });
    

    /**
     * Show File Finder
     * @todo  open file selctor when box is click
     * @return void
     */
    doc.on('click', '#uploadfile', function(){    	
        rw("#rw-file-upload")
        	.val('')
        	.trigger('click');
    });


    /**
     * File Selected
     * @todo  get user file selected
     * @return void
     */        
    doc.on('change', '#rw-file-upload', function(){    	
        var fd = new FormData();
        var files = rw("#rw-file-upload")[0].files[0];

        fd.append('file',files);
        fd.append('action', 'reward_import_handler');
        fd.append('handler', 'upload_file');
        reward_upload_zip_file(fd);
    });


    /**
     * Cancel File Upload
     * @toda delete uploaded zip and data
     * @return void
     */
    doc.on('click', '.rw-zip-cancel', function() {
    	reward_import_request({
    		url: ajax_object.ajax_url,
	        type: 'POST',
	        data: {
	    		action: 'reward_import_handler',
	    		handler: 'cancel_upload',
	    		zip_file: zip_file
	    	}
    	}, reward_cancel_zip_upload);
    });


    /**
     * Submit File Upload
     * @toda insert zip data into database
     * @return void
     */
    doc.on('click', '.rw-zip-submit', function() {
    	reward_import_request({
    		url: ajax_object.ajax_url,
	        type: 'POST',
	        data: {
	    		action: 'reward_import_handler',
	    		handler: 'submit_zip_data',
	    		zip_file: zip_file,
                zip_type: zip_type
	    	}
    	}, function(response) { 
            console.log(response, zip_type); 
    		reward_customers();    		

            if( response.popup )
                reward_unclaimed_certificates();

    		reward_cancel_zip_upload();
    	});
    });


})(jQuery);