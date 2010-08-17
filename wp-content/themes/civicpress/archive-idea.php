<?php
/*
Template Name: Ideas Archive Page
*/
?>

<?php get_header(); ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_archive' ) ?>

		<div class="page" id="blog-archives">

			<h3 class="pagetitle"><?php printf( __( '%1$s', 'buddypress' ), wp_title( false, false ) ); ?></h3>

			<?php query_posts('post_type=idea'); 
			  		if ( have_posts() ) : ?>

				<div class="navigation">

					<div class="alignleft"><?php next_posts_link( __( '&larr; Previous Entries', 'buddypress' ) ) ?></div>
					<div class="alignright"><?php previous_posts_link( __( 'Next Entries &rarr;', 'buddypress' ) ) ?></div>

				</div>

				<?php while (have_posts()) : the_post(); ?>

					<?php do_action( 'bp_before_blog_post' ) ?>

					<div class="post" id="post-<?php the_ID(); ?>">
					<div class='post_votes'>
						<?php $BallotBox->DisplayVotes(get_the_ID()); ?>
					</div>
						<div class="post-content">
							<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>

							

							<div class="entry">
								<?php the_content( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>
							</div>
							<p class="date">
								<?php the_date() ?>
								<?php _e( 'in', 'buddypress' ) ?> 
								<?php the_terms(get_the_id(), 'topic');?> 
								<?php printf( __( 'by %s', 'buddypress' ), bp_core_get_userlink( $post->post_author ) ) ?>&nbsp;|&nbsp; 
								<span class="tags">
									<?php the_tags( __( 'Tags: ', 'buddypress' ), ', ', '<br />'); ?>
								</span> 
								<span class="comments">
								<img src='<?php bloginfo('stylesheet_directory'); ?>/assets/images/comment_icon.gif' id='commenticon' />
									<?php comments_popup_link( __( 'No Comments &#187;', 'buddypress' ), __( '1 Comment &#187;', 'buddypress' ), __( '% Comments &#187;', 'buddypress' ) ); ?>
								</span>
							</p>							
						</div>

					</div>

					<?php do_action( 'bp_after_blog_post' ) ?>

				<?php endwhile; ?>

				<div class="navigation">

					<div class="alignleft"><?php next_posts_link( __( '&larr; Previous Entries', 'buddypress' ) ) ?></div>
					<div class="alignright"><?php previous_posts_link( __( 'Next Entries &rarr;', 'buddypress' ) ) ?></div>

				</div>

			<?php else : ?>

				<h2 class="center"><?php _e( 'Not Found', 'buddypress' ) ?></h2>
				<?php locate_template( array( 'searchform.php' ), true ) ?>

			<?php endif; ?>

		</div>

		<?php do_action( 'bp_after_archive' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar-idea.php' ), true ) ?>

<?php get_footer(); ?>
