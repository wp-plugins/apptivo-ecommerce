<?php
/**
 * Apptivo Ecommerce Configuration
 * @package Apptivo eCommerce
 * @author Rajkumar <rmohanasundaram[at]apptivo[dot]com>
*/
define('API_SERVICES','https://api.apptivo.com/'); //API
$apptivo_ecommerce_apikey = trim(get_option('apptivo_ecommerce_apikey'));
$apptivo_ecommerce_accesskey = trim(get_option('apptivo_ecommerce_accesskey'));
define('APPTIVO_ECOMMERCE_API_KEY',$apptivo_ecommerce_apikey);
define('APPTIVO_ECOMMERCE_ACCESSKEY',$apptivo_ecommerce_accesskey);
define('ITEM_WSDL',API_SERVICES.'app/services/v1/ItemServices?wsdl');
define('INDEX_ITEM_WSDL',API_SERVICES.'ts/services/AppItemWebService?wsdl');
define('CART_WSDL',API_SERVICES.'app/services/v1/CartServices?wsdl');
define('USER_WSDL',API_SERVICES.'app/services/v1/UserServices?wsdl');
define('SHIPPING_WSDL',API_SERVICES.'app/services/v1/ShippingServices?wsdl');
