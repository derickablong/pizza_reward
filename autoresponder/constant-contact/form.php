<style>
	#cc-form {
		width: 100%;
		max-width: 400px;
	}
	#cc-form textarea {
		width: 100%;
		height: 150px;
		padding: 15px;
	}
	#cc-form label {
		font-weight: 600;
		color: #0085ba;
	}
	.cc-cta {
		text-align: right;
	}
	.cc-cta .button {
		display: inline-block;
		vertical-align: top;
		margin-left: 10px;
	}
</style>

<h1>Enter Access Tokens</h1>
<?php self::message(); ?>
<p>Access token is required to add contacts to your constant contact.</p>

<div id="cc-form">
	<form action="" method="post">
		<input type="hidden" name="cc-id" value="<?php echo LIST_ID ?>">
		<label for="cc-tokens">Enter Access Token:</label>
		<textarea name="cc-tokens" id="cc-tokens"><?php echo self::get_tokens() ?></textarea>
		<div class="cc-cta">
			<a href="<?php echo AUTH_URL ?>" class="button button-default" target="_blank">Get Access Token Here</a>
			<input type="submit" value="Save Access Tokens" class="button button-primary" name="cc-save-tokens">
		</div>
	</form>
</div>