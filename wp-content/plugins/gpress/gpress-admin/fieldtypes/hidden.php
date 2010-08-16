<?php
	
	function display_tppo_hidden($thisfield, $tpp_id){		
		$gpress_version = $thisfield->default_value;
		?>
		<li>
        
		  	<label class="general-label"><?php echo $thisfield->field_label?>:</label>
		  	<div class="general-input">            
	            <input type="hidden" id="<?php echo $thisfield->option_name; ?>_hidden" name="tppo[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>]" value="<?php echo $gpress_version; ?>">	
			</div>
			
			<div class="clear"></div>
            
		</li>	
		<?php
	}
?>