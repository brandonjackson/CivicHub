<?php
/*
Template Name: Submit Project
*/
global $current_user;
?>

<?php get_header() ?>

	<div id="content" class='idea'>
		<div class="padder">

		<?php do_action( 'bp_before_blog_single_post' ) ?>

		<div class="page" id="blog-single">

			<h2>Post a Project</h2>
			<form method='post' name='submit_project' action='<?php bloginfo('stylesheet_directory');?>/inc/process-new-project.php' class='new_submission'>
			<p>
				<label>Summary</label>
				<input type='text' name='summary' value='' />
			</p>
			<p>
				<label>Description</label>
				<textarea name='description'></textarea>
			<p>
				<label>Topic</label>
				<select name='topic'>
				<?php $terms = get_terms('topic', 'hide_empty=0');
				foreach($terms as $term)
					echo "<option value='{$term->slug}'>{$term->name}</option>";
				?>
				</select>
			</p>
			<input type='hidden' name='user_id' value='<?php echo $current_user->ID; ?>' />
			<input type='submit' />
			</form>

		</div><!-- .padder -->
	</div>
</div>

	<?php locate_template( array( 'sidebar-idea.php' ), true ); ?>

<?php get_footer() ?>