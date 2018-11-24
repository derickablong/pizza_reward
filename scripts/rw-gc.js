/**
 * Gift Certificates
 *
 * @since 1.2
 * @todo gift certificate validator and generator
 * making the plugin single page
 */

var reward_typing_timer;                
var reward_done_typing_interval = 500;
var last_gc_number = '000';  
var active_page = 1;


/**
 * GC Request
 * @todo  ajax request
 * @return json data
 */
function reward_gc_request( data, callback ) {
	reward_wait( rw('.rw-gc-content'), true );
	rw.ajax({
		url: ajax_object.ajax_url,
        type: 'POST',
        data: data
	}).done(function(response) {
		callback( rw.parseJSON(response) );		
	});
}


/**
 * GC Menu
 * @todo  show active content
 * @return void
 */
function reward_gc_menu(menu) {
	rw('.rw-gc-content').hide();
	rw('.rw-gc-content-' + menu).show();

	if(menu === 'validate')
		reward_focus(rw('.rw-gc-'+ menu +'-number'));
}


/**
 * Popup: Info
 * @todo  Popup for info
 * @return void
 */
function reward_info_popup(el, show, valid, result) {
	if( show ) {
		
		var msg, button = `<button class="rw-button rw-button-default rw-info rw-check-ok">OK, Thanks</button>`;
		if(valid) {
			
			msg = `
					<h4>Certificate number <font color="#000" style="text-decoration:underline">`+ result.gc_number +`</font> owner was <font color="#771f1a">`+ result.name +`</font></h4>					
				  `;
			
			valid = 'check';
			button = `<div class="rw-info-popup-cta">
						<button class="rw-button rw-button-primary rw-check rw-gc-claim">Redeem Certificate</button>
						<button class="rw-button rw-button-default rw-info rw-check-ok">OK, Thanks</button>
					  </div>`;

		} else {
			msg = '<h4>Certificate number is not valid!</h4>';
			valid = 'wrong';
		}



		el.append(
			`<div class="rw-gc-popup">
				<div class="rw-gc-popup-wrap">
					<div class="rw-gc-popup-content">
						<div class="rw-gc-popup-box">
							<span class="rw-icon rw-icon-`+ valid +`"></span>
							`+ msg + button + 
						`</div>
					</div>
				</div>
			</div>`
		);
	} else {
		rw('.rw-gc-popup').remove();
	}
}


/**
 * Certificate Validity
 * @todo  to check if certificate is valid
 * @return boolean
 */
function reward_validate_gc_number(gc_number) {
	reward_gc_request({
		action: 'reward_gc_handler',
		handler: 'gc_validate',
		gc_number: gc_number
	}, function(response) {
		reward_info_popup(
			rw('.rw-gc-content-validate'), 
			true, 
			response.valid,
			response.result
		);
		reward_wait( null, false );
	});
}


/**
 * Generate New Certificates
 * @todo  to generate new certificate numbers
 * @return void
 */
function reward_generate_certificates(starting_number) {
	reward_gc_request({
		action: 'reward_gc_handler',
		handler: 'gc_generate',
		starting_number: starting_number	
	}, function(response) {
		reward_message(
			rw('.rw-gc-content-generate'),
			response.status,
			response.msg
		);
		reward_focus(rw('.rw-gc-generate-number'));
		reward_wait( null, false );

		rw('.rw-user-data-popup').remove();		
	});
}



/**
 * Check Starting Number
 * @todo  to check if starting number is used
 * @return boolean
 */
function reward_check_starting_number(starting_number, callback) {
	reward_gc_request({
		action: 'reward_gc_handler',
		handler: 'gc_check_start_number',
		starting_number: starting_number		
	}, callback);
}



/**
 * Populate GC
 * @todo  load genereated certificates
 * @return void
 */
function reward_gc_lists(results) {
	
	var certificates = results.results;
	var claimed = results.claimed;
	var unclaimed = results.unclaimed;
	var total = results.total;
	var pagi = Math.floor(total / 8);

	if( total % 8 > 0 )
		pagi += 1;


	var feed = '', status = ['unclaimed', 'claimed'];


	if(rw.isEmptyObject(certificates) === true) {
		feed = '<tr class="rw-empty-output"><td colspan="2" style="text-align:center;font-weight:400">No certificate found.</td></tr>';
	} else {
		rw(certificates).each(function(index, certificate) {
			feed += `<tr>` +						
						`<td>`+ certificate.gc_number +`</td>` +
						`<td>` +
							`<span class="rw-status-`+ status[certificate.claimed] +`" title="`+ status[certificate.claimed] +` certificate"></span>` + 
						`</td>` +
					`</tr>`;
		});


		/**
		 * Pagination
		 */
		rw('.rw-gc-nav').html('');
		for( var i = 1; i <= pagi; i++ ) {
			var active = '';
			if( active_page === i )
				active = 'class="active"';
			rw('.rw-gc-nav').append( '<a href="#" data-nav="'+ i +'" '+ active +'>'+ i +'</a>' );
		}

	}

	rw('.rw-gc-box-claimed .rw-gc-box-total').text(claimed);
	rw('.rw-gc-box-unclaimed .rw-gc-box-total').text(unclaimed);
	rw('.rw-gc-box-total-all .rw-gc-box-total').text(total);
	rw('.rw-gc-feed').html(feed);

	reward_gc_menu('all');
	setTimeout(function() {
		reward_wait( null, false );				
	}, 1000);
	
}



/**
 * Populate GC
 * @todo  load genereated certificates
 * @return void
 */
function reward_populate_gc_all(options) {
	reward_gc_request({
		action: 'reward_gc_handler',
		handler: 'gc_all',
		filter: options,
		pagi: active_page
	}, reward_gc_lists);
}



/**
 * Get Last Number
 * @todo  get last certificate number
 * @return last number
 */
function reward_gc_get_last_number(onload) {
	reward_gc_request({
		action: 'reward_gc_handler',
		handler: 'gc_last_number',		
	}, function(response) {		

		last_gc_number = response.last_number;
		var value = (isNaN(last_gc_number) || last_gc_number === null) ? '0000000000' : parseInt(last_gc_number) + 1;
		rw('.rw-gc-generate-number').val(value);
		

		reward_focus(rw('.rw-gc-generate-number'));

		if(onload) {			
			if(last_gc_number === null) {
				alert('You should generate first the gift certificate numbers!');
				rw('.rw-gc-mgnt').trigger('click');				
			}

			rw('.rw-gc-sidebar .rw-button').removeClass('active');
			rw('.rw-gc-sidebar .rw-gear').addClass('active');
		}
		
		

		reward_gc_menu('generate');		
		reward_wait(null, false);
		
	});
}



/**
 * Done Typing
 * @todo  search certificate number
 * @return certificate
 */
function reward_done_typing() {
	var search = rw('.rw-gc-num-search').val();
	var query = "WHERE gc_number = "+ search;
	if( search === '' )
		query = 'ORDER BY gc_number ASC LIMIT 8';
	reward_populate_gc_all(query);
}




(function(rw) {


	/**
	 * Action: Check Validity
	 * @action click
	 * @todo  action to check if certificate
	 * number is still valid
	 */
	doc.on('click', '.rw-gc-check-validity', function() {
		reward_validate_gc_number(rw('.rw-gc-validate-number').val());
	});


	/**
	 * Action: Close Popup
	 * @action click
	 * @todo  action to close info popup
	 */
	doc.on('click', '.rw-check-ok', function() {
		reward_info_popup(
			null, 
			false, 
			false,
			{}
		);
		reward_focus(rw('.rw-gc-validate-number'));
	});


	/**
	 * Action: GC Menu
	 * @action click
	 * @todo  action to show selected content
	 */
	doc.on('click', '.rw-gc-sidebar .rw-button', function() {
		var role = rw(this).attr('data-role');

		rw('.rw-gc-sidebar .rw-button').removeClass('active');
		rw(this).addClass('active');

		if(role === 'all')
			reward_populate_gc_all('ORDER BY gc_number ASC LIMIT 8');			
		else if(role === 'generate')
			reward_gc_get_last_number(false);
		else
			reward_gc_menu(role);
		

		
		
	});


	/**
	 * Action: Redeem Certificate
	 * @action: click
	 * @todo  set certificate number status to claimed
	 */
	doc.on('click', '.rw-gc-claim', function() {
		reward_gc_request({
		action: 'reward_gc_handler',
			handler: 'gc_claim_certificate',
			gc_number: rw('.rw-gc-validate-number').val()
		}, function(response) {
			reward_info_popup(
				null, 
				false, 
				false,
				{}
			);
			reward_focus(rw('.rw-gc-validate-number'));
			reward_message(
				rw('.rw-gc-content-validate'),
				response.status,
				response.msg
			);
			reward_wait( null, false );
			rw('.rw-detail-gift .rw-info').trigger('click');

		});		
	});


	/**
	 * Action: Typing
	 * @action keypress
	 * @totdo action to check if done typing
	 */
	//on keyup, start the countdown
	doc.on('keyup', '.rw-gc-num-search', function () {
	  clearTimeout(reward_typing_timer);
	  reward_typing_timer = setTimeout(reward_done_typing, reward_done_typing_interval);
	});

	//on keydown, clear the countdown 
	doc.on('keydown', '.rw-gc-num-search', function () {
	  clearTimeout(reward_typing_timer);
	});


	/**
	 * Action: GC Generate
	 * @action click
	 * @todo  action to generate gc number
	 */
	doc.on('click', '.rw-gc-generate-submit', function() {

		var starting_number = parseInt(rw('.rw-gc-generate-number').val());		

		if( isNaN(starting_number)  || starting_number === '' || starting_number <= 0 ) {			
			reward_message(
				rw('.rw-gc-content-generate'),
				'error',
				"Invalid 'Starting Number' value!"
			);
			reward_focus(rw('.rw-gc-generate-number'));					
		} else {
			reward_check_starting_number(starting_number, function(response) {


				if(response.excess) {
					reward_message(
						rw('.rw-gc-content-generate'),
						response.status,
						response.msg
					);
					reward_focus(rw('.rw-gc-generate-number'));
				} else {
					reward_generate_certificates(starting_number);
				}

				reward_wait( null, false );


			});
		}

	});


	/**
	 * Action: GC Pagination
	 * @action click
	 * @todo  action paginate
	 */
	rw(document).on('click', '.rw-gc-nav a', function(e) {
		e.stopPropagation();
		e.preventDefault();

		var limit;
		active_page = parseInt(rw(this).attr('data-nav'));

		if( active_page === 1 )
			limit = '0,8';
		else
			limit = (( ( active_page - 1 ) * 8 ) - 1) + ',8';

		reward_populate_gc_all('ORDER BY gc_number ASC LIMIT ' + limit);
	});


	rw(window).on('load', function() {
		reward_gc_get_last_number(true);
	});



})(jQuery);