<?php get_header() ?>

	<div id="content" class='idea'>
		<div class="padder">

		<?php do_action( 'bp_before_blog_single_post' ) ?>

		<div class="page" id="blog-single">

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<a href='<?php echo site_url(); ?>/ideas'>&laquo; View More Ideas</a>
				<div class="item-options">

					<div class="alignleft"><?php next_posts_link( __( '&larr; Previous Entries', 'buddypress' ) ) ?></div>
					<div class="alignright"><?php previous_posts_link( __( 'Next Entries &rarr;', 'buddypress' ) ) ?></div>

				</div>

				<div class="post" id="post-<?php the_ID(); ?>">
					<div class='post_votes'>
						<?php $BallotBox->DisplayVotes(get_the_ID()); ?>
					</div>

					<div class="post-content">
						<h2 class="posttitle idea-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
						<p>						<?php // echo get_avatar( get_the_author_meta( 'user_email' ), '30' ); ?>
</p>
						<div class="entry idea-content">
							<?php the_content( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>
						</div>

						<p class="date">
							<?php the_date() ?> 
							<em>
								<?php printf( __( 'by %s', 'buddypress' ), bp_core_get_userlink( $post->post_author ) ) ?></em> | <span class="comments"><?php comments_popup_link( __( 'No Comments', 'buddypress' ), __( '1 Comment', 'buddypress' ), __( '% Comments', 'buddypress' ) ); ?></span>
						</p>


						
					</div>

				</div>

			<?php comments_template(); ?>

			<?php endwhile; else: ?>

				<p><?php _e( 'Sorry, no posts matched your criteria.', 'buddypress' ) ?></p>

			<?php endif; ?>

		</div>

		<?php do_action( 'bp_after_blog_single_post' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar-idea.php' ), true ) ?>

<?php get_footer() ?>