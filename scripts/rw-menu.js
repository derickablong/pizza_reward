/**
 * Menu
 *
 * @since 1.2
 * @todo main plugin script and
 * making the plugin single page
 */

/**
 * Menu Button
 * @todo variable for menu button
 */
var menu_box = rw('.rw-top-cta .rw-button');


/**
 * Active Menu
 * @todo set active menu
 * @return void
 */
function reward_active_menu( el ) {
	menu_box.removeClass('active');
	el.addClass('active');
}


(function(rw) {



	/**
	 * Action: Home
	 * @action click
	 * @todo  action to show home
	 */
	doc.on('click', '.rw-user-home', function(e){
		var el = rw('.rw-user-add-panel');		
		reward_previous_panel(e);
	});



	/**
	 * Action: Add Customer View
	 * @action click
	 * @todo  action to show add customer form
	 */
	doc.on('click', '.rw-user-add', function(){
		var el = rw('.rw-user-add-panel');
		reward_active_menu(rw(this));
		reward_panel(el, function() {
			setTimeout(function(){
				reward_focus(rw('#rw-text-card-number'));
				reward_wait(null, false);
			},500);
		});
	});



	/**
	 * Action: Gift Certificates
	 * @action click
	 * @todo  gift certificates
	 */
	doc.on('click', '.rw-gc-mgnt', function(){
		var el = rw('.rw-gc-mgnt-panel');
		reward_active_menu(rw(this));
		reward_panel(el, function() {							
			setTimeout(function(){	
				reward_focus(rw('.rw-gc-validate-number'));			
				reward_wait(null, false);
			},500);
		});
	});



	/**
	 * Action: Import View
	 * @action click
	 * @todo  upload csv
	 */
	doc.on('click', '.rw-import', function(){
		var el = rw('.rw-import-panel');
		reward_active_menu(rw(this));
		reward_panel(el, function() {
			rw('.rw-csv-display').hide();
			rw('.rw-upload-area').show();
			setTimeout(function(){				
				reward_wait(null, false);
			},500);
		});
	});



	/**
	 * Action: Export View
	 * @action click
	 * @todo  upload csv
	 */
	doc.on('click', '.rw-export', function(){
		var el = rw('.rw-export-panel');
		reward_active_menu(rw(this));
		reward_panel(el, function() {
			setTimeout(function(){				
				reward_wait(null, false);
			},500);
		});
	});

})(jQuery);