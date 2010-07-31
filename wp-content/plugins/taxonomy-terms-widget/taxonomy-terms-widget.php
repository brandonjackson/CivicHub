<?php
/*
Plugin Name: Taxonomy Terms Widget
Plugin URI: http://wpprogrammer.com/taxonomy-terms-widget/
Description: This plugin allows you to list the terms of any taxonomy in the form of a widget. Supports several options to select, order, and display the terms.
Version: 1.0
Author: Utkarsh Kukreti
Author URI: http://utkar.sh

== Release Notes ==
2010-05-06 - v1.0 - First version.

This plugin uses some code borrowed from Justin Tadlock's awesome Query Posts plugin.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
Online: http://www.gnu.org/licenses/gpl.txt
*/

TaxonomyTermsWidget::init();

class TaxonomyTermsWidget extends WP_Widget
{
	static function init()
	{
		add_action('widgets_init', array('TaxonomyTermsWidget', 'register_widget'));
	}
	
	static function register_widget()
	{
		register_widget('TaxonomyTermsWidget', 'widget');
	}
	
	function TaxonomyTermsWidget()
	{
		$widget_ops = array( 'classname' => 'taxonomy-terms-widget', 'description' => __('Display posts and pages however you want.') );
		$control_ops = array( 'width' => 300, 'height' => 450, 'id_base' => 'taxonomy-terms' );
		$this->WP_Widget( 'taxonomy-terms', __('Taxonomy Terms'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance )
	{
		extract( $args );

		/* Arguments for the query. */
		$args = array();
		
		$args['taxonomy'] = $instance['taxonomy'];

		/* Widget title and things not in query arguments. */
		$title = apply_filters('widget_title', $instance['title'] );
		$display = $instance['display'];
		$wp_reset_query = $instance['wp_reset_query'] ? '1' : '0';


		/* Ordering and such. */
		if ( $instance['child_of'] )
			$args['child_of'] = (int)$instance['child_of'];
		if ( $instance['order'] )
			$args['order'] = $instance['order'];
		if ( $instance['orderby'] )
			$args['orderby'] = $instance['orderby'];

		/* Hide Empty */
		if ( isset($instance['hide_empty']) )
			$args['hide_empty'] = $instance['hide_empty'];
		
		/* Begin display of widget. */
		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;
		$terms = get_terms( $args['taxonomy'] , $args );

		$tax = $args['taxonomy'];
		
		$class = "taxonomy-list taxonomy-list-$display taxonomy-list-$tax";
		
		if ( $terms ): ?>
			<<?php echo $display; ?> class='<?php echo $class; ?>'>
			
			<?php foreach($terms as $term):
				echo '<li><a href="' . esc_url(get_term_link( $term, $tax )) . '" title="' . $term->name . '" rel="bookmark">' . $term->name . '</a></li>';
			endforeach;?>
			
			</<?php echo $display; ?>>
		<?php endif;

		if ( $wp_reset_query )
			wp_reset_query();

		echo $after_widget;
	}

	
	function update( $new_instance, $old_instance )
	{
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );

		$instance['display'] = $new_instance['display'];

		$instance['wp_reset_query'] = ( isset( $new_instance['wp_reset_query'] ) ? 1 : 0 );
		$instance['hide_empty'] = ( isset( $new_instance['hide_empty'] ) ? 1 : 0 );

		$instance['child_of'] = strip_tags( $new_instance['child_of'] );
		$instance['order'] = $new_instance['order'];
		$instance['orderby'] = $new_instance['orderby'];

		$instance['taxonomy'] = $new_instance['taxonomy'];
		
		$taxonomies = get_object_taxonomies( 'post' );
		if ( is_array( $taxonomies ) ) :
			foreach ( $taxonomies as $taxonomy ) :
				$tax = get_taxonomy( $taxonomy );
				if ( $tax->query_var ) :
					$instance[$tax->query_var] = $new_instance[$tax->query_var];
				endif;
			endforeach;
		endif;

		return $instance;
	}
	
	
	function form( $instance ) {

		//Defaults
		$defaults = array( 'display' => 'ul', 'order' => 'ASC', 'orderby' => 'name', 'hide_empty' => false, 'wp_reset_query' => true );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<div>
		<p>
			<label title="<?php _e('What should the title of your widget be?'); ?>" for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label title="<?php _e('What format to display your posts in'); ?>" for="<?php echo $this->get_field_id( 'display' ); ?>"><?php _e('Display:'); ?></label>
			<select id="<?php echo $this->get_field_id( 'display' ); ?>" name="<?php echo $this->get_field_name( 'display' ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( 'ul' == $instance['display'] ) echo ' selected="selected"'; ?>>ul</option>
				<option <?php if ( 'ol' == $instance['display'] ) echo ' selected="selected"'; ?>>ol</option>
			</select>
		</p>
		<p>
			<label title="<?php _e('ID of the term parent'); ?>" for="<?php echo $this->get_field_id( 'child_of' ); ?>"><code>child_of</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'child_of' ); ?>" name="<?php echo $this->get_field_name( 'child_of' ); ?>" value="<?php echo $instance['child_of']; ?>" />
		</p>
		
		<p>
			<label title="<?php _e('Order posts in ascending or descending order'); ?>" for="<?php echo $this->get_field_id( 'order' ); ?>"><code>order</code></label>
			<select id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( 'ASC' == $instance['order'] ) echo ' selected="selected"'; ?>>ASC</option>
				<option <?php if ( 'DESC' == $instance['order'] ) echo ' selected="selected"'; ?>>DESC</option>
			</select>
		</p>
		<p>
			<label title="<?php _e('What criteria the posts should be ordered by'); ?>" for="<?php echo $this->get_field_id( 'orderby' ); ?>"><code>orderby</code></label>
			<select id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( 'name' == $instance['orderby'] ) echo ' selected="selected"'; ?>>name</option>
				<option <?php if ( 'count' == $instance['orderby'] ) echo ' selected="selected"'; ?>>count</option>
				<option <?php if ( 'none' == $instance['orderby'] ) echo ' selected="selected"'; ?> value="none">none (Uses term_id)</option>
			</select>
		</p>
		
		<p>
			<label title="<?php _e('Which taxonomy to use?'); ?>" for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><code>taxonomy</code></label>
			
			<?php $taxonomies = get_taxonomies(); ?>
			<?php if ( is_array( $taxonomies ) ) : ?>
			<select id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'taxonomy' ); ?>" class="widefat" style="width:100%;">
				<?php foreach ( $taxonomies as $taxonomy ) : $tax = get_taxonomy( $taxonomy ); ?>
					<?php if ( true || $tax->query_var ) : ?>
						<option <?php if ( $instance['taxonomy'] == $tax->name ) echo ' selected="selected"'; ?> value="<?php echo $tax->name ?>"><?php echo $tax->name ?></option>
					<?php endif; ?>
				<?php endforeach; ?>
			</select>
			<?php endif; ?>
		</p>
		
		<p>
			<label title="<?php _e('Reset the query back to the original after showing posts'); ?>" for="<?php echo $this->get_field_id( 'wp_reset_query' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['wp_reset_query'], true ); ?> id="<?php echo $this->get_field_id( 'wp_reset_query' ); ?>" name="<?php echo $this->get_field_name( 'wp_reset_query' ); ?>" /> <code>wp_reset_query</code></label>
		</p>
		<p>
			<label title="<?php _e('Hide terms having no posts?'); ?>" for="<?php echo $this->get_field_id( 'hide_empty' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['hide_empty'], true ); ?> id="<?php echo $this->get_field_id( 'hide_empty' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" /> <code>hide_empty</code></label>
		</p>
		</div>

		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}