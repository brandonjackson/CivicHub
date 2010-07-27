<?php 
global $current_user;
global $oqp_form;
?>


		<?php do_action('oqp_creation_form_before_fields');?>
		<?php if ($oqp_form->is_guest) {?>
			<p>
				<label for="oqp_dummy_name"><?php _e('Name');?></label>
				<input type="text" name="oqp_dummy_name" id="oqp_dummy_name" value="<?php echo $dummy_name;?>"/>
			</p>
			<p>
				<label for="oqp_dummy_email"><?php _e('Email');?></label>
				<input type="text" name="oqp_dummy_email" id="oqp_dummy_email" value="<?php echo $dummy_email;?>"/>
			</p>
		<?php } ?>
		<?php if ($oqp_form->args['title']) {?>
			<p>
				<label for="oqp_title"><?php _e('Title');?></label>
				<input type="text" name="oqp_title" id="oqp_title" value="<?php echo $oqp_form->post->post_title;?>"/>
			</p>
		<?php } ?>
		<?php if ($oqp_form->args['desc']) {?>
			<p>
				<label for="oqp_desc"><?php _e('Description');?>
				<?php
				if (!$oqp_form->args['tiny_mce']) {?>
				<small>- <em><?php _e('HTML allowed','oqp');?></em></small>
				<?php } else {
				?>
					<span class="generic-button">
						<a href="#" class="toggleVisual"><?php _e('Visual');?></a>
						<a href="#" class="toggleHTML"><?php _e('HTML');?></a>
					</span>

				<?php
				}?>
				</label>
				
				<textarea name="oqp_desc" id="oqp_desc" rows="8" col="45"><?php echo $oqp_form->post->post_content;?></textarea>
			</p>
		<?php } ?>
		<?php oqp_form_taxonomies_html($oqp_form->args['taxonomies']);?>

		<p>
			<?php 
			if (!$oqp_form->post->ID) {
				wp_nonce_field( 'oqp-new-post-blog-'.$oqp_form->args['blog_id'] );
				$button_text=__('Publish');
			}else {
				wp_nonce_field( 'oqp-edit-post'.$oqp_form->post->ID.'-blog-'.$oqp_form->args['blog_id'] );
				$button_text=__('Update');
				?>
				<input type="hidden" name="oqp-post-id" value="<?php echo $oqp_form->post->ID;?>"/>
				<?php
			}
			if ($oqp_form->args['blog_id']) {
			?>
				<input type="hidden" name="oqp-blog-id" value="<?php echo $oqp_form->args['blog_id'];?>"/>
			<?php }?>
			
			<input type="hidden" name="oqp-action" value="oqp-save"/>
			<input type="hidden" name="oqp-form-id" value="<?php echo $oqp_form->args['form_id'];?>"/>
			<?php do_action('oqp_creation_form_after_fields');?>
