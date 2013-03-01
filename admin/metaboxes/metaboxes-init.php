<?php
/**
 * Apptivo Ecommerce Write Panels.
 * 
 * Sets up the write panels used by products and orders (custom post types)
 * @category 	Admin Write Panels
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */
require_once('metaboxes-product_data.php');
/**
 * Init the meta boxes
 * 
 * Inits the write panels for both products and orders. Also removes unused default write panels.
 */
add_action( 'add_meta_boxes', 'apptivo_ecommerce_meta_boxes' );
function apptivo_ecommerce_meta_boxes() {
	
	// Post Excerpt
	if ( function_exists('wp_editor') ) {
		remove_meta_box( 'postexcerpt', 'product', 'normal' );
		add_meta_box( 'postexcerpt','Product Short Description', 'apptivo_ecommerce_item_short_description_meta_box', 'item', 'normal' );
	}
	add_meta_box( 'apptivo_ecommerce-item-data', __('Product Visibility & Data', 'apptivo_ecommerce'), 'apptivo_ecommerce_product_data_box', 'item', 'normal', 'high' );	
}

function apptivo_ecommerce_item_short_description_meta_box( $post ) {
	
	$settings = array(
		'quicktags' 	=> array( 'buttons' => 'em,strong,link' ),
		'textarea_name'	=> 'excerpt',
		'quicktags' 	=> true,
		'tinymce' 		=> true,
		'editor_css'	=> '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>'
		);
		
	wp_editor( htmlspecialchars_decode( $post->post_excerpt ), 'excerpt', $settings );
global $apptivo_ecommerce;	
?>
<style type="text/css">#submitdiv h3 span,#postimagediv h3 span,#postexcerpt h3 span,#tagsdiv-item_tag h3 span {background:url(<?php echo $apptivo_ecommerce->plugin_url(); ?>/assets/images/icons/apptivo.png) no-repeat scroll 8px center transparent;
display: inline-block;margin: 0;padding: 2px 2px 2px 34px; }</style>
<?php 
}
	

/**
 * Save meta boxes
 */
add_action( 'save_post', 'apptivo_ecommerce_meta_boxes_save', 1, 2 );
function apptivo_ecommerce_meta_boxes_save( $post_id, $post ) {
	if ( !$_POST ) return $post_id;
	if ( $post->post_type != 'item' ) return $post_id;
	do_action( 'apptivo_ecommerce_process_'.$post->post_type.'_meta', $post_id, $post );
}
/**
 * Save Errors
 */
add_action( 'admin_notices', 'apptivo_ecommerce_meta_boxes_save_errors' );
function apptivo_ecommerce_meta_boxes_save_errors() {
	$apptivo_ecommerce_errors = maybe_unserialize(get_option('apptivo_ecommerce_errors'));
    if ($apptivo_ecommerce_errors && sizeof($apptivo_ecommerce_errors)>0) :
    	echo '<div id="apptivo_ecommerce_errors" class="error fade">';
    	foreach ($apptivo_ecommerce_errors as $error) :
    		echo '<p>'.$error.'</p>';
    	endforeach;
    	echo '</div>';
    	update_option('apptivo_ecommerce_errors', '');
    endif; 
}
/**
 * Output write panel form elements
 */
function apptivo_ecommerce_wp_text_input( $field ) {
	global $thepostid, $post;
	
	if (!$thepostid) $thepostid = $post->ID;
	if (!isset($field['placeholder'])) $field['placeholder'] = '';
	if (!isset($field['class'])) $field['class'] = 'short';
	if (!isset($field['value'])) $field['value'] = get_post_meta($thepostid, $field['id'], true);
	if( $field['value'] == '' ){
		if( $field['id'] == '_apptivo_item_code' ){
			$field['value'] = $thepostid;
		}
	}
	echo '<p class="form-field '.$field['id'].'_field">
	      <label for="'.$field['id'].'">'.$field['label'].'</label>
	      <input type="text" class="'.$field['class'].'" name="'.$field['id'].'" id="'.$field['id'].'" value="'.esc_attr( $field['value'] ).'" placeholder="'.$field['placeholder'].'" />
	      '.$field['notes'];
	
	if (isset($field['description'])) echo '<span class="description">' .$field['description'] . '</span>';
		
	echo '</p>';
}

function apptivo_ecommerce_wp_select_suppliers( $field ) {
	global $thepostid, $post;
	
	if (!$thepostid) $thepostid = $post->ID;
	if (!isset($field['class'])) $field['class'] = 'select short';
	if (!isset($field['value'])) $field['value'] = get_post_meta($thepostid,'_apptivo_supplier', true);
	

	$apptivo_suppliers = app_convertObjectToArray(getSupliers()->return->supplierList);
	
	echo '<p class="form-field '.$field['id'].'_field"><label for="'.$field['id'].'">'.$field['label'].'</label><select id="'.$field['id'].'" name="'.$field['id'].'" class="'.$field['class'].'">';
	echo '<option value="" >-- None --</option>';
	echo '<option value="add_new_suppliers" >-- Add New Supplier --</option>';	
	if(!empty($apptivo_suppliers[0])) :
	foreach ($apptivo_suppliers as $suppliers) :		
		echo '<option value="'.$suppliers->supplierName.'" ';
		selected($field['value'], $suppliers->supplierName);
		echo '>'.$suppliers->supplierName.'</option>';		
	endforeach;
	endif;
	 
	echo '</select> ';
	if (isset($field['description'])) echo '<span class="description">' .$field['description'] . '</span>';
	echo '</p>';
}

function apptivo_ecommerce_wp_checkbox( $field ) {
	global $thepostid, $post;
	if (!$thepostid) $thepostid = $post->ID;
	if (!isset($field['class'])) $field['class'] = 'checkbox';
	if (!isset($field['value'])) $field['value'] = get_post_meta($thepostid, $field['id'], true);	
	echo '<p class="form-field '.$field['id'].'_field"><label for="'.$field['id'].'">'.$field['label'].'</label><input type="checkbox" class="'.$field['class'].'" name="'.$field['id'].'" id="'.$field['id'].'" ';
	checked($field['value'], 'yes');
	echo ' /> ';
	if (isset($field['description'])) echo '<span class="description">' .$field['description'] . '</span>';
	echo '</p>';
}