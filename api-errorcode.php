<?php
/**
 * Apptivo Error code and Error Messages
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */
function apptivo_ecommerce_error_message($code)
{
$error_message = array( 'PE-001' => 'Payment Failed',
'AE-100' => 'Zip Code can not be empty',
'AE-101' => 'Zip Code is not a valid Zip Code(US)',
'AE-102' => 'Coupon Code can not be empty',
'AE-103' => 'Invalid item quanity',
'AE-104' => 'Invalid Coupon code',
'AE-105' => 'Password is not updated,Please try again',
'AE-106' => 'Password is not updated,Please try again',
'AE-107' => 'Passwords do not match.',
'AE-108' => 'Please enter your password',
'AE-109' => 'Invalid Product quantity',
'AE-110' => 'Username is required',
'AE-111' => 'Password is required',
'AE-112' => 'Invalid Username',
'AE-113' => 'Action was failed.Please try again after 10 mins.',
'AE-114' => 'You cannot add that product to the Shopping Cart.Plese try again later',
'AE-115' => 'Sorry, your session has expired',
'AE-116' => 'Invalid Account username(email)',
'AE-117' => 'Invalid Payment method',
'AE-118' => 'Invalid Credit Card number',
'AE-119' => 'Credit Card is expired',
'AE-120' => 'Invalid CVV Code.',
'AE-122' => 'You must accept our Terms and Conditions.',
'AE-123' => 'Please enter an password',
'AE-124' => 'Password should be minimum 8 characters',
'AE-125' => 'Please enter an Email Address (Username)',
'AE-126' => 'Please enter valid Email Address (Username)',
'AE-127' => 'Action was failed.Please try again after 10 mins.',
'AE-128' => 'Coupon code updated successfully',
'AE-129' => "You didn't select card type",
'AE-130' => "Please enter an confirm password",
'AS-021' => 'Could not locate any user registered with the specified email address. Check your user name.',
'AS-015' => 'Password updated successfully',
'AS-019' => 'Password has been reset successfully and sent in mail.',
'AS-020' => 'Action was failed.Please try again after 10 mins.',
'AS-004' => 'An account is already registered. Please choose another.',
'CE-001' => 'The reCAPTCHA was not entered correctly. Please try again.',
'DE-100' => 'Chosen item successfully removed from cart.',
'DE-101' => 'Action was failed.Please try again after 10 mins.',
'UE-100' => 'Cart Updated.',
'UE-101' => 'Product successfully added to your cart.'

);
apply_filters('apptivo_ecommerce_error_message',$error_message);
return $error_message[$code];
}

function apptivo_ecommerce_api($code)
{
	$api_urls = array('apikey' => 'http://www.apptivo.com/where-to-find-your-apptivo-api-key-apptivo-access-key/',
	                  'recaptcha' => 'https://www.google.com/recaptcha/admin/create');
	return $api_urls[$code];
}