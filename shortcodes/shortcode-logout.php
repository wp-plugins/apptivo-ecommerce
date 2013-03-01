<?php
/**
 * Logout ShortCode 
 * Unset the sessions
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */
function get_apptivo_ecommerce_logout( ) {
	global $apptivo_ecommerce;
    if(function_exists('custom_apptivo_ecommerce_logout'))
	{
	return custom_apptivo_ecommerce_logout();	
	}else {
	return $apptivo_ecommerce->shortcode_wrapper('apptivo_ecommerce_logout');
	}
}
function apptivo_ecommerce_logout( ) {
	if(is_apptivo_user_logged_in()) 
	{
		unset($_SESSION['cart']);
		unset($_SESSION['apptivo_cart_sessionId']);
		unset($_SESSION['apptivo_biillingandshipping_address']);
		unset($_SESSION['apptivo_payeridwithtoken']);
		unset($_SESSION['apptivo_user_account_name']);
		unset($_SESSION['apptivo_user_account_id']);
		unset($_SESSION['apptivo_cart_baginfo']);
		unset($_SESSION['chosen_shippingandtax_zipcode']);
		unset($_SESSION['apptivo_ecommerce_orderid']);
		unset($_SESSION['apptivo_add_ons_p_gateways']);
		unset($_SESSION);
	header("location: /");
	exit;
	} else {
		wp_safe_redirect(get_permalink(get_option('apptivo_ecommerce_myaccount_page_id')));
	exit();
	}	
}