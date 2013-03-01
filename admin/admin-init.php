<?php
/**
 * apptivo eCommerce Admin settings
 * 
 * Main admin file which loads all settings panels and sets up admin menus.
 *
 * @category 	Admin
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */
include_once( 'admin-install.php' );
function apptivo_ecommerce_admin_init() {
	include_once( 'admin-settings-forms.php' );
	include_once( 'admin-settings.php' );		
	include_once( 'admin-post-types.php' );	
	include_once( 'admin-taxonomies.php' );
	include_once( 'metaboxes/metaboxes-init.php' );	
}
add_action('admin_init', 'apptivo_ecommerce_admin_init');
/**
 * Admin Menus
 * 
 * Sets up the admin menus in wordpress.
 */
function apptivo_ecommerce_admin_menu() {
	global $apptivo_ecommerce;	
	add_menu_page(__('eCommerce Settings', 'apptivo_ecommerce'), __('eCommerce Settings', 'apptivo_ecommerce'), 'manage_apptivo_ecommerce', 'apptivo_ecommerce' , 'apptivo_ecommerce_settings',APPTIVO_ECOMMERCE_PLUGIN_BASEURL.'/assets/images/icons/apptivo.png',85);
    add_submenu_page('apptivo_ecommerce','eCommerce Settings','General', 'manage_apptivo_ecommerce', 'apptivo_ecommerce', 'apptivo_ecommerce_settings');
    add_submenu_page('apptivo_ecommerce','eCommerce Settings','Products', 'manage_apptivo_ecommerce', 'apptivo_ecommerce_products', 'apptivo_ecommerce_settings');
    add_submenu_page('apptivo_ecommerce','eCommerce Settings','Shopping Cart', 'manage_apptivo_ecommerce', 'apptivo_ecommerce_shopping_cart', 'apptivo_ecommerce_settings');
    add_submenu_page('apptivo_ecommerce','eCommerce Settings','Pages', 'manage_apptivo_ecommerce', 'apptivo_ecommerce_pages', 'apptivo_ecommerce_settings');
    add_submenu_page('apptivo_ecommerce','eCommerce Settings','Print Receipt', 'manage_apptivo_ecommerce', 'apptivo_ecommerce_print_receipt', 'apptivo_ecommerce_settings');
    add_submenu_page('apptivo_ecommerce','eCommerce Settings','Order Number', 'manage_apptivo_ecommerce', 'apptivo_ecommerce_order_number', 'apptivo_ecommerce_settings');
    add_submenu_page('apptivo_ecommerce','eCommerce Settings','Payment Gateways', 'manage_apptivo_ecommerce', 'payment_gateways', 'apptivo_ecommerce_settings');    
    add_submenu_page('apptivo_ecommerce','eCommerce Sync',  'Sync' , 'manage_apptivo_ecommerce', 'apptivo_ecommerce_syncs', 'apptivo_ecommerce_syncs');
       
    if( is_admin())
    {
    	wp_enqueue_style( 'apptivo_ecommerce_plugin_styles', $apptivo_ecommerce->plugin_url() . '/assets/css/ecommerce-plugin.css' );	
    }
}
add_action('admin_menu', 'apptivo_ecommerce_admin_menu', 9);

/**
 * Admin Scripts
 */
function apptivo_ecommerce_admin_scripts() {
	// Get admin screen id
    $screen = get_current_screen();
    // apptivo_ecommerce admin pages
    if (in_array( $screen->id, array( 'toplevel_page_apptivo_ecommerce','edit-item', 'item' ))) :    
    	wp_deregister_script('autosave');//Remove Auto Save Scripts
    	wp_enqueue_script('farbtastic');    
    endif;
    
    // Edit product category pages
    if (in_array( $screen->id, array('edit-apptivo_product_cat','toplevel_page_apptivo_ecommerce') )) :
		wp_enqueue_script( 'thickbox' );
		wp_deregister_script('autosave');//Remove Auto Save Scripts
	endif;

	if (in_array( $screen->id, array('ecommerce-settings_page_apptivo_ecommerce_print_receipt') )) :
 	 wp_enqueue_style( 'thickbox' );
	 wp_enqueue_script( 'media-upload' );
	endif;
	
	// Product
	if (in_array( $screen->id, array('item' ))) :
	    wp_deregister_script('autosave');//Remove Auto Save Scripts
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );		
		
	endif;
	
	/* shortcode in editor */
	if (in_array( $screen->id, array( 'post','page' ))) {
				
	if ( get_user_option('rich_editing') == 'true' && ( current_user_can('edit_posts') && current_user_can('edit_pages') ) ) :
		add_filter('mce_buttons', 'apptivo_ecommerce_add_shortcode_tinymce_plugin');		
		add_filter('mce_external_plugins', 'apptivo_ecommerce_register_shortcode_button');
	endif;
	
	 }
	
}
add_action('admin_enqueue_scripts', 'apptivo_ecommerce_admin_scripts');

/** Admin menus Order  */
function apptivo_ecommerce_admin_menu_order( $menu_order ) {
	// Initialize our custom order array
	$apptivo_ecommerce_menu_order = array();
		// Get index of product menu
	$apptivo_ecommerce_product = array_search( 'edit.php?post_type=item', $menu_order );
	// Loop through menu order and do some rearranging
	foreach ( $menu_order as $index => $item ) :
		if ( ( ( 'apptivo_ecommerce' ) == $item ) ) :
		   $apptivo_ecommerce_menu_order[] = 'apptivo_ecommerce';
			$apptivo_ecommerce_menu_order[] = 'edit.php?post_type=item';
			unset( $menu_order[$apptivo_ecommerce_product] );
		elseif ( !in_array( $item, array( 'separator-apptivo_ecommerce' ) ) ) :
			$apptivo_ecommerce_menu_order[] = $item;
		endif;
	endforeach;
	// Return order
	return $apptivo_ecommerce_menu_order;
}
add_action('menu_order', 'apptivo_ecommerce_admin_menu_order');

function apptivo_ecommerce_admin_custom_menu_order() {
	if ( !current_user_can( 'manage_apptivo_ecommerce' ) ) return false;
	return true;
}
add_action('custom_menu_order', 'apptivo_ecommerce_admin_custom_menu_order');


function apptivo_ecommerce_admin_head() {
	global $apptivo_ecommerce;
	?>
	<style type="text/css">
	    #menu-posts-item .wp-menu-image{background:url(<?php echo $apptivo_ecommerce->plugin_url(); ?>/assets/images/icons/apptivo.png) no-repeat  !important; margin: 5px 0px 0px 5px;table.wp-list-table .column-thumb{width:66px;text-align:center;white-space:nowrap;}
		table.wp-list-table img{margin:1px 2px;}
		table.wp-list-table .column-thumb img{padding:2px;margin:0;border:1px solid #dfdfdf;vertical-align:middle;width:42px;height:42px;}
		table.wp-list-table span.na{color:#999;}
		table.wp-list-table .column-featured,table.wp-list-table .column-is_in_stock{text-align:left !important;}					
	</style>
    <!-- Add seperator menu if not exists -->
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		if(!$('li#menu-posts-item').next('li.wp-menu-separator').length){
			$('li#menu-posts-item').after('<li class="wp-not-current-submenu wp-menu-separator"><div class="separator"></div></li>');
		}
		$('li#toplevel_page_apptivo_ecommerce').before('<li class="wp-not-current-submenu wp-menu-separator"><div class="separator"></div></li>');
	});
	</script>
	
	<?php
}
add_action('admin_head', 'apptivo_ecommerce_admin_head');	
/**  UnSet Product Post Links */
function apptivo_ecommerce_unset_product_link_row($actions, $post) {
	
	if (function_exists('duplicate_post_plugin_activation')) return $actions;	
	if (!current_user_can('manage_apptivo_ecommerce')) return $actions;	
	if ($post->post_type!='item') return $actions;	
    unset( $actions['inline hide-if-no-js'] );
	unset( $actions['trash'] );	
	return $actions;
}
add_filter('post_row_actions', 'apptivo_ecommerce_unset_product_link_row',10,2);
add_filter('page_row_actions', 'apptivo_ecommerce_unset_product_link_row',10,2);

function apptivo_item_cat_row_actions($actions,$tag)
{
	unset($actions['delete']);
	unset( $actions['inline hide-if-no-js'] );
	return $actions;
}
add_filter('item_cat_row_actions','apptivo_item_cat_row_actions',10,4);

function apptivo_delete_bulk_actions($actions){
	global $post_type;
	if( $post_type == 'item' )
	{     
	 $actions = array();
	}
	return $actions;
}
add_filter('bulk_actions-edit-item_cat','apptivo_delete_bulk_actions');
add_filter('bulk_actions-edit-item','apptivo_delete_bulk_actions');