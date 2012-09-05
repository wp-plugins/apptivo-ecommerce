<?php
/**
 * Authorize.Net Checkout
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
**/
 
function get_apptivo_ecommerce_secure_checkout( ) {
	global $apptivo_ecommerce;
	if(function_exists('custom_apptivo_ecommerce_secure_checkout'))
	{
	return custom_apptivo_ecommerce_secure_checkout( );	
	}else {
	return $apptivo_ecommerce->shortcode_wrapper('apptivo_ecommerce_secure_checkout');
	}
}

function apptivo_ecommerce_secure_checkout() {
	global $apptivo_ecommerce_checkout;
	//To get Cart details.
	 $Cart_Lines = get_baginfo()->shoppingCartLines; //Select shopping cart Lines for update cart.
	 $CartLines = app_convertObjectToArray($Cart_Lines);
	if(empty($CartLines[0])) :
		wp_redirect(get_permalink(get_option('apptivo_ecommerce_cart_page_id')));
		exit;
	endif;
	$apptivo_ecommerce_checkout = &new apptivo_ecommerce_checkout();
	
	if (isset($_GET['step']) && trim($_GET['step']) != '2' )
	{
		unset($_SESSION['apptivo_ecommerce_confirm_page']);
		wp_redirect(get_permalink(get_option('apptivo_ecommerce_secure_checkout_page_id')));
		exit;
	}else if (isset($_GET['step']) && trim($_GET['step']) == '2' ) {
		if( !isset($_SESSION['apptivo_ecommerce_confirm_page']))
		{
		  wp_redirect(get_permalink(get_option('apptivo_ecommerce_secure_checkout_page_id')));
		  exit;			
		}
	}
	
	if (isset($_GET['step']) && trim($_GET['step']) == '2' ) {
		$apptivo_ecommerce_checkout->process_confirm_checkout();
	    apptivo_ecommerce_get_template('checkout/confirm.php', false);
	}else{
		$apptivo_ecommerce_checkout->process_checkout();
	    apptivo_ecommerce_get_template('checkout/form.php', false);
	}
		
}