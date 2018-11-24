<!-- start of .rw-export-panel -->
<div class="rw-user-data rw-gc-mgnt-panel rw-panel" style="display: none">
	<a href="#" class="rw-button rw-button-primary rw-back-panel" title="Back to main"></a>
	<div class="rw-gc-mgnt-content">
		
		<div class="rw-gc-sidebar">
			<button class="rw-button rw-button-default rw-bulb active" data-role="validate">
				Validate Certificate
			</button>
			<button class="rw-button rw-button-default rw-gear" data-role="generate">
				Generate Certificate
			</button>
			<button class="rw-button rw-button-default rw-paper" data-role="all">
				Certificates Statistics
			</button>
		</div>

		<div class="rw-gc-content rw-gc-content-validate" style="display: block;">
			<h2>Validate Gift Certificate Number</h2>
			<p>Enter gift certificate number below to check if it's still valid or already used.</p>			
			<input type="number" class="rw-text rw-gc-validate-number" value="0000000000">
			<button class="rw-button rw-button-primary rw-check rw-gc-check-validity">Check Validity</button>
		</div>

		<div class="rw-gc-content rw-gc-content-generate">
			<h2>Generate Gift Certificate</h2>
			<p>Enter the format of your starting gift certificate number. The system will auto increment the given certificate number up to 10 certificate numbers by default. This will be the basis of your certificate number format and cannot be modified.</p>
			<div class="rw-gc-generate-form">
				<div class="rw-field-box">
					<label>Starting Certificate Number</label>
					<input type="number" class="rw-text rw-gc-generate-number" value="0000000000">
				</div>
				<div class="rw-field-box" style="width: 140px;">
					<label>&nbsp;</label>
					<button class="rw-button rw-button-primary rw-check rw-gc-generate-submit">Generate</button>
				</div>
			</div>		
		</div>

		<div class="rw-gc-content rw-gc-content-all">
			<h2>Generated Certificates</h2>	
			
			<div class="rw-gc-right-sidebar">
				<div class="rw-gc-box rw-gc-box-claimed">					
					<div class="rw-gc-box-wrap">
						<span class="rw-gc-box-total">00</span>						
						<span class="rw-gc-box-label">Redeemed</span>
					</div>					
				</div>
				<div class="rw-gc-box rw-gc-box-unclaimed">					
					<div class="rw-gc-box-wrap">
						<span class="rw-gc-box-total">00</span>						
						<span class="rw-gc-box-label">Unclaimed</span>
					</div>					
				</div>
				<div class="rw-gc-box rw-gc-box-total-all">					
					<div class="rw-gc-box-wrap">
						<span class="rw-gc-box-total">00</span>						
						<span class="rw-gc-box-label">TOTAL</span>
					</div>					
				</div>
			</div>

			<table border="0" cellpadding="0" cellspacing="0">
				<thead>
					<tr>						
						<th colspan="2">
							<input type="number" class="rw-text rw-gc-num-search" placeholder="Search number">
						</th>
					</tr>
				</thead>
				<tbody class="rw-gc-feed"></tbody>				
			</table>

			<div class="rw-gc-nav"></div>		
		</div>

	</div>
</div>
<!-- end of .rw-export-panel -->