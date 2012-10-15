<?php
/**
 * apptivo_ecommerce cart
 */
class apptivo_ecommerce_cart {
		
	/** constructor */
	function __construct() {
		
	}
    /** Empty the cart */
	function emty_shopping_cart() {
	
	unset($_SESSION['apptivo_cart_sessionId']);
	unset($_SESSION['apptivo_cart_baginfo']);
	unset($_SESSION['apptivo_shopping cart_lines']);
    unset($_SESSION['chosen_shippingandtax_zipcode']);
    unset($_SESSION['cart'] );
	unset($_SESSION['coupons'] );
	unset($_SESSION['apptivo_checkout_shipping_type']);
	unset($_SESSION['apptivo_ecommerce_orderid']);
	unset($_SESSION['apptivo_cart_sessionId']);
	unset($_SESSION['apptivo_biillingandshipping_address']);
	unset($_SESSION['apptivo_payeridwithtoken']);
	unset($_SESSION['apptivo_add_ons_p_gateways']);
	
		
	}
	/** gets the url to the cart page */
	function shopping_cart_url() {
		$cart_page_id = get_option('apptivo_ecommerce_cart_page_id');
		if ($cart_page_id) return get_permalink($cart_page_id);
	}
	/** gets the url to the checkout page */
	function checkout_url() {
		$checkout_page_id = get_option('apptivo_ecommerce_checkout_page_id');
		if ($checkout_page_id) :
			if (is_ssl()) return str_replace('http:', 'https:', get_permalink($checkout_page_id));
			return get_permalink($checkout_page_id);
		endif;
	}
	/** gets the url to the secure checkout page */
   function secure_checkout_url() {
		$secure_checkout_page_id = get_option('apptivo_ecommerce_secure_checkout_page_id');
		if ($secure_checkout_page_id) :
			if (is_ssl()) return str_replace('http:', 'https:', get_permalink($secure_checkout_page_id));
			return get_permalink($secure_checkout_page_id);
		endif;
	}
	/** gets the url to remove an item from the cart */
	function delete_item_url( $cart_item_key ) {
		global $apptivo_ecommerce;
		$cart_page_id = get_option('apptivo_ecommerce_cart_page_id');
		if ($cart_page_id) return $apptivo_ecommerce->nonce_url( 'cart', add_query_arg('remove_item', $cart_item_key, get_permalink($cart_page_id)));
	}
	/** Sees if we need a shipping address */
	function ship_to_billing_address() {	
		$ship_to_billing_address_only = get_option('apptivo_ecommerce_ship_to_billing_address_only');
		
		if ($ship_to_billing_address_only=='yes') return true;
		
		return false;
	}
	
	/** looks at the totals to see if payment is actually required */
	function needs_payment() {		
	 $Cart_Lines = get_baginfo()->shoppingCartLines; //Select shopping cart Lines for update cart.
	 $CartLines = app_convertObjectToArray($Cart_Lines);
	 if(!empty($CartLines[0])) return true;
	 return false;
	
	}
	
	/** clears the cart/coupon data and re-calcs totals */
	function clear_cache() {
		unset( $_SESSION['cart'] );
		unset( $_SESSION['coupons'] );
		
	}
}