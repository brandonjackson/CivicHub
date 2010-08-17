<?php
/*
 * Taken from wp-login.php
 * If you place a register.php file in your lwa template folder, you'll have it inserted automatically at the footer 
 * of your theme, providing it calls the wp_footer action.
 */ 
?>
<div id="LoginWithAjax_Register" style="display:none;" class="default">
	<h4 class="message register"><?php _e('Register For This Site') ?></h4>
	<form name="registerform" id="registerform" action="<?php echo $this->url_remember ?>" method="post">
		<p>
			<label><?php _e('Username') ?><br />
			<input type="text" name="user_login" id="user_login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" size="20" tabindex="10" /></label>
		</p>
		<p>
			<label><?php _e('E-mail') ?><br />
			<input type="text" name="user_email" id="user_email" class="input" value="<?php echo esc_attr(stripslashes($user_email)); ?>" size="25" tabindex="20" /></label>
		</p>
		<?php do_action('register_form'); ?>
		<p id="reg_passmail"><?php _e('A password will be e-mailed to you.') ?></p>
		<p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="<?php esc_attr_e('Register'); ?>" tabindex="100" /></p>
		<input type="hidden" name="lwa" value="1" />
	</form>
</div>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		var triggers = $("#LoginWithAjax_Links_Register").overlay({
			mask: { 
				color: '#ebecff',
				loadSpeed: 200,
				opacity: 0.9
			},
			closeOnClick: true
		});		
	});
</script>