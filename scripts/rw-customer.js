/**
 * Customer
 *
 * @since 1.2
 * @todo main plugin script and
 * making the plugin single page
 */

/**
 * jQuery
 * @todo variable for jQuery
 */
var rw = jQuery;


/**
 * Document
 * @todo variable for document
 */
var doc = rw(document);


/**
 * Customer ID
 * @todo variable for customer id
 */
var customer_id = 0;


/**
 * Group ID
 * @todo  variable for group id
 */
var customer_group_id = 0;


/**
 * Target Points
 * @todo variable for total points
 */
var target_points = 250.00;


/**
 * Total Certificates
 * @todo variable for total certificates
 */
var customer_certificates_total = 0;


/**
 * Total Amount
 * @todo variable for total amount
 */
var customer_total_amount = 0;


/**
 * Total Points
 * @todo variable for total points
 */
var customer_total_points = 0;


/**
 * Option
 * @todo variable data for elements
 * being updated
 */
var option = {};


/**
 * Assigned Row
 * @todo variable assigned row
 */
var assigned_row = 0;


/**
 * Points
 * @todo variable data points
 */
var user_data = {};


/**
 * Months
 * @todo variable for document
 */
var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];


/**
 * Search Filter
 * @todo variable for search filter
 */
var search_filter = {
	search: '',
	filter: 'all'
};	


/**
 * Interval
 * @todo variable for interval
 */
var interval;





/**
 * Wait
 * @param  {element} el 
 * @todo  show loading state
 * @return void
 */
function reward_wait( el, show ) {
	if(show) {
		rw('.rw-wait').remove();
		el.append('<div class="rw-wait"></div>');
	} else {
		rw('.rw-wait').remove();
	}
}




/**
 * Create Field
 * @param  {array} options 
 * @todo  convert element into input field
 * @return void
 */
function reward_create_field(callback) {

	var type = 'text';
	if(option.role === 'card_number')
		type = 'number';

	option.el.html(
		`<input type="`+ type +`"
		value="`+ option.data +`" 
		 class="rw-update-field" 
		data-role="`+ option.role +`">`
	);
	callback(rw('.rw-update-field'));
}




/**
 * Focus Created Field
 * @param  {element} el
 * focus active created element
 * @return void
 */
function reward_generate_el(el) {
	if( typeof option.role !== 'undefined' ) {
		reward_create_field(function(el){
			reward_focus(el);
		});
	} else {
		rw('.rw-user-table').css('display', 'none');
		rw('.rw-user-gc-table').css('display', 'table');
		reward_feed({
			action: 'reward_feed',
			request: 'customer_certificates',
			customer_id: customer_id		
		}, function(response) {			
			
			var feed = '', checkbox = '', status = ['unclaimed', 'claimed'];

			if(rw.isEmptyObject(response.results) === true) {
				feed = '<tr><td colspan="3" class="rw-empty-output" style="font-size:18px!important;">No customer found.</td></tr>';
			} else {
				rw(response.results).each(function(index, result) {

					if( parseInt(result.claimed) === 0 )
						checkbox = `<input type="checkbox" class="rw-user-claim-checkbox" value="`+ result.gc_number +`">`;
					else
						checkbox = '';

					if (result.gc_number <= 0)
						result.gc_number = '<a href="#" class="assign-certificate" data-row="'+ result.id +'">Unassigned</a>';

					feed += `
						 <tr>
						 	<td>`+checkbox+`</td>
						 	<td>`+ result.gc_number +`</td>
						 	<td><span class="rw-status-`+ status[result.claimed] +`" title="`+ status[result.claimed] +` certificate"></span></td>					 	
						 </tr>
					     `;
				});
			}
			rw('.rw-user-gc-feed').html(feed);

		});
	}
}




/**
 * Panel
 * @todo  create animation 
 * and load content
 * @param  {element} el
 * @param  {Function} callback
 * @return void
 */
function reward_panel(el, callback) {		
	rw('.rw-panel')
		.addClass('rw-panel-hide')
		.animate({
			'left' : '-2000px'
		}, 500, function() {
			rw('.rw-panel').css('display', 'none');
			el
				.removeClass('rw-panel-hide')
				.removeAttr('style')
				.css('display', 'block');
			reward_wait( el, true );	
			callback();	
		});		
}




function reward_previous_panel(e) {
	e.stopPropagation();
    e.preventDefault();

	rw('.rw-panel')
		.addClass('rw-panel-hide')
			.animate({
				'left' : '-2000px'
			}, 500, function() {
				rw('.rw-sidebar')
					.removeClass('rw-panel-hide')
					.removeAttr('style')
					.css('display', 'block');
				rw('.rw-top-cta .rw-button').removeClass('active');
				reward_active_menu(rw('.rw-user-home'));
			});
}




/**
 * Feed
 * @todo  ajax feed
 * @param  {string} content
 * @return void
 */
function reward_feed(data, callback) {
	rw.ajax({
		url: ajax_object.ajax_url,
		type: 'POST',			
		data: data
	})
	.done(function(response) {
		callback( rw.parseJSON(response));	
		reward_wait( null, false); 		
	});
}





/**
 * Message
 * @param  {element} el
 * @param  {string} status
 * @param  {string} msg
 * @todo  show message on front-end
 * @return void
 */
function reward_message(el, status, msg) {
	if(rw('.rw-msg').length)
		rw('.rw-msg').remove();
	el.prepend(
		`<div class="rw-msg rw-msg-`+ status +`">` +
			msg +
		`</div>`
	);
	setTimeout(function(){
		rw('.rw-msg').fadeOut('fast', function(){
			rw('.rw-msg').remove();
		});
	}, 6000);
}



/**
 * Populate Customers
 * @todo  load customers
 * @param  {object} customers
 * @return void
 */
function reward_populate_customers(customers) {
	var frontend = '';

	if(rw.isEmptyObject(customers) === true) {
		frontend = '<div class="rw-empty-output">No customer found.</div>';
	} else {
		rw(customers).each(function(index, customer) {
			
			var points = isNaN(parseFloat(customer.points))? '0.00' : parseFloat(customer.points).toFixed(2);

			frontend += `<div class="rw-user-box" data-id="`+ customer.id +`" data-gid="`+ customer.gc_g +`">` +
				`<div class="rw-user-box-wrap">` +
					`<div class="rw-user-box-left">` +
						`<span class="rw-user-name">`+ customer.fname + ' ' + customer.lname +`</span>` +
						`<span class="rw-user-card">`+ customer.card_number +`</span>` +
					`</div>` +
					`<div class="rw-user-box-right">` +
						`<span class="rw-user-points">`+ points +`</span>` +
						`<span class="rw-user-label">Points</span>` +
					`</div>` +
				`</div>` +
			`</div>`;
		});
	}
	rw('.rw-sidebar').html(frontend);
}





/**
 * Customers
 * @todo Get and load all customers
 * @return void
 */
function reward_customers() {
	reward_feed({
		action: 'reward_feed',
		request: 'all_customers'			
	}, reward_populate_customers);
}





/**
 * Load Customer Info
 * @todo show in front end
 * @return void
 */
function reward_customer_info(data) {
	rw(data).each(function(index, customer){	
		customer_certificates_total = customer.gc;		
		rw('.rw-detail-card .rw-info').text( customer.card_number );			
		rw('.rw-detail-user-fname .rw-info').text( customer.fname );			
		rw('.rw-detail-user-lname .rw-info').text( customer.lname );			
		rw('.rw-detail-email .rw-info').text( customer.email );
		rw('.rw-detail-gift .rw-info').text( ((customer.gc == null)? 0 : customer.gc) );		
	});
}




/**
 * Format Date
 * @todo show in front end
 * @return void
 */
function reward_get_date(date) {
	date = new Date(date);	
	date = months[ date.getMonth() ] + ' ' + ('0' + date.getDate()).slice(-2) + ', ' + date.getFullYear();
	return date;
}




/**
 * Load Customer Points
 * @todo show in front end
 * @return void
 */
function reward_customer_load_points(data) {
	var	feed = '', count = 1;

	customer_total_points = 0;

	if(rw.isEmptyObject(data) === true) {			
			feed = '<tr><td colspan="3" class="rw-empty-output">No record found.</td></tr>';
	} else {
		rw(data).each(function(index, customer){	

			var edit_cta = '';
			if(count === 1) {
				edit_cta = `<a href="#" title="Edit row" class="rw-report-cta rw-edit-points" data-plug="`+ customer.id + '::' + customer.points +`"></a>` +
							`<a href="#" title="Remove row" class="rw-report-cta rw-remove-points" data-plug="`+ customer.id + '::' + customer.points +`"></a>`;
			}

			feed +=	`<tr>` +
						`<td width="100">`+ edit_cta + `</td>` +
						`<td>`+ reward_get_date(customer.created_at) +`</td>` +							
						`<td width="40">`+ customer.points +`</td>` +
					`</tr>`;
			
			customer_total_amount += parseFloat(customer.amount);
			customer_total_points += parseFloat(customer.points);		
			

			count++;

		});
	}
	rw('.rw-points-feed').html(feed);
	rw('.rw-points-total').text(customer_total_points.toFixed(2));
}




/**
 * Update Customer
 * @todo update customer info
 * @return void
 */
function reward_update_customer(el) {
	reward_feed({
		action: 'reward_feed',
		request: 'update_customer',
		data: {
			customer_id: customer_id,
			column: el.attr('data-role'),
			value: ((el.val() === '')? 'None' : el.val()) 
		}
	}, function(response) {
		reward_message(
			rw('.rw-user-details'),
			response.status,
			response.msg
		);
		reward_customers();
	});
}




/**
 * Search Customer
 * @todo search customer
 * @return void
 */
function reward_search_customer(e) {	
	if(search_filter.search === '' && search_filter.filter == 'all') {
		reward_customers();
		reward_previous_panel(e);
	} else {
		reward_feed({
			action: 'reward_feed',
			request: 'search_customer',
			filter: search_filter
		}, function(response) {
			rw('#rw-customer-input').val(response.search);
			reward_populate_customers(response.results);
		});
		reward_previous_panel(e);
	}
}





/**
 * Points Popup
 * @todo show points form
 * @return void
 */
function reward_popup_points(display, text, points, callback) {
	if( display ) {
		rw('.rw-user-info-panel').append(
			`<div class="rw-popup-points">` +					
				`<div>` +
					`<input type="text" class="rw-text" id="rw-text-points" value="`+ parseFloat(points).toFixed(2) +`">` +
				`</div>` +
				`<div>` +
					`<button class="rw-button rw-button-primary rw-dollar rw-button-manage-points">`+ text +` Amount</button>` +
					`<button class="rw-button rw-button-default rw-button-cta rw-cancel rw-button-cancel-points">Cancel</button>` +
				`</div>` +
			`</div>`
		);
	} else {
		rw('.rw-popup-points').remove();
	}
	callback();
}




/**
 * Check Total Points
 * @todo to check if total points reach
 * the target points for certifications
 * @return true
 */
function reward_is_target_points_reach(callback) {

	var valid = true, no_of_certificates = 0, remaining_points = 0;

	if( customer_total_points >= target_points ) {


		no_of_certificates = Math.floor(customer_total_points / target_points);
		remaining_points = customer_total_points % target_points;

		rw('.rw-user-data-popup').remove();

		var gc_data = {
			total: no_of_certificates,
			action: 'rw-user-data-assign-gc'
		};

		reward_show_available_gc( gc_data, function( table ) {

			rw('.rw-user-info-panel').append(table);
			callback(true, valid, no_of_certificates, remaining_points);

		});


		
	} else {
		callback(false, valid, 0, 0);
	}
	
}



/**
 * Show Available Certificates
 * @action show certicates
 * @todo  show available certificates
 */
function reward_show_available_gc( gc_data, callback ) {




	reward_feed({
		action: 'reward_feed',
		request: 'gc_available',
		total_gc: gc_data.total	
	}, function(response) {

		var gc_available = '', gc_fields = '', first_gc_number = 0;

		/**
		 * Produce Number Of GC
		 */
		for( var i = 1; i <= gc_data.total; i++ ) {
			gc_fields += '<input type="number" class="rw-text rw-user-data-gc-number" id="rw-user-data-gc-number">';
		}


		if(rw.isEmptyObject(response.results) === false) {
			rw(response.results).each(function(index, result) {
				gc_available += `
					<tr>
						<td>
							<input type="checkbox" name="rw-user-data-gc-number" id="rw-user-data-gc-number-radio" value="`+ result.gc_number +`">
						</td>
						<td>`+ result.gc_number +`</td>
					</tr>
					`;
			});
		} else {
			valid = false;
			gc_available += `
					<tr>							
						<td colspan="2" class="rw-empty-output">There's no available certificate number to assign.</td>
					</tr>
					`;
		}


		var table = `
			<div class="rw-user-data-popup">
				<div class="rw-user-data-popup-wrap">
					<div class="rw-user-data-popup-content">
						<div class="rw-user-data-popup-box">
							
							<div class="rw-user-data-popup-left">
								<h2>Assign Certificate Number</h2>
								<p>Please assign certificate number below or select the available certificate number on the right side.</p>
								`+ gc_fields +`
								<button class="rw-button rw-button-primary rw-check `+ gc_data.action +`">Assign Certificates</button>
								<button class="rw-button rw-button-default rw-cancel rw-user-data-assign-gc-cancel">Cancel</button>
							</div>

							<div class="rw-user-data-popup-right">
								<table border="0" cellspacing="0" cellpadding="0" class="rw-user-data-gc-table">`
									+ gc_available +
								`</table>
							</div>

						</div>
					</div>
				</div>
			</div>		
			`;


		callback( table );

	});



}



/**
 * Focus Input
 * @action focus
 * @todo  focus input text
 */
function reward_focus(el) {
	el.focus().select();
}




/**
 * Points Prepare
 * @todo prepare form
 * @return void
 */
function reward_prepare_points(action, data) {
	reward_popup_points(true, action, data.points, function() {

		reward_focus(rw('#rw-text-points'));		
		
		rw('.rw-button-manage-points').on('click', function(e) {
			e.stopPropagation();
        	e.preventDefault();

			var points_enter = parseFloat(rw('#rw-text-points').val());	
			var db_total_points = customer_total_points;			

			if(points_enter <= 0) {

				alert('Amount should be greater than zero!');
				reward_focus(rw('#rw-text-points'));

			} else {	

			
				/**
				 * Update Points				 
				 */
				if( action === 'update' ) {															
					customer_total_points = Math.abs(customer_total_points - parseFloat(data.points)) + points_enter;					
				}


				/**
				 * Add Points				 
				 */
				if( action === 'add' ) {
					customer_total_points += points_enter;
				}


				data.points = points_enter;
				data.total_points = customer_total_points;

				
				/**
				 * Check Points
				 */
				
				reward_is_target_points_reach(function(is_rewarded, no_certificate, total_certificates, remaining_points) {

					if(is_rewarded) {
						

						var gc_el = rw('.rw-detail-gift .rw-info');
						gc_el.text( parseInt(gc_el.text()) + total_certificates );

						customer_certificates_total = parseInt(customer_certificates_total) + total_certificates;										
						
						data.reward = 1;						
						data.total_gc = total_certificates;
						data.remaining_points = remaining_points;


						user_data.data = data;
						user_data.action = action;

						rw('.rw-button-cancel-points').trigger('click');
						
					} else if( is_rewarded === false && no_certificate !== false ) {
						reward_save_points(action, data);
					}
					

				});					


			}//end of if points enter

		});
	});
}




/**
 * Save Points
 * @todo save customer points
 * @return void
 */
function reward_save_points(action, data) {	
	reward_feed({
		action: 'reward_feed',
		request: action + '_points',
		data: data
	}, function(response) {
		reward_message(
			rw('.rw-user-report'),
			response.status,
			response.msg
		);
		
		reward_popup_points(false, null, 0, function() {
			customer_group_id = response.group_id;

			reward_customer_load_points( response.points );
			reward_customers();				
		});
	});
}




/**
 * Popup: Unclaimed Certificates
 * @todo show unclaimed certificates
 * @return void
 */
function reward_show_unclaimed_certificates_popup( results ) {
	if(rw('.rw-modal').length < 1) {
		
		reward_close_interval();

		var modal = '', report = '';


		rw(results).each(function(index, result) {
			report += `
				<tr>						
					<td>
						<div class="gc-name">`+ result.fname + ' ' + result.lname +`</div>
						<div class="gc-card">`+ result.card_number +`</div>
					</td>
					<td class="gc-total">`+ result.total +`</td>
					<td>
						<button class="rw-button rw-button-default rw-button-cta rw-user rw-show-certificates" data-id="`+ result.id +`" data-gid="`+ result.gc_g +`">Show</button>
					</td>
				</tr>
			`;
		});
		

		modal = `
			<div class="rw-modal rw-celebrate-modal">
				<div class="rw-modal-wrap">
					<span class="rw-ribbon"></span>
					<a href="#" class="rw-modal-close">x</a>
					<h2>Gift Certificates</h2>
					<table cellpadding="0" cellspacing="0">
						`+ report +`
					</table>
				</div>
			</div>`;


		rw('body').append(modal);
		
	}
}




/**
 * Unclaimed Certificates
 * @todo get unclaimed certificates
 * @return void
 */
function reward_unclaimed_certificates() {		
	reward_feed({
		action: 'reward_feed',
		request: 'unclaimed_certificates'
	}, function(response) {
		if( parseInt( response.count ) )
			reward_show_unclaimed_certificates_popup( response.results );
	});		
}




/**
 * Show Customer Info
 * @todo show customer info
 * @return void
 */
function reward_show_customer_info( info, gc ) {
	var data = info;

	reward_panel(rw('.rw-user-info-panel'), function() {			
		if(rw.isEmptyObject(data) === false) {
			reward_feed(data, function(response){		
				
				reward_customer_info( response.info );					
				reward_customer_load_points( response.points );
				if(gc) {
					rw('.rw-modal-close').trigger('click');
					rw('.rw-detail-gift .rw-info').trigger('click');					
				}

			});
		}
		data = {};
	});
}




/**
 * Update Customer
 * @todo update customer info
 * @return void
 */
function reward_assign_certificate() {


	var gc_data = {
		total: 1,
		action: 'rw-gc-assign'
	};

	reward_show_available_gc( gc_data, function( table ) {		


		rw('.rw-user-info-panel').append( table );		
		

	});
	

}



/**
 * Clear Interval
 */
function reward_close_interval() {
	clearInterval(interval);
	interval = null
}



/**
 * Get Selected Certificates
 */
function reward_get_selected_certificates(callback) {
	var certificate_numbers = [];			
	rw('.rw-user-claim-checkbox:checked').each(function() {
		certificate_numbers.push(this.value);
	});	
	callback( certificate_numbers );
}




(function(rw){


	/**
	 * Create Field
	 * @action click
	 * @todo  edit detail info
	 */
	doc.on('click', '.rw-user-details .rw-detail-box .rw-info', function(e){
		e.stopPropagation();
        e.preventDefault();

		option = {
			el: rw(this),
			data: rw(this).text(),
			role: rw(this).attr('data-role')
		};
		reward_generate_el();
	});
	doc.on('click', '.rw-edit-info', function(e){
		e.stopPropagation();
        e.preventDefault();


		var active_el = rw(this).prev();		
		option = {
			el: active_el,
			data: active_el.text(),
			role: active_el.attr('data-role')
		};		
		reward_generate_el();
	});



	/**
	 * Reverse
	 * @action blur
	 * @todo  reverse to original element
	 */
	doc.on('blur', '.rw-update-field', function(){		

		var data;


		if( rw(this).val() !== option.data ) {
			data = (rw(this).val() === '')? 'None' : rw(this).val();		
			reward_update_customer(rw(this));
		} else {
			data = option.data;		
		}
		
		option.el.html(data);
		
	});



	/**
	 * Disable Enter
	 * @action enter
	 * @todo  disable on entering edit field
	 */
	doc.on('keyup', '.rw-update-field', function(event){
		if(event.which === 13) {
			rw(this).trigger('blur');
			return false;
		}
	});



	/**
	 * Action: Customer Info
	 * @action click
	 * @todo  action to show customer info
	 */
	doc.on('click', '.rw-user-box', function(e){
		e.stopPropagation();
        e.preventDefault();

		customer_id = rw(this).attr('data-id');
		customer_group_id = rw(this).attr('data-gid');

		rw('.rw-user-table').css('display', 'none');
		rw('.rw-user-points-table').css('display', 'table');

		reward_show_customer_info({
			action: 'reward_feed',
			request: 'customer_info',
			customer_id: customer_id,
			group_id: customer_group_id
		}, false);
		
	});



	/**
	 * Action: Previous Panel
	 * @action click
	 * @todo  action to back to previous panel
	 */
	doc.on('click', '.rw-back-panel', reward_previous_panel);



	/**
	 * Action: Add Customer DB
	 * @action click
	 * @todo  insert data to database
	 */
	doc.on('click', '.rw-add-customer', function(e){
		e.stopPropagation();
        e.preventDefault();

        var card_number = rw('#rw-text-card-number').val();
        var fname = rw('#rw-text-fname').val();
        var lname = rw('#rw-text-lname').val();
        var email = rw('#rw-text-email').val();
        var amount = rw('#rw-text-amount').val();


        if( fname === '' || lname === '' || email === '' ) {


        	reward_message(
				rw('.rw-user-add-panel .rw-user-details'),
				'error',
				'<strong>First Name, Last Name and Email is required</strong>.<br>Kindly check if fields are not empty.'
			);


        } else {



        	var customer_data = {
        		has_points: 0
        	};
        	var data = {
        		customer_id: 0,
        		points: 0,
        		gc: 0,
        		total_points: 0,
        		total_gc: 0,
        		reward: 0,
        		remaining_points: 0,
        		gc_number: []
        	};

        	customer_total_points = amount;
        	data.points = amount;
			data.total_points = amount;

			if( customer_total_points >= target_points ) {

				var no_of_certificates = 0;
				
				no_of_certificates = Math.floor(customer_total_points / target_points);
				remaining_points = customer_total_points % target_points;				
				
				data.reward = 1;						
				data.total_gc = no_of_certificates;
				data.remaining_points = remaining_points;			

				var gc_number = [];
				for ( var i = 0; i < no_of_certificates; i++  )
					gc_number.push( 'Not Assigned' );
				data.gc_number = gc_number;


			}

			if ( amount > 0 ) {
				customer_data.has_points = 1;
				customer_data.data = data;
			}




			reward_feed({
				action: 'reward_feed',
				request: 'add_customer',
				data: {
					card_number: card_number,
					fname: fname,
					lname: lname,
					email: email,
					points: customer_data				
				}
			}, function(response){
				rw('#rw-text-card-number').val(response.card_number);
				reward_message(
					rw('.rw-user-add-panel .rw-user-details'),
					response.status,
					response.msg
				);

				if( response.status === 'success' ) {
					rw('.rw-user-add-panel .rw-user-details .rw-text').val('');
					reward_focus(rw('#rw-text-card-number'));
					reward_customers();
				}
			});

		}
	});



	/**
	 * Action: Search Customer
	 * @action click
	 * @todo  search customer
	 */
	doc.on('click', '.rw-search-customer', function(e) {
		e.stopPropagation();
        e.preventDefault();

        search_filter = {
        	search: rw('#rw-customer-input').val(),
        	filter: rw('.rw-filter-cta.active').attr('data-role')
        };

        reward_search_customer(e);
	});
	doc.on('keyup', '#rw-customer-input', function(e) {
		if(e.which === 13)
			rw('.rw-search-customer').trigger('click');
	});


	/**
	 * Action: Delete Customer
	 * @action click
	 * @todo  delete customer
	 */
	doc.on('click', '.rw-remove-customer', function(e) {		
		e.stopPropagation();
        e.preventDefault();

		var del = confirm('Delete ' + rw('.rw-detail-user .rw-info').text() + ' account?');
		if(del) {
			reward_feed({
				action: 'reward_feed',
				request: 'delete_customer',
				customer_id: customer_id
			}, function(response) {
				reward_customers();
				reward_previous_panel(e);
			});
		}
	});



	/**
	 * Action: Delete Points
	 * @action click
	 * @todo  delete row points
	 */
	doc.on('click', '.rw-remove-points', function(e) {		
		e.stopPropagation();
        e.preventDefault();

		var plug = (rw(this).attr('data-plug')).split('::');
		var del = confirm('Are you sure you want to delete this row?');
		var data = {
			customer_id: customer_id,			
			row_id: plug[0],
			group_id: customer_group_id,
			total_points: (parseFloat(rw('.rw-points-total').text()) - parseFloat( plug[1] ))
		};
		
		if(del) {
			reward_feed({
				action: 'reward_feed',
				request: 'delete_points',
				data: data				
			}, function(response){	

				reward_message(
					rw('.rw-user-report'),
					response.status,
					response.msg
				);
				reward_customer_load_points( response.points );
				reward_customers();
			});
		}
	});



	/**
	 * Action: Points
	 * @action click
	 * @todo  row points
	 */
	doc.on('click', '.rw-edit-points', function(e) {
		e.stopPropagation();
        e.preventDefault();

		var plug = (rw(this).attr('data-plug')).split('::');

		var data = {
			row_id: plug[0],
			group_id: customer_group_id,
			gc: customer_certificates_total,
			points: plug[1],
			customer_id: customer_id,
			excess: 0,
			total_gc: 0,
			remaining_points: 0
		};

		reward_prepare_points('update', data);

	});
	doc.on('click', '.rw-add-points', function(e) {
		e.stopPropagation();
        e.preventDefault();

		var data = {			
			group_id: customer_group_id,
			gc: customer_certificates_total,
			points: 0,
			customer_id: customer_id,
			excess: 0,
			total_gc: 0,
			remaining_points: 0
		};
		
		reward_prepare_points('add', data);

	});
	doc.on('click', '.rw-button-cancel-points', function(e) {
		e.stopPropagation();
        e.preventDefault();

		reward_popup_points(false, null, 0, function() {
			//do nothing
		});
	});



	/**
	 * Close Modal
	 */
	doc.on('click', '.rw-modal-close', function(e) {
		e.stopPropagation();
        e.preventDefault();

		reward_close_interval();
		rw('.rw-modal').remove();
	});


	/**
	 * Cancel Customer GC
	 */
	doc.on('click', '.rw-user-gc-cancel', function(e) {				
		e.stopPropagation();
        e.preventDefault();

		rw('.rw-user-table').css('display', 'none');
		rw('.rw-user-points-table').css('display', 'table');
	});


	/**
	 * TD
	 */
	doc.on('click', 'tr input', function() {		

		var el = rw(this), assigned = false;

		if( el.is(':checked') ) {

			el.closest('tr').addClass('active');
			rw('.rw-user-data-gc-number').each(function() {
        	if( assigned === false && rw(this).val() === '' ) {
        		rw(this).val( el.val() );
        		assigned = true;
        	}

        });                      

		} else {

			var num = el.val();
			assigned = false;

			el.closest('tr').removeClass('active');   

			rw('.rw-user-data-gc-number').each(function() {
        	if( assigned === false && num === rw(this).val() ) {
        		rw(this).val( '' );
        		assigned = true;
        	}
        });        

		}

        
	});


	/**
	 * Redeem Customer GC
	 */
	doc.on('click', '.rw-user-claim-submit', function(e) {	
		e.stopPropagation();
        e.preventDefault();

		reward_get_selected_certificates(function(certificate_numbers) {
			reward_feed({
				action: 'reward_feed',
				request: 'customer_certificates_submit',
				certificates: certificate_numbers
			}, function(response) {
				reward_message(
					rw('.rw-user-report'),
					response.status,
					response.msg
				);
				rw('.rw-detail-gift .rw-info').trigger('click');
			});
		});
	});



	/**
	 * Show Customer Gift Certficates
	 */
	doc.on('click', '.rw-show-certificates', function(e) {
		e.stopPropagation();
        e.preventDefault();

		customer_id = rw(this).attr('data-id');
		group_id = rw(this).attr('data-gid');
		reward_show_customer_info({
			action: 'reward_feed',
			request: 'customer_info',
			customer_id: customer_id,
			group_id: group_id
		}, true);
	});



	/**
	 * Show Customer Gift Certficates
	 */
	doc.on('click', '.rw-user-data-assign-gc', function(e) {
		e.stopPropagation();
        e.preventDefault();

        var gc_number = [], has_empty = false;
        rw('.rw-user-data-gc-number').each(function() {        	
        	gc_number.push( rw(this).val() );
        	if( rw(this).val() === '' )
        		has_empty = true;
        });

        if( has_empty ) {

        	alert('Certficate number is required. Kindly check if fields are not empty.');

        } else {

	        user_data.data.gc_number = gc_number;

	        reward_feed({
				action: 'reward_feed',
				request: 'validate_certificate_number',
				gc_number: user_data.data.gc_number
			}, function(response) {			
				if(response.valid) {
					reward_save_points(
			        	user_data.action,
			        	user_data.data
			        );

			        rw('.rw-user-data-popup').remove();
				}
			});		
	    }
        
	});
	doc.on('click', '.rw-user-data-assign-gc-cancel', function(e) {
		e.stopPropagation();
		e.preventDefault();

		rw('.rw-user-data-popup').remove();
	});



	/**
	 * Search Filter
	 */
	doc.on('click', '.rw-filter-cta', function(e) {
		e.stopPropagation();
        e.preventDefault();

        rw('#rw-customer-input').val('');

        rw('.rw-filter-cta').removeClass('active');
        rw(this).addClass('active');
        rw('.rw-search-customer').trigger('click');
	});



	/**
	 * Default actions 
	 * in page load	 
	 */
	rw(window).load(function() {		
		if(rw('.toplevel_page_reward_main_page').length) {

			/**
			 * Action: Load Customers
			 * @action load
			 * @todo  default load customers
			 */
			reward_customers();



			/**
			 * Action: Hide WP Admin Menu
			 * @action load
			 * @todo  default hide wp admin menu
			 */			
			document.body.className+=' folded';
			rw('#wpfooter').remove();	
			rw('.collapse-button-icon').css('display', 'none');
				

		}
	});



	/**
	 * Assign Certificate
	 */
	doc.on('click', '.assign-certificate', function(e) {
		e.stopPropagation();
        e.preventDefault();

        var row_id = rw(e.target).data('row');
        assigned_row = row_id;
        
        reward_assign_certificate();
        
	});



	/**
	 * Set Assigned Certificate
	 */
	doc.on('click', '.rw-gc-assign', function(e) {
		e.stopPropagation();
        e.preventDefault();

        var gc_number = rw('.rw-user-data-gc-number').val();
        if ( gc_number !== '' ) {

        	var gc_data = {
        		certificate: gc_number,
        		row_id: assigned_row
        	};

        	reward_feed({
				action: 'reward_feed',
				request: 'assign-certificate-row',
				data: gc_data
			}, function(response) {
				
				rw('.assign-certificate[data-row="'+ assigned_row +'"]').parent().html( gc_number );

				assigned_row = 0;

				reward_message(
					rw('.rw-user-report'),
					response.status,
					response.msg
				);

				rw('.rw-user-data-assign-gc-cancel').trigger('click');
				
			});


        }
        
	});
	
	

})(jQuery);