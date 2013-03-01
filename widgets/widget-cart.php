<?php
/**
 * Shopping Cart Widget
 * Displays shopping cart widget
 */
class apptivo_ecommerce_Widget_Cart extends WP_Widget {
	
	/** Variables to setup the widget. */
	var $apptivo_widget_cssclass;
	var $apptivo_widget_description;
	var $apptivo_widget_idbase;
	var $apptivo_widget_name;

	/** constructor */
	function apptivo_ecommerce_Widget_Cart() {
	
		/* Widget variable settings. */
		$this->apptivo_widget_cssclass = 'widget_apptivo_shopping_cart';
		$this->apptivo_widget_description = __( 'Display the users Shopping Cart in the sidebar.', 'apptivo_ecommerce' );
		$this->apptivo_widget_idbase = 'apptivo_ecommerce_shopping_cart';
		$this->apptivo_widget_name = __('[Apptivo] Shopping Cart', 'apptivo_ecommerce' );
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->apptivo_widget_cssclass, 'description' => $this->apptivo_widget_description );
		
		/* Create the widget. */
		$this->WP_Widget('apptivo_shopping_cart', $this->apptivo_widget_name, $widget_ops);
	}

	/** @see WP_Widget */
	function widget( $args, $instance ) {
		global $apptivo_ecommerce;
		
		$available_gateways = $apptivo_ecommerce->payment_gateways->available_payment_gateway_lists();
		$checkout_pageid = '';
		if($available_gateways):
		foreach ($available_gateways as $gateway ) :
			if($gateway->id == 'SecureCheckout') :
			   $checkout_pageid = get_permalink(get_option('apptivo_ecommerce_secure_checkout_page_id'));
			   break;
		    elseif( $gateway->id == 'paypal' || $gateway->id == 'GoogleCheckout'):
			    $checkout_pageid = get_permalink(get_option('apptivo_ecommerce_checkout_page_id'));
			   break;
		    endif;
		endforeach;
		endif;
	   
		
		if (is_shopping_cart()) return;
		extract($args);
		if ( !empty($instance['title']) ) $title = $instance['title']; else $title = __('Cart', 'apptivo_ecommerce');
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);
		
		
		$getcartDetails = getCartDetails();
		$shoppingCart_Lines = get_baginfo()->shoppingCartLines;
		$shoppingCartLines = app_convertObjectToArray($shoppingCart_Lines);
		
		 if (sizeof($shoppingCartLines) == 0 || empty($shoppingCartLines[0])) 
		 {
		 	echo $before_widget;
		 	if ( $title ) echo $before_title . '<span id="widget_cart_title" style="display:none;">'.$title.'</span>' . $after_title;
		 	echo '<ul class="app_widget_shopping_cart app_cart_list app_product_list_widget hidden_cart0">';
		 	echo '</ul>';
		 	echo $after_widget;
		 	return;
		 }
		
		
		echo $before_widget;		
		if ( $title ) echo $before_title . $title . $after_title; 		
		echo '<ul class="app_widget_shopping_cart app_cart_list app_product_list_widget">';
		
	
     if (sizeof($shoppingCartLines)>0 && !empty($shoppingCartLines[0])) : 
		foreach ($shoppingCartLines as $cartLines) :
		 $product_postid = getIdFromMeta('_apptivo_item_id', $cartLines->itemId);
	     $cart_lineid = $cartLines->shoppingCartLineId;
		 $item_qty = $cartLines->quantity;
			if ($item_qty>0) :
				echo '<li><a href="'.get_permalink($product_postid).'">';
				if (has_post_thumbnail($product_postid))
				{
					$attr = array('alt'=> trim(strip_tags( get_the_title($product_postid))),
	                              'title'	=> trim(strip_tags( get_the_title($product_postid))));
					echo get_the_post_thumbnail($product_postid, 'product_thumbnail',$attr); 
				}
				else{
					$thumbnail_noproduct_image = apply_filters('apptivo_ecommerce_thumbnail_noproduct_img',$apptivo_ecommerce->plugin_url().'/assets/images/no-product-90.jpg');
					echo '<img src="'.$thumbnail_noproduct_image. '" title="'.get_the_title($product_postid).'" alt="Placeholder" width="'.$apptivo_ecommerce->get_image_size('product_thumbnail_image_width').'" height="'.$apptivo_ecommerce->get_image_size('product_thumbnail_image_height').'" />'; 
				}
				echo get_post($product_postid)->post_title.'</a>';
				echo '<span class="quantity">' .$item_qty.' &times; '.apptivo_ecommerce_price($cartLines->effectiveUnitPrice).'</span></li>';
		    endif;
		endforeach; 
		else: echo '<li class="empty">'.__('No products in the cart.', 'apptivo_ecommerce').'</li>'; endif;
		
		
		if (sizeof($shoppingCartLines)>0 && !empty($shoppingCartLines[0])) : 
				$sub_total = cart_subtotal();
				if($sub_total) :
					echo '<p class="total"><strong>';			
					_e('Subtotal', 'apptivo_ecommerce');
					echo ':</strong> '.$sub_total;
					echo '</p>';
				endif;
			do_action( 'apptivo_ecommerce_widget_shopping_cart_before_buttons' );
			echo '<p class="btn" style="text-align:center;" ><a href="'.$apptivo_ecommerce->cart->shopping_cart_url().'" class="btn">'.__('View Your Shopping Cart', 'apptivo_ecommerce').'</a>'; 
			echo '</p>';
			echo '<div class="clear"></div>';
		endif;
		
		echo '</ul>';
		
		
		
		echo $after_widget;
	}

	/** @see WP_Widget->update */
	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		return $instance;
	}

	/** @see WP_Widget->form */
	function form( $instance ) {
	?>
	<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'apptivo_ecommerce') ?></label>
	<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" value="<?php if (isset ( $instance['title'])) {echo esc_attr( $instance['title'] );} ?>" /></p>
	<?php
	}

} // class apptivo_ecommerce_Widget_Cart