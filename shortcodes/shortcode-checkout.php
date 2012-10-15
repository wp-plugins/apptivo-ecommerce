<?php
/**
 * Checkout Shortcode (Paypal And google)
 * 
 * Used on the checkout page, the checkout shortcode displays the checkout process.
 *
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */
function get_apptivo_ecommerce_checkout() {
	global $apptivo_ecommerce;
	if(function_exists('custom_apptivo_ecommerce_checkout'))
	{
	return custom_apptivo_ecommerce_checkout();	
	}else {
	return $apptivo_ecommerce->shortcode_wrapper('apptivo_ecommerce_checkout');
	}
}

function apptivo_ecommerce_checkout() {
  global $apptivo_ecommerce_checkout;
	//To get Cart details.	
	 $Cart_Lines = get_baginfo()->shoppingCartLines; //Select shopping cart Lines for update cart.
	 $CartLines = app_convertObjectToArray($Cart_Lines);
	if(empty($CartLines[0])) :
		wp_redirect(get_permalink(get_option('apptivo_ecommerce_cart_page_id')));
		exit;
	endif;
	$apptivo_ecommerce_checkout = &new apptivo_ecommerce_checkout();
	$apptivo_ecommerce_checkout->process_paypal_checkout();
	apptivo_ecommerce_get_template('checkout/checkoutform.php', false);	
}