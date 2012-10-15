<?php
if ( ! is_admin() )
{
	
/** template loade **/
add_filter( 'template_include', 'apptivo_ecommerce_template_loader' );
/** body Class **/
add_action('wp_head', 'apptivo_ecommerce_page_body_classes');
/* Remove the singular class */
add_action( 'after_setup_theme', 'apptivo_ecommerce_body_classes_check' );
	
}

function apptivo_ecommerce_body_classes_check () {

	add_filter( 'body_class', 'apptivo_ecommerce_body_classes' );
}

/**
 * Apptivo eCommerce Templates
 */
function apptivo_ecommerce_template_loader( $template ) {
	 global $apptivo_ecommerce;	
     global $posts;
     if( $posts[0]->ID == get_option('apptivo_ecommerce_print_receipt_page_id') )
     {
     	$template = locate_template( array( 'print-receipt.php', APPTIVO_ECOMMERCE_TEMPLATE_URL . 'print-receipt.php' ) );
     	$template = $apptivo_ecommerce->plugin_path() . '/templates/print-receipt.php';
     	return $template;
     }
	if ( is_single() && get_post_type() == 'item' ) {
		
		//Include Pretty Photo JS and CSS
		if ( get_option('apptivo_ecommerce_enable_lightbox') == 'yes')
		{
			wp_enqueue_style( 'apptivo_ecommerce_prettyphoto_styles'); //CSS
			wp_enqueue_script('apptivo_ecommerce_prettyphoto_script');//JS
		}
		
		$template = locate_template( array( 'single-product.php', APPTIVO_ECOMMERCE_TEMPLATE_URL . 'single-product.php' ) );		
		if ( ! $template ) $template = $apptivo_ecommerce->plugin_path() . '/templates/single-product.php';
				
	}
	elseif ( is_tax('item_cat') ) {
				
		$template = locate_template(  array( 'taxonomy-product_cat.php', APPTIVO_ECOMMERCE_TEMPLATE_URL . 'taxonomy-product_cat.php' ) );
		if ( ! $template ) $template = $apptivo_ecommerce->plugin_path() . '/templates/taxonomy-product_cat.php';
						
	}
	elseif ( is_tax('item_tag') ) {
			    
		$template = locate_template(  array( 'taxonomy-item_tag.php', APPTIVO_ECOMMERCE_TEMPLATE_URL . 'taxonomy-item_tag.php' ) );
		if ( ! $template ) $template = $apptivo_ecommerce->plugin_path() . '/templates/taxonomy-item_tag.php';
		
	}elseif ( is_post_type_archive('item') ||  is_page( get_option('apptivo_ecommerce_products_page_id') )) {
				
		$template = locate_template( array( 'archive-product.php', APPTIVO_ECOMMERCE_TEMPLATE_URL . 'archive-product.php' ) );
		if ( ! $template ) $template = $apptivo_ecommerce->plugin_path() . '/templates/archive-product.php';
			
		}

	return $template;
}


/**
 * get eCommerce template part
 *
 * @param $slug
 * @param $name
 */
function apptivo_ecommerce_get_template_part( $slug, $name = '' ) {
	global $apptivo_ecommerce;
	if ($name=='products') :
		if (!locate_template(array( $slug.'-products.php', APPTIVO_ECOMMERCE_TEMPLATE_URL . $slug.'-products.php' ))) :
		     load_template( $apptivo_ecommerce->plugin_path() . '/templates/'.$slug.'-products.php',false );
			 return;
	    else:
	      if(file_exists( STYLESHEETPATH . '/' . APPTIVO_ECOMMERCE_TEMPLATE_URL . $slug.'-products.php' ))
		    {
		     load_template( STYLESHEETPATH . '/' . APPTIVO_ECOMMERCE_TEMPLATE_URL . $slug.'-products.php',false );
		     return;
		    }
		endif;
	endif;

}

function apptivo_ecommerce_get_template($template_name, $require_once = true) {

	global $apptivo_ecommerce;
	if (file_exists( STYLESHEETPATH . '/' . APPTIVO_ECOMMERCE_TEMPLATE_URL . $template_name )) load_template( STYLESHEETPATH . '/' . APPTIVO_ECOMMERCE_TEMPLATE_URL . $template_name, $require_once ); 
	elseif (file_exists( STYLESHEETPATH . '/' . $template_name )) load_template( STYLESHEETPATH . '/' . $template_name , $require_once); 
	else load_template( $apptivo_ecommerce->plugin_path() . '/templates/' . $template_name , $require_once);
	
}

/**  Add Body classes based on page/template and Browser  **/
function apptivo_ecommerce_page_body_classes() {

	$theme_name = ( function_exists( 'wp_get_theme' ) ) ? wp_get_theme() : get_current_theme();
	
	add_bodytag_class( "theme-{$theme_name}" );
	
	if (is_apptivo_ecommerce()) add_bodytag_class('apptivo_ecommerce');
	
	if (is_paypal_google_checkout()) add_bodytag_class('apptivo_ecommerce-checkout');
	
	if (is_secure_checkout()) add_bodytag_class('apptivo_ecommerce-securecheckout');
	
	if (is_shopping_cart()) add_bodytag_class('apptivo_ecommerce-cart');
	
	if (is_account_page()) add_bodytag_class('apptivo_ecommerce-account');
	
    if( is_thankyou_page() )  add_bodytag_class('apptivo_ecommerce-thankyou');
    
    if( is_register_page() )  add_bodytag_class('apptivo_ecommerce-register');
    
    if( is_login_page() )  add_bodytag_class('apptivo_ecommerce-login');
	
}

function add_bodytag_class( $class ) {
	
	global $apptivo_ecommerce_body_classes;
	$apptivo_ecommerce_body_classes[] = sanitize_html_class( strtolower($class) );
	
	//Browser body class	
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;
    if($is_lynx) $apptivo_ecommerce_body_classes[] = 'lynx';
    elseif($is_gecko) $apptivo_ecommerce_body_classes[] = 'gecko';
    elseif($is_opera) $apptivo_ecommerce_body_classes[] = 'opera';
    elseif($is_NS4) $apptivo_ecommerce_body_classes[] = 'ns4';
    elseif($is_safari) $apptivo_ecommerce_body_classes[] = 'safari';
    elseif($is_chrome) $apptivo_ecommerce_body_classes[] = 'chrome';
    elseif($is_IE) $apptivo_ecommerce_body_classes[] = 'ie';
    else $apptivo_ecommerce_body_classes[] = 'unknown';
    if($is_iphone) $apptivo_ecommerce_body_classes[] = 'iphone';
    
}
	
/**
 * Remove the singular class
 **/
function apptivo_ecommerce_body_classes ($classes) {
	
	global $apptivo_ecommerce_body_classes;
	
	if ( sizeof( $apptivo_ecommerce_body_classes ) > 0 ) $classes = array_merge( $classes, $apptivo_ecommerce_body_classes );

	  if ( is_singular('item') || is_page(get_option('apptivo_ecommerce_products_page_id')) ) {
			$key = array_search( 'singular', $classes );
			if ( $key !== false ) unset( $classes[$key] );
		}
		
	return $classes;
	
}