<?php
/**
 * apptivo_ecommerce Install
 * @category 	Admin
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */

/* Activate apptivo_ecommerce  */
function activate_apptivo_ecommerce() {  install_apptivo_ecommerce(); }

/* Install apptivo_ecommerce  */
function install_apptivo_ecommerce() {
	// Do install
	apptivo_ecommerce_default_options();
	apptivo_ecommerce_create_pages();
	apptivo_ecommerce_populate_custom_fields();	
	// Update version
	update_option( "apptivo_ecommerce_plugin_version", APPTIVO_ECOMMERCE_VERSION );
	update_option( "apptivo_ecommerce_plugin_installed", 1 );		
}

add_action('admin_init', 'install_apptivo_commerce_redirect');
function install_apptivo_commerce_redirect() {
	global $pagenow;
	if ( is_admin() && isset( $_GET['activate'] ) && ($_GET['activate'] == true) && $pagenow == 'plugins.php' & get_option('apptivo_ecommerce_plugin_installed') == 1) :
	update_option( "apptivo_ecommerce_plugin_installed", 0 );
	// Flush rewrites
		flush_rewrite_rules( false );		
		// Redirect to settings
		wp_redirect(admin_url('admin.php?page=apptivo_ecommerce&installed=true'));
		exit;
	endif;
}
/**
 * Add required post meta so queries work
 */
function apptivo_ecommerce_populate_custom_fields() {
	// Attachment exclusion
	$args = array( 
		'post_type' 	=> 'attachment', 
		'numberposts' 	=> -1, 
		'post_status' 	=> null, 
		'fields' 		=> 'ids'
	); 
	$attachments = get_posts($args);
	if ($attachments) foreach ($attachments as $id) :
		add_post_meta($id, '_apptivo_ecommerce_exclude_image', 0);
	endforeach;	
}
/* Default options */
function apptivo_ecommerce_default_options() {
	global $apptivo_ecommerce_settings;
	// Include settings so that we can run through defaults
	include_once( 'admin-settings.php' );	
	foreach ($apptivo_ecommerce_settings as $section) :
		foreach ($section as $value) :
	        if (isset($value['std'])) :
	        	if ($value['type']=='image_width') :
	        		add_option($value['id'].'_width', $value['std']);
	        		add_option($value['id'].'_height', $value['std']);
	        	else :
	        		add_option($value['id'], $value['std']);
	        	endif;
	        endif;
        endforeach;
     endforeach;
    add_option('apptivo_ecommerce_products_slug', 'products');
}
/* Create a page  */
function apptivo_ecommerce_create_page( $slug, $option, $page_title = '', $page_content = '', $post_parent = 0 ) {
	global $wpdb;
	 
	$option_value = get_option($option); 
	 
	if ($option_value>0) :
		if (get_post( $option_value )) :
			// Page exists
			return;
		endif;
	endif;
	
	$page_found = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = '$slug' LIMIT 1;");
	if ($page_found) :
		// Page exists
		return;
	endif;
		
	$page_data = array(
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1,
        'post_name' => $slug,
        'post_title' => $page_title,
        'post_content' => $page_content,
        'post_parent' => $post_parent,
        'comment_status' => 'closed'
    );
    $page_id = wp_insert_post($page_data);    
    update_option($option, $page_id);
}
 
/* Create pages  */
function apptivo_ecommerce_create_pages() {	
	// Products page
    apptivo_ecommerce_create_page( esc_sql( _x('products', 'page_slug', 'apptivo_ecommerce') ), 'apptivo_ecommerce_products_page_id', __('Products', 'apptivo_ecommerce'), '' );
    // Cart page
    apptivo_ecommerce_create_page( esc_sql( _x('cart', 'page_slug', 'apptivo_ecommerce') ), 'apptivo_ecommerce_cart_page_id', __('Cart', 'apptivo_ecommerce'), '[apptivo_ecommerce_cart]' );
    // Checkout page
    apptivo_ecommerce_create_page( esc_sql( _x('checkout', 'page_slug', 'apptivo_ecommerce') ), 'apptivo_ecommerce_checkout_page_id', __('Checkout', 'apptivo_ecommerce'), '[apptivo_ecommerce_checkout]' );
    // Secure Checkout page
    apptivo_ecommerce_create_page( esc_sql( _x('secure-checkout', 'page_slug', 'apptivo_ecommerce') ), 'apptivo_ecommerce_secure_checkout_page_id', __('Secure Checkout', 'apptivo_ecommerce'), '[apptivo_ecommerce_secure_checkout]' );
    // My Account page
    apptivo_ecommerce_create_page( esc_sql( _x('my-account', 'page_slug', 'apptivo_ecommerce') ), 'apptivo_ecommerce_myaccount_page_id', __('My Account', 'apptivo_ecommerce'), '[apptivo_ecommerce_my_account]' );
	// Thanks page
    apptivo_ecommerce_create_page( esc_sql( _x('thanks', 'page_slug', 'apptivo_ecommerce') ), 'apptivo_ecommerce_thanks_page_id', __('Order Received', 'apptivo_ecommerce'), '[apptivo_ecommerce_thankyou]', '');
    // Print_receipt
    apptivo_ecommerce_create_page( esc_sql( _x('print_receipt', 'page_slug', 'apptivo_ecommerce') ), 'apptivo_ecommerce_print_receipt_page_id', __('Print receipt', 'apptivo_ecommerce'), '', '' );
    // Register page
    apptivo_ecommerce_create_page( esc_sql( _x('register', 'page_slug', 'apptivo_ecommerce') ), 'apptivo_ecommerce_register_page_id', __('Register', 'apptivo_ecommerce'), '[apptivo_ecommerce_register]' );
    // Logout page
    apptivo_ecommerce_create_page( esc_sql( _x('Logout', 'page_slug', 'apptivo_ecommerce') ), 'apptivo_ecommerce_logout_page_id', __('Logout', 'apptivo_ecommerce'), '[apptivo_ecommerce_logout]' );
    // Login page
    apptivo_ecommerce_create_page( esc_sql( _x('Login', 'page_slug', 'apptivo_ecommerce') ), 'apptivo_ecommerce_login_page_id', __('Login', 'apptivo_ecommerce'), '[apptivo_ecommerce_login]' );    
}