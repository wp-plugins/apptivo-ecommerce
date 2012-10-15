<?php
/*loop-products*/
add_action( 'apptivo_ecommerce_before_main_content', 'apptivo_ecommerce_output_content_wrapper', 10);
add_action( 'apptivo_ecommerce_after_main_content', 'apptivo_ecommerce_output_content_wrapper_end', 10);
add_action( 'apptivo_ecommerce_breadcrumb', 'apptivo_ecommerce_breadcrumb', 10);
add_action( 'apptivo_ecommerce_breadcrumb_content', 'apptivo_ecommerce_breadcrumb_content', 20, 0);
add_action( 'apptivo_ecommerce_add_to_cart_and_view_details_btn', 'apptivo_ecommerce_template_loop_add_to_cart', 10, 5);
add_action( 'apptivo_ecommerce_pagination', 'apptivo_ecommerce_pagination', 10 );
/*side bar*/
add_action( 'apptivo_ecommerce_sidebar', 'apptivo_ecommerce_get_sidebar', 10);
/*Products View Page*/
add_action( 'apptivo_ecommerce_show_product_images', 'apptivo_ecommerce_show_product_images', 20);
add_action( 'apptivo_ecommerce_product_thumbnails', 'apptivo_ecommerce_show_product_thumbnails', 20 );
add_action( 'apptivo_ecommerce_single_product_summary', 'apptivo_ecommerce_template_single_excerpt', 20, 2);
add_action( 'apptivo_ecommerce_single_product_summary', 'apptivo_ecommerce_template_single_add_to_cart', 30, 2 );
add_action( 'apptivo_ecommerce_simple_add_to_cart', 'apptivo_ecommerce_simple_add_to_cart' ); 
add_action( 'apptivo_ecommerce_add_to_cart_form', 'apptivo_ecommerce_add_to_cart_form_nonce', 10);
add_action( 'apptivo_ecommerce_productdescription', 'apptivo_ecommerce_product_description_panel', 10 );
/* Checkout */
add_action( 'apptivo_ecommerce_before_checkout_form', 'apptivo_ecommerce_checkout_login_form', 10 );
add_action( 'apptivo_ecommerce_before_authorize_checkout_form', 'apptivo_ecommerce_checkout_login_form', 10 );
add_action( 'apptivo_ecommerce_order_review', 'apptivo_ecommerce_order_review', 10 );
add_action( 'apptivo_ecommerce_checkout_order_review', 'apptivo_ecommerce_checkout_order_review', 10 );

//Filter for Page Menus and List Page Menus/
/*wp page menu args */
add_filter('wp_page_menu_args','wp_pagemenu_args');
add_filter("wp_list_pages_excludes", "filter_wp_list_pages");
function wp_pagemenu_args($args)
{	
	global $apptivo_ecommerce;
	$available_gateways = $apptivo_ecommerce->payment_gateways->available_payment_gateway_lists();
	if($available_gateways):
	$secure_checkout_method = false;
	$checkout_page = false;
	foreach ($available_gateways as $gateway ) :
		if($gateway->id == 'SecureCheckout') :
		   $secure_checkout_method = true;
	    endif;
	     if( $gateway->id == 'paypal' || $gateway->id == 'GoogleCheckout'):
		   $checkout_page = true;
	    endif;
	endforeach;
	endif;
	
	if(!is_apptivo_user_logged_in()) :
	   	$logout_pageid = get_option('apptivo_ecommerce_logout_page_id ');
		$account_pageid = get_option('apptivo_ecommerce_myaccount_page_id ');
		$args[exclude] = $logout_pageid.','.$changepwd_pageid.','.$account_pageid;
	else :
	    $regisdter_pageid = get_option('apptivo_ecommerce_register_page_id ');
	    $login_pageid = get_option('apptivo_ecommerce_login_page_id ');
		$args[exclude] = $regisdter_pageid.','.$login_pageid;	
	endif;
	$args[exclude] = get_option('apptivo_ecommerce_thanks_page_id');
	$args[exclude] = get_option('apptivo_ecommerce_print_receipt_page_id');
	//Secure Checkout Page
	if(!$secure_checkout_method):
	 $args[exclude] = get_option('apptivo_ecommerce_secure_checkout_page_id');
	endif;
	//Checkout Page
	if(!$checkout_page):
	 $args[exclude] = get_option('apptivo_ecommerce_checkout_page_id');
	endif;
	return $args;
}
function filter_wp_list_pages($exclude){
	
	global $apptivo_ecommerce;
	$available_gateways = $apptivo_ecommerce->payment_gateways->available_payment_gateway_lists();
	if($available_gateways):
	$secure_checkout_method = false;
	$checkout_page = false;
	foreach ($available_gateways as $gateway ) :
		if($gateway->id == 'SecureCheckout') :
		   $secure_checkout_method = true;
	    endif;
	     if( $gateway->id == 'paypal' || $gateway->id == 'GoogleCheckout'):
		   $checkout_page = true;
	    endif;
	endforeach;
	endif;
	
	if(!is_apptivo_user_logged_in()) :
	    $logout_pageid = get_option('apptivo_ecommerce_logout_page_id ');
		$account_pageid = get_option('apptivo_ecommerce_myaccount_page_id ');
		$exclude[] = $logout_pageid.','.$changepwd_pageid.','.$account_pageid;
	else :
	    $regisdter_pageid = get_option('apptivo_ecommerce_register_page_id ');
	    $login_pageid = get_option('apptivo_ecommerce_login_page_id ');
		$exclude[] = $regisdter_pageid.','.$login_pageid;	
	endif;	 
	$exclude[] = get_option('apptivo_ecommerce_thanks_page_id');
	$exclude[] = get_option('apptivo_ecommerce_print_receipt_page_id');	
	
	//Secure Checkout Page
	if(!$secure_checkout_method):
	 $exclude[] = get_option('apptivo_ecommerce_secure_checkout_page_id');
	endif;
	//Checkout Page
	if(!$checkout_page):
	$exclude[] = get_option('apptivo_ecommerce_checkout_page_id');
	endif;
	
    return $exclude;
}
/* Footer */
add_action( 'wp_footer', 'apptivo_ecommerce_demo_store' );
/**
 * Demo Site
 *
 * Adds a demo store banner to the site if enabled
 **/
function apptivo_ecommerce_demo_store() {
	if (get_option('apptivo_ecommerce_demo_store')=='yes') :
		echo '<p class="demostore">'.__("This demo site for testing purpose. You can add items to the cart and view various pages, but you can't place the order.", 'apptivo-ecommerce').'</p>';
	endif;
}