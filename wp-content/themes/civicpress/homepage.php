<?php
/*
Template Name: Home Page
*/
?>
<?php get_header() ?>

	<div id="content" class="full_width">
		<div id='home'>
			<div id='home_left'>		
				<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/tagline.png" />
				<p>CivicHaven connects you with ideas that inspire and other citizens who are 
	passionate about changing our city. Here you can pitch in your talents, your dedication and your voice to bring about change in New Haven.</p>
				<a href='http://beta.civichaven.com/ideas'>Start Exploring Ideas Now</a>
			</div>
			<img src='<?php echo bloginfo('stylesheet_directory'); ?>/assets/images/venn.png' id='venn_diagram' />
		</div>
		
	</div><!-- #content -->

<?php get_footer(); ?>
