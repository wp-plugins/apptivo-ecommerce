<?php
/*
 * Manage products columns
 * Sorting Products columns
 * atachment Image
 * exclude image
 */
 add_filter("manage_edit-item_cat_columns", 'apptivo_ecommerce_product_cat_columns');
 add_filter("manage_item_cat_custom_column", 'apptivo_ecommerce_apptivo_product_cat_column', 10, 3);	
  
 function apptivo_ecommerce_product_cat_columns( $columns ) {
 	$columns['id'] = 'ID';
 	return $columns;
 }
 
 function apptivo_ecommerce_apptivo_product_cat_column( $columns, $column, $id ) {
 	if ($column=='id') :
 		$columns = $id;
 	endif;
 	return $columns;	
 }
add_filter("manage_edit-item_cat_sortable_columns", 'apptivo_item_cat_sort');
function apptivo_item_cat_sort($columns) {
	$columns['id'] = 'id';
	return $columns;
}

/**
 * Columns for Items page
 **/

add_filter('manage_edit-item_columns', 'apptivo_ecommerce_edit_product_columns');

function apptivo_ecommerce_edit_product_columns($columns){
	
	
	global $post_type;
	if( $post_type != 'item' ) return $columns;
	
	$apptivo_ecommerce = apply_filters('apptivo_ecommerce_edit_product_columns_for_add_ons',$columns);
	
	$columns = array();	
	//$columns["cb"] = "<input type=\"checkbox\" />";	
	$columns["thumb"] = __("Image", 'apptivo-ecommerce');	
	$columns["title"] = __("Name", 'apptivo-ecommerce');	
	$columns["id"] = __("ID", 'apptivo-ecommerce');
	$columns["item_code"] = __("Item Code", 'apptivo-ecommerce');			
	$columns["item_cat"] = __("Categories", 'apptivo-ecommerce');
	$columns["item_tag"] = __("Tags", 'apptivo-ecommerce');
    if($apptivo_ecommerce == 'payments')
	{
		$columns["payments"] = __("Payments", 'apptivo-ecommerce');
	}	
	$columns["featured_item"] = __("Featured", 'apptivo-ecommerce');	
	$columns["enabled"] = __("Enabled", 'apptivo-ecommerce');	
	$columns["sale_price"] = __("Price", 'apptivo-ecommerce');	
	$columns["product_date"] = __("Date", 'apptivo-ecommerce');	
	return $columns;
}


/**
 * Custom Columns for Products page
 **/
add_action('manage_item_posts_custom_column', 'apptivo_ecommerce_custom_product_columns', 2);

function apptivo_ecommerce_custom_product_columns($column) {
	global $post, $apptivo_ecommerce;
	
	$product = &new apptivo_ecommerce_product($post->ID);

	switch ($column) {
		case "thumb" :
			if (has_post_thumbnail($post->ID)) :
				echo get_the_post_thumbnail($post->ID, 'product_thumbnail');
			endif;
		break;
		
		case "sale_price":
			echo $product->sale_regular_price_html();	
		break;
		
		case "item_cat" :
		case "item_tag" :
			if ( ! $terms = get_the_terms( $post->ID, $column ) ) {
				echo '<span class="na">&ndash;</span>';
			} else {
				foreach ( $terms as $term ) {
					$termlist[] = '<a href="' . admin_url( 'edit.php?' . $column . '=' . $term->slug . '&post_type=item' ) . ' ">' . $term->name . '</a>';
				}

				echo implode( ', ', $termlist );
			}
		break;
		
		case "id" :
				echo '#'.$post->ID;
			
		break;
		
		case "item_code" :
			if ( $item_code = get_post_meta( $post->ID, '_apptivo_item_code', true )) :
				echo $item_code;	
			else :
				echo '-';
			endif;
		break;
		
		case "featured_item" :
			if ($product->is_item_featured()) echo '<img src="'.$apptivo_ecommerce->plugin_url().'/assets/images/success.gif" alt="yes" />';
			else echo '<img src="'.$apptivo_ecommerce->plugin_url().'/assets/images/success-off.gif" alt="no" />';
		break;
		 
		case "enabled" :
			if ($product->is_item_enabled()) echo '<img src="'.$apptivo_ecommerce->plugin_url().'/assets/images/success.gif" alt="yes" />';
			else echo '<img src="'.$apptivo_ecommerce->plugin_url().'/assets/images/success-off.gif" alt="no" />';
		break;

		case "product_date" :
			if ( '0000-00-00 00:00:00' == $post->post_date ) :
				$t_time = $h_time = __( 'Unpublished' );
				$time_diff = 0;
			else :
				$t_time = get_the_time( __( 'Y/m/d g:i:s A' ) );
				$m_time = $post->post_date;
				$time = get_post_time( 'G', true, $post );

				$time_diff = time() - $time;

				if ( $time_diff > 0 && $time_diff < 24*60*60 )
					$h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
				else
					$h_time = mysql2date( __( 'Y/m/d' ), $m_time );
			endif;

			echo '<abbr title="' . $t_time . '">' . apply_filters( 'post_date_column_time', $h_time, $post ) . '</abbr><br />';
			
			
			
		break;
	}
}


/**
 * Make product columns sortable
 **/

add_filter("manage_edit-item_sortable_columns", 'apptivo_ecommerce_custom_product_sort');
function apptivo_ecommerce_custom_product_sort($columns) {
	$custom = array(
		'sale_price'			=> 'sale_price',
		'item_code'			=> 'item_code',
	    'id'			=> 'id',
	    'featured_item'		=> 'featured_item',
	    'enabled'	=> 'enabled',
		'product_date'	=> 'date'
	);
	return wp_parse_args($custom, $columns);
}


add_filter( 'request', 'apptivo_ecommerce_custom_item_orderby' );

function apptivo_ecommerce_custom_item_orderby( $vars ) {
	
	if (isset( $vars['orderby'] )) :		
		if ( 'sale_price' == $vars['orderby'] ) :
			$vars = array_merge( $vars, array(
				'meta_key' 	=> '_apptivo_sale_price',
				'orderby' 	=> 'meta_value_num'
			) );
		endif;
		
		if ( 'featured_item' == $vars['orderby'] ) :
			$vars = array_merge( $vars, array(
				'meta_key' 	=> '_apptivo_featured',
				'orderby' 	=> 'meta_value'
			) );
		endif;
		
		if ( 'enabled' == $vars['orderby'] ) :
			$vars = array_merge( $vars, array(
				'meta_key' 	=> '_apptivo_enabled',
				'orderby' 	=> 'meta_value'
			) );
		endif;
		
		if ( 'item_code' == $vars['orderby'] ) :
			$vars = array_merge( $vars, array(
				'meta_key' 	=> '_apptivo_item_code',
				'orderby' 	=> 'meta_value_num'
			) );
		endif;
		
		if ( 'id' == $vars['orderby'] ) :
			$vars = array_merge( $vars, array(
				'orderby' 	=> 'ID'
			) );
		endif;
	endif;
	
	return $vars;
}

/**
 * Add functionality to the image uploader on product pages to exlcude an image
 **/
add_filter('attachment_fields_to_edit', 'apptivo_ecommerce_exclude_image_from_product_page_field', 1, 2);
add_filter('attachment_fields_to_save', 'apptivo_ecommerce_exclude_image_from_product_page_field_save', 1, 2);

function apptivo_ecommerce_exclude_image_from_product_page_field( $fields, $object ) {
	
	if (!$object->post_parent) return $fields;
	
	$parent = get_post( $object->post_parent );
	
	if ($parent->post_type!=='item') return $fields;
	
	$exclude_image = (int) get_post_meta($object->ID, '_apptivo_ecommerce_exclude_image', true);
	
	$label = __('Exclude image', 'apptivo_ecommerce');
	
	$html = '<input type="checkbox" '.checked($exclude_image, 1, false).' name="attachments['.$object->ID.'][apptivo_ecommerce_exclude_image]" id="attachments['.$object->ID.'][apptivo_ecommerce_exclude_image" />';
	
	$fields['apptivo_ecommerce_exclude_image'] = array(
			'label' => $label,
			'input' => 'html',
			'html' =>  $html,
			'value' => '',
			'helps' => __('Enabling this option will hide it from the product page image gallery.', 'apptivo_ecommerce')
	);
	
	return $fields;
}

function apptivo_ecommerce_exclude_image_from_product_page_field_save( $post, $attachment ) {
	
	if (isset($_REQUEST['attachments'][$post['ID']]['apptivo_ecommerce_exclude_image'])) :
		delete_post_meta( (int) $post['ID'], '_apptivo_ecommerce_exclude_image' );
		update_post_meta( (int) $post['ID'], '_apptivo_ecommerce_exclude_image', 1);
	else :
		delete_post_meta( (int) $post['ID'], '_apptivo_ecommerce_exclude_image' );
		update_post_meta( (int) $post['ID'], '_apptivo_ecommerce_exclude_image', 0);
	endif;
			
	return $post;				
}