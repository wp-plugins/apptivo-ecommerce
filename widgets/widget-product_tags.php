<?php
/**
 * Widget
 * Product Category lists.
 */
class apptivo_ecommerce_Widget_Product_Tags extends WP_Widget {

	/** Variables to setup the widget. */
	var $apptivo_widget_cssclass;
	var $apptivo_widget_description;
	var $apptivo_widget_idbase;
	var $apptivo_widget_name;
	
	/** constructor */
	function apptivo_ecommerce_Widget_Product_Tags() {
	
		/* Widget variable settings. */
		$this->apptivo_widget_cssclass = 'widget_apptivo_product_tags';
		$this->apptivo_widget_description = __( 'Apptivo product tags.', 'apptivo_ecommerce' );
		$this->apptivo_widget_idbase = 'apptivo_ecommerce_product_tags';
		$this->apptivo_widget_name = __('[Apptivo] Product Tags', 'apptivo_ecommerce' );
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->apptivo_widget_cssclass, 'description' => $this->apptivo_widget_description );
		
		/* Create the widget. */
		$this->WP_Widget('apptivo_product_tags', $this->apptivo_widget_name, $widget_ops);
	}

	/** @see WP_Widget */
	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Product Categories', 'apptivo_ecommerce' ) : $instance['title'], $instance, $this->id_base);
		
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		$cat_args = array('menu_order' => 'asc', 'hierarchical' => 0, 'taxonomy' => 'item_tag');

?>
		<ul>
<?php
		$cat_args['title_li'] = '';
		wp_list_categories(apply_filters('apptivo_ecommerce_product_tags_widget_args', $cat_args));
?>
		</ul>
<?php
		
		echo $after_widget;
	}

	/** @see WP_Widget->update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		
		return $instance;
	}
	
	/** @see WP_Widget->form */
	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = esc_attr( $instance['title'] );
		
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'apptivo_ecommerce' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		
<?php
	}

} // class apptivo_ecommerce_Widget_Product_Tags