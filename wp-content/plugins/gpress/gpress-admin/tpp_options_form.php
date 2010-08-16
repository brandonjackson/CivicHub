<?php

function tppo_sort_fields($a,$b){
	if($a->field_order > $b->field_order)
		return true;
}

if (@$_POST['submit'] == 'Save Changes') {
	update_tpp_options($this);
}

$tpp_blog_id = $this->check_blog_id();
$tpp_user_id = $this->check_user_id();

$tppo = $this->getTPPoptions(true);
$tppoptions_fields = array_merge(	array('blogs' => $tppo['blogs'][$tpp_blog_id]),
									array('users' => $tppo['users'][$tpp_user_id]),
									array('sitewide' => $tppo['sitewide'][1]));


$tppoptions_top_tabs = $tppo['top_tabs'];
$tppoptions_sub_tabs = $tppo['sub_tabs'];

do_action('tppo_before_form');
$use_selects = true;
$use_selects = apply_filters('tppo_use_selects', $use_selects);

?>
	<link href="<?php echo TPPO_URL; ?>/css/style.css" rel="stylesheet" type="text/css" />
     <!--[if IE 7]>
    	<link href="<?php echo TPPO_URL; ?>/css/style_ie7.css" rel="stylesheet" type="text/css" />
    <![endif]-->
    <script language="javascript">
		if(!jQuery)
			document.write('<sc'+'ript type="text/javascript" src="<?php echo TPPO_URL; ?>/js/jquery.js"></scri'+'pt>');
	</script>
	<script type="text/javascript" src="<?php echo TPPO_URL; ?>/js/inputs.js"></script>
    <?php if($use_selects) { ?>
		<script type="text/javascript" src="<?php echo TPPO_URL; ?>/js/selects.js"></script>
	<?php } ?>
	<script type="text/javascript"> 
		jQuery.fn.pause = function(duration) {
			jQuery(this).animate({ dummy: 1 }, duration);
			return this;
		};
	</script>
	<script type="text/javascript">
		jQuery(function(){
			// Tabs
			jQuery('.tpp_form_container #tabs').tabs({
				   show: function(event, ui) { 
				   			<?php if($use_selects) { ?>
					   			jQuery('select.tpp_dropdown').sSelect();
							<?php } ?>
					},
				   selected:0
			});
		});
		
		jQuery(function(){
			jQuery('.tpp_form_container input').customInput();
		});
		
		jQuery(document).ready(function(){
			
			jQuery(".tpp_form_container ul.form-list li").hover(function() {
				//console.log("in");
				jQuery(".tpp_form_container ul.form-list li .tooltips").stop().css({opacity: 0, display: "none"});
				jQuery(this).find(".tooltips")
							.css({opacity: 0, display: "block"})
							.pause(800)
							.animate({opacity: 1, marginTop: "-40px"}, "slow")
							.pause(3000)
							.animate({opacity: 0, marginTop: "-20px"}, "fast");
			}, function() {
				//console.log("out");
				jQuery(".tpp_form_container ul.form-list li .tooltips").stop().css({opacity: 0, display: "none"});
				jQuery(this).find(".tooltips")
					.css({display: "block"})
					.animate({opacity: 0, marginTop: "-20px"}, "fast");
			});
		
		});
	
	</script>

	<div class="tpp_form_container">
		<form id="tpp_options_id" name="tpp_options_name" method="post">
		
			<div id="option-container">
				<div id="tabs">
					<ul>
					<?php
					if(empty($tppoptions_top_tabs))
						echo '<li style="width:100%; text-align:center; margin:-12px 0 13px 0;"><a href="#" style="width:100%">
						THESE OPTIONS ARE ONLY AVAILABLE TO SUPER ADMINS
						</li>';
						
					foreach ($tppoptions_top_tabs as $this_top_tab_name => $this_top_tab) {
						if(!$this_top_tab->is_empty){
							?>
							<li><a href="#tabs-<?php echo $this_top_tab_name; ?>" onclick="return false"><?php echo $this_top_tab->label; ?></a></li>
							<?php
						}
					}
					?>
					</ul>
					<?php	
					// START OF MASTER CHECKS (USES NAME AS UNIQUE REFERENCE TO ENSURE DUPLICATE TABS AND SIDEBARS ARE NOT GENERATED)
					foreach ($tppoptions_top_tabs as $this_top_tab_name => $this_top_tab) {
						if(!$this_top_tab->is_empty){
							foreach ($tppoptions_sub_tabs[$this_top_tab_name] as $this_sub_tab_name => $this_sub_tab) {
								if(!$this_sub_tab->is_empty){
									//global $value_sub_tabs_temp_to_use_later;
									//$value_sub_tabs_temp_to_use_later = $key_sub_tabs_temp;
									
									?>
			
									<script type="text/javascript">
										
										jQuery(document).ready(function() {
										
											//Default Action
											jQuery(".tpp_form_container .tab_content<?php echo $this_top_tab_name; ?>").hide(); //Hide all content
											jQuery(".tpp_form_container ul.tabs<?php echo $this_top_tab_name; ?> li:first").addClass("active").show(); //Activate first tab
											jQuery(".tpp_form_container .tab_content<?php echo $this_top_tab_name; ?>:first").show("fast", function() { <?php if($use_selects) { ?> jQuery('select.tpp_dropdown').sSelect(); <?php } ?> }); //Show first tab content
											
											//On Click Event
											jQuery(".tpp_form_container ul.tabs<?php echo $this_top_tab_name; ?> li").click(function() {
												jQuery(".tpp_form_container ul.tabs<?php echo $this_top_tab_name; ?> li").removeClass("active"); //Remove any "active" class
												jQuery(this).addClass("active"); //Add "active" class to selected tab
												jQuery(".tpp_form_container .tab_content<?php echo $this_top_tab_name; ?>").hide(); //Hide all tab content
												var activeTab = jQuery(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
												jQuery(activeTab).fadeIn("fast", function() { jQuery('select.tpp_dropdown').sSelect();}); //Fade in the active content
												
											});
											
									
										});
									
									</script>
								
									<?php
								}
							}
						
						?>	
						<div id="tabs-<?php echo $this_top_tab_name; ?>">
						
							<div class="left-container">
							<?php 
								if($this->config['show_brand']){
							?>
								<script type="text/javascript">	
                                    var the_brand_name;
                                    jQuery(document).ready(function() {
                                        the_brand_name = jQuery('h1.brand_name').html();
                                        if (the_brand_name !== "ThePremiumPress") {
                                            jQuery('h1.brand_name').css({ "background" : "none", "text-indent" : 0, "color" : "#FFF", "font-size" : 13, "text-align" : "center" });
                                        }
                                    });
                                </script>
                                
                                <div class="side-header">
                                    <a href="<?php echo $this->config['brand_url']; ?>" target="_blank"><h1 class="brand_name"><?php echo $this->config['brand_name']; ?></h1></a>
        
                                    <ul class="social-media">
                                        <li class="twitter"><a href="<?php echo $this->config['twitter_url']; ?>" target="_blank" <?php echo (empty($this->config['twitter_url'])?"onclick='return false'":''); ?>>Twitter</a></li>
                                        <li class="facebook"><a href="<?php echo $this->config['facebook_url']; ?>" target="_blank"> <?php echo (empty($this->config['facebook_url'])?"onclick='return false'":''); ?>Facebook</a></li>
                                        <li class="rss"><a href="<?php echo $this->config['rss_url']; ?>" target="_blank" <?php echo (empty($this->config['rss_url'])?"onclick='return false'":''); ?>>RSS</a></li>
                                        <div class="clear"></div>
                                    </ul>
                                    
                                    <div class="clear"></div>
                                </div><!-- side-header -->
							<?php
                            }
							?>
								<div class="side-nav">
								
									<ul class="nav tabs<?php echo $this_top_tab_name; ?>">
									<?php
										foreach ($tppoptions_sub_tabs[$this_top_tab_name] as $this_sub_tab_name => $this_sub_tab) {
											
											if(!$this_sub_tab->is_empty){
												?>
												<li><a href="#subtabs-<?php echo $this_top_tab_name; ?>-<?php echo $this_sub_tab_name; ?>" onclick="return false"><?php echo $this_sub_tab->label; ?></a></li>
												<?php
											}
											
										}
									?>
									
									</ul>
									
									<div class="clear"></div>
								</div><!-- side-nav -->
								
								<div class="clear"></div>
							</div><!-- left-container -->
							
							
							<div class="right-container">
							<?php
								foreach ($tppoptions_sub_tabs[$this_top_tab_name] as $this_sub_tab_name => $this_sub_tab) {
									if(!$this_sub_tab->is_empty){
										?>
											<div id="subtabs-<?php echo $this_top_tab_name; ?>-<?php echo $this_sub_tab_name; ?>" class="tab_content<?php echo $this_top_tab_name; ?>" style="display:none">
												<h1><?php echo $this_sub_tab->label; ?></h1>
												
												
												<div class="form-container">
													
													<ul class="form-list">
														<?php
															if($this_sub_tab->description !== "") {
																?>
																	<li class="sub_tab_intro"><?php echo $this_sub_tab->description; ?></li>
																<?php															
															}
														 
															// START OF FIELDS 
														  
															usort($this_sub_tab->fields,"tppo_sort_fields");
															foreach($this_sub_tab->fields as $thisfield){
																// OUTPUT FIELDS
																tpp_output_field($thisfield,$this);
															}
															
															// END OF FIELDS
															
														?>
													</ul>
																						
													<div class="button">
														<input name="submit" type="submit" class="submit" value="Save Changes" onfocus="if(this.blur)this.blur()" />
													</div>                           
													
													<div class="clear"></div>
												</div>
											</div>
																	
											<div class="clear"></div>
										<?php
									}
								}
							}
						?>
						</div><!-- right-container -->
						
						<div class="clear"></div>
					</div><!-- each tab -->
					<?php
					
					// END OF MASTER FOR EACH CHECKS
						
					}
					
					if($this->config['debug_view'])
						$this->view_options_array();

					?>
					
				</div><!-- tabs -->
				
			</div><!-- options-container -->
			
			<div class="clear"></div>
			</form>
			
		</div><!-- tpp_form_container -->

<?php
	do_action('tppo_after_form');
?>