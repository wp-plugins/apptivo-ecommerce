<?php
/**
 * Shortcode Init
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */
include_once('shortcode-cart.php');
include_once('shortcode-secure_checkout.php');
include_once('shortcode-checkout.php');
include_once('shortcode-my_account.php');
include_once('shortcode-login.php');
include_once('shortcode-logout.php');
include_once('shortcode-register.php');
include_once('shortcode-thankyou.php');
/**Shortcode creation **/
/* default Shortcode */
add_shortcode('apptivo_ecommerce_cart', 'get_apptivo_ecommerce_cart');
add_shortcode('apptivo_ecommerce_secure_checkout', 'get_apptivo_ecommerce_secure_checkout');
add_shortcode('apptivo_ecommerce_checkout', 'get_apptivo_ecommerce_checkout');
add_shortcode('apptivo_ecommerce_logout', 'get_apptivo_ecommerce_logout');
add_shortcode('apptivo_ecommerce_register', 'get_apptivo_ecommerce_register');
add_shortcode('apptivo_ecommerce_login', 'get_apptivo_ecommerce_login');
add_shortcode('apptivo_ecommerce_my_account', 'get_apptivo_ecommerce_my_account');
add_shortcode('apptivo_ecommerce_edit_address', 'get_apptivo_ecommerce_edit_address');
add_shortcode('apptivo_ecommerce_change_password', 'get_apptivo_ecommerce_change_password');
add_shortcode('apptivo_ecommerce_thankyou', 'get_apptivo_ecommerce_thankyou');
/* Custom Shortcode */
add_shortcode('apptivo_ecommerce_featured_products', 'apptivo_ecommerce_featured_products');//Featured products

add_shortcode('apptivo_ecommerce_products_by_category', 'apptivo_ecommerce_products_by_category'); //Products by category ID
add_shortcode('apptivo_ecommerce_recent_products', 'apptivo_ecommerce_recent_products');//Recent Products
add_shortcode('apptivo_ecommerce_products_by_price', 'apptivo_ecommerce_products_by_price');//Products by Price

/*
 * Apptivo eCommerce products by category Ids
 */
function apptivo_ecommerce_products_by_category($atts)
{

	global $apptivo_ecommerce_loop;

	extract(shortcode_atts(array(
	    'featured' =>'',
	    'per_page'    => '8',
	    'columns' 	=> '4',
	    'pagination_type'=>'',
	    'category_id'=>'',
	    'orderby'=> 'date',
	    'order' => 'desc'
	    ), $atts));


	    $apptivo_ecommerce_loop['columns'] = $columns;
	    $apptivo_ecommerce_loop['page_id'] = get_the_ID();
	    $apptivo_ecommerce_loop['pagination_type'] = $pagination_type;

	    $featured_type = trim(strtolower($featured));
	    if( $featured_type == 'yes')
	    {
	    	$meta_query = array(
		    array('key' => '_apptivo_featured','value' => 'yes','compare' => '==','type' => 'CHAR'),
		    array('key' => '_apptivo_enabled','value' => 'yes','compare' => '=','type' => 'CHAR')
		    );
	    }else if( $featured_type == 'no' )
	    {
	    	$meta_query = array(
		    array('key' => '_apptivo_featured','value' => 'no','compare' => '==','type' => 'CHAR'),
		    array('key' => '_apptivo_enabled','value' => 'yes','compare' => '=','type' => 'CHAR')
		    );

	    }else{
	    	$meta_query = array(
		    array('key' => '_apptivo_enabled','value' => 'yes','compare' => '=','type' => 'CHAR')
		    );
	    }

	    $args = array(
		'post_type'	=> 'item',
	    'paged'          => get_query_var('paged'),	
		'post_status' => 'publish',
		'posts_per_page' => $per_page,
		'orderby' => $orderby,
		'order' => $order,
	    'meta_key' 	=> '_apptivo_sale_price',
		'meta_query' => $meta_query
	    );

	    $array_category = explode(',',trim($category_id));

	    $args['tax_query'] = array(
	    array(
				        'taxonomy' => 'item_cat',
				        'terms' => $array_category,
				        'field' => 'id'
				        ));
				        query_posts($args);
				        ob_start();
				        apptivo_ecommerce_get_template_part( 'custom', 'products');
				        wp_reset_query();
				        return ob_get_clean();

}

/**
 * Apptivo eCommerce featured products
 **/
function apptivo_ecommerce_featured_products( $atts ) {

	global $apptivo_ecommerce_loop;

	extract(shortcode_atts(array(
	    'per_page'    => '8',
	    'columns' 	=> '4',
	    'pagination_type'=>'',
	    'orderby' => 'date',
	     'order'=>'desc'
	     ), $atts));

	     $apptivo_ecommerce_loop['columns'] = $columns;
	     $apptivo_ecommerce_loop['page_id'] = get_the_ID();
	     $apptivo_ecommerce_loop['pagination_type'] = $pagination_type;
	     $args = array(
		'post_type'	=> 'item',
		'post_status' => 'publish',
	    'paged'          => get_query_var('paged'),
		'posts_per_page' => $per_page,
		'orderby' => $orderby,
		'order' => $order,
	    'meta_key' 	=> '_apptivo_sale_price',
		'meta_query' => array(
	     array('key' => '_apptivo_featured','value' => 'yes','compare' => '=','type' => 'CHAR'),
	     array('key' => '_apptivo_enabled','value' => 'yes','compare' => '=','type' => 'CHAR')
	     )
	     );

	     query_posts($args);
	     ob_start();
	     apptivo_ecommerce_get_template_part( 'custom', 'products');
	     wp_reset_query();
	     return ob_get_clean();
}

/**
 * Apptivo eCommerce recent products.
 */
function apptivo_ecommerce_recent_products( $atts )
{


	global $apptivo_ecommerce_loop;

	extract(shortcode_atts(array(
	    'featured' =>'',
	    'per_page'    => '8',
	    'columns' 	=> '4',
	    'pagination_type'=>'',
	    'orderby' => 'date',
	     'order'=>'desc'
	     ), $atts));

	     $apptivo_ecommerce_loop['columns'] = $columns;
	     $apptivo_ecommerce_loop['page_id'] = get_the_ID();
	     $apptivo_ecommerce_loop['pagination_type'] = $pagination_type;

	     $featured_type = trim(strtolower($featured));
	     if( $featured_type == 'yes')
	     {
	     	$meta_query = array(
	     	array('key' => '_apptivo_featured','value' => 'yes','compare' => '==','type' => 'CHAR'),
	     	array('key' => '_apptivo_enabled','value' => 'yes','compare' => '=','type' => 'CHAR')
	     	);
	     }else if( $featured_type == 'no' )
	     {
	     	$meta_query = array(
	     	array('key' => '_apptivo_featured','value' => 'no','compare' => '==','type' => 'CHAR'),
	     	array('key' => '_apptivo_enabled','value' => 'yes','compare' => '=','type' => 'CHAR')
	     	);

	     }else{
	     	$meta_query = array(
	     	array('key' => '_apptivo_enabled','value' => 'yes','compare' => '=','type' => 'CHAR')
	     	);
	     }

	     $args = array(
		'post_type'	=> 'item',
	    'paged'          => get_query_var('paged'),	
		'post_status' => 'publish',
		'posts_per_page' => $per_page,
		'orderby' => $orderby,
		'order' => $order,
	    'meta_key' 	=> '_apptivo_sale_price',
		'meta_query' => $meta_query
	     );

	     query_posts($args);
	     ob_start();
	     apptivo_ecommerce_get_template_part( 'custom', 'products');
	     wp_reset_query();
	     return ob_get_clean();

}
/**
 * Apptivo eCommerce products by price rate.
 */
function apptivo_ecommerce_products_by_price( $atts )
{


	global $apptivo_ecommerce_loop;

	extract(shortcode_atts(array(
	    'featured'=>'',
	    'per_page'    => '8',
	    'columns' 	=> '4',
	    'min'=>'',
	    'max' => '',
	    'orderby'=> 'meta_value_num',
	    'order'  =>'desc',
	    'pagination_type'=>''
	    ), $atts));

	    $apptivo_ecommerce_loop['columns'] = $columns;
	    $apptivo_ecommerce_loop['page_id'] = get_the_ID();
	    $apptivo_ecommerce_loop['pagination_type'] = $pagination_type;

	    $args = array(
		'post_type'	=> 'item',
	    'paged'          => get_query_var('paged'),	
		'post_status' => 'publish',
		'posts_per_page' => $per_page,
		'orderby' => $orderby,
		'order' => $order,
	    'meta_key' 	=> '_apptivo_sale_price'		
	    );

	    if( $max != '' )
	    {
	    	$args_meta_query = array(
	    	array('key' => '_apptivo_enabled','value' => 'yes','compare' => '=','type' => 'CHAR'),
	    	array('key' => '_apptivo_sale_price','value' => array($min,$max),'compare' => 'BETWEEN','type' => 'numeric')
	    	);
	    }else{
	    	if( $min != '' )
	    	{
	    		$args_meta_query = array(
	    		array('key' => '_apptivo_enabled','value' => 'yes','compare' => '=','type' => 'CHAR'),
	    		array('key' => '_apptivo_sale_price','value' => $min,'compare' => '>=','type' => 'numeric')
	    		);
	    	}else {
	    		$args_meta_query = array(
	    		array('key' => '_apptivo_enabled','value' => 'yes','compare' => '=','type' => 'CHAR')
	    		);
	    	}
	    }

	    $featured_type = trim(strtolower($featured));
	    if( $featured_type == 'yes') {
	    	$meta_query = array('key' => '_apptivo_featured','value' => 'yes','compare' => '==','type' => 'CHAR');
	    	array_push($args_meta_query,$meta_query);
	    	$args['meta_query'] = $args_meta_query;

	    }else if( $featured_type == 'no') {
	    	$meta_query = array('key' => '_apptivo_featured','value' => 'no','compare' => '==','type' => 'CHAR');
	    	array_push($args_meta_query,$meta_query);
	    	$args['meta_query'] = $args_meta_query;

	    }else{
	    	$args['meta_query'] = $args_meta_query;
	    }

	   
	    query_posts($args);
	    ob_start();
	    apptivo_ecommerce_get_template_part( 'custom', 'products');
	    wp_reset_query();
	    return ob_get_clean();

}
/*
 * Apptivo eComemrce recent Products
 */
function apptivo_recent_products($per_page=8,$orderby='date',$order='desc')
{
	wp_reset_query();
	$args = array(
		'post_type'	=> 'item',
		'post_status' => 'publish',	   
		'posts_per_page' => $per_page,
		'orderby' => $orderby,
		'order' => $order,
	    'meta_key' 	=> '_apptivo_sale_price',
		'meta_query' => array(
	array('key' => '_apptivo_enabled','value' => 'yes','compare' => '=','type' => 'CHAR')
	)
	);

	query_posts($args);
}
/*
 * Apptivo eComemrce Featured Products
 */
function apptivo_featured_products($per_page=8,$orderby='date',$order='desc')
{
	wp_reset_query();
	$args = array(
		'post_type'	=> 'item',
		'post_status' => 'publish',	   
		'posts_per_page' => $per_page,
		'orderby' => $orderby,
		'order' => $order,
	    'meta_key' 	=> '_apptivo_sale_price',
		'meta_query' => array(
	array('key' => '_apptivo_featured','value' => 'yes','compare' => '=','type' => 'CHAR'),
	array('key' => '_apptivo_enabled','value' => 'yes','compare' => '=','type' => 'CHAR')
	)
	);

	query_posts($args);
}