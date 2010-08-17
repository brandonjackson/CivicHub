<?php
/*
Template Name: Submit Idea
*/
global $current_user;
?>

<?php get_header() ?>

	<div id="content" class='idea'>
		<div class="padder">

		<?php do_action( 'bp_before_blog_single_post' ) ?>

		<div class="page" id="blog-single">

			<h2>Share Your Idea</h2>
			<form method='post' name='new_idea' action='<?php bloginfo('stylesheet_directory');?>/inc/process-new-idea.php'>
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