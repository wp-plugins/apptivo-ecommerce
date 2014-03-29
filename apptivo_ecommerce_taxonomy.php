<?php
/**
 * Custom Post Types
 **/
function apptivo_ecommerce_post_type() {

	global $wpdb, $apptivo_ecommerce;
	$products_page_id = get_option('apptivo_ecommerce_products_page_id');
	$base_slug = ($products_page_id > 0 && get_page( $products_page_id )) ? get_page_uri( $products_page_id ) : 'products';	
	$product_base 	= '';
	$product_base = trailingslashit(__('product', 'apptivo-ecommerce'));
	$product_base = untrailingslashit($product_base);
  
	     $labels = array(
                    'name' => __( 'Product Categories', 'apptivo-ecommerce'),
                    'singular_name' => __( 'Product Item', 'apptivo-ecommerce'),
                    'search_items' =>  __( 'Search Product Categories', 'apptivo-ecommerce'),
                    'all_items' => __( 'All Product Categories', 'apptivo-ecommerce'),
                    'parent_item' => __( 'Parent Product Category', 'apptivo-ecommerce'),
                    'parent_item_colon' => __( 'Parent Product Category:', 'apptivo-ecommerce'),
                    'edit_item' => __( 'Edit Product Category', 'apptivo-ecommerce'),
                    'update_item' => __( 'Update Product Category', 'apptivo-ecommerce'),
                    'add_new_item' => __( 'Add New Product Category', 'apptivo-ecommerce'),
                    'new_item_name' => __( 'New Product Category Name', 'apptivo-ecommerce')
            ); 	


	register_taxonomy( 'item_cat',
        array('item'),
        array(
            'hierarchical' => true,
            'update_count_callback' => '_update_post_term_count',
            'label' => __( 'Categories', 'apptivo-ecommerce'),
            'labels' => $labels ,
            'show_ui' => true,
            'query_var' => true,
            'hierarchical' =>true,
            'rewrite' 	   => true,
        )
    );    
    register_taxonomy( 'item_tag',
	        array('item'),
	        array(
	            'hierarchical' 			=> false,
	            'update_count_callback' => '_update_post_term_count',
	            'label' 				=> __( 'Product Tags', 'apptivo-ecommerce'),
	            'labels' => array(
	                    'name' 				=> __( 'Product Tags', 'apptivo-ecommerce'),
	                    'singular_name' 	=> __( 'Product Tag', 'apptivo-ecommerce'),
						'menu_name'			=> _x( 'Product Tags', 'Admin menu name', 'apptivo-ecommerce' ),
	                    'search_items' 		=> __( 'Search Product Tags', 'apptivo-ecommerce'),
	                    'all_items' 		=> __( 'All Product Tags', 'apptivo-ecommerce'),
	                    'parent_item' 		=> __( 'Parent Product Tag', 'apptivo-ecommerce'),
	                    'parent_item_colon' => __( 'Parent Product Tag:', 'apptivo-ecommerce'),
	                    'edit_item' 		=> __( 'Edit Product Tag', 'apptivo-ecommerce'),
	                    'update_item' 		=> __( 'Update Product Tag', 'apptivo-ecommerce'),
	                    'add_new_item' 		=> __( 'Add New Product Tag', 'apptivo-ecommerce'),
	                    'new_item_name' 	=> __( 'New Product Tag Name', 'apptivo-ecommerce')
	            	),
	            'show_ui' 				=> true,
	            'query_var' 			=> true,
			    'rewrite' 				=> true,
	        )
	    );
	    
	    

	register_post_type( "item",
		array(
			'labels' => array(
				'name' => __( 'Products', 'apptivo-ecommerce' ),
				'singular_name' => __( 'Product', 'apptivo-ecommerce' ),
				'add_new' => __( 'Add Product', 'apptivo-ecommerce' ),
				'add_new_item' => __( 'Add New Product', 'apptivo-ecommerce' ),
				'edit' => __( 'Edit', 'apptivo-ecommerce' ),
				'edit_item' => __( 'Edit Product', 'apptivo-ecommerce' ),
				'new_item' => __( 'New Product', 'apptivo-ecommerce' ),
				'view' => __( 'View Product', 'apptivo-ecommerce' ),
				'view_item' => __( 'View Product', 'apptivo-ecommerce' ),
				'search_items' => __( 'Search Products', 'apptivo-ecommerce' ),
				'not_found' => __( 'No Products found', 'apptivo-ecommerce' ),
				'not_found_in_trash' => __( 'No Products found in trash', 'apptivo-ecommerce' ),
				'parent' => __( 'Parent Product', 'apptivo-ecommerce' )
			),
			'description' => '',
			'public' => true,
			'show_ui' => true,
		    'show_in_menu' =>true,
			'capability_type' => 'post',
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'hierarchical' => true,
			'rewrite' => array( 'slug' => $product_base, 'with_front' => false ),
			'query_var' => true,			
			'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
			'has_archive' => $base_slug,
			'show_in_nav_menus' => false,
		)
	);

} 

add_filter( 'gettext', 'apptivo_product_gettext', 10, 2 );
function apptivo_product_gettext( $translation, $original )
{
	global $post_type;
	if($post_type == 'item')
	{	
	   if('Featured Image' == $original)
	    {
	    	return 'Featured Image (Product Image)';
	    }
	}
	return $translation;
}

add_action( 'admin_head', 'hide_post_page_options'  );
function hide_post_page_options() {
	global $post_type;
	if($post_type == 'item')
	{
	$hide_post_options = "<style type=\"text/css\">#minor-publishing,#major-publishing-actions #delete-action,#misc-publishing-actions,#minor-publishing-actions #save-action,#post-preview, .misc-pub-section a { display: none; }
	                       #publishing-action{float:none;text-align:center;}</style>";
	print($hide_post_options);
	}
}
//Update Post Updated Messages.
add_filter('post_updated_messages','apptivo_ecommerce_post_updated_messages');
function apptivo_ecommerce_post_updated_messages($messages){
	global $post_type;
	if($post_type == 'item') :
	$messages['post'][1] = 'Product updated Sucessfully.';
	$messages['post'][6] = 'Product Added Sucessfully.';
	endif;
	return $messages;
}