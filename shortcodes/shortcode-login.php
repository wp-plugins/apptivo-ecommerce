<?php
/**
 * Login Page Shord Code.
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */
function get_apptivo_ecommerce_login() {
	
	global $apptivo_ecommerce;
	if(function_exists('custom_apptivo_ecommerce_login'))
	{
	return custom_apptivo_ecommerce_login();	
	}else {
	return $apptivo_ecommerce->shortcode_wrapper('apptivo_ecommerce_login');
	}
}

function apptivo_ecommerce_login() {
	global $apptivo_ecommerce;
	$apptivo_ecommerce->show_messages();
	if(is_apptivo_user_logged_in()) 
	{
	wp_safe_redirect(get_permalink(get_option('apptivo_ecommerce_myaccount_page_id')));
	exit();
	} else {
		apptivo_ecommerce_login_form('','login');
	}
}