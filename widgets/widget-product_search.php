<?php
/**
 * Apptivo Product Search Widget
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */

class apptivo_ecommerce_Widget_Product_Search extends WP_Widget {

	/** Variables to setup the widget. */
	var $apptivo_widget_cssclass;
	var $apptivo_widget_description;
	var $apptivo_widget_idbase;
	var $apptivo_widget_name;
	
	/** constructor */
	function apptivo_ecommerce_Widget_Product_Search() {
	
		/* Widget variable settings. */
		$this->apptivo_widget_cssclass = 'widget_product_search';
		$this->apptivo_widget_description = __( 'A Search box for products only.', 'apptivo_ecommerce' );
		$this->apptivo_widget_idbase = 'apptivo_ecommerce_product_search';
		$this->apptivo_widget_name = __('[Apptivo] Product Search', 'apptivo_ecommerce' );
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->apptivo_widget_cssclass, 'description' => $this->apptivo_widget_description );
		
		/* Create the widget. */
		$this->WP_Widget('apptivo_product_search', $this->apptivo_widget_name, $widget_ops);
	}

	/** @see WP_Widget */
	function widget( $args, $instance ) {
		extract($args);

		$title = $instance['title'];
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);
		
		echo $before_widget;
		
		if ($title) echo $before_title .$title . $after_title;
		
		?>
		<div class="item_search">
		<form role="search" method="get" id="searchform" action="<?php echo esc_url( home_url() ); ?>">
			
				<label class="screen-reader-text" for="s"><?php _e('Search for:', 'apptivo_ecommerce'); ?></label>
				<input type="text" value="<?php the_search_query(); ?>" name="s" id="s" placeholder="<?php _e('Search for products', 'apptivo_ecommerce'); ?>" />
				<input type="submit" id="searchsubmit" value="<?php _e('Search', 'apptivo_ecommerce'); ?>" />
				<input type="hidden" name="post_type" value="item" />

		</form>
		</div>
		<?php
		
		echo $after_widget;
	}

	/** @see WP_Widget->update */
	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		return $instance;
	}

	/** @see WP_Widget->form */
	function form( $instance ) {
		global $wpdb;
		?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'apptivo_ecommerce') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" value="<?php if (isset ( $instance['title'])) {echo esc_attr( $instance['title'] );} ?>" /></p>
		<?php
	}
}