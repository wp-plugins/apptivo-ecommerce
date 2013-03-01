<?php
/**
 * Register ShortCode
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */
function get_apptivo_ecommerce_register( ) {
	global $apptivo_ecommerce;
	if(function_exists('custom_apptivo_ecommerce_register'))
	{
	return custom_apptivo_ecommerce_register( );	
	}else {
	return $apptivo_ecommerce->shortcode_wrapper('apptivo_ecommerce_register');
	}
}

function apptivo_ecommerce_register( ) {
	
	if(is_apptivo_user_logged_in()) :
		wp_safe_redirect(get_permalink(get_option('apptivo_ecommerce_myaccount_page_id'))); //Myaccount Page.
		exit;
    endif;
	echo '<div id="register_focus" ></div>';
	
	$apptivo_ecommerce_register = &new apptivo_ecommerce_register();
	$apptivo_ecommerce_register->process_registerform(); //Post Forms
	apptivo_ecommerce_get_template('register/registerform.php', false);//Form fields	
}