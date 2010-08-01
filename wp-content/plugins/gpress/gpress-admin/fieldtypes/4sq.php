<?php
	
global $deactivate_foursquare;

if(phpversion() < 5) { 
	function display_tppo_4sq($thisfield, $tpp_id){
		?>
		
		<li>
			<p style="font-weight:bold !important;">YOU MUST HAVE PHP5+ TO USE FOURSQUARE FUNCTIONALITY</p>
		</li>
		
		<?php
	}
}elseif($deactivate_foursquare == 'yes'){
	function display_tppo_4sq($thisfield, $tpp_id){
		?>
		
		<li>
			<p style="font-weight:bold !important;">FOURSQUARE FUNCTIONALITY HAS BEEN TEMPORARILY DEACTIVATED</p>
		</li>
		
		<?php
	}
}else{
	
	// Foursquare and oAuthg Requires session to work
	if(!isset($_SESSION)) {
		session_start();
	}
	
	function display_tppo_4sq($thisfield, $tpp_id){
		global $tppo;
		
		?>
		<li>
			<label class="general-label"><?php echo $thisfield->field_label?>:</label>
			<div class="general-input">
			
				<?php
					// Process 4sq callback 
					callback4sq($thisfield, $tpp_id);
					
					/* FOURSQUARE INFO */
					$oauth_info = $tppo->get_tppo($thisfield->option_name, 'blogs', $tpp_id);
					$consumer_key = $tppo->get_tppo('foursquare_key', 'blogs', $tpp_id);
					$consumer_secret = $tppo->get_tppo('foursquare_secret', 'blogs', $tpp_id);
					
					require_once( dirname(__FILE__) . '/4square/EpiCurl.php');
					require_once( dirname(__FILE__) . '/4square/EpiOAuth.php');
					require_once( dirname(__FILE__) . '/4square/EpiFoursquare.php');
					
					try{
					  $foursquareObj = new EpiFoursquare($consumer_key, $consumer_secret);
					  $results = $foursquareObj->getAuthorizeUrl();
					  $loginurl = $results['url'] . "?oauth_token=" . $results['oauth_token'];
					  $_SESSION['secret'] = $results['oauth_token_secret'];
					} catch (EpiOAuthUnauthorizedException $e) {
						$waiting_for_api_keys = true;
						?>
						<p>Please note that you have incorrect or missing API Keys. Please <a target="_blank" href="http://foursquare.com/oauth/">register this website with foursquare</a> using the following information:</p>
						<p><strong>Application Name:</strong> <?php bloginfo('name'); ?></p>
						<p><strong>Application Website:</strong> <?php bloginfo('url'); ?></p>
						<p><strong>Callback URL:</strong> <?php bloginfo('url'); ?>/wp-admin/admin.php?page=tpp_options_form</p>
						
						<?php
					} catch (EpiOAuthException $e) {
						$waiting_for_api_keys = true;
						?>
						<p><strong>There was a problem connecting to Foursquare</strong></p>
						<?php
					}
					if(empty($oauth_info['oauth_token']) || empty($oauth_info['oauth_secret'])){
						if($waiting_for_api_keys == false) {
						?>
							<p>You have not logged into Foursquare and authenticated your account yet...</p><p>&nbsp; <a href="<?php echo $loginurl?>" class="foursquare_signin">Sign-In with Foursquare</a></p>
						<?php
						}
					}else{
						?>
							<script language="javascript">
								jQuery(document).ready(function(){
									jQuery('#remove_4sq_oauth').click(function(){
										if(!confirm("Are you sure you want to remove the tokens?"))
											return false;
											
										var data = {
											'action' : 'remove-4sq-oauth',
											'tpp_id' : '<?php echo $tpp_id?>',
											'option_type' : '<?php echo $thisfield->option_type?>',
											'option_name' : '<?php echo $thisfield->option_name?>',
											'_ajax_nonce' : '<?php echo wp_create_nonce( 'remove-4sq-oauth' )?>'
										};
										
										var callback = function(data){
											jQuery('#remove_4sq').html('<p>You have not logged into Foursquare and authenticated your account yet...</p><p>&nbsp; <a href="<?php echo $loginurl?>" class="foursquare_signin">Sign-In with Foursquare</a></p>');
										};
										
										jQuery('#remove_4sq img').css('display','block');
										jQuery.post(ajaxurl,data,callback);										  
										return false;
									});
								});
							</script>
							<p id="remove_4sq">&nbsp; <a href="#" id="remove_4sq_oauth">Remove Presently Saved OAuth Tokens</a> <img src="<?php bloginfo('url'); ?>/wp-admin/images/loading.gif" style="display:none; float:right;" /></p>
						<?php
					}
					/* END OF FOURSQUARE */
				?>
				
			</div>
			<div class="clear"></div>
		</li>	
<?php
	}
	
	function callback4sq($thisfield, $tpp_id){
		global $tppo;
		if(isset($_REQUEST['oauth_token'])){
			
			$consumer_key = $tppo->get_tppo('foursquare_key', 'blogs', $tpp_id);
			$consumer_secret = $tppo->get_tppo('foursquare_secret', 'blogs', $tpp_id);
			
			require_once( dirname(__FILE__) . '/4square/EpiCurl.php');
			require_once( dirname(__FILE__) . '/4square/EpiOAuth.php');
			require_once( dirname(__FILE__) . '/4square/EpiFoursquare.php');			

			try{
				$foursquareObj = new EpiFoursquare($consumer_key, $consumer_secret);
				
				$foursquareObj->setToken($_REQUEST['oauth_token'],$_SESSION['secret']);
				$token = $foursquareObj->getAccessToken();
				
				$oauth_token = $token->oauth_token;
				$oauth_token_secret = $token->oauth_token_secret;
				
				save4sqData(&$thisfield,$tpp_id,$oauth_token,$oauth_token_secret);
				
			} catch (EpiOAuthUnauthorizedException $e) {
				
				echo "<strong>Invalid oAuth Security Token.</strong>";
			} catch (EpiOAuthException $e){
				
				echo "<strong>Invalid oAuth Security Token.</strong>";
			}
			unset($_SESSION['secret']);
		}
		
	}
	
	function save4sqData($thisfield,$tpp_id,$oauth_token,$oauth_token_secret){
		global $tppo;
		
		$tppo_data = $tppo->getTPPoptions();
	
		$value = array();
		$value['oauth_token'] = $thisfield->value['oauth_token'] = $oauth_token;
		$value['oauth_secret'] = $thisfield->value['oauth_secret'] = $oauth_token_secret;
		
		$tppo_data[$thisfield->option_type][$tpp_id][$thisfield->option_name]->value = $value;
		
		$tppo->updateTPPOdata(array('value'=>$value),$thisfield->option_type,$tpp_id,$thisfield->option_name);
	}
	
	function remove4sqData(){
		global $tppo;
		
		check_ajax_referer('remove-4sq-oauth');
		
		$tppo_data = $tppo->getTPPoptions();
	
		$value = array();
		$value['oauth_token'] = $thisfield->value['oauth_token'] = "";
		$value['oauth_secret'] = $thisfield->value['oauth_secret'] = "";
		
		$tppo_data[$_POST['option_type']][$_POST['tpp_id']][$_POST['option_name']]->value = $value;
		
		$tppo->updateTPPOdata(array('value'=>$value),$_POST['option_type'],$_POST['tpp_id'],$_POST['option_name']);
		
	}
	
	add_action( 'wp_ajax_remove-4sq-oauth', 'remove4sqData');
}

?>