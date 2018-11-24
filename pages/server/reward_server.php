<!-- start of .rw-wrap -->
<div class="wrap rw-wrap">

	<!-- start of .rw-bar -->
	<div class="rw-bar">
		<div class="rw-search-bar">
			<input type="text" class="rw-text" id="rw-customer-input" placeholder="Customer or card number">
			<button class="rw-button rw-button-primary rw-search rw-search-customer"></button>
		</div>
		<div class="rw-top-cta">
			<button class="rw-button rw-button-default rw-button-cta rw-home rw-user-home active">Home</button>
			<button class="rw-button rw-button-default rw-button-cta rw-user rw-user-add">Add Customer</button>
			<button class="rw-button rw-button-default rw-button-cta rw-gift rw-gc-mgnt">Gift Certificates</button>
			<button class="rw-button rw-button-default rw-button-cta rw-import">Import</button>
			<button class="rw-button rw-button-default rw-button-cta rw-export">Export</button>
		</div>
		<div class="rw-search-filter-bar">
			<?php reward_alphabet_filter(); ?>
		</div>
	</div><!-- end of .rw-bar -->
	

	<!-- start of .rw-content -->
	<div class="rw-content">

		<?php
		reward_server_comp('customers');
		reward_server_comp('customer_info');
		reward_server_comp('add_customer');
		reward_server_comp('gift_certificates');
		reward_server_comp('import');
		reward_server_comp('export');
		?>

		
		
	</div> <!-- end of .rw-content -->
</div><!-- end of .rw-wrap -->