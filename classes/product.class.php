<?php
/**
 * Product Class
 */
class apptivo_ecommerce_product {
	
	var $id;
	var $_apptivo_track_color;
	var $_apptivo_track_size;
	var $_apptivo_sale_price;
	var $_apptivo_regular_price;
	var $_apptivo_item_id;
	var $_apptivo_item_uom_id;

	
	/**
	 * Loads all product data from custom fields
	 *
	 * @param   int		$id		ID of the product to load
	 */
	function apptivo_ecommerce_product( $id) {
		
		$this->id = (int)$id;		
		$app_item_id = get_post_meta($this->id,'_apptivo_item_id',true); 
		$app_item_uom_id = get_post_meta($this->id,'_apptivo_item_uom_id',true);
		$app_item_manufactured_id = get_post_meta($this->id,'_apptivo_item_manufactured_id',true);        
		$this->product_custom_fields = get_post_custom( $this->id );		
		$this->exists = (sizeof($this->product_custom_fields)>0) ? true : false;
		// Define the data we're going to load: Key => Default value
		$load_data = array(
			'_apptivo_item_code'			=> $this->id,
		    '_apptivo_track_color' => '',
		    '_apptivo_track_size' => '',	
			'_apptivo_sale_price'	=> '',
			'_apptivo_regular_price' => '',			
		    '_apptivo_item_id'       => $awpItemId,//$app_item_id,
		    '_apptivo_item_uom_id'   => $awpItem_UOMId,//$_apptivo_item_uom_id
			'_apptivo_item_manufactured_id' => $app_item_manufactured_id	    
		);
		
		// Load the data from the custom fields
		foreach ($load_data as $key => $default) :
			$this->$key = (isset($this->product_custom_fields[$key][0]) && $this->product_custom_fields[$key][0]!=='') ? $this->product_custom_fields[$key][0] : $default;
		endforeach;
		
		// Load serialised data, unserialise twice to fix WP bug
		if (isset($this->product_custom_fields['product_attributes'][0])) $this->attributes = maybe_unserialize( maybe_unserialize( $this->product_custom_fields['product_attributes'][0] )); else $this->attributes = array();		
						
		
	}
	
	/** Get the add to cart url */
	function add_to_shopping_cart_url() {
		global $apptivo_ecommerce;
		$url = add_query_arg('add-to-cart', $this->id.'+'.$this->_apptivo_item_id.'+'.$this->_apptivo_item_uom_id);
		$url = $apptivo_ecommerce->nonce_url( 'add_to_cart', $url );
		return $url;
	}
	
	/** Returns whether or not the product is featured */
	function is_item_featured() {
		if (get_post_meta($this->id, '_apptivo_featured', true)=='yes') return true;
		return false;
	}
	
	
/** Returns whether or not the product is enabled */
	function is_item_enabled() {
		if (get_post_meta($this->id, '_apptivo_enabled', true)=='yes') return true;
		return false;
	}
	
	
	
	
	/** Returns the product's price */
	function sale_price() {
		return $this->_apptivo_sale_price;
	}
	
	
	/** Returns the price in html format */
	function sale_regular_price_html() {
		$price = '';
		
		if( $this->_apptivo_sale_price <= 0)
		{
			 $price .= 'This product is not ready for sale';
			 $price = apply_filters('apptivo_ecommerce_empty_price_html', $price, $this);
			 return $price;		
		}
			if ($this->_apptivo_sale_price) :
				if (isset($this->_apptivo_regular_price)) :
				     if($this->_apptivo_regular_price == '' ) { $this->_apptivo_regular_price = '0.00'; } 				
					$price .= '<del>'.apptivo_ecommerce_price( $this->_apptivo_regular_price ).'</del> <ins>'.apptivo_ecommerce_price($this->_apptivo_sale_price).'</ins>';
					$price = apply_filters('apptivo_ecommerce_sale_price_html', $price, $this);	
									
				else :	
				    $price .= apptivo_ecommerce_price($this->sale_price());					
					$price = apply_filters('apptivo_ecommerce_price_html', $price, $this);					
				endif;
			elseif ($this->_apptivo_sale_price === '' ) :
			    $price .= 'This product is not ready for sale';
			    $price = apply_filters('apptivo_ecommerce_empty_price_html', $price, $this);				
			elseif ($this->_apptivo_sale_price === '0' ) :			
				$price = __('Free!', 'apptivo_ecommerce');  
				$price = apply_filters('apptivo_ecommerce_free_price_html', $price, $this);
				
			endif;
	
		
		return $price;
	}
	
	function get_sizes()
	{
	  if( $this->_apptivo_track_size == '') :
	    return '';
	  else :
	    $sizes = explode(',',$this->_apptivo_track_size);
	    return $sizes;
	  endif;
	    
	}
	
  function get_colors()
	{
	  if( $this->_apptivo_track_color == '') :
	    return '';
	  else :
	    $colors = explode(',',$this->_apptivo_track_color);
	    return $colors;
	  endif;
	    
	}

}