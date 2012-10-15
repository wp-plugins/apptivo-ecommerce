<?php
/*
Plugin Name: Apptivo eCommerce
Plugin URI: https://www.apptivo.com
Description: Turn your Wordpress blog into a complete integrated eCommerce solution.  Easily add products, integrate your payment gateway, and start collecting orders.  Get started by signing up for an Apptivo account to get your API key!
Version: 1.0.1
Author: Rajkumar Mohanasundaram
Author URI: https://www.apptivo.com
Requires at least: 3.1
*/
if (!session_id()) session_start();
/**
 * Constants
 **/ 
if (!defined('APPTIVO_ECOMMERCE_TEMPLATE_URL')) define('APPTIVO_ECOMMERCE_TEMPLATE_URL', 'apptivo-ecommerce/');
if (!defined("APPTIVO_ECOMMERCE_VERSION")) define("APPTIVO_ECOMMERCE_VERSION", "1.0.1");	
if (!defined("PHP_EOL")) define("PHP_EOL", "\r\n");
if (!defined('APPTIVO_ECOMMERCE_PLUGIN_BASEURL')) define('APPTIVO_ECOMMERCE_PLUGIN_BASEURL',plugins_url(basename( dirname(__FILE__))));
if (!defined('APPTIVO_ECOMMERCE_PLUGIN_BASEPATH')) define('APPTIVO_ECOMMERCE_PLUGIN_BASEPATH',dirname(__FILE__));

/**
 * Include admin area
 **/
if (is_admin()) :
	require_once( 'admin/admin-init.php' );
	/**
	 * Installs and upgrades
	 **/
	register_activation_hook( __FILE__, 'activate_apptivo_ecommerce' );
endif;
/*
Include core files
*/
include_once( 'apptivo-config.php' );
include_once( 'apptivo-define.php' );
include_once( 'api-errorcode.php' );

include_once( 'apptivo_ecommerce_taxonomy.php' );
include_once( 'widgets/widgets-init.php' );
include_once( 'shortcodes/shortcodes-init.php' );
include_once( 'apptivo_ecommerce_actions.php' );

include_once( 'apptivo_ecommerce_template_actions.php' );
include_once( 'apptivo_ecommerce_templates.php' );
include_once( 'classes/gateways/gateways.class.php' );
include_once( 'classes/gateways/gateway.class.php' );
/*
 Inclue Class Files
*/
include_once( 'classes/cart.class.php' );
include_once( 'classes/checkout.class.php' );
include_once( 'classes/register.class.php' );
include_once( 'classes/countries.class.php' );
include_once( 'classes/product.class.php' );
include_once( 'classes/validation.class.php' ); 

include_once( 'classes/apptivo_ecommerce.class.php' );
/*
 Include core payment gateways
*/
include_once( 'classes/gateways/gateway-secure-checkout.php' );
include_once( 'classes/gateways/gateway-paypal.php' );
include_once( 'classes/gateways/gateway-google-checkout.php' );
/**
 * Init apptivo_ecommerce class
 */
global $apptivo_ecommerce;
$apptivo_ecommerce = &new apptivo_ecommerce();
/**
 * Init apptivo_ecommerce
 **/
add_action('init', 'apptivo_ecommerce_init', 0);
function apptivo_ecommerce_init() {
	global $apptivo_ecommerce;
	ob_start();		
	//Register Post_type Products.
	apptivo_ecommerce_post_type();	
	// Image sizes
	$product_thumbnail_crop 	= (get_option('apptivo_ecommerce_thumbnail_image_crop')==1) ? true : false;
	$product_catalog_crop 		= (get_option('apptivo_ecommerce_catalog_image_crop')==1) ? true : false;
	$product_single_crop 		= (get_option('apptivo_ecommerce_single_image_crop')==1) ? true : false;

	add_image_size( 'product_thumbnail', $apptivo_ecommerce->get_image_size('product_thumbnail_image_width'), $apptivo_ecommerce->get_image_size('product_thumbnail_image_height'), $product_thumbnail_crop );
	add_image_size( 'product_catalog', $apptivo_ecommerce->get_image_size('product_catalog_image_width'), $apptivo_ecommerce->get_image_size('product_catalog_image_height'), $product_catalog_crop );
	add_image_size( 'product_single', $apptivo_ecommerce->get_image_size('product_single_image_width'), $apptivo_ecommerce->get_image_size('product_single_image_height'), $product_single_crop );

	// Include template functions here so they are pluggable by themes
	include_once( 'apptivo_ecommerce_template_functions.php' );
	
	
    if (!is_admin()) :
            $css = file_exists(get_stylesheet_directory() . '/apptivo-ecommerce/css/apptivo_ecommerce.css') ? get_stylesheet_directory_uri() . '/apptivo-ecommerce/css/apptivo_ecommerce.css' : $apptivo_ecommerce->plugin_url() . '/assets/css/apptivo_ecommerce.css';            
			wp_register_style('apptivo_ecommerce_frontend_styles', $css );
			wp_register_style( 'apptivo_ecommerce_prettyphoto_styles', $apptivo_ecommerce->plugin_url() . '/assets/css/prettyphoto.css' );
			wp_enqueue_style( 'apptivo_ecommerce_frontend_styles' );
	endif;
	
	/** Update meta_key 'appptivo_%' by '_apptivo_%' */
	global $wpdb;
	$limit = (int) apply_filters( 'postmeta_form_limit', 30 );
	$meta_keys = $wpdb->get_col( "
		SELECT meta_key
		FROM $wpdb->postmeta
		GROUP BY meta_key
		HAVING meta_key LIKE 'apptivo_%'
		ORDER BY meta_key
		LIMIT $limit" );
	
	if($meta_keys)
	{
	
		foreach ( $meta_keys as $key )
		{
		 $meta_key_name = esc_attr($key);
		 $query = "UPDATE $wpdb->postmeta SET meta_key='_$meta_key_name' WHERE meta_key='$meta_key_name'";
		 $wpdb->query($query);
		}
	}
	
}

/**  Set up Roles & Capabilities  **/
add_action('init', 'apptivo_ecommerce_init_roles');
function apptivo_ecommerce_init_roles() {
	global $wp_roles;
	if (class_exists('WP_Roles')) if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();		
	if (is_object($wp_roles)) :		
		$wp_roles->add_cap( 'administrator', 'manage_apptivo_ecommerce' );		
	endif;
}

/** Frontend scripts  **/
function apptivo_ecommerce_frontend_scripts() {
	global $apptivo_ecommerce;
	wp_register_script( 'apptivo_ecommerce', $apptivo_ecommerce->plugin_url() . '/assets/js/apptivo_ecommerce.js', 'jquery', '1.0' );
	wp_register_script( 'apptivo_ecommerce_prettyphoto_script', $apptivo_ecommerce->plugin_url() . '/assets/js/prettyphoto.js', 'jquery', '1.0' );
	
	wp_enqueue_script('jquery');
	wp_enqueue_script('apptivo_ecommerce');
	
    	
	/* Script variables */
	$apptivo_ecommerce_params = array(
	    'checkout_page'                 =>get_permalink(get_option('apptivo_ecommerce_checkout_page_id')),
	    'cart_page'                     =>get_permalink(get_option('apptivo_ecommerce_cart_page_id')),
	    'single_image_height'           =>get_option('apptivo_ecommerce_single_image_height'),
	    'print_receipt_page'            =>get_option('apptivo_ecommerce_print_receipt_page_id'),
	    'payment_error'                 => apply_filters('apptivo_ecommerce_payment_error','Error Code : PE-001  Your transaction has been failed, Please go to the cart page and continue the process again'),
		'countries' 					=> json_encode($apptivo_ecommerce->countries->states),
		'select_state_text' 			=> __('Select a state&hellip;', 'apptivo_ecommerce'),
		'state_text' 					=> __('state', 'apptivo_ecommerce'),
		'plugin_url' 					=> $apptivo_ecommerce->plugin_url(),
		'ajax_url' 						=> admin_url('admin-ajax.php'),
		'get_variation_nonce' 			=> wp_create_nonce("get-variation"),
		'add_to_cart_nonce' 			=> wp_create_nonce("add-to-cart"),
		'update_shipping_method_nonce' 	=> wp_create_nonce("update-shipping-method"),		
		'checkout_url'					=> admin_url('admin-ajax.php?action=apptivo_ecommerce-checkout'),
	    'paypal_checkout_url'			=> admin_url('admin-ajax.php?action=apptivo_ecommerce-paypal-checkout'),
	 	'google_checkout_url'			=> admin_url('admin-ajax.php?action=apptivo_ecommerce-google-checkout'),
	    'login_url'					    => admin_url('admin-ajax.php?action=apptivo_ecommerce-login'),
	    'register_url'				    => admin_url('admin-ajax.php?action=apptivo_ecommerce-register'),
	    'confirm_checkout_url'			=> admin_url('admin-ajax.php?action=apptivo_ecommerce-confirm-checkout'),
	    'confirm_page'                  => get_option('apptivo_ecommerce_enable_a_net_confirm'),
	    'confirm_url'                   => add_query_arg( 'step', '2', get_permalink(get_option('apptivo_ecommerce_secure_checkout_page_id')) )
	);
	
	if (isset($_SESSION['min_price'])) $apptivo_ecommerce_params['min_price'] = $_SESSION['min_price'];
	if (isset($_SESSION['max_price'])) $apptivo_ecommerce_params['max_price'] = $_SESSION['max_price'];
	//For Secure and Checkout Page.	
	if ( is_page(get_option('apptivo_ecommerce_checkout_page_id')) || is_page(get_option('apptivo_ecommerce_secure_checkout_page_id')) ) :
		$apptivo_ecommerce_params['is_checkout'] = 1;
	else :
		$apptivo_ecommerce_params['is_checkout'] = 0;
	endif;
	//For Cart Page.
	if ( is_shopping_cart() ) :
		$apptivo_ecommerce_params['is_cart'] = 1;
	else :
		$apptivo_ecommerce_params['is_cart'] = 0;
	endif;
	wp_localize_script( 'apptivo_ecommerce', 'apptivo_ecommerce_params', $apptivo_ecommerce_params );
	
}
add_action('template_redirect', 'apptivo_ecommerce_frontend_scripts');

function is_apptivo_ecommerce() {
	if (is_items() || is_item_category() || is_item_tag() || is_item()) return true; else return false;
}
if (!function_exists('is_items')) {
	function is_items() {
		if (is_post_type_archive( 'item' ) || is_page(get_option('apptivo_ecommerce_products_page_id'))) return true; else return false;
	}
}

if (!function_exists('is_item_category')) {
	function is_item_category() {
		return is_tax( 'item_cat' );
	}
}


if (!function_exists('is_item_tag')) {
	function is_item_tag() {
		return is_tax( 'item_tag' );
	}
}

if (!function_exists('is_item')) {
	function is_item() {
		return is_singular( array('item') );
	}
}
if (!function_exists('is_shopping_cart')) {
	function is_shopping_cart() {
		return is_page(get_option('apptivo_ecommerce_cart_page_id'));
	}
}
if (!function_exists('is_paypal_google_checkout')) {
	function is_paypal_google_checkout() {
		return is_page(get_option('apptivo_ecommerce_checkout_page_id'));
	}
}

if (!function_exists('is_secure_checkout')) {
	function is_secure_checkout() {
		return is_page(get_option('apptivo_ecommerce_secure_checkout_page_id'));
	}
}


if (!function_exists('is_thankyou_page')) {
	function is_thankyou_page() {
		return is_page(get_option('apptivo_ecommerce_thanks_page_id'));
	}
}

if (!function_exists('is_register_page')) {
	function is_register_page() {
		return is_page(get_option('apptivo_ecommerce_register_page_id'));
	}
}

if (!function_exists('is_login_page')) {
	function is_login_page() {
		return is_page(get_option('apptivo_ecommerce_login_page_id'));
	}
}


if (!function_exists('is_account_page')) {
	
	function is_account_page() {
		if ( is_page(get_option('apptivo_ecommerce_myaccount_page_id'))) return true; else return false;		
	}
	
	if (!function_exists('is_ajax')) {
		function is_ajax() {
			if ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) return true; else return false;
		}
	}
	
}

/**
 * Force SSL (if enabled)
 **/
if (get_option('apptivo_ecommerce_force_ssl_checkout')=='yes') add_action( 'wp', 'apptivo_ecommerce_force_ssl');

function apptivo_ecommerce_force_ssl() {
	if (is_paypal_google_checkout() && !is_ssl()) :
	    wp_redirect( str_replace('http:', 'https:', get_permalink(get_option('apptivo_ecommerce_checkout_page_id'))), 301 );
	exit;
	endif;
	
	if (is_secure_checkout() && !is_ssl()) :
		wp_redirect( str_replace('http:', 'https:', get_permalink(get_option('apptivo_ecommerce_secure_checkout_page_id'))), 301 );
	exit;
	endif;	
}
/** IIS compatability fix/fallback  **/
if (!isset($_SERVER['REQUEST_URI'])) {
	$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'],1 );
	if (isset($_SERVER['QUERY_STRING'])) { $_SERVER['REQUEST_URI'].='?'.$_SERVER['QUERY_STRING']; }
}
/** Currency **/
function get_apptivo_ecommerce_currency_symbol() {
	$currency = '$';
	return $currency;
}
/** Price Formatting  **/
function apptivo_ecommerce_price( $price) {
	$return = '';
	if(trim($price) == '') :
	return $return;
	endif;
		
	$currency_symbol = get_apptivo_ecommerce_currency_symbol();
	$price = number_format($price,2,'.','');
	$return = $currency_symbol. $price;
	return $return;
}
/** Clean variables  **/
function apptivo_ecommerce_clean( $var ) {
	return trim(strip_tags(stripslashes($var)));
}
add_theme_support( 'post-thumbnails' );

/* shortcode in editor */
if( is_admin())
{
add_action( 'init', 'apptivo_commerce_add_shortcode_button' );
}

function apptivo_commerce_add_shortcode_button() {
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) return;
	if ( get_user_option('rich_editing') == 'true') :
		add_filter('mce_buttons', 'apptivo_ecommerce_add_shortcode_tinymce_plugin');
		add_filter('mce_external_plugins', 'apptivo_ecommerce_register_shortcode_button');
	endif;
}

function apptivo_ecommerce_add_shortcode_tinymce_plugin($buttons) {
	array_push($buttons, "|", "apptivo-ecommerce_shortcodes_button");
	return $buttons;
}

function apptivo_ecommerce_register_shortcode_button($plugin_array) {
	if( isset( $plugin_array['ApptivoBusinesssiteShortcodes']) ) //Combined eCommerce and Business shortcodes
	{
		unset($plugin_array['ApptivoBusinesssiteShortcodes']);
		$plugin_array['ApptivoecombusinessShortcodes'] = APPTIVO_ECOMMERCE_PLUGIN_BASEURL . '/assets/js/editor.js';
	}else {                                                     //eCommerce Shortcodes
	    $plugin_array['ApptivoecommerceShortcodes'] = APPTIVO_ECOMMERCE_PLUGIN_BASEURL . '/assets/js/editor.js';
	}
	return $plugin_array;
}

function apptivo_ecommerce_itemname($title)
{
	$length = apply_filters('apptivo_ecommerce_item_title_length',27);
	if( strlen(trim($title)) <= $length)
	{
		return $title;
	}else{
		return substr(trim($title),0,$length).'...';
	}
}