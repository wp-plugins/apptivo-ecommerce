<?php
global $wpdb;
add_action('wp_ajax_apptivo_ecommerce_check_apikey','apptivo_ecommerce_check_apikey');
add_action('wp_ajax_nopriv_apptivo_ecommerce_check_apikey','apptivo_ecommerce_check_apikey');
function apptivo_ecommerce_check_apikey()
{ 
	$apiKey = trim($_POST['apikey']);
	$apptivo_ecommerce_apikey = trim(get_option('apptivo_ecommerce_apikey',true));
	if( $apptivo_ecommerce_apikey == ''){
		echo 1000;die();
	}
	if( $apiKey == $apptivo_ecommerce_apikey ):
	echo 1000;
	else:
	echo 1005;
	endif;
	die();
}

add_action('wp_ajax_apptivo_ecommerce_update_apikey','apptivo_ecommerce_update_apikey');
add_action('wp_ajax_nopriv_apptivo_ecommerce_update_apikey','apptivo_ecommerce_update_apikey');
function apptivo_ecommerce_update_apikey()
{
	global $wpdb;
    $table_posts=$wpdb->prefix.'posts'; 

    /* Delete Products */
    $delete_ptoduct = 'delete from '.$table_posts.' where post_type="item"';
    $result = $wpdb->query($delete_ptoduct);
    
    $table_postmeta=$wpdb->prefix.'postmeta';
    $delete_postmeta = 'delete from '.$table_postmeta.' where meta_key="_apptivo_featured" or meta_key="_apptivo_item_code" or meta_key="_apptivo_enabled" or meta_key="_apptivo_track_color"
     or meta_key="_apptivo_track_size" or meta_key="_apptivo_regular_price" or meta_key="_apptivo_sale_price" or meta_key="_apptivo_item_id" or meta_key="_apptivo_item_uom_id" or meta_key="_apptivo_item_manufactured_id" or meta_key="_apptivo_category_id" ';
    $result = $wpdb->query($delete_postmeta);

        /* Delete Categories */
	    $get_all_terms = get_terms( 'item_cat', 'orderby=count&hide_empty=0' );
		foreach($get_all_terms as $terms)
		{
		 wp_delete_term( $terms->term_id, 'item_cat' );
		 delete_post_meta($terms->term_id, '_apptivo_category_id');
		}
		
	   /* Delete Tags */
	 	$get_all_terms = get_terms( 'item_tag', 'orderby=count&hide_empty=0' );
		foreach($get_all_terms as $terms)
		{
		 wp_delete_term( $terms->term_id, 'item_tag' );
		}
		
	$apiKey = trim($_POST['apikey']);
    update_option('apptivo_ecommerce_apikey',$apiKey);
    echo 1000;
    die();
    
	
}
/* Theme Templates*/
add_action('wp_ajax_apptivo_ecommerce_upload_theme_template','apptivo_ecommerce_upload_theme_template');
add_action('wp_ajax_nopriv_apptivo_ecommerce_upload_theme_template','apptivo_ecommerce_upload_theme_template');
function apptivo_ecommerce_upload_theme_template()
{
	//Coped Products files
	$destination = TEMPLATEPATH.'/apptivo-ecommerce';
	$eCommerce_css_file = APPTIVO_ECOMMERCE_PLUGIN_BASEPATH.'/assets/css/apptivo_ecommerce.css';	
	$eCommerce_archive_file = APPTIVO_ECOMMERCE_PLUGIN_BASEPATH.'/templates/archive-product.php';
	$eCommerce_taxonomy_file = APPTIVO_ECOMMERCE_PLUGIN_BASEPATH.'/templates/product_taxonomy.php';
	$eCommerce_taxonomy_tag_file = APPTIVO_ECOMMERCE_PLUGIN_BASEPATH.'/templates/taxonomy-item_tag.php';
	$eCommerce_single_file = APPTIVO_ECOMMERCE_PLUGIN_BASEPATH.'/templates/single-product.php';	
	$eCommerce_products_file = APPTIVO_ECOMMERCE_PLUGIN_BASEPATH.'/templates/loop-products.php';
	
	if(is_dir(TEMPLATEPATH.'/apptivo-ecommerce')){
		$folder_name = TEMPLATEPATH.'/apptivo-ecommerce-'.strtotime("now");
		copy_directory($destination, $folder_name);
	}else{
	   @mkdir( $destination );
	}
	@mkdir( TEMPLATEPATH.'/apptivo-ecommerce/css' );
	copy( $eCommerce_css_file, $destination.'/css/apptivo_ecommerce.css');
	copy( $eCommerce_archive_file, $destination.'/archive-product.php');
	copy( $eCommerce_taxonomy_file, $destination.'/product_taxonomy.php');
	copy( $eCommerce_taxonomy_tag_file, $destination.'/taxonomy-item_tag.php');
	copy( $eCommerce_single_file, $destination.'/single-product.php');
	copy( $eCommerce_products_file, $destination.'/loop-products.php');
		
	
	//Copied Images
	$source_template_images = dirname(__FILE__).'/template_images';
	$target_template_images = TEMPLATEPATH.'/apptivo-ecommerce/images';
	copy_directory($source_template_images, $target_template_images);
	
	echo 1000;
	die();
}

function copy_directory( $source, $destination ) {
	$i =0;
	if ( is_dir( $source ) ) {
		@mkdir( $destination );
		$directory = dir( $source );
		while ( FALSE !== ( $readdirectory = $directory->read() ) ) {
			if ( $readdirectory == '.' || $readdirectory == '..' ) {
				continue;
			}
			$PathDir = $source . '/' . $readdirectory; 
			if ( is_dir( $PathDir ) ) {
				copy_directory( $PathDir, $destination . '/' . $readdirectory );
				continue;
			}
			copy( $PathDir, $destination . '/' . $readdirectory );
			
		}
 
		$directory->close();
	}else {
		copy( $source, $destination );
	}
	return '1000';
}
/**
 * eCommerce Settings Page
 */
global $apptivo_ecommerce_settings;

$business_api_key = get_option("apptivo_apikey");
$apptivo_ecommerce_settings['apptivo_ecommerce'] = array(
	array( 'name' => __( 'Genral', 'apptivo_ecommerce' ), 'type' => 'title', 'desc' => '', 'id' => 'general_options' ),
	array(  
		'name' => __( 'API Key', 'apptivo_ecommerce' ),
		'desc' 		=> '<a target="_blank" title="Get an Apptivo API Key" href="'.apptivo_ecommerce_api("apikey").'">Get an Apptivo API Key</a>',
		'id' 		=> 'apptivo_ecommerce_apikey',
		'std' 		=> get_option("apptivo_apikey"),
	    'css' 		=> 'width:500px;',
		'type' 		=> 'text'
	),
	array(  
		'name' => __( 'Access Key', 'apptivo_ecommerce' ),
		'desc' 		=> '',
		'id' 		=> 'apptivo_ecommerce_accesskey',
		'std' 		=> (!empty($business_api_key))?get_option("apptivo_accesskey"):'',
		'css' 		=> 'width:500px;',
		'type' 		=> 'text'
	),
	array(  
		'name' => __( 'Force SSL', 'apptivo_ecommerce' ),
		'desc' 		=> __( 'Force SSL on the checkout for added security (SSL certificate required).', 'apptivo_ecommerce' ),
		'id' 		=> 'apptivo_ecommerce_force_ssl_checkout',
		'std' 		=> 'no',
		'type' 		=> 'checkbox'
	),
	array(  
		'name' => __( 'Error Message', 'apptivo_ecommerce' ),
		'desc' 		=> __( 'Enable single error message for form fields', 'apptivo_ecommerce' ),
		'id' 		=> 'apptivo_ecommerce_single_error_message',
		'std' 		=> 'yes',
		'type' 		=> 'checkbox'
	),
	array(  
		'name' => __( 'Demo Store', 'apptivo_ecommerce' ),
		'desc' 		=> __( 'Enable the "Demo Store" notice on your site', 'apptivo_ecommerce' ),
		'id' 		=> 'apptivo_ecommerce_demo_store',
		'std' 		=> 'no',
		'type' 		=> 'checkbox'
	),
	
	array( 'type' => 'sectionend', 'id' => 'general_options'),
	
	array( 'name' => __( 'reCaptcha in Register Page', 'apptivo_ecommerce' ), 'type' => 'title', 'desc' => '', 'id' => 'recaptcha_options' ),
	
	array(  
		'name' =>   'Enable reCaptcha' ,
		'desc' 		=> '<a target="_blank" title="Create a reCAPTCHA key" href="'.apptivo_ecommerce_api("recaptcha").'">Create a reCAPTCHA key</a>',
		'id' 		=> 'apptivo_ecommerce_recaptcha_mode',
		'std' 		=> 'no',
		'type' 		=> 'select',
		'class'		=> 'chosen_select',
		'css' 		=> 'width:185px;',
		'desc_tip'	=>  true,
		'options' => array(
	        'yes'  => 'Enabled',
			'no'  => 'Disabled'
			)
	),
	
	array(  
		'name' => __( 'reCaptcha - Public Key', 'apptivo_ecommerce' ),
		'desc' 		=> '',
		'id' 		=> 'apptivo_ecommerce_recaptcha_publickey',
		'std' 		=> '',
		'css' 		=> 'width:500px;',
		'type' 		=> 'text'
	),
	
	array(  
		'name' => __( 'reCaptcha - Private Key', 'apptivo_ecommerce' ),
		'desc' 		=> '',
		'id' 		=> 'apptivo_ecommerce_recaptcha_privatekey',
		'std' 		=> '',
		'css' 		=> 'width:500px;',
		'type' 		=> 'text'
	),
	
	array(  
		'name' =>   'reCaptcha - Theme' ,
		'desc' 		=> '',
		'id' 		=> 'apptivo_ecommerce_recaptcha_theme',
		'std' 		=> 'red',
		'type' 		=> 'select',
		'class'		=> 'chosen_select',
		'css' 		=> 'width:185px;',
		'desc_tip'	=>  true,
		'options' => array(
	        'red'  => 'Red',
			'white'  => 'White',
	        'blackglass'  => 'Black Glass',
	        'clean'  => 'Clean'
			)
	),
	
	array(  
		'name' =>   'reCaptcha - Language' ,
		'desc' 		=> '',
		'id' 		=> 'apptivo_ecommerce_recaptcha_language',
		'std' 		=> 'en',
		'type' 		=> 'select',
		'class'		=> 'chosen_select',
		'css' 		=> 'width:185px;',
		'desc_tip'	=>  true,
		'options' => array(
	        'en'  => 'English',
			'nl'  => 'Dutch',
	        'fr'  => 'French',
	        'de'  => 'German',
			'pt'  => 'Portuguese',
	        'ru'  => 'Russian',
			'es'  => 'Spanish',
			'tr'  => 'Turkish',
			)
	),
	
	array( 'type' => 'sectionend', 'id' => 'general_options')
	
); // End general settings

$apptivo_ecommerce_settings['apptivo_ecommerce_shopping_cart'] = array(
	array( 'name' => __( '', 'apptivo_ecommerce' ), 'type' => 'title', 'desc' => '', 'id' => 'shopping_cart' ),
	array(  
		'name' => __( 'Enable Apply Coupon', 'apptivo_ecommerce' ),
		'desc' 		=> __( 'Enable apply coupon on your site', 'apptivo_ecommerce' ),
		'id' 		=> 'apptivo_ecommerce_apply_coupan',
		'std' 		=> 'no',
		'type' 		=> 'checkbox'
	),
	array(  
		'name' => __( 'Enable Gift Note', 'apptivo_ecommerce' ),
		'desc' 		=> __( 'Enable gift note on your site', 'apptivo_ecommerce' ),
		'id' 		=> 'apptivo_ecommerce_enable_gift',
		'std' 		=> 'no',
		'type' 		=> 'checkbox'
	),
	array(  
		'name' => __( 'Enable 2 steps', 'apptivo_ecommerce' ),
		'desc' 		=> __( 'Enable 2 steps for authorize.net checkout', 'apptivo_ecommerce' ),
		'id' 		=> 'apptivo_ecommerce_enable_a_net_confirm',
		'std' 		=> 'yes',
		'type' 		=> 'checkbox'
	),
	array(  
		'name' => __( 'Zip Code to Calculate Tax and Shipping(default)', 'apptivo_ecommerce' ),
		'desc' 		=> __( 'Enable the zip code to calculate tax and shipping  ', 'apptivo_ecommerce' ),
		'id' 		=> 'apptivo_ecommerce_auto_zipcode_calculation',
		'std' 		=> 'no',
		'type' 		=> 'checkbox'
	),
	array(  
		'name' => __( 'Zip Code', 'apptivo_ecommerce' ),
		'desc' 		=> __( 'Enter zipcode for calculate tax and shipping.  ', 'apptivo_ecommerce' ),
		'id' 		=> 'apptivo_ecommerce_auto_zipcode',
		'std' 		=> '',
		'type' 		=> 'text'
	),
	array( 'type' => 'sectionend', 'id' => 'general_options'),
); // End general settings

                     
$products_page_id = get_option('apptivo_ecommerce_products_page_id');
$base_slug = ($products_page_id > 0 && get_page( $products_page_id )) ? get_page_uri( $products_page_id ) : 'products';	
	
$apptivo_ecommerce_settings['apptivo_ecommerce_products'] = array(
array( 'type' => 'sectionend', 'id' => 'pricing_options' ),
	array( 'name' => __( 'Products Page', 'apptivo_ecommerce' ), 'type' => 'title', 'desc' => '', 'id' => 'page_options' ),
	
	array(  
		'name' => __( 'Redirects To Cart Page', 'apptivo_ecommerce' ),
		'desc' 		=> __( 'Redirect to cart after adding a product to the cart (on products page)', 'apptivo_ecommerce' ),
		'id' 		=> 'apptivo_ecommerce_redirects_to_cart',
		'std' 		=> 'yes',
		'type' 		=> 'checkbox'
	),
	array(  
		'name' => 'Related Products',
		'desc' 		=> 'Show related products in your website',
		'id' 		=> 'apptivo_ecommerce_enable_related_products',
		'std' 		=> 'no',
		'type' 		=> 'checkbox'
	),
   	array(  
		'name' => 'Regular Price',
		'desc' 		=> 'Show regular price in your website',
		'id' 		=> 'apptivo_ecommerce_enable_regular_price',
		'std' 		=> 'no',
		'type' 		=> 'checkbox'
	),
	array(  
		'name' => 'Item Code',
		'desc' 		=> 'Show item code in your website',
		'id' 		=> 'apptivo_ecommerce_enable_item_code',
		'std' 		=> 'no',
		'type' 		=> 'checkbox'
	),
	array(  
		'name' => 'Products Sort by',
		'desc' 		=> 'Show products sort by options in your website',
		'id' 		=> 'apptivo_ecommerce_enable_sortby',
		'std' 		=> 'no',
		'type' 		=> 'checkbox'
	),
	array(  
		'name' => __( 'Products Base Page', 'apptivo_ecommerce' ),
		'desc' 		=> sprintf( __( 'This sets the base page of your products.', 'apptivo_ecommerce' ), '<a target="_blank" href="options-permalink.php">', '</a>' ),
		'id' 		=> 'apptivo_ecommerce_products_page_id',
		'css' 		=> 'min-width:175px;',
		'type' 		=> 'single_select_page',
		'std' 		=> ''
	),
	array(  
		'name' =>   'Products Sorting Type' ,
		'desc' 		=> '',
		'id' 		=> 'apptivo_ecommerce_products_sorting_type',
		'std' 		=> '1',
		'type' 		=> 'select',
		'class'		=> 'chosen_select',
		'css' 		=> 'width:185px;',
		'desc_tip'	=>  true,
		'options' => array(
	        '1'  => 'Price: Low to High',
			'2'  => 'Price: High to Low',
	        '6'  => 'Name: A to Z',
			'7'  => 'Name: Z to A'
			)
	),
	array(  
		'name' => __( 'PrettyPhoto Lightbox', 'apptivo_ecommerce' ),
		'desc' 		=> __( 'Enable jQuery PrettyPhoto lightbox in product description page', 'apptivo_ecommerce' ),
		'id' 		=> 'apptivo_ecommerce_enable_lightbox',
		'std' 		=> 'no',
		'type' 		=> 'checkbox'
	),
	array(  
		'name' => __( 'Products Per Page', 'apptivo_ecommerce' ),
		'desc' 		=> 'Number of products per page',
		'id' 		=> 'apptivo_ecommerce_products_per_page',
		'std' 		=> 8,
		'css' 		=> 'width:50px;',
		'type' 		=> 'text'
	),
	array(  
		'name' =>   'Products Pagination Type' ,
		'desc' 		=> '',
		'id' 		=> 'apptivo_ecommerce_products_pagination_type',
		'std' 		=> '1',
		'type' 		=> 'select',
		'class'		=> 'chosen_select',
		'css' 		=> 'width:185px;',
		'desc_tip'	=>  true,
		'options' => array(  
			'1'  => 'Bottom',
			'2'  => 'Top',
	        '3'  => 'Bottom & Top'       			
		)
	),
	array(  
		'name' => __( 'Base Page Title', 'apptivo_ecommerce' ),
		'desc' 		=> __( 'This title to show on the shop base page. Leave blank to use the page title.', 'apptivo_ecommerce' ),
		'id' 		=> 'apptivo_ecommerce_products_page_title',
		'type' 		=> 'text',
		'std' 		=> 'All Products' // Default value for the page title - changed in settings
	),
	array( 'type' => 'sectionend', 'id' => 'page_options'),
	array(	'name' => __( 'Products Image Settings', 'apptivo_ecommerce' ), 'type' => 'title','desc' => __('These settings affect the actual dimensions of images in your catalog - the display on the front-end will still be affected by CSS styles.', 'apptivo_ecommerce'), 'id' => 'image_options' ),
	array(  
		'name' => __( 'Catalog Images', 'apptivo_ecommerce' ),
		'desc' 		=> __('Product listings image', 'apptivo_ecommerce'),
		'id' 		=> 'apptivo_ecommerce_catalog_image',
		'css' 		=> '',
		'type' 		=> 'image_width',
		'std' 		=> '150'
	),
	array(  
		'name' => __( 'Single Product Image', 'apptivo_ecommerce' ),
		'desc' 		=> __('Product main image', 'apptivo_ecommerce'),
		'id' 		=> 'apptivo_ecommerce_single_image',
		'css' 		=> '',
		'type' 		=> 'image_width',
		'std' 		=> '300'
	),
	array(  
		'name' => __( 'Product Thumbnails', 'apptivo_ecommerce' ),
		'desc' 		=> __('Product gallery image', 'apptivo_ecommerce'),
		'id' 		=> 'apptivo_ecommerce_thumbnail_image',
		'css' 		=> '',
		'type' 		=> 'image_width',
		'std' 		=> '90'
	),
	array( 'type' => 'sectionend', 'id' => 'image_options' ),
); // End pages settings

$apptivo_ecommerce_settings['apptivo_ecommerce_pages'] = array(
    array( 'type' => 'sectionend', 'id' => 'pricing_options' ),
    array( 'name' => __( 'eCommerce Products Template Settings', 'apptivo_ecommerce' ), 'type' => 'title', 'desc' => '', 'id' => 'page_options' ),
    array(  
		'name' => __( 'Copy eCommerce Template To Your Current Theme', 'apptivo_ecommerce' ),
		'desc' 		=> 'Most suggested, if you dont have customized ecommerce theme or templates.',
		'id' 		=> 'apptivo_ecommerce_template_upload',
		'css' 		=> 'min-width:50px;',
		'type' 		=> 'template_upload',
		'std' 		=> ''
	),
	array(),
	array( 'type' => 'sectionend', 'id' => 'pricing_options' ),
	array( 'name' => __( 'eCommerce Page Settings', 'apptivo_ecommerce' ), 'type' => 'title', 'desc' => '', 'id' => 'page_options' ),
	array(  
		'name' => __( 'Terms page ID', 'apptivo_ecommerce' ),
		'tip' 		=> '',
		'id' 		=> 'apptivo_ecommerce_terms_page_id',
		'css' 		=> 'min-width:50px;',
		'std' 		=> '',
		'type' 		=> 'single_select_page',
		'args'		=> 'show_option_none=' . __('None', 'apptivo_ecommerce'),
	),
	array(  
		'name' => __( 'Cart Page', 'apptivo_ecommerce' ),
		'desc' 		=> __( '<b> Shortcode : </b> [apptivo_ecommerce_cart]', 'apptivo_ecommerce' ),
		'id' 		=> 'apptivo_ecommerce_cart_page_id',
		'css' 		=> 'min-width:50px;',
		'type' 		=> 'single_select_page',
		'std' 		=> ''
	),
	array(  
		'name' => __( 'Checkout Page', 'apptivo_ecommerce' ),
		'desc' 		=> __( '<b> Shortcode : </b> [apptivo_ecommerce_checkout]', 'apptivo_ecommerce' ),
		'id' 		=> 'apptivo_ecommerce_checkout_page_id',
		'css' 		=> 'min-width:50px;',
		'type' 		=> 'single_select_page',
		'std' 		=> ''
	),
	array(  
		'name' => __( 'Secure Checkout Page', 'apptivo_ecommerce' ),
		'desc' 		=> __( '<b> Shortcode : </b> [apptivo_ecommerce_secure_checkout]', 'apptivo_ecommerce' ),
		'id' 		=> 'apptivo_ecommerce_secure_checkout_page_id',
		'css' 		=> 'min-width:50px;',
		'type' 		=> 'single_select_page',
		'std' 		=> ''
	),
	array(  
		'name' => __('Thanks Page', 'apptivo_ecommerce'),
		'desc' 		=> __( '<b> Shortcode : </b> [apptivo_ecommerce_thankyou]', 'apptivo_ecommerce' ),
		'id' 		=> 'apptivo_ecommerce_thanks_page_id',
		'css' 		=> 'min-width:50px;',
		'type' 		=> 'single_select_page',
		'std' 		=> ''
	),
	array(  
		'name' => __( 'My Account Page', 'apptivo_ecommerce' ),
		'desc' 		=> __( '<b> Shortcode : </b> [apptivo_ecommerce_my_account]', 'apptivo_ecommerce' ),
		'id' 		=> 'apptivo_ecommerce_myaccount_page_id',
		'css' 		=> 'min-width:50px;',
		'type' 		=> 'single_select_page',
		'std' 		=> ''
	),
	array(  
		'name' => __( 'Register Page', 'apptivo_ecommerce' ),
		'desc' 		=> __( '<b> Shortcode : </b> [apptivo_ecommerce_register]', 'apptivo_ecommerce' ),
		'id' 		=> 'apptivo_ecommerce_register_page_id',
		'css' 		=> 'min-width:50px;',
		'type' 		=> 'single_select_page',
		'std' 		=> ''
	),	
	array(  
		'name' => __( 'Logout Page', 'apptivo_ecommerce' ),
		'desc' 		=> __( '<b> Shortcode : </b> [apptivo_ecommerce_logout]', 'apptivo_ecommerce' ),
		'id' 		=> 'apptivo_ecommerce_logout_page_id',
		'css' 		=> 'min-width:50px;',
		'type' 		=> 'single_select_page',
		'std' 		=> ''
	),	
	
	array(  
		'name' => __( 'Login Page', 'apptivo_ecommerce' ),
		'desc' 		=> __( '<b> Shortcode : </b> [apptivo_ecommerce_login]', 'apptivo_ecommerce' ),
		'id' 		=> 'apptivo_ecommerce_login_page_id',
		'css' 		=> 'min-width:50px;',
		'type' 		=> 'single_select_page',
		'std' 		=> ''
	),	
	array( 'type' => 'sectionend', 'id' => 'page_options'),
); // End pages settings

$apptivo_ecommerce_settings['apptivo_ecommerce_print_receipt'] = array(
    array( 'type' => 'sectionend', 'id' => 'pricing_receipt' ),
	array( 'name' => __( '', 'apptivo_ecommerce' ), 'type' => 'title', 'desc' => '', 'id' => 'page_options' ),
	array(  
		'name' => __('Print Receipt Page', 'apptivo_ecommerce'),
		'id' 		=> 'apptivo_ecommerce_print_receipt_page_id',
		'css' 		=> 'min-width:50px;',
		'type' 		=> 'single_select_page',
		'std' 		=> ''
	),
	array(  
		'name' => __( 'Print Receipt Address', 'apptivo_ecommerce' ),
		'desc' 		=> '',
		'id' 		=> 'apptivo_ecommerce_print_receipt_address',
		'std' 		=> get_option('home').'
Insert your address		',
	     'css' 		=> 'width:500px;',
		'type' 		=> 'textarea',
	    'editor' 		=> 'yes'
	),
	array(  
		'name' => __( 'Print Receipt Logo', 'apptivo_ecommerce' ),
		'desc' 		=> __( 'Print receipt logo.  ', 'apptivo_ecommerce' ),
		'id' 		=> 'apptivo_ecommerce_print_receipt_logo',
		'std' 		=> '',
	    'css' 		=> 'width:400px;',
		'type' 		=> 'text',
	    'upload'    => 'yes'
	),			
	array( 'type' => 'sectionend', 'id' => 'print_receipt_end'),
); 

//Order Number Settings Start.
$apptivo_ecommerce_settings['apptivo_ecommerce_order_number'] = array(
	array( 'name' => __( 'Order Number Settings', 'apptivo_ecommerce' ), 'type' => 'title', 'desc' => '', 'id' => 'order_number' ),	
	array(  
		'name' => __( 'Prefix', 'apptivo_ecommerce' ),
		'desc' 		=> __( 'Order Number Prefix  ', 'apptivo_ecommerce' ),
		'id' 		=> 'apptivo_ecommerce_ordernumber_prefix',
		'std' 		=> '',
	    'tab'       => 'order_number',
		'type' 		=> 'text'
	),
	array(  
		'name' => __( 'Starts With', 'apptivo_ecommerce' ),
		'desc' 		=> __( 'Order Number Starts with.  ', 'apptivo_ecommerce' ),
		'id' 		=> 'apptivo_ecommerce_ordernumber_startswith',
		'std' 		=> '',
	     'tab'       => 'order_number',
		'type' 		=> 'text'
	),
	array( 'type' => 'sectionend', 'id' => 'order_number')	
);
//Order Number settings End.

if(!function_exists('apptivo_ecommerce_syncs')){
	function apptivo_ecommerce_syncs(){
		global $wpdb;
		if($_POST['sync'] != 'Sync With Apptivo')
		{
		echo '<div class="wrap">';
		?>
		<div style="margin-top:8px;background: url('<?php echo settings_image('sync'); ?>') " id="icon-apptivo_ecommerce" class="icon32 icon32-apptivo_ecommerce-settings">
			<br></div>
		<?php 
        echo '<h2>Apptivo Sync</h2>		      
              <form id="apptivo_ecommrece_sync" name="apptivo_ecommrece_sync" method="post">
              <p>
		      You can add new products directly in Wordpress, or by using the Items App in Apptivo.  To sync up Wordpress & Apptivo, press the button below
		      </p>
		      <p class="submit"><input type="submit" value="Sync With Apptivo" class="button-primary" name="sync"></p></form>
		      </div>';
		}

		if(isset($_POST) && $_POST['sync'] == 'Sync With Apptivo')
		{
       /**************************************** Create Category start **********************/ /**Upto Level 5**/
		echo '<div class="wrap"><h2>Sync Apptivo Item Categories and Items</h2><br />';	
		$product_category = all_product_category();	 
		
		if($product_category->statusCode != 1005 ) {
			
	    $table_postmeta=$wpdb->prefix.'postmeta';
        $delete_postmeta = 'delete from '.$table_postmeta.' where meta_key="_apptivo_category_id" ';
        $result = $wpdb->query($delete_postmeta);
    
		echo '<br />Sync starting...<br />';
		$categories = app_convertObjectToArray($product_category->subCategories);
	 if(!empty($categories[0])) :
		$get_all_terms = get_terms( 'item_cat', 'orderby=count&hide_empty=0' );
	       foreach($get_all_terms as $terms)
			{
				wp_delete_term( $terms->term_id, 'item_cat' );
				delete_post_meta($terms->term_id, '_apptivo_category_id');
				echo 'Removing the old category Term ID ('.$terms->term_id.')...<br />';
	        }
        //Create New Category.
		for($idx = 0; $idx < count ( $categories ); $idx ++) {
			   //echo '========Fist Level';
			    $category = $categories [$idx];
			    $productscategory [$idx] ['parent'] = 0;
				$productscategory [$idx] ['categoryName'] = $category->categoryName;
				$productscategory [$idx] ['categoryId'] = $category->categoryId;
				$productscategory [$idx] ['description'] = $category->description;	
				$product_catid = getIdFromMeta( '_apptivo_category_id', $category->categoryId );
				if($product_catid == '')
				{
				    $arg = array('description' => $category->description, 'parent' => 0);	
					$product_catid = apptivo_wp_insert_term($category->categoryName, "item_cat", $arg,$category->categoryId);
					$term_id = $product_catid['term_id'];
					if($term_id != '')
					 {
					  update_post_meta( $term_id, '_apptivo_category_id', $category->categoryId );
					  echo 'Synced the New category "'.$category->categoryName.'" with Term ID "'.$term_id.'"...<br />';
					 }else {
					  $term_id = 0;		
					 }
				}else {
					 $arg = array('name'=>$category->categoryName,'slug'=>'','description' => $category->description, 'parent' => 0);					  
					 $product_update_catid = apptivo_wp_update_term($product_catid, "item_cat", $arg);
					 $term_id = $product_update_catid['term_id'];
					 echo 'Synced the Old category "'.$category->categoryName.'" with Term ID "'.$term_id.'"...<br />';					
				}
				$apptivo_item_category[] = $term_id;				
				$productscategory [$idx] ['term_id'] = $term_id ;								
				$secondlevelcategory = app_convertObjectToArray ( $category->subCategories );
				//echo '========2nd Level';	
				for($subidx = 0; $subidx < count ( $secondlevelcategory ); $subidx ++) {
					$itemIds = array ();
					if (! empty ( $secondlevelcategory [$subidx] )) {
						$productscategory [$idx] ['subMenu'] [$subidx] ['parent'] = $productscategory [$idx] ['term_id'];
						$productscategory [$idx] ['subMenu'] [$subidx] ['categoryName'] = $secondlevelcategory [$subidx]->categoryName;
						$productscategory [$idx] ['subMenu'] [$subidx] ['categoryId'] = $secondlevelcategory [$subidx]->categoryId;
						$productscategory [$idx] ['subMenu'] [$subidx] ['description'] = $secondlevelcategory [$subidx]->description;
						
						$product_catid = getIdFromMeta( '_apptivo_category_id', $secondlevelcategory [$subidx]->categoryId );
						if($product_catid == '')
						{   							
						    $arg = array('description' => $secondlevelcategory [$subidx]->description, 'parent' => $productscategory [$idx] ['subMenu'] [$subidx] ['parent']);
						    $product_catid = apptivo_wp_insert_term($secondlevelcategory [$subidx]->categoryName, "item_cat", $arg,$secondlevelcategory [$subidx]->categoryId);
							$term_id = $product_catid['term_id'];
							if($term_id != '')
							 {							  		
					          update_post_meta( $term_id, '_apptivo_category_id', $secondlevelcategory [$subidx]->categoryId );
					          echo 'Synced the New category "'.$secondlevelcategory [$subidx]->categoryName.'" with Term ID "'.$term_id.'"...<br />';
							 }
						}else {
						 $arg = array('name'=>$secondlevelcategory [$subidx]->categoryName,'slug'=>'','description' => $secondlevelcategory [$subidx]->description, 'parent' => $productscategory [$idx] ['subMenu'] [$subidx] ['parent']);
						 $product_update_catid = apptivo_wp_update_term($product_catid, "item_cat", $arg);
						 $term_id = $product_update_catid['term_id'];
						 echo 'Synced the Old category "'.$secondlevelcategory [$subidx]->categoryName.'" with Term ID "'.$term_id.'"...<br />';						
				        }
				       $apptivo_item_category[] = $term_id; 
			           $productscategory [$idx] ['subMenu'] [$subidx] ['term_id'] =$term_id;
						
					}
					$thirdlevelcategory = app_convertObjectToArray ( $secondlevelcategory [$subidx]->subCategories );
					//echo '========3rd Level';
					if (! empty ( $thirdlevelcategory )) {
						for($i = 0; $i < count ( $thirdlevelcategory ); $i ++) {
							if (! empty ( $thirdlevelcategory [$i] )) {
								$productscategory [$idx] ['subMenu'] [$subidx] ['subMenu'] [$i] ['parent'] = $productscategory [$idx] ['subMenu'] [$subidx] ['term_id'];
								$productscategory [$idx] ['subMenu'] [$subidx] ['subMenu'] [$i] ['categoryName'] = $thirdlevelcategory [$i]->categoryName;
								$productscategory [$idx] ['subMenu'] [$subidx] ['subMenu'] [$i] ['categoryId'] = $thirdlevelcategory [$i]->categoryId;
								$productscategory [$idx] ['subMenu'] [$subidx] ['subMenu'] [$i] ['description'] = $thirdlevelcategory [$i]->description;
						
								$product_catid = getIdFromMeta( '_apptivo_category_id', $thirdlevelcategory [$i]->categoryId);
								if($product_catid == '')
								{  
								    $arg = array('description' => $thirdlevelcategory [$i]->description, 'parent' => $productscategory [$idx] ['subMenu'] [$subidx] ['subMenu'] [$i] ['parent']);
								    $product_catid = apptivo_wp_insert_term($thirdlevelcategory [$i]->categoryName, "item_cat", $arg,$thirdlevelcategory [$i]->categoryId);
									$term_id = $product_catid['term_id'];
									if($term_id != '')
									 {									  
							          update_post_meta( $term_id, '_apptivo_category_id', $thirdlevelcategory [$i]->categoryId);
							          echo 'Synced the New category "'.$thirdlevelcategory [$i]->categoryName.'" with Term ID "'.$term_id.'"...<br />';
									 }
								}else {
									 $arg = array('name'=>$thirdlevelcategory [$i]->categoryName,'slug'=>'','description' => $thirdlevelcategory [$i]->description, 'parent' => $productscategory [$idx] ['subMenu'] [$subidx] ['subMenu'] [$i] ['parent']);			  
							 		 $product_update_catid = apptivo_wp_update_term($product_catid, "item_cat", $arg);
							 		 $term_id = $product_update_catid['term_id'];
							 		 echo 'Synced the Old category "'.$thirdlevelcategory [$i]->categoryName.'" with Term ID "'.$term_id.'"...<br />';							
						        }
						        $apptivo_item_category[] = $term_id;
								$productscategory [$idx] ['subMenu'] [$subidx] ['subMenu'] [$i] ['term_id'] =$term_id;
							}
							
						$fourthlevelcategory = app_convertObjectToArray ( $thirdlevelcategory [$i]->subCategories );
					//echo '========4th Level';
					if (! empty ( $fourthlevelcategory )) {
						for($j = 0; $j < count ( $fourthlevelcategory ); $j ++) {
							if (! empty ( $fourthlevelcategory [$j] )) {
								$productscategory [$idx] ['subMenu'] [$subidx] ['subMenu'] [$i] ['subMenu'] [$j] ['parent']= $productscategory [$idx] ['subMenu'] [$subidx] ['subMenu'] [$i] ['term_id'];
								$productscategory [$idx] ['subMenu'] [$subidx] ['subMenu'] [$i] ['subMenu'] [$j] ['categoryName']= $fourthlevelcategory [$j]->categoryName;
								$productscategory [$idx] ['subMenu'] [$subidx] ['subMenu'] [$i] ['subMenu'] [$j] ['categoryId']= $fourthlevelcategory [$j]->categoryId;
								$productscategory [$idx] ['subMenu'] [$subidx] ['subMenu'] [$i] ['subMenu'] [$j] ['description']= $fourthlevelcategory [$j]->description;
								
							    $product_catid = getIdFromMeta( '_apptivo_category_id', $fourthlevelcategory [$j]->categoryId);
								if($product_catid == '')
								{
								    $arg = array('description' => $fourthlevelcategory [$j]->description, 'parent' => $productscategory [$idx] ['subMenu'] [$subidx] ['subMenu'] [$i] ['subMenu'] [$j] ['parent']);
								    $product_catid = apptivo_wp_insert_term($fourthlevelcategory [$j]->categoryName, "item_cat", $arg,$fourthlevelcategory [$j]->categoryId);
									$term_id = $product_catid['term_id'];
									if($term_id != '')
									 {									 
							          update_post_meta( $term_id, '_apptivo_category_id', $fourthlevelcategory [$j]->categoryId);
							          echo 'Synced the New category "'.$fourthlevelcategory [$j]->categoryName.'" with Term ID "'.$term_id.'"...<br />';
									 }
								}else {
									 $arg = array('name'=>$fourthlevelcategory [$j]->categoryName,'slug'=>'','description' => $fourthlevelcategory [$j]->description, 'parent' => $productscategory [$idx] ['subMenu'] [$subidx] ['subMenu'] [$i] ['subMenu'] [$j] ['parent']);			  
							 		 $product_update_catid = apptivo_wp_update_term($product_catid, "item_cat", $arg);
							 		 $term_id = $product_update_catid['term_id'];	
							 		 echo 'Synced the Old category "'.$fourthlevelcategory [$j]->categoryName.'" with Term ID "'.$term_id.'"...<br />';					
						        }
						        $apptivo_item_category[] = $term_id;
								$productscategory [$idx] ['subMenu'] [$subidx] ['subMenu'] [$i] ['subMenu'] [$j] ['term_id'] =$term_id;								
							}
							
						$fifthlevelcategory = app_convertObjectToArray ( $fourthlevelcategory [$j]->subCategories );
						//echo '========5th Level';
						if (! empty ( $fifthlevelcategory )) {
							for($k = 0; $k < count ( $fifthlevelcategory ); $k ++) {
								if (! empty ( $fifthlevelcategory [$k] )) {
									$productscategory [$idx] ['subMenu'] [$subidx] ['subMenu'] [$i] ['subMenu'] [$j] ['subMenu'] [$k] ['parent']= $productscategory [$idx] ['subMenu'] [$subidx] ['subMenu'] [$i] ['subMenu'] [$j] ['term_id'];
									$productscategory [$idx] ['subMenu'] [$subidx] ['subMenu'] [$i] ['subMenu'] [$j] ['subMenu'] [$k] ['categoryName']= $fifthlevelcategory [$k]->categoryName;
									$productscategory [$idx] ['subMenu'] [$subidx] ['subMenu'] [$i] ['subMenu'] [$j] ['subMenu'] [$k] ['categoryId']= $fifthlevelcategory [$k]->categoryId;
									$productscategory [$idx] ['subMenu'] [$subidx] ['subMenu'] [$i] ['subMenu'] [$j] ['subMenu'] [$k] ['description']= $fifthlevelcategory [$k]->description;

									
								$product_catid = getIdFromMeta( '_apptivo_category_id', $fifthlevelcategory [$k]->categoryId);
								if($product_catid == '')
								{
								    $arg = array('description' => $fifthlevelcategory [$k]->description, 'parent' => $productscategory [$idx] ['subMenu'] [$subidx] ['subMenu'] [$i] ['subMenu'] [$j] ['subMenu'] [$k] ['parent']);
									$product_catid = apptivo_wp_insert_term($fifthlevelcategory [$k]->categoryName, "item_cat", $arg,$fifthlevelcategory [$k]->categoryId);
									$term_id = $product_catid['term_id'];
									if($term_id != '')
									 {
							          update_post_meta( $term_id, '_apptivo_category_id', $fifthlevelcategory [$k]->categoryId);
							          echo 'Synced the New category "'.$fifthlevelcategory [$k]->categoryName.'" with Term ID "'.$term_id.'"...<br />';
									 }
								}else{
									 $arg = array('name'=>$fifthlevelcategory [$k]->categoryName,'slug'=>'','description' => $fifthlevelcategory [$k]->description, 'parent' => $productscategory [$idx] ['subMenu'] [$subidx] ['subMenu'] [$i] ['subMenu'] [$j] ['subMenu'] [$k] ['parent']);			  
							 		 $product_update_catid = apptivo_wp_update_term($product_catid, "item_cat", $arg);
							 		 $term_id = $product_update_catid['term_id'];
							 		 echo 'Synced the Old category "'.$fifthlevelcategory [$k]->categoryName.'" with Term ID "'.$term_id.'"...<br />';		
								}
								
								$apptivo_item_category[] = $term_id;
								
								}
							}
						}
					
						}
					}
						}
					}
						
				}
		}
	  echo 'Fetched' .count($apptivo_item_category).' categories'.'<br />';	
	  echo 'Categories updated successfully. <br /><br /><br />';
	  endif; //Category Checking...
	  /**************************************** Create Category End **********************/	  
      /**********************************  Item creation Start ********************************************/	
		list($item_lists,$total_items_in_apptivo) = getAllItemsForSync();
		
			$new_items = 0;
			$old_items = 0;	
			//echo "Total No.of Items".$total_items_in_apptivo.'<br />';
			foreach($item_lists as $items_details) :  
			
			$product_postid = getIdFromMeta( '_apptivo_item_id', $items_details->itemId );
			 //For Newly Items From Apptivo.
				if($items_details->itemId != '')
				{	
					$product_catid   = '';
					$itemCategories  = app_convertObjectToArray($items_details->itemCategories);
					$item_categoryID = '';
					foreach($itemCategories as $item_Categories)
					{
						$categoryID = $item_Categories->categoryId;
						if( $categoryID != '')
						{						
							$product_catid = getIdFromMeta( '_apptivo_category_id', $categoryID );
							/*if($product_catid == '') // Category Creation in Wordpress.
							{
								$category_name = $item_Categories->categoryName;
								$category_description = $item_Categories->description;
								
								$arg = array('description' => $category_description, 'parent' => "");
								$product_catid = apptivo_wp_insert_term($category_name, "item_cat", $arg);
								$term_id = $product_catid['term_id'];
								if($term_id != '')
								{
		                        update_post_meta( $term_id, '_apptivo_category_id', $categoryID );
		                        $item_categoryID[] = $term_id;
								}
		                        
							}else{
	    						$item_categoryID[] = $product_catid;
	    					}*/
                            if($product_catid != '') {
	    					$item_categoryID[] = $product_catid; }
						}
					}
                 
				  if ( $product_postid == '' ){					
				   $new_post = array(
					    'post_title' => $items_details->itemName,
					    'post_content' => $items_details->itemDescription,
					    'post_excerpt' => $items_details->itemShortDescription,
					    'post_status' => 'publish',
					    'post_date' => date('Y-m-d H:i:s'),
					    'post_author' => $user_ID,
					    'post_type' => 'item',
					    'post_category' => array(),
				        'tax_input' => array( 'item_cat' => $item_categoryID ) 
					);
					remove_action('apptivo_ecommerce_process_item_meta', 'apptivo_ecommerce_process_item_meta', 1, 2);
					$post_id = wp_insert_post($new_post);
					echo 'Synced the New Item "'.$items_details->itemName.'" with Post ID "'.$post_id.'"...<br />';
					$new_items++;			
				  }
				  else 
					{									
					$update_post = array(
					    'ID' => $product_postid,
					    'post_title'    =>   $items_details->itemName,
					    'post_content'  => $items_details->itemDescription,
					    'post_excerpt'  => $items_details->itemShortDescription,
					    'post_status'   => 'publish',
					    'post_date'     => date('Y-m-d H:i:s'),
					    'post_author'   => $user_ID,
					    'post_type'     => 'item',
					    'post_category' => array(),
				        'tax_input' => array( 'item_cat' => $item_categoryID ) 
					);
					remove_action('apptivo_ecommerce_process_item_meta', 'apptivo_ecommerce_process_item_meta', 1, 2);
					$post_id = wp_update_post($update_post);					
					echo 'Synced the Old Item "'.$items_details->itemName.'" with Post ID "'.$post_id.'"...<br />';
					$old_items++;
				
				   }
				   
                  if($post_id){
                  	    update_post_meta($post_id, '_apptivo_item_code', $items_details->itemCode);
                   		update_post_meta($post_id, '_apptivo_item_id', $items_details->itemId);
						update_post_meta($post_id, '_apptivo_item_uom_id', $items_details->itemPrimaryUOMId);
						update_post_meta($post_id, '_apptivo_item_manufactured_id', $items_details->itemManufacturerId);
						update_post_meta( $post_id, '_apptivo_regular_price', $items_details->itemMSRP );
						update_post_meta( $post_id, '_apptivo_supplier', $items_details->itemManufacturerName );
						update_post_meta( $post_id, '_apptivo_sale_price', $items_details->itemEffectivePrice );
						update_post_meta( $post_id, '_apptivo_track_color', $items_details->trackColors );
						update_post_meta( $post_id, '_apptivo_track_size', $items_details->trackSizes );
						$apptivo_enabled = ($items_details->enabledForSales)?'yes':'no';
						update_post_meta( $post_id, '_apptivo_enabled', $apptivo_enabled );
						$apptivo_featured = ($items_details->featured)?'yes':'no';
						update_post_meta( $post_id, '_apptivo_featured', $apptivo_featured );		
                  }
					
				}
				
			endforeach;			
			
			if ( count($apptivo_item_category) == 0) 
			{
				echo '<br /> No categories found...<br /> ';
			}
			
			if($new_items == 0 )
			{
				echo '<br /> No items found...<br /> ';
			}
			else if($new_items != 0){
				echo '<br />Synced '.$new_items.' New Items';
				echo '<br /> Items Added Successfully..<br /><br />';
			}
			if($old_items != 0)
			{
				echo '<br />Synced '.$old_items.' Old Items';
				echo '<br /> Items Updated Successfully..<br /><br />';
			}
			update_option('apptivo_ecommerce_sync_status', 'yes'); //For Deafult category Update.
			echo '<br /> Sync Completed';
			echo '</div>';
		}
			else{
				if($product_category == 'E_100') {
				echo '<span style="color:#f00"><b>Please try again after few mins.</b></span>';	
				}else{
			     echo '<span style="color:#f00;"><b>'.$product_category->statusMessage.'</b></span>';
				}
			}
		}
			
	update_option('apptivo_ecommerce_errors', '');
	}
  		
}
if (!function_exists('settings_label')) { 
function settings_label($page){
	$settings =  array( 'apptivo_ecommerce' => 'eCommerce Settings - General',
	'apptivo_ecommerce_pages' => 'eCommerce Settings - Pages',
	'apptivo_ecommerce_shopping_cart' => 'eCommerce Settings - Shopping Cart',
	'apptivo_ecommerce_products' => 'eCommerce Settings - Products',
	'apptivo_ecommerce_print_receipt' => 'eCommerce Settings - Print Receipt',
	'apptivo_ecommerce_order_number' => 'eCommerce Settings - Order Number',
	'payment_gateways' => 'eCommerce Settings - Payment Gateways');
	return $settings[$page];
}
}

if (!function_exists('settings_image')) { 
function settings_image($page){
	global $apptivo_ecommerce;
	$settings =  array( 'apptivo_ecommerce' => $apptivo_ecommerce->plugin_url().'/assets/images/keys.png',
	'apptivo_ecommerce_pages' => $apptivo_ecommerce->plugin_url().'/assets/images/pages.jpeg',
	'apptivo_ecommerce_shopping_cart' => $apptivo_ecommerce->plugin_url().'/assets/images/cart.png',
	'apptivo_ecommerce_products' => $apptivo_ecommerce->plugin_url().'/assets/images/products.jpeg',
	'apptivo_ecommerce_print_receipt' => $apptivo_ecommerce->plugin_url().'/assets/images/print.jpeg',
	'sync' => $apptivo_ecommerce->plugin_url().'/assets/images/sync.jpeg',
	'apptivo_ecommerce_order_number' => $apptivo_ecommerce->plugin_url().'/assets/images/order.jpeg',
	'payment_gateways' => $apptivo_ecommerce->plugin_url().'/assets/images/payments.jpeg');
	return $settings[$page];
}
}


/**
 * Settings page
 * 
 * Handles the display of the main apptivo_ecommerce settings page in admin.
 */
if (!function_exists('apptivo_ecommerce_settings')) {
function apptivo_ecommerce_settings() {
    global $apptivo_ecommerce, $apptivo_ecommerce_settings;
    
    $current_tab = (isset($_GET['page'])) ? $_GET['page'] : 'apptivo_ecommerce';
   
    if( isset( $_POST ) && $_POST ) :
    if($current_tab == 'apptivo_ecommerce'  )
    {
    	add_action('wp_ajax_apptivo_ecommerce_check_apikey','apptivo_ecommerce_check_apikey');
    
    }
    switch ( $current_tab ) :
			case "apptivo_ecommerce" :
			case "apptivo_ecommerce_pages" :
			case "apptivo_ecommerce_shopping_cart" :
			case "apptivo_ecommerce_products" :
			case "apptivo_ecommerce_print_receipt" :			
				apptivo_ecommerce_update_options( $apptivo_ecommerce_settings[$current_tab] );
			case "apptivo_ecommerce_order_number" :
				apptivo_ecommerce_update_order_number();
			break;
	endswitch;
	
		do_action( 'apptivo_ecommerce_update_options_' . $current_tab );
		flush_rewrite_rules( false );
		wp_redirect( add_query_arg( 'saved', 'true', admin_url( 'admin.php?page=' . $current_tab ) ) );
    endif;
    
    if (isset($_GET['saved']) && $_GET['saved']) :
    	echo '<div id="message" class="updated fade"><p><strong>' . __( 'Your settings have been saved.', 'apptivo_ecommerce' ) . '</strong></p></div>';
        flush_rewrite_rules( false );
    endif;    
    
    ?>
	<div class="wrap apptivo_ecommerce">
		<form method="post" id="mainform" action="">
		 
			<div class="wrap">
			<div style="margin-top:8px;background: url('<?php echo settings_image($current_tab); ?>') " id="icon-apptivo_ecommerce" class="icon32 icon32-apptivo_ecommerce-settings">
			<br></div>
		
              <h2><?php echo settings_label($current_tab); ?></h2>
             </div>
			<?php wp_nonce_field( 'apptivo_ecommerce-settings', '_wpnonce', true, true ); ?>
			<?php
			//Prepopulate Order Number Configuration in apptivo.
			if( $current_tab == 'apptivo_ecommerce_order_number' || $current_tab == 'apptivo_ecommerce' )
			{
				$nextOrder = getNextOrderNumber();
				
				if($nextOrder->return->statusCode == 1000)
				{
					update_option('apptivo_ecommerce_ordernumber_prefix',$nextOrder->return->prefix);
					update_option('apptivo_ecommerce_ordernumber_startswith',$nextOrder->return->startsWith);
					
					if( trim($nextOrder->return->startsWith) == '' )
					{
						$configNumber = configureOrderNumberGeneration('ORD',100);
					}else{
						if( !$nextOrder->return->autoGenerate ) //Reset the Order Number
						{  
							$order_prfix = $nextOrder->return->prefix;
							$order_startswith = $nextOrder->return->startsWith;
							$configNumber = configureOrderNumberGeneration($order_prfix,$order_startswith);
						}
					}
				}else{
					if( $nextOrder != 'E_100') {
					update_option('apptivo_ecommerce_ordernumber_prefix','');
					update_option('apptivo_ecommerce_ordernumber_startswith','');
					}
				}
				
			}
				switch ($current_tab) :
					case "apptivo_ecommerce" :
			        case "apptivo_ecommerce_pages" :
			        case "apptivo_ecommerce_shopping_cart" :
					case "apptivo_ecommerce_products" :
					case "apptivo_ecommerce_print_receipt" :
					case "apptivo_ecommerce_order_number" :
						apptivo_ecommerce_admin_fields( $apptivo_ecommerce_settings[$current_tab] );
					break;
					
					case "payment_gateways" : 	
						echo '<br class="clear" />';
		            	foreach ( $apptivo_ecommerce->payment_gateways->payment_gateways() as $gateway ) :
		            		echo '<div class="section" id="gateway-'.$gateway->id.'">';
		            		$gateway->admin_options();
		            		echo '</div>';
		            	endforeach;
            		break;
            		default :
						do_action( 'apptivo_ecommerce_settings_tabs_' . $current_tab );
					break;
				endswitch;
			?>
	        <p class="submit"><input name="save" class="button-primary" type="submit" value="<?php _e( 'Save changes', 'apptivo_ecommerce' ); ?>" /></p>
		</form>
		<?php $ajax_admin_url = admin_url('admin-ajax.php');  ?>
		<script type="text/javascript">
		jQuery(document).ready(function(){		
			var formfield = '';     
			jQuery('.upload_image_button').click(function() {		
			formfield = jQuery(this).attr('rel');
			tb_show('Upload Image', 'media-upload.php?type=image&amp;TB_iframe=true');
			tbframe_interval = setInterval(function() {
	            jQuery('#TB_iframeContent').contents().find('.savesend .button').val('Use as Logo');
	            }, 2000);
			return false;
			});
		window.original_send_to_editor = window.send_to_editor;
		window.send_to_editor = function(html) {
			 imgurl = jQuery('img',html).attr('src');
			 jQuery('#'+formfield).val(imgurl);
			 tb_remove();
			}
		     });	     
		jQuery(document).ready(function($) {

		   var zipcode_calculation= jQuery('#apptivo_ecommerce_auto_zipcode_calculation').is(':checked');
           if(!zipcode_calculation)
           {
            	jQuery('#apptivo_ecommerce_auto_zipcode').attr("disabled", "disabled");
           }
           jQuery('#apptivo_ecommerce_auto_zipcode_calculation').click(function(){
        	   var zipcode_calculation= jQuery('#apptivo_ecommerce_auto_zipcode_calculation').is(':checked');
               if(!zipcode_calculation)
               {
               	jQuery('#apptivo_ecommerce_auto_zipcode').attr("disabled", "disabled");
              }else{
            	  jQuery('#apptivo_ecommerce_auto_zipcode').removeAttr("disabled"); 
              }
           });
           
			$("input#apptivo_ecommerce_ordernumber_startswith, input#apptivo_ecommerce_products_per_page, input#apptivo_ecommerce_catalog_image_width, input#apptivo_ecommerce_catalog_image_height").keypress(function (e){
				  var charCode = (e.which) ? e.which : e.keyCode;
				  if (charCode > 31 && (charCode < 48 || charCode > 57)) {
				    return false;
				  }
				});

			$("input#apptivo_ecommerce_single_image_width, input#apptivo_ecommerce_single_image_height, input#apptivo_ecommerce_thumbnail_image_width, input#apptivo_ecommerce_thumbnail_image_height").keypress(function (e){
				  var charCode = (e.which) ? e.which : e.keyCode;
				  if (charCode > 31 && (charCode < 48 || charCode > 57)) {
				    return false;
				  }
				});
			

	       $('#apptivo_ecommerce_apikey').live('blur',function(){
				var data = {
						apikey: 			$('#apptivo_ecommerce_apikey').val()
					};				
				$.ajax({
					type: 		'POST',
					url: 		'<?php echo $ajax_admin_url; ?>?action=apptivo_ecommerce_check_apikey',
					data: 		data,
					success: 	function( code ) {
				      if( code == 1005){
					      
				    	  var answer = confirm('Changing API Key will delete all eCommerce contents ( Products and Categories ) stored in Wordpress. Are you sure to change the API key?');
				    		if (answer){
				    			
				    			$.ajax({
									type: 		'POST',
									url: 		'<?php echo $ajax_admin_url; ?>?action=apptivo_ecommerce_update_apikey',
									data: 		data,
									success: 	function( code ) {
								    
										},
									dataType: 	"html"
								});	
				    		}
				    		else{
				    			$('#apptivo_ecommerce_apikey').val('<?php echo APPTIVO_ECOMMERCE_API_KEY; ?>');
				    		}				    	  
					      
				      }else{
					      return false;
				      }
						},
					dataType: 	"html"
				});				
			});			
			
			$('#apptivo_ecommerce_upload_template').live('click',function(){
				var answer = confirm('Are you sure to copy eCommerce templates to your current theme?');
	    		if (answer){
				$.ajax({
					type: 		'POST',
					url: 		'<?php echo $ajax_admin_url; ?>?action=apptivo_ecommerce_upload_theme_template',
					data: 		'',
					success: 	function( code ) {
				if(code == 1000 ){ 
						$('#update_template_ver').html('&nbsp;&nbsp;&nbsp;&nbsp;<b style="color:green">Products template successfully copied  into your theme</b>');
					}else {
						$('#update_template_ver').html('&nbsp;&nbsp;&nbsp;&nbsp;<b style="color:#f00">Failure. Please try again</b>');
					}

				 setTimeout(function () {
				        $('#update_template_ver').hide();
				    }, 4000);
				    
						},
					dataType: 	"html"
				});	
				return false;
	    		}	    		
			});
		})
		
		
		var checked_secure= jQuery('#apptivo_ecommerce_SecureCheckout_enabled').is(':checked');
		if(checked_secure)
		{
			jQuery('#gateway-SecureCheckout table tr:gt(0)').show();
		}else{
			jQuery('#gateway-SecureCheckout table tr:gt(0)').hide();
		}
		var checked_paypal= jQuery('#apptivo_ecommerce_paypal_enabled').is(':checked');
		if(checked_paypal)
		{
			jQuery('#gateway-paypal table tr:gt(0)').show();
		}else{
			jQuery('#gateway-paypal table tr:gt(0)').hide();
		}
		var checked_google= jQuery('#apptivo_ecommerce_GoogleCheckout_enabled').is(':checked');
		if(checked_google)
		{
			jQuery('#gateway-GoogleCheckout table tr:gt(0)').show();
		}else{
			jQuery('#gateway-GoogleCheckout table tr:gt(0)').hide();
		}	
		
		jQuery('#apptivo_ecommerce_SecureCheckout_enabled').click(function(){
				var checked= jQuery('#apptivo_ecommerce_SecureCheckout_enabled').is(':checked');
				if(checked)
				{
					jQuery('#gateway-SecureCheckout table tr:gt(0)').show();
				}else{
					jQuery('#gateway-SecureCheckout table tr:gt(0)').hide();
				}
			});
		
		jQuery('#apptivo_ecommerce_paypal_enabled').click(function(){
				var checked= jQuery('#apptivo_ecommerce_paypal_enabled').is(':checked');
				if(checked)
				{
					jQuery('#gateway-paypal table tr:gt(0)').show();
				}else{
					jQuery('#gateway-paypal table tr:gt(0)').hide();
				}
			});
		jQuery('#apptivo_ecommerce_GoogleCheckout_enabled').click(function(){
			var checked= jQuery('#apptivo_ecommerce_GoogleCheckout_enabled').is(':checked');
			if(checked)
			{
				jQuery('#gateway-GoogleCheckout table tr:gt(0)').show();
			}else{
				jQuery('#gateway-GoogleCheckout table tr:gt(0)').hide();
			}
		});
		</script>
	</div>
	<?php
}
}